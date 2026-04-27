<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MockTest extends Model
{
    protected $fillable = ['title', 'test_type', 'is_published', 'created_by'];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function modules()
    {
        return $this->hasMany(MockTestModule::class)->orderBy('order_index');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attempts()
    {
        return $this->hasMany(TestAttempt::class);
    }

    public function totalDurationMinutes(): int
    {
        return (int) $this->modules()->sum('duration_minutes');
    }
}
