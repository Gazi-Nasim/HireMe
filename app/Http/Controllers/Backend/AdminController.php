<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\EmployerJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function users()
    {
        $users = User::all();
        return response()->json(['users' => $users]);
    }

    public function editUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        return response()->json(['user' => $user]);
    }

    public function updateUser(Request $request, $id)
    {
        if (User::where('email', $request->email)->exists()) {
            return response()->json(['error' => 'Email already exists'], 400);
        }
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        User::where('id', $id)->update([
            'name' => request('name'),
            'email' => request('email'),
            'role' => request('role'),
            'password' => request('password'),
        ]);
        return response()->json(['user' => $user]);
    }

    public function deleteUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }


    public function editJob($id)
    {
        $job = EmployerJob::find($id);
        if (!$job) {
            return response()->json(['error' => 'Job not found'], 404);
        }
        return response()->json(['job' => $job]);
    }

    public function updateJob($id)
    {
        $validation = Validator::make(request()->all(), [
            'title' => 'required',
            'description' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json(['error' => $validation->errors()], 400);
        }

        $job = EmployerJob::find($id);
        if (!$job) {
            return response()->json(['error' => 'Job not found'], 404);
        }

        EmployerJob::where('id', $id)->update([
            'title' => request('title'),
            'description' => request('description'),
        ]);
        return response()->json(['message' => 'Job updated successfully']);
    }

    public function deleteJob($id)
    {
        $job = EmployerJob::find($id);
        if (!$job) {
            return response()->json(['error' => 'Job not found'], 404);
        }
        $job->delete();
        return response()->json(['message' => 'Job deleted successfully']);
    }

    public function allJobs(Request $request)
    {
        $validation = Validator::make(request()->all(), [
            'company_id' => 'nullable|exists:users,id',
            'status'     => 'nullable|in:active,inactive',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'error' => $validation->errors(),
                'valid_status' => ['active', 'inactive']
            ], 400);
        }
        $query = EmployerJob::query()->with('user');

        if ($request->has('company_id')) {
            $query->where('user_id', $request->company_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $jobs = $query->get();
        return response()->json(['jobs' => $jobs]);
    }

    public function analytics()
    {
        $jobs = [
            'total_jobs' => EmployerJob::count(),
            'total_applications' => \App\Models\Application::count(),
            'total_employers' => User::where('role', 'employer')->count(),
            'total_jobseekers' => User::where('role', 'jobseeker')->count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'total_users' => User::count(),
            'payments' => \App\Models\Payment::sum('amount'),
        ];
        return response()->json(['analytics' => $jobs]);
    }

    public function applications(Request $request)
    {
        $validation = Validator::make(request()->all(), [
            'company_id' => 'nullable|exists:users,id',
            'status'     => 'nullable|in:paid,pending,rejected,accepted',
        ]);

        if ($validation->fails()) {
            return response()->json(['error' => $validation->errors(), 'valid_status' => ['paid', 'pending', 'rejected', 'accepted']], 400);
        }

        $query = \App\Models\Application::query()->with('job', 'job.user');
        if ($request->has('company_id')) {
            $query->whereHas('job.user', function ($q) use ($request) {
                $q->where('id', $request->company_id);
            });
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        $applications = $query->get();
        return response()->json(['applications' => $applications]);
    }
}
