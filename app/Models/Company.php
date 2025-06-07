<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'company_telephone',
        'street_address',
        'city',
        'province',
        'country',
        'industry_type',
        'profile_picture',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function profilePicture()
    {
        return $this->belongsTo(ProfilePicture::class, 'profile_picture');
    }

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }
}
