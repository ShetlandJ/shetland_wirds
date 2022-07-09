<?php

namespace App\Models;

use App\Models\Word;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WordToWord extends Model
{
    protected $table = 'words_to_words';

    public function word(): BelongsTo
    {
        return $this->belongsTo(Word::class, 'word_d');
    }

    public function wordChild(): BelongsTo
    {
        return $this->belongsTo(Word::class, 'linked_word_id');
    }
}