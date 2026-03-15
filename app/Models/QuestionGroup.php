<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'instructions',
        'question_type',
        'start_no',
        'end_no',
    ];

    public function asset()
    {
        return $this->belongsTo(ContentAsset::class, 'asset_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'group_id');
    }
}
