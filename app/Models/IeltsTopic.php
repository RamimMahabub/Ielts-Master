<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IeltsTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function words()
    {
        return $this->hasMany(IeltsWord::class, 'topic_id');
    }

    public function quizzes()
    {
        return $this->hasMany(IeltsQuiz::class, 'topic_id');
    }
}
