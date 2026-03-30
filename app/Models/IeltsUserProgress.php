<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IeltsUserProgress extends Model
{
    use HasFactory;

    protected $table = 'ielts_user_progress';

    protected $fillable = [
        'user_id',
        'word_id',
        'status',
        'correct_streak',
        'last_reviewed_at',
        'next_review_at',
    ];

    protected $casts = [
        'last_reviewed_at' => 'datetime',
        'next_review_at' => 'datetime',
    ];

    public function word()
    {
        return $this->belongsTo(IeltsWord::class, 'word_id');
    }
}
