<?php

namespace App\Models;

use App\Models\User;
use App\Models\Word;
use Illuminate\Database\Eloquent\Model;

class WordReport extends Model
{
    protected $table = 'reported_words';

    public $fillable = [
        'uuid',
        'word_id',
        'user_id',
        'reason',
        'reason_type',
    ];

    public function word()
    {
        return $this->belongsTo(Word::class, 'word_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
