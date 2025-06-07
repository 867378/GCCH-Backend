<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CoverLetter extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'applicant_id',
        'file_name',
        'drive_file_id',
        'mime_type',
    ];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }
}
