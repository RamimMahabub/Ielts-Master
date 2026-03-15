<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestAttempt extends Model
{
    use HasFactory;

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected $fillable = [
        'user_id',
        'mock_test_id',
        'status',
        'raw_score',
        'placeholder_band',
        'started_at',
        'completed_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function mockTest()
    {
        return $this->belongsTo(MockTest::class, 'mock_test_id');
    }

    public function answers()
    {
        return $this->hasMany(TestAttemptAnswer::class, 'attempt_id');
    }
}
