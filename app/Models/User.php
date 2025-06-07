<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'password',
        'role',
        'google_id',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        //'remember_token',
    ];

    public function profilePicture()
    {
        return $this->hasOne(ProfilePicture::class);
    }
    public function applicant()
    {
        return $this->hasOne(Applicant::class);
    }
    public function company()
    {
        return $this->hasOne(Company::class);
    }
    public function isApplicant():bool{
        return $this->role === 'applicant';
    }
    public function isCompany():bool{
        return $this->role === 'company';
    }
    public function sentMessages(){
        return $this->hasMany(Message::class, 'sender_id');
    }
    public function receivedMessages(){
        return $this->hasMany(Message::class, 'receiver_id');
    }


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
