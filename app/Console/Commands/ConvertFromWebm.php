<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Word;
use App\Models\Comment;
use App\Models\UserLog;
use App\Models\SearchLog;
use App\Models\WordReport;
use Illuminate\Support\Str;
use App\Models\UserWordLike;
use FFMpeg\Format\Audio\Mp3;
use App\Models\WordRecording;
use App\Services\UserService;
use App\Services\WordService;
use App\Models\WordDefinition;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class ConvertFromWebm extends Command
{
    protected $signature = 'recordings:webm-to-mp3';
    protected $description = 'Update the recordings to use mp3 instead of webm';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $recordings = WordRecording::where('filename', 'like', '%webm')->get();

        foreach ($recordings as $recording) {
            // if recording->filename contains .webm, update .webm to .mp3
            if (strpos($recording->filename, '.webm') !== false) {
                // replace $recording->filename's .webm with .mp3
                $recording->filename = str_replace('.webm', '.mp3', $recording->filename);
                $recording->save();
            }
        }
    }
}
