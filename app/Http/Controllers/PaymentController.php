<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    // Step 1: Show checkout page
    public function checkout()
    {
        return view('checkout'); // create a checkout.blade.php
    }

    // Step 2: Initiate payment
    public function pay(Request $request, $id)
    {
        // Check for already applied or not
        $applied = DB::table('applications')
            ->where('applications.job_id', $id)
            ->where('applications.applicant_id', auth()->id())
            ->exists();

        if (!$applied) {
            return response()->json([
                'message' => 'Apply first then apply for this job !!'
            ]);
        }

        $post_data = [
            'store_id' => env('SSLCOMMERZ_STORE_ID'),
            'store_passwd' => env('SSLCOMMERZ_STORE_PASSWORD'),
            'total_amount' => 100,
            'currency' => 'BDT',
            'tran_id' => "trans_" . uniqid() . "_id_" . $id, // unique transaction id
            'success_url' => route('payment.success'),
            'fail_url' => route('payment.fail'),
            'cancel_url' => route('payment.cancel'),
            'cus_name' => auth()->user()->name,
            'cus_email' => auth()->user()->email,
            'cus_add1' => 'Dhaka',
            'cus_city' => 'Dhaka',
            'cus_state' => 'Dhaka',
            'cus_postcode' => '1200',
            'cus_country' => 'Bangladesh',
            'cus_phone' => '01928788589',
            'shipping_method' => 'NO',
            'product_name' => 'Job Posting Fee',
            'product_category' => 'job',
            'product_profile' => 'job-application',
        ];

        $response = Http::asForm()->post('https://sandbox.sslcommerz.com/gwprocess/v4/api.php', $post_data);
        $sslcommerzResponse = $response->json();
        // return response()->json($response->json());
        return response()->json($sslcommerzResponse['redirectGatewayURL']);

        if (!empty($sslcommerzResponse['GatewayPageURL'])) {
            // return redirect()->away($sslcommerzResponse['GatewayPageURL']);
            return redirect()->away($sslcommerzResponse['redirectGatewayURL']);
        } else {
            return response()->json(
                [
                    'message' => 'Payment initiation failed!'
                ]
            );
        }
    }

    // Step 3: Handle success callback
    public function success(Request $request)
    {
        // Payment success data
        $transaction_id = $request->input('tran_id');
        $ids = explode('_', $transaction_id);
        $transaction_id = $ids[1];
        $application_id = $ids[3];

        $application = Application::find($application_id);
        $application->status = 'paid';
        $application->save();

        $payment = Payment::create([
            'application_id' => $application_id,
            'amount' => $request->input('amount'),
            'payment_method' => $request->input('card_type'),
            'transaction_id' => $transaction_id,
            'status' => 'paid',
        ]);

        return response()->json([
            'message' => 'Payment and Application successfully completed !',
            'invice' => $payment
        ]);
    }

    public function fail(Request $request)
    {
        return response()->json([
            'message' => 'Payment failed !'
        ]);
    }

    public function cancel(Request $request)
    {
        return response()->json([
            'message' => 'Payment canceled !'
        ]);
    }
}
