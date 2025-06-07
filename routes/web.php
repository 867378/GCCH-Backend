<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\GoogleDriveController;
use App\Http\Controllers\ReportController;
use App\Http\Middleware\JWTMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('auth.google-auth');
})->name('login');

Route::prefix('auth')->group(function () {
    Route::get('google/redirect', [UserController::class, 'redirectToGoogle'])->name('google.redirect');
    Route::get('google/callback', [UserController::class, 'handleGoogleCallback'])->name('google.callback');
});


Route::middleware(['auth:sanctum','company'])->group(function () {
    Route::get('/preview/job-report/{job}', [ReportController::class, 'previewJobReport']);
    Route::get('/api/report/job/{id}/download', [ReportController::class, 'downloadReport']);
});


Route::middleware(['auth:sanctum','applicant'])->group(function () {
    Route::get('/preview/certificate/{applicationId}', [ReportController::class, 'previewCertificate']);
    Route::get('/api/certificate/download/{applicationId}', [ReportController::class, 'downloadCertificate']);
});


// Public route for deleting all data (if needed)
Route::get('/delete-all-data', function () {
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    DB::table('applicants')->truncate();
    DB::table('companies')->truncate();
    DB::table('users')->truncate();
    DB::table('jobs')->truncate();
    DB::table('job_applications')->truncate();
    DB::table('resumes')->truncate();
    DB::table('cover_letters')->truncate();
    DB::table('sessions')->truncate();
    DB::table('messages')->truncate();
    DB::table('profile_pictures')->truncate();
    DB::table('notifications')->truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    return 'All data has been deleted successfully!';
});