<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'body_text',
        'file_path',
        'transcript_text',
        'meta_json',
    ];

    public function questionGroups()
    {
        return $this->hasMany(QuestionGroup::class, 'asset_id');
    }
}
