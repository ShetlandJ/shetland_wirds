<?php

namespace App\Models;

use App\Models\User;
use App\Models\WordToWord;
use Illuminate\Support\Str;
use App\Models\UserWordLike;
use App\Models\WordRecording;
use App\Models\WordDefinition;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Word extends Model
{
    protected $table = 'words';

    protected $casts = [
        'pending' => 'boolean'
    ];

    protected $fillable = [
        'uuid',
        'word',
        'translation',
        'example_sentence',
        'external_id',
        'pending',
        'creator_id',
        'type',
    ];

    public function likes()
    {
        return $this->hasMany(UserWordLike::class);
    }

    public function relatedWords(): HasMany
    {
        return $this->hasMany(WordToWord::class, 'word_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function recordings()
    {
        return $this->hasMany(WordRecording::class);
    }

    public function definitions(): HasMany
    {
        return $this->hasMany(WordDefinition::class, 'word_id', 'id');
    }

    public function getFormattedDefinitionsAttribute(): object
    {
        return $this->definitions->map(function (WordDefinition $definition) {
            return [
                'id' => $definition->uuid,
                'definition' => $definition->definition,
                'example_sentence' => $definition->example_sentence,
                'type' => $definition->type,
                'creator' => $definition->user ? $definition->user->name : null,
                'word_id' => $definition->word->uuid,
                'created_at' => $definition->created_at,
                'updated_at' => $definition->updated_at,
            ];
        });
    }

    public function getIsLikedByUserAttribute(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return $this->likes()->where('user_id', Auth::user()->id)->exists();
    }

    public function getCleanSlugAttribute()
    {
        return Str::slug($this->word);
    }

    public function scopeUserAdded(Builder $query): Builder
    {
        return $query->where(function (Builder $query) {
            $query->whereNull('creator_id');
            $query->orWhere('creator_id', '!=', 0);
        });
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('pending', false)->where('rejected', false);
    }

    public function scopeWhereLike(Builder $query, string $searchString): Builder
    {
        $lowercasedSearchString = Str::lower($searchString);

        return $query->where('word_definitions.definition', 'like', "%{$lowercasedSearchString}%")
            ->where('word_definitions.example_sentence', 'like', "%{$lowercasedSearchString}%")
            ->orWhere('words.word', 'like', "%{$lowercasedSearchString}%");
    }
}
