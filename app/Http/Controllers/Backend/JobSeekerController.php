<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobSeekerController extends Controller
{
    public function allJobs()
    {
        $jobs = \App\Models\EmployerJob::all();
        return response()->json(['jobs' => $jobs]);
    }

    public function getJob($id)
    {
        $job = \App\Models\EmployerJob::find($id);
        return response()->json(['job' => $job]);
    }
    public function applyJob(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'cv' => 'required|mimes:pdf,doc,docx',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $alreadyApplied = Application::where('applicant_id', auth()->id())
            ->where('job_id', $id)
            ->exists();

        if ($alreadyApplied) {
            return response()->json([
                'message' => 'You have already applied for this job.'
            ], 400);
        }

        $cv = $request->file('cv');
        $cvName = auth()->user()->name . '-' . time() . '.' . $cv->getClientOriginalExtension();
        $cv->move(public_path('storage/cvs'), $cvName);

        Application::create(
            [
                'job_id' => $id,
                'applicant_id' => auth()->id(),
                'cv' => $cvName,
                'status' => 'pending'
            ]
        );

        return response()->json(['message' => 'Complete payment to finish application']);
    }

    public function myApplications()
    {
        $jobs = Application::with('job')->where('applicant_id', auth()->id())->get();
        return response()->json(['jobs' => $jobs]);
    }
}
