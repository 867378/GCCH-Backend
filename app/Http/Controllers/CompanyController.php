<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use App\Models\JobApplication;
use App\Services\GoogleDriveService;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class CompanyController extends Controller
{   
    protected $googleDriveService;

    public function __construct(GoogleDriveService $googleDriveService)
    {
        $this->googleDriveService = $googleDriveService;
    }

    public function fetchCompanyData($id)
    {
        $user = User::with('company.profilePicture')->findOrFail($id);

        $company = $user->company;

        $profilePictureUrl = null;

        if ($company && $company->profilePicture) {
            $fileId = $company->profilePicture->drive_file_id;
            $profilePictureUrl = $this->googleDriveService->getPublicImageUrl($fileId);
        }

        return response()->json([
            'user' => $user,
            'company' => $company,
            'profile_picture_url' => $profilePictureUrl,
        ]);
    }


    public function totalClients(){
        try{
            $userId = auth()->id();

            $clients = JobApplication::whereHas('job', function ($q) use ($userId) {
                    $q->whereHas('company', function ($qq) use ($userId) {
                        $qq->where('user_id', $userId);
                    });
                })
                ->where('offer_status', 'accepted')
                ->where('finalized', true)
                ->distinct('applicant_id')
                ->count('applicant_id');

            return response()->json(['count' => $clients]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not retrieve total clients'], 500);
        }
    }


    public function totalJobs(){
        try{
            $userId = auth()->id();
            $jobs = Job::whereHas('company', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->count();

            return response()->json(['count' => $jobs]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not retrieve total jobs'], 500);
        }
    }

    public function pendingApplications(){
        try{
            $userId = auth()->id();

            $applicationsQuery = JobApplication::whereIn('status', ['applied', 'for_interview', 'screening', 'accepted', 'interviewed'])
                ->whereHas('job.company', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                });

            $total = $applicationsQuery->count();

            $countsPerStatus = $applicationsQuery
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status');

            return response()->json([
                'total' => $total,
                'counts' => $countsPerStatus,
        ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not retrieve pending applications'], 500);
        }
    }

    public function postjob(){
        try{
            $user = Auth::user();

            $company = $user->company;

            if (!$company) {
                return response()->json(['error' => 'Company not found'], 404);
            }

            $validated = request()->validate([
                'job_title' => 'required|string|max:255',
                'job_description' => 'required|string',
                'job_location' => 'required|string|max:255',
                'job_type' => 'required|in:full_time,part_time,contract,internship',
                'monthly_salary' => 'required|numeric|min:0',
                'recommended_course' => 'required|string|max:255',
                'recommended_course_2' => 'nullable|string|max:255',
                'recommended_course_3' => 'nullable|string|max:255',
                'recommended_expertise' => 'nullable|string|max:255',
                'recommended_expertise_2' => 'nullable|string|max:255',
                'recommended_expertise_3' => 'nullable|string|max:255',
                'total_slots' => 'required|integer|min:1',
            ]);

        $status = $validated['total_slots'] > 0 ? 'open' : 'closed';
        
        $job = Job::create([
            'company_id' => $company->id,
            'job_title' => $validated['job_title'],
            'job_description' => $validated['job_description'],
            'job_location' => $validated['job_location'],
            'job_type' => $validated['job_type'],
            'monthly_salary' => $validated['monthly_salary'],
            'recommended_course' => $validated['recommended_course'],
            'recommended_course_2' => $validated['recommended_course_2'],
            'recommended_course_3' => $validated['recommended_course_3'],
            'recommended_expertise' => $validated['recommended_expertise'],
            'recommended_expertise_2' => $validated['recommended_expertise_2'],
            'recommended_expertise_3' => $validated['recommended_expertise_3'],
            'total_slots' => $validated['total_slots'],
            'filled_slots' => 0,
            'date_posted' => now(),
            'status' => $status,
        ]);

        return response()->json(['message' => 'Job posted successfully', 'job' => $job], 201);

        }catch (ValidationException $e) {
            return response()->json(['error' => $e->validator->errors()], 422);
        }
    }

    public function jobdisplay(){
        try{
            $user = Auth::user();

            $company = $user->company;

            if (!$company) {
                return response()->json(['error' => 'Company not found'], 404);
            }
            
            $jobs = Job::where('company_id', $company->id)->get();

            if ($jobs->isEmpty()) {
                return response()->json(['message' => 'No jobs posted yet.'], 404);
            }

            return response()->json(['jobs' => $jobs], 200);

        } catch (ValidationException $e) {
            return response()->json(['error' => 'Could not retrieve jobs'], 500);
        }
    }

    public function viewJobApplications(Job $job){
        try{
            $user = Auth::user();
            $company = $user->company;

            if (!$company) {
                return response()->json(['error' => 'Company not found'], 404);
            }

            $applications = JobApplication::where('job_id', $job->id)->with(['applicant', 'resumeFile', 'coverLetterFile'])->get();

            $applicationsData = $applications->map(function ($application){
                $embedUrlResume = null;
                $embedUrlCoverLetter = null;
                $resumeFile = $application->resumeFile;
                $coverLetterFile = $application->coverLetterFile;

                if ($resumeFile) {
                    $embedUrlResume = $this->googleDriveService->getFileEmbedUrl($resumeFile->drive_file_id);
                }

                if ($coverLetterFile) {
                    $embedUrlCoverLetter = $this->googleDriveService->getFileEmbedUrl($coverLetterFile->drive_file_id);
                }

                return [
                    'id' => $application->id,
                    'applicant' => $application->applicant,
                    'resume' => [
                        'file_name' => $resumeFile ? $resumeFile->file_name : null,
                        'embed_url' => $embedUrlResume,
                    ],
                    'cover_letter' => [
                        'file_name' => $coverLetterFile ? $coverLetterFile->file_name : null,
                        'embed_url' => $embedUrlCoverLetter,
                    ],
                    'status' => $application->status,
                    'scheduled_at' => $application->scheduled_at,
                    'date_applied' => $application->date_applied,
                ];
            });

            return response()->json(['applications' => $applicationsData], 200);

        }catch (ValidationException $e) {
            return response()->json(['error' => 'Could not retrieve applications'], 500);
        }
    }

    public function assessApplication(Request $request, $applicationId){
        try{
            $application = JobApplication::with('job')->find($applicationId);

            if (!$application) {
                return response()->json(['error' => 'Application not found'], 404);
            }

            $job = $application->job;

            if ($application->status === 'hired') {
                return response()->json(['error' => 'Cannot update application. Applicant already hired.'], 403);
            }

            $validated = $request->validate([
                'status' => 'required|in:applied,for_interview,screening,interviewed,rejected,accepted,hired',
                'scheduled_at' => 'nullable|date_format:Y-m-d H:i:s',
                'comment' => 'nullable|string',
            ]);

            if ($application->status !== 'applied' && $validated['status'] === 'screening') {
                return response()->json(['error' => 'Can only move to screening from applied status.'], 403);
            }

            if ($validated['status'] === 'interviewed' && $application->status !== 'for_interview') {
                return response()->json(['error' => 'Can only mark as interviewed after interview is scheduled.'], 403);
            }

            $user = Auth::user();
            $company = $user->company;

            if (!$company || $job->company_id !== $company->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            if (in_array($validated['status'], ['for_interview'])) {
                if (empty($validated['scheduled_at'])) {
                    return response()->json(['error' => 'Scheduled date is required for interview or assessment'], 422);
                }
                $application->scheduled_at = $validated['scheduled_at'];
            } else {
                $application->scheduled_at = null;
            }

            if ($validated['status'] === 'hired') {
                if ($job->status === 'closed') {
                    return response()->json(['error' => 'Job is already closed. Cannot hire more applicants.'], 403);
                }

                if ($job->filled_slots >= $job->total_slots) {
                    $job->status = 'closed';
                    $job->save();

                    JobApplication::where('job_id', $job->id)
                        ->whereNotIn('status', ['hired'])
                        ->update([
                            'status' => 'rejected',
                            'comment' => 'Job has reached its slot limit.'
                        ]);
                } else {
                    $job->save();
                }
            }

            $application->status = $validated['status'];

            if (isset($validated['comment'])) {
                $application->comment = $validated['comment'];
            }

            $application->save();

            $notifier = new NotificationController();
            $content = "Your application for " . $job->job_title . " has been updated";
            $notifier->notifyUser($application->applicant->user_id, $content, 'application_update');

            return response()->json([
                'message' => 'Application status updated successfully',
                'application' => $application
            ], 200);

        } catch (ValidationException $e){
            return response()->json(['error' => 'Could not update application status'], 422);
        }
    }


    public function offerJob($applicationId)
    {
        $application = JobApplication::findOrFail($applicationId);

        if ($application->status !== 'accepted') {
            return response()->json(['message' => 'Only accepted applications can be offered.'], 403);
        }

        $application->offer_status = 'offered';
        $application->save();

        return response()->json(['message' => 'Job offer sent successfully.']);
    }

}
