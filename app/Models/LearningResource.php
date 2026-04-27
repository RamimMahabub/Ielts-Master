<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningResource extends Model
{
    protected $fillable = ['category', 'content', 'order_index', 'created_by'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function studentStatuses()
    {
        return $this->hasMany(StudentLearningResourceStatus::class);
    }
}
