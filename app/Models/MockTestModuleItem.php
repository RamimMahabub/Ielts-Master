<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MockTestModuleItem extends Model
{
    protected $fillable = ['mock_test_module_id', 'item_id', 'order_index'];

    public function module()
    {
        return $this->belongsTo(MockTestModule::class, 'mock_test_module_id');
    }

    public function bankItem()
    {
        return $this->belongsTo(QuestionBankItem::class, 'item_id');
    }
}
