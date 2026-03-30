<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VocabularyQuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'section_no',
        'total_questions',
        'score',
        'attempted_at',
    ];

    protected $casts = [
        'attempted_at' => 'datetime',
    ];
}
