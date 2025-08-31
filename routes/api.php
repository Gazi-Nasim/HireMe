<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Backend\AdminController;
use App\Http\Controllers\Backend\EmployerController;
use App\Http\Controllers\Backend\JobSeekerController;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post("/register", [RegisterController::class, 'register'])->name('register');
Route::post("/login", [LoginController::class, 'login'])->name('login');
Route::post("/logout", [LoginController::class, 'logout'])->name('logout');

// Admin Routes
Route::middleware(['role:admin'])->group(function () {
    Route::get("/admin/users", [AdminController::class, 'users']);
    Route::get("/admin/dashboard", [AdminController::class, 'dashboard']);
    Route::get("/admin/users/{id}/edit", [AdminController::class, 'editUser']);
    Route::put("/admin/users/{id}/update", [AdminController::class, 'updateUser']);
    Route::delete("/admin/users/{id}/delete", [AdminController::class, 'deleteUser']);

    Route::get("/admin/jobs", [AdminController::class, 'allJobs']);
    Route::get("/admin/jobs/{id}/edit", [AdminController::class, 'editJob']);
    Route::put("/admin/jobs/{id}/update", [AdminController::class, 'updateJob']);
    Route::delete("/admin/jobs/{id}/delete", [AdminController::class, 'deleteJob']);
    Route::get("/admin/analytics", [AdminController::class, 'analytics']);
    Route::get("/admin/applications", [AdminController::class, 'applications']);
    Route::get("/admin/google-analytics", function () {
        return redirect()->away('https://analytics.google.com/analytics/web/#/a366726606p503031516/realtime/pages?params=_u..nav%3Dmaui');
    });
});

// Employer Routes

Route::middleware(['role:employer'])->group(function () {
    Route::get("/employer/jobs", [EmployerController::class, 'myJobs']);
    Route::post("/employer/post-job", [EmployerController::class, 'createJob']);
    Route::get("/employer/{id}/edit-job", [EmployerController::class, 'editJob']);
    Route::put("/employer/{id}/update-job", [EmployerController::class, 'updateJob']);
    Route::delete("/employer/{id}/delete-job", [EmployerController::class, 'deleteJob']);
    Route::get("/employer/applicants", [EmployerController::class, 'employerApplications']);
    Route::patch("/employer/applications/{id}/accept", [EmployerController::class, 'acceptApplication']);
    Route::patch("/employer/applications/{id}/reject", [EmployerController::class, 'rejectApplication']);
});

// Job Seeker Routes
Route::middleware('role:jobseeker')->group(function () {
    Route::get("/jobseeker/jobs", [JobSeekerController::class, 'allJobs']);
    Route::get("/jobseeker/jobs/{id}/view", [JobSeekerController::class, 'getJob']);
    Route::post("/jobseeker/jobs/{id}/apply", [JobSeekerController::class, 'applyJob']);
    Route::get("/jobseeker/applications", [JobSeekerController::class, 'myApplications']);
    Route::post('/jobseeker/jobs/{id}/pay', [PaymentController::class, 'pay'])->name('payment.pay');
});
