<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\JobApplication;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function downloadReport($jobId){
        $job = Job::with(['applications.applicant.user'])->findOrFail($jobId);

        $pdf = Pdf::loadView('reports.job_report', compact('job'));
        return $pdf->download("job_report_{$job->id}.pdf");
    }

    public function previewJobReport(Job $job)
    {
        // Load relationships needed for the report
        $job->load(['applications.applicant.user']);
        return view('reports.job_report', compact('job'));
    }

    public function downloadCertificate($applicationId)
    {
        $application = JobApplication::with(['job', 'applicant.user'])
            ->where('status', 'hired')
            ->findOrFail($applicationId);

        $pdf = Pdf::loadView('reports.acceptance_certificate', compact('application'));
        return $pdf->download("acceptance_certificate_{$application->id}.pdf");
    }

    public function previewCertificate($applicationId)
    {
        $application = JobApplication::with(['job', 'applicant.user'])
            ->where('status', 'hired')
            ->findOrFail($applicationId);

        $pdf = Pdf::loadView('reports.acceptance_certificate', compact('application'));

        return $pdf->stream("acceptance_certificate_{$application->id}.pdf");
    }
}


