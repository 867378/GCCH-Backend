<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'date_of_birth',
        'sex',
        'phone_number',
        'course',
        'profile_picture',
        'expertise',
        'street_address',
        'city',
        'province',
        'country',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function profilePicture()
    {
        return $this->belongsTo(ProfilePicture::class, 'profile_picture');
    }

    public function jobApplications()
    {
        return $this->hasMany(JobApplication::class);
    }

}
