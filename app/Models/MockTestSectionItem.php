<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MockTestSectionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'mock_test_section_id',
        'asset_id',
        'order_index'
    ];

    public function section()
    {
        return $this->belongsTo(MockTestSection::class, 'mock_test_section_id');
    }

    public function asset()
    {
        return $this->belongsTo(ContentAsset::class, 'asset_id');
    }
}
