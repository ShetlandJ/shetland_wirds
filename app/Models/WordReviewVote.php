<?php

namespace App\Models;

use App\Models\User;
use App\Models\Word;
use Illuminate\Database\Eloquent\Model;

class WordReviewVote extends Model
{
    protected $table = 'word_peer_reviews';

    protected $casts = [
        'approved' => 'boolean',
    ];

    protected $fillable = [
        'uuid',
        'user_id',
        'word_id',
        'approved',
        'comment',
    ];

    public function word()
    {
        return $this->belongsTo(Word::class, 'word_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
