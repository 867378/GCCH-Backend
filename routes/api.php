<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});

Route::get('/login', function () {
    return view('auth.google-auth');
})->name('login');

Route::prefix('user')->name('api.')->group(function () { 
    Route::get('/applicant/{id}',[ApplicantController::class, 'fetchApplicantData'])->name('applicant.data');
    Route::get('/company/{id}',[CompanyController::class, 'fetchCompanyData'])->name('company.data');
    Route::get('select-role/{user}', [UserController::class, 'selectRole'])->name('select-role');
    Route::post('set-role/{userId}', [UserController::class, 'setRole'])->name('set-role');
    Route::get('applicant/profile/{user}', [UserController::class, 'showApplicantProfileForm'])->name('applicant-form');
    Route::post('applicant/profile/{user}', [UserController::class, 'completeApplicantProfile'])->name('applicant-profile');
    Route::get('company/profile/{user}', [UserController::class, 'showCompanyProfileForm'])->name('company-form');
    Route::post('company/profile/{user}', [UserController::class, 'completeCompanyProfile'])->name('company-profile');
});

//Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
    Route::get('/user/${id}', [UserController::class, 'getRole'])->name('getRole');
    Route::get('/user', function (Request $request) {return $request->user();});
});

//Applicant routes
Route::middleware(['auth:sanctum','applicant'])->group(function () {
    Route::post('/applicant/jobapply', [ApplicantController::class, 'jobapply'])->name('applicant.jobapply');
    Route::get('/applicant/jobdisplay', [ApplicantController::class, 'jobdisplay'])->name('applicant.jobdisplay');
    Route::get('/applicant/jobdisplay/{id}', [ApplicantController::class, 'jobdisplay'])->name('applicant.jobdisplay');
    Route::get('/applicant/applications', [ApplicantController::class, 'applicationStatus'])->name('applicant.applicationStatus');
    Route::get('/applicant/ongoing-applications', [ApplicantController::class, 'listedJobs'])->name('applicant.listedJobs');
    Route::get('/applicant/accepted-applications', [ApplicantController::class, 'acceptedCount'])->name('applicant.acceptedCountS');
    Route::get('/applicant/matched-jobs', [ApplicantController::class, 'matchedJobs'])->name('applicant.matchedJobs');
    Route::post('/applicant/job-application/respond-offer/{application}', [ApplicantController::class, 'respondToOffer'])->name('applicant.jobOffer');
});

//Company routes
Route::middleware(['auth:sanctum','company'])->group(function () {
    Route::post('/company/postjob', [CompanyController::class, 'postjob'])->name('company.postjob');
    Route::get('/company/jobdisplay', [CompanyController::class, 'jobdisplay'])->name('company.jobdisplay');
    Route::get('/company/jobdisplay/{id}', [CompanyController::class, 'jobdisplay'])->name('company.jobdisplay');
    Route::get('/job/{job}/applications', [CompanyController::class, 'viewJobApplications'])->name('company.jobapplications');
    Route::post('/company/job-applications/{jobApplication}/assess', [CompanyController::class, 'assessApplication'])->name('company.update.application');
    Route::post('/company/offer-job/{id}',[CompanyController::class, 'offerJob'])->name('company.offerJob');
    Route::get('/company/total-clients', [CompanyController::class, 'totalClients']);
    Route::get('/company/total-jobs', [CompanyController::class, 'totalJobs']);
    Route::get('/company/pending-applications', [CompanyController::class, 'pendingApplications']);
    //
});

//Message routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/message/send', [MessageController::class, 'send'])->name('message.send');
    Route::get('/message/conversation/{userId}', [MessageController::class, 'conversation'])->name('message.conversation');
    Route::post('/message/mark-as-read/{messageId}', [MessageController::class, 'markAsRead'])->name('message.markAsRead');
});

//Notification routes
Route::middleware(['auth:sanctum'])->group(function (){
    Route::get('/notifications', [NotificationController::class, 'userNotifications'] )->name('user.notifications');
});