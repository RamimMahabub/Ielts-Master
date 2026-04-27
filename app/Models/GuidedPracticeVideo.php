<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuidedPracticeVideo extends Model
{
    protected $fillable = ['category', 'title', 'video_path', 'description', 'created_by'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
