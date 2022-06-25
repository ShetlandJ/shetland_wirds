<?php

namespace App\Models;

use App\Models\User;
use App\Models\Word;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'pending'
    ];

    public const WORD_TYPE_ABBR_MAP = [
        'v' => 'verb',
        'n' => 'noun',
        'adv' => 'adverb',
        'prep' => 'preposition',
        'indf' => 'indefinite_article',
        'num' => 'numeral',
        'det' => 'determiner',
        'pro-form' => 'pro-form',
        'interj' => 'interjection',
        'conj' => 'conjunction',
        'coordconn' => 'coordinating_conjunction',
        'pn' => 'pronoun',
        'aux' => 'auxiliary_verb',
        'dem' => 'demonstrative_pronoun',
        'prt' => 'particle',
        'pers' => 'personal_pronoun',
        'poss' => 'possessive_pronoun',
        'cardnum' => 'cardinal_number',
        'ord_num' => 'ordinal_number',
        'interrog' => 'interrogative_pronoun',
        'refl' => 'reflexive_pronoun',
        'quant' => 'quantifier',
        'mod' => 'modal_verb',
        'verbprt' => 'verb_particle',
    ];

    public function word(): BelongsTo
    {
        return $this->belongsTo(Word::class, 'word_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function getConvertedTypeAttribute(): ?string
    {
        // if word isn't valid, return empty string
        if (!in_array($this->type, $this->validWordTypes)) {
            return null;
        }

        // snake case to capital case
        return ucwords(str_replace('_', ' ', $this->type));
    }

    public function getValidWordTypesAttribute(): array
    {
        return array_keys(self::WORD_TYPE_ABBR_MAP);
    }
}
