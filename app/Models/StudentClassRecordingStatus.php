<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentClassRecordingStatus extends Model
{
    protected $fillable = ['user_id', 'class_recording_id', 'status', 'completed_at'];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function classRecording()
    {
        return $this->belongsTo(ClassRecording::class);
    }
}
