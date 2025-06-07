<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\Applicant;
use App\Http\Models\ProfilePicture;
use App\Models\Job;
use App\Models\User;
use App\Models\Resume;
use App\Models\CoverLetter;
use App\Models\JobApplication;
use App\Services\GoogleDriveService;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Yaza\LaravelGoogleDriveStorage\GDrive;
use Illuminate\Support\Facades\Log; 

class ApplicantController extends Controller
{
    
    protected $googleDriveService;

    public function __construct(GoogleDriveService $googleDriveService)
    {
        $this->googleDriveService = $googleDriveService;
    }

    public function fetchApplicantData($id)
    {
        $user = User::with('applicant.profilePicture')->findOrFail($id);

        $applicant = $user->applicant;

        $profilePictureUrl = null;

        if ($applicant && $applicant->profilePicture) {
            $fileId = $applicant->profilePicture->drive_file_id;
            $profilePictureUrl = $this->googleDriveService->getPublicImageUrl($fileId);
        }

        return response()->json([
            'user' => $user,
            'applicant' => $applicant,
            'profile_picture_url' => $profilePictureUrl,
        ]);
    }

    public function listedJobs(){
        $user = Auth::user();

        $jobCount = JobApplication::whereHas('applicant', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->whereNotIN('status',['accepted','hired'])
        ->count();

        return response()->json(['total_jobs_applied' => $jobCount]);
    }

    public function acceptedCount(){
        $user = Auth::user();

        $acceptedCount = JobApplication::where('status', 'accepted')
            ->whereHas('applicant', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count();

        return response()->json(['accepted_jobs_count' => $acceptedCount]);
    }

    public function matchedJobs(){
        $user = Auth::user();

        if (!$user->applicant) {
            return response()->json(['matched_jobs_count' => 0]);
        }

        $applicantCourse = $user->applicant->course;

        $matchedCount = Job::where('status', 'open')
            ->where(function ($query) use ($applicantCourse) {
                $query->where('recommended_course', 'LIKE', '%' . $applicantCourse . '%')
                    ->orWhere('recommended_course_2', 'LIKE', '%' . $applicantCourse . '%')
                    ->orWhere('recommended_course_3', 'LIKE', '%' . $applicantCourse . '%');
            })
            ->count();

        return response()->json(['matched_jobs_count' => $matchedCount]);
    }



    public function jobapply(Request $request)
    {
        try {
            $user = Auth::user();
            $applicant = $user->applicant;
            if (!$applicant) {
                return response()->json(['error' => 'Applicant not found'], 404);
            }

            try {
                $validated = $request->validate([
                    'job_id' => 'required|exists:jobs,id',
                    'resume' => 'nullable|file|mimes:pdf|max:2048',
                    'cover_letter' => 'required|file|mimes:pdf,docx,doc|max:2048',
                ]);
            } catch (ValidationException $e) {
                return response()->json([
                    'error' => 'Validation failed',
                    'messages' => $e->errors()
                ], 422);
            }

            $job = Job::find($validated['job_id']);

            if (!$job || $job->status === 'closed' || $job->filled_slots >= $job->total_slots) {
                return response()->json(['error' => 'Job is no longer accepting applications'], 403);
            }

            $existingApplication = JobApplication::where('applicant_id', $applicant->id)
                ->where('job_id', $validated['job_id'])
                ->first();


            if ($existingApplication) {
                return response()->json([
                    'error' => 'You have already applied for this job',
                    'status' => $existingApplication->status
                ], 422);
            }

            $jobApplication = new JobApplication();
            $jobApplication->job_id = $validated['job_id'];
            $jobApplication->applicant_id = $applicant->id;

            $fileMeta = [];

            if ($request->hasFile('cover_letter')) {
                $file = $request->file('cover_letter');
                $customFileName = $applicant->first_name . '_' . $applicant->last_name . '_cover_letter';
                $fileMeta['cover_letter'] = $this->googleDriveService->uploadFile($file, $customFileName);

                 if ($fileMeta['cover_letter'] && isset($fileMeta['cover_letter']['file_id'])) {
                    $this->googleDriveService->setPublicPermission($fileMeta['cover_letter']['file_id']);
                }

                $coverLetter = new CoverLetter();
                $coverLetter->applicant_id = $applicant->id;
                $coverLetter->file_name = $fileMeta['cover_letter']['name'];
                $coverLetter->drive_file_id = $fileMeta['cover_letter']['file_id'];
                $coverLetter->mime_type = $file->getMimeType();
                $coverLetter->save();

                $jobApplication->cover_letter = $coverLetter->id;
            } else {
                Log::warning("Cover letter file missing despite validation success", ['applicant_id' => $applicant->id]);
            }

            if ($request->hasFile('resume')) {
                $file = $request->file('resume');
                $customFileName = $applicant->first_name . '_' . $applicant->last_name . '_resume';
                $fileMeta['resume'] = $this->googleDriveService->uploadFile($file, $customFileName);

                 if ($fileMeta['resume'] && isset($fileMeta['resume']['file_id'])) {
                    $this->googleDriveService->setPublicPermission($fileMeta['resume']['file_id']);
                }

                $resume = new Resume();
                $resume->applicant_id = $applicant->id;
                $resume->file_name = $fileMeta['resume']['name'];
                $resume->drive_file_id = $fileMeta['resume']['file_id'];
                $resume->mime_type = $file->getMimeType();
                $resume->save();

                $jobApplication->resume = $resume->id;
            }

            $jobApplication->save();

            // Notify the company about the new application
            $job = Job::find($validated['job_id']);
            if ($job && $job->company_id) {
                $notificationController = new NotificationController();
                $content = "New job application for " . $job->job_title . " from " . $applicant->first_name . " " . $applicant->last_name;
                $notificationController->notifyUser($job->company_id, $content, 'job_application');
            }

            return response()->json([
                'message' => 'Job application submitted successfully',
                'fileMeta' => $fileMeta,
                'job_application' => $jobApplication
            ], 201);

        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while applying for the job'], 500);
        }
    }

    public function jobdisplay(Request $request)
    {
        try { 
            $user = Auth::user();
            $applicant = $user->applicant;
            if (!$applicant) {
                return response()->json(['error' => 'Applicant not found'], 404);
            }

            // Get filters from request, or use applicant's info if relevant
            $program = $applicant->course;  // or $request->input('program') if passed explicitly
            $expertises = $request->input('expertises', []);  // array of expertise IDs or names
            $minSalary = $request->input('min_salary');
            $maxSalary = $request->input('max_salary');

            $query = Job::with('company')->where('status', 'open');

            // Filter by applicant's program (assuming recommended_course fields)
            if ($program) {
                $query->where(function($q) use ($program) {
                    $q->where('recommended_course', 'LIKE', "%$program%")
                    ->orWhere('recommended_course_2', 'LIKE', "%$program%")
                    ->orWhere('recommended_course_3', 'LIKE', "%$program%");
                });
            }

            // Filter by expertises if provided (assuming many-to-many relationship)
            if (!empty($expertises)) {
                $query->where(function($q) use ($expertises) {
                    foreach ($expertises as $expertise) {
                        $q->orWhere('recommended_expertise', 'LIKE', "%$expertise%")
                        ->orWhere('recommended_expertise_2', 'LIKE', "%$expertise%")
                        ->orWhere('recommended_expertise_3', 'LIKE', "%$expertise%");
                    }
                });
            }

            // Filter by monthly salary range
            if ($minSalary) {
                $query->where('monthly_salary', '>=', $minSalary);
            }
            if ($maxSalary) {
                $query->where('monthly_salary', '<=', $maxSalary);
            }

            // Get filtered jobs
            $jobs = $query->get();

            return response()->json([
                'jobs' => $jobs
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong', 'details' => $e->getMessage()], 500);
        }
    }



    public function applicationStatus(){
        try{
            $user = Auth::user();
            $applicant = $user->applicant;

            if (!$applicant) {
                return response()->json(['error' => 'Applicant not found'], 404);
            }

            $applications = JobApplication::where('applicant_id', $applicant->id)->with(['job'])->get();
            if ($applications->isEmpty()) {
                return response()->json(['message' => 'No applications found.'], 404);
            }
            $applicationsData = $applications->map(function ($application) {
                return [
                    'id' => $application->id,
                    'job_id' => $application->job_id,
                    'job_title' => $application->job->job_title,
                    'status' => $application->status,
                    'comment' => $application->comment,
                    'schedule' => $application->scheduled_at,
                    'updated_at' => $application->created_at,
                ];
            });
            return response()->json(['applications' => $applicationsData], 200);  
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->validator->errors()], 422);
        }
    }

    public function respondToOffer(Request $request, JobApplication $application)
    {
        $user = Auth::user();

        if ($application->applicant->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($application->offer_status !== 'offered') {
            return response()->json(['error' => 'No job offer to respond to'], 400);
        }

        if ($application->finalized) {
            return response()->json(['error' => 'You have already finalized a job offer'], 400);
        }

        $validated = $request->validate([
            'offer_status' => 'required|in:accepted,rejected'
        ]);

        if ($validated['offer_status'] === 'accepted') {
            $application->status = 'hired';
            $application->offer_status = 'accepted';
            $application->finalized = true;
            $application->save();

            $job = $application->job;
            $job->filled_slots += 1;

            if ($job->filled_slots >= $job->total_slots) {
                $job->status = 'closed';
            }

            $job->save();

            JobApplication::where('applicant_id', $application->applicant_id)
            ->where('id', '!=', $application->id)
            ->update([
                'offer_status' => 'rejected',
                'finalized' => true,
            ]);

            JobApplication::where('job_id', $job->id)
                ->whereNotIn('status', ['hired', 'rejected'])
                ->update([
                    'status' => 'rejected',
                    'offer_status' => 'rejected',
                    'finalized' => true,
                ]);

        } else {
            $application->offer_status = 'rejected';
            $application->status = 'rejected';
            $application->save();
        }

        return response()->json(['message' => 'Offer response recorded successfully']);
    }
}
