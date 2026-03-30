<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VocabularyWord extends Model
{
    use HasFactory;

    protected $fillable = [
        'word',
        'section_no',
        'section_order',
        'global_order',
    ];
}
