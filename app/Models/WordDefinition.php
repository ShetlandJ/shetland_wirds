<?php

namespace App\Models;

use App\Models\User;
use App\Models\Word;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WordDefinition extends Model
{
    protected $table = 'word_definitions';

    protected $fillable = [
        'uuid',
        'definition',
        'example_sentence',
        'type',
        'user_id',
        'word_id',
    ];

    public function word(): BelongsTo
    {
        return $this->belongsTo(Word::class, 'word_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
