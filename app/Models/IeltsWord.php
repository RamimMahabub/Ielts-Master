<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IeltsWord extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'word',
        'meaning',
        'example_sentence',
    ];

    public function topic()
    {
        return $this->belongsTo(IeltsTopic::class, 'topic_id');
    }

    public function collocations()
    {
        return $this->hasMany(IeltsCollocation::class, 'word_id');
    }
}
