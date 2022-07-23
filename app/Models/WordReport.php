<?php

namespace App\Models;

use App\Models\User;
use App\Models\Word;
use Illuminate\Database\Eloquent\Model;

class WordReport extends Model
{
    protected $table = 'reported_words';

    public $fillable = [
        'word_id',
        'user_id',
        'reason',
    ];

    public function word()
    {
        return $this->belongsTo(Word::class, 'word_id');
    }
}
