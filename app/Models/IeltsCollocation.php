<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IeltsCollocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'word_id',
        'collocation',
    ];

    public function word()
    {
        return $this->belongsTo(IeltsWord::class, 'word_id');
    }
}
