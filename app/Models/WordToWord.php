<?php

namespace App\Models;

use App\Models\Word;
use App\Models\WordRelationType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WordToWord extends Model
{
    protected $table = 'words_to_words';

    public function word(): HasOne
    {
        return $this->hasOne(Word::class, 'id', 'word_id');
    }

    public function linkedWord(): HasOne
    {
        return $this->hasOne(Word::class, 'id', 'linked_word_id');
    }

    public function wordChildren(): HasMany
    {
        return $this->hasMany(Word::class, 'id', 'linked_word_id');
    }

    public function wordRelationType()
    {
        return $this->hasOne(WordRelationType::class, 'type_id', 'id');
    }
}