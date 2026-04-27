<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'group_id', 'q_number', 'order_index', 'prompt', 'options_json', 'correct_answers_json', 'points',
    ];

    protected $casts = [
        'options_json' => 'array',
        'correct_answers_json' => 'array',
        'points' => 'decimal:2',
    ];

    public function group()
    {
        return $this->belongsTo(QuestionGroup::class, 'group_id');
    }

    public function answers()
    {
        return $this->hasMany(TestAttemptAnswer::class, 'question_id');
    }

    public function bookmarks()
    {
        return $this->hasMany(StudentQuestionBookmark::class);
    }
}
