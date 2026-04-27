<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentLearningResourceStatus extends Model
{
    protected $fillable = ['user_id', 'learning_resource_id', 'completed_at'];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function learningResource()
    {
        return $this->belongsTo(LearningResource::class);
    }
}
