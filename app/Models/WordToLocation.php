<?php

namespace App\Models;

use App\Models\User;
use App\Models\Word;
use App\Models\Location;
use Illuminate\Database\Eloquent\Model;

class WordToLocation extends Model
{
    protected $table = 'word_locations';

    protected $fillable = [
        'uuid',
        'user_id',
        'word_id',
        'location_id',
    ];

    public function word()
    {
        return $this->belongsTo(Word::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
