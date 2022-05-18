<?php

namespace App\Models;

use App\Models\UserWordLike;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'type'
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

    public function getIsLikedByUserAttribute(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return $this->likes()->where('user_id', Auth::user()->id)->exists();
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
}
