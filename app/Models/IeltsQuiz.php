<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IeltsQuiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'word_id',
        'quiz_type',
        'question',
        'options_json',
        'correct_answer',
    ];

    protected $casts = [
        'options_json' => 'array',
    ];

    public function topic()
    {
        return $this->belongsTo(IeltsTopic::class, 'topic_id');
    }

    public function word()
    {
        return $this->belongsTo(IeltsWord::class, 'word_id');
    }
}
