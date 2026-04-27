<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestAttemptAnswer extends Model
{
    protected $fillable = [
        'attempt_id', 'question_id', 'answer_text', 'answer_json', 'is_correct', 'score',
    ];

    protected $casts = [
        'answer_json' => 'array',
        'is_correct' => 'boolean',
        'score' => 'decimal:2',
    ];

    public function attempt()
    {
        return $this->belongsTo(TestAttempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
