<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MockTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'duration_minutes',
        'is_published',
        'created_by',
    ];

    public function sections()
    {
        return $this->hasMany(MockTestSection::class, 'mock_test_id');
    }

    public function attempts()
    {
        return $this->hasMany(TestAttempt::class, 'mock_test_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
