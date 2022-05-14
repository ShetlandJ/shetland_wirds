<?php

namespace App\Models;

use App\Models\Word;
use Illuminate\Database\Eloquent\Model;

class WordToWord extends Model
{
    protected $table = 'words_to_words';

    public function word()
    {
        return $this->belongsTo(Word::class, 'parent_word_id');
    }
}
