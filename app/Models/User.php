<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo',
        'target_band',
        'preferred_test_date',
        'is_blocked',
        'instructor_status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
            'preferred_test_date' => 'date',
            'is_blocked' => 'boolean',
        ];
    }

    public function attempts()
    {
        return $this->hasMany(TestAttempt::class, 'user_id');
    }

    public function createdMockTests()
    {
        return $this->hasMany(MockTest::class, 'created_by');
    }

    public function createdLearningResources()
    {
        return $this->hasMany(LearningResource::class, 'created_by');
    }

    public function createdClassRecordings()
    {
        return $this->hasMany(ClassRecording::class, 'created_by');
    }

    public function learningResourceStatuses()
    {
        return $this->hasMany(StudentLearningResourceStatus::class);
    }

    public function classRecordingStatuses()
    {
        return $this->hasMany(StudentClassRecordingStatus::class);
    }

    public function questionBookmarks()
    {
        return $this->hasMany(StudentQuestionBookmark::class);
    }
}
