<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\EmployerJob;
use Dotenv\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class EmployerController extends Controller
{

    public function createJob(Request $request)
    {
        $validate = FacadesValidator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'salary_range' => 'required',
            'location' => 'required|string',
        ]);

        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }

        $job = EmployerJob::create([
            'title' => $request->input('title'),
            'user_id' => auth()->id(),
            'salary_range' => $request->input('salary_range'),
            'location' => $request->input('location'),
            'status' => $request->input('status') ?? 'active',
            'description' => $request->input('description'),
        ]);
        return response()->json(['message' => 'Job created successfully', 'job' => $job], 201);
    }

    public function myJobs()
    {
        $jobs = EmployerJob::where('user_id', auth()->id())->get();
        return response()->json(['jobs' => $jobs]);
    }

    public function editJob($id)
    {
        $job = EmployerJob::where('user_id', auth()->id())
            ->where('id', $id)
            ->first();
        return response()->json(['job' => $job]);
    }

    public function updateJob(Request $request, $id)
    {
        $job = EmployerJob::where('user_id', auth()->id())
            ->where('id', $id)
            ->first();

        // Update the request fields only
        $job->fill($request->only([
            'title',
            'description',
            'status',
            'salary_range',
            'location',
        ]));

        // $job->title = $request->title;
        // $job->description = $request->description;
        $job->status = $request->status ?? 'active';
        // $job->salary_range = $request->salary_range;
        // $job->location = $request->location;
        $job->save();
        return response()->json(['message' => 'Job updated successfully']);
    }

    public function deleteJob($id)
    {
        $job = EmployerJob::where('user_id', auth()->id())
            ->where('id', $id)
            ->first();
        $job->delete();
        return response()->json(['message' => 'Job deleted successfully']);
    }


    public function employerApplications()
    {
        $applications = DB::table('applications')
            ->join('employer_jobs', 'applications.job_id', '=', 'employer_jobs.id') // join by job_id, not user_id
            ->join('users', 'employer_jobs.user_id', '=', 'users.id')
            ->join('payments', 'applications.id', '=', 'payments.application_id')
            ->where('users.id', auth()->id()) // filter by current user
            ->where('payments.status', 'paid')
            ->select('employer_jobs.title', 'employer_jobs.description', 'applications.id', 'applications.cv', 'applications.status')
            ->get();
        return response()->json(['applications' => $applications]);
    }
    
    public function acceptApplication($id)
    {
        Application::where('id', $id)->update(['status' => 'accepted']);
        return response()->json(['message' => 'Application accepted successfully']);
    }

    public function rejectApplication($id)
    {
        Application::where('id', $id)->update(['status' => 'rejected']);
        return response()->json(['message' => 'Application rejected successfully']);
    }
}
