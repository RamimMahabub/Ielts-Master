<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionGroup extends Model
{
    protected $fillable = [
        'item_id', 'order_index', 'question_type', 'instructions', 'shared_data_json',
    ];

    protected $casts = [
        'shared_data_json' => 'array',
    ];

    public function item()
    {
        return $this->belongsTo(QuestionBankItem::class, 'item_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'group_id')->orderBy('q_number');
    }
}
