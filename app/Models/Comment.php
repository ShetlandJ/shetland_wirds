<?php

namespace App\Models;

use App\Models\User;
use App\Models\Word;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    protected $table = 'comments';

    protected $fillable = [
        'uuid',
        'user_id',
        'word_id',
        'comment_id',
        'comment',
    ];

    public function word(): BelongsTo
    {
        return $this->belongsTo(Word::class, 'word_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'comment_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function childComments()
    {
        return $this->hasMany(self::class, 'comment_id');
    }
}
