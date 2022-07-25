<?php

namespace App\Models;

use App\Models\User;
use App\Models\Word;
use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{
    protected $table = 'user_logs';

    protected $dates = [
        'created_at',
    ];

    public $fillable = [
        'uuid',
        'route',
        'user_id',
        'session_id',
        'word_id',
        'ip_address',
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
