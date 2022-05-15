<?php

namespace App\Models;

use App\Models\UserWordLike;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Word extends Model
{
    protected $table = 'words';

    public function likes()
    {
        return $this->hasMany(UserWordLike::class);
    }

    public function relatedWords(): HasMany
    {
        return $this->hasMany(WordToWord::class, 'word_id');
    }

    public function getIsLikedByUserAttribute(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return $this->likes()->where('user_id', Auth::user()->id)->exists();
    }
}
