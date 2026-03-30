<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IeltsQuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'topic_id',
        'total_questions',
        'score',
        'weak_word_ids',
        'attempted_at',
    ];

    protected $casts = [
        'weak_word_ids' => 'array',
        'attempted_at' => 'datetime',
    ];
}
