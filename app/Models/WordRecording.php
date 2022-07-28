<?php

namespace App\Models;

use App\Models\User;
use App\Models\Word;
use Illuminate\Database\Eloquent\Model;

class WordRecording extends Model
{
    protected $table = 'word_recordings';

    public function word()
    {
        return $this->belongsTo(Word::class, 'word_id');
    }

    public function speaker()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getFilePathsAttribute(): array
    {
        $dir = sprintf('storage/%s', $this->word->word);
        $files = array_diff(scandir($dir), array('..', '.'));

        return array_values($files);
    }
}
