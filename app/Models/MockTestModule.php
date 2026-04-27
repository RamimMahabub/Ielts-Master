<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MockTestModule extends Model
{
    protected $fillable = ['mock_test_id', 'module', 'order_index', 'duration_minutes'];

    public function mockTest()
    {
        return $this->belongsTo(MockTest::class);
    }

    public function items()
    {
        return $this->hasMany(MockTestModuleItem::class)->orderBy('order_index');
    }
}
