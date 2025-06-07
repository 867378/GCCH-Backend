<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $fillable = [
        'job_id', // based on job that is being applied on
        'applicant_id', // foreign key from applicant that applied for the job
        'resume',
        'cover_letter',
        'status', // e.g., applied, interview, assessment, rejected, accepted
        'date_applied',
        'scheduled_at', // for interview or assessment
        'offer_status',
        'finalized',
        'comment', // for feedback or notes
    ];

    public function applicant(){
        return $this->belongsTo(Applicant::class, 'applicant_id');
    }

    public function job(){
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function resumeFile(){
        return $this->belongsTo(Resume::class, 'resume');
    }

    public function coverLetterFile(){
        return $this->belongsTo(CoverLetter::class, 'cover_letter');
    }
}
