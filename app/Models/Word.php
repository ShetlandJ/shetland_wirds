<?php

namespace App\Models;

use App\Models\UserWordLike;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Word extends Model
{
    protected $table = 'words';

    public function likes()
    {
        return $this->hasMany(UserWordLike::class);
    }

    public function getIsLikedByUserAttribute()
    {
        return $this->likes()->where('user_id', Auth::user()->id)->exists();
    }
}
