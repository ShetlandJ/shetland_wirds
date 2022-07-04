<?php

namespace App\Models;

use App\Models\User;
use App\Models\Word;
use Illuminate\Database\Eloquent\Model;

class WordOfTheDay extends Model
{
    protected $table = 'word_of_the_day';

    protected $dates = [
        'created_at',
        'scheduled_for',
    ];

    // fillable
    public $fillable = [
        'uuid',
        'word_id',
        'user_id',
        'scheduled_for',
        'created_at',
        'updated_at',
    ];

    public function word()
    {
        return $this->belongsTo(Word::class, 'word_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
