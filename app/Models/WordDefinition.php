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

    public const WORD_TYPE_MEANING_MAP = [
        'adjective' => 'Pairt-o-speech at pits across qualities or attributes. / Part-of-speech denoting qualities or attributes.',
        'adverb' => 'Pairt-o-speech at modifies categories idder as nouns (entities). / Part-of-speech which modifies other categories than nouns (entities).',
        'auxillary' => 'Verbs at’s mair or less emty semantically, at gies grammar information aboot lexical verbs. / Semantically more or less empty verbs which give grammatical information about lexical verbs.',
        'article' => 'Marker at shaas if a noun can be identifier or no. / Marker that indicates whether the noun phrase is identifiable of not',
        'conjunction' => 'Pairt o speech at conneks entities. / Part-of-speech which connects entities.',
        'determiner' => 'Marker at nairroos doon da reference o nouns./  Marker that narrow down the reference of nouns.',
        'noun' => 'Pairt o speech at refers tae entities (whidder d’ir concrete or abstract). / Part-of-speech which refers to entities (whether concrete or abstract).',
        'numeral' => 'Pairt - o - speech øsed fir shaain da quantity o entities. / Part-of-speech used to indicate the amount of entities.',
        'idiom' => 'A group o wirds at hae a culture specific meaning. / A group words that have a culture-specific meaning.',
        'interjection' => 'Pairt o speech at forms a hael utterance an at shaas da spaekker’s reaction.	/ Part-of-speech which forms a full utterance and which indicates the speaker’s reaction',
        'preposition' => 'Pairt o speech øsed tae connek da noun phrase wi some idder element o da sentence. / Part-of-Speech used to connect the noun phrase with some other element of the sentence.',
        'pronoun' => 'Pairt o speech at swaps oot a noun or a noun phrase. / Part-of-speech which substitutes a noun or noun phrase.',
        'proper_noun' => 'Noun at spaeks aboot a parteeclar body or plaess.	/ Noun referring to a specific individual or place.',
        'verb' => 'Pairt o speech at spaeks aboot actions an processes.	/ Part-of-speech which refers to actions and processes.'
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
        if (isset(self::WORD_TYPE_ABBR_MAP[$this->type])) {
            return self::WORD_TYPE_ABBR_MAP[$this->type];
        } else if (!in_array($this->type, $this->validWordTypes)
            || isset(self::WORD_TYPE_ABBR_MAP[$this->type])
        ) {
            return null;
        }

        // snake case to capital case
        return ucwords(str_replace('_', ' ', $this->type));
    }

    public function getValidWordTypesAttribute(): array
    {
        return array_values(self::WORD_TYPE_ABBR_MAP);
    }
}
