<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestAttempt extends Model
{
    protected $fillable = [
        'user_id', 'mock_test_id', 'status', 'current_module',
        'module_started_at', 'started_at', 'completed_at',
        'listening_raw', 'reading_raw',
        'listening_band', 'reading_band', 'writing_band', 'speaking_band', 'overall_band',
        'speaking_audio_path',
    ];

    protected $casts = [
        'module_started_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'listening_band' => 'decimal:1',
        'reading_band' => 'decimal:1',
        'writing_band' => 'decimal:1',
        'speaking_band' => 'decimal:1',
        'overall_band' => 'decimal:1',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mockTest()
    {
        return $this->belongsTo(MockTest::class);
    }

    public function answers()
    {
        return $this->hasMany(TestAttemptAnswer::class, 'attempt_id');
    }
}
