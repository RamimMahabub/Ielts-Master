<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'q_no',
        'prompt',
        'meta_json',
    ];

    public function group()
    {
        return $this->belongsTo(QuestionGroup::class, 'group_id');
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class, 'question_id');
    }

    public function answers()
    {
        return $this->hasMany(QuestionAnswer::class, 'question_id');
    }
}
