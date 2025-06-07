<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProfilePicture extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'file_name',
        'drive_file_id',
        'mime_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function applicant()
    {
        return $this->hasOne(Applicant::class, 'profile_picture');
    }

    public function company()
    {
        return $this->hasOne(Company::class, 'profile_picture');
    }


}
