<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserVocabularyProgress extends Model
{
    use HasFactory;

    protected $table = 'user_vocabulary_progress';

    protected $fillable = [
        'user_id',
        'vocabulary_word_id',
        'mastered',
        'times_correct',
        'last_reviewed_at',
    ];

    protected $casts = [
        'mastered' => 'boolean',
        'last_reviewed_at' => 'datetime',
    ];
}
