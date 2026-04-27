<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionBankItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'module', 'title',
        'audio_path', 'transcript',
        'passage_html', 'passage_subtitle',
        'prompt_html', 'image_path',
        'meta_json', 'created_by',
    ];

    protected $casts = [
        'meta_json' => 'array',
    ];

    public function groups()
    {
        return $this->hasMany(QuestionGroup::class, 'item_id')->orderBy('order_index');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
