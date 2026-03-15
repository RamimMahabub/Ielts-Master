<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MockTestSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'mock_test_id',
        'section_type',
        'order_index'
    ];

    public function mockTest()
    {
        return $this->belongsTo(MockTest::class, 'mock_test_id');
    }

    public function items()
    {
        return $this->hasMany(MockTestSectionItem::class, 'mock_test_section_id');
    }
}
