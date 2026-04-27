<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassRecording extends Model
{
    protected $fillable = ['title', 'recording_path', 'description', 'created_by'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function studentStatuses()
    {
        return $this->hasMany(StudentClassRecordingStatus::class);
    }
}
