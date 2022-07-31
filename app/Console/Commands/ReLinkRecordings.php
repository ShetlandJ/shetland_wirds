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
use App\Models\WordRecording;
use App\Services\UserService;
use App\Services\WordService;
use App\Models\WordDefinition;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PurgeUser extends Command
{
    protected $signature = 'recordings:reconnect';
    protected $description = 'Link any broken recordings';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $allRecordings = WordRecording::all();

        foreach ($allRecordings as $recording) {
            try {
                // split the filename by /
                $filename = explode('/', $recording->filename);
                // get the first part of the filename
                $word = $filename[0];

                // get the word
                $word = Word::where('word', $word)->first();

                // set the recording's word_id as $word->id
                $recording->word_id = $word->id;
                $recording->save();
            } catch (\Exception $e) {
                // log failure
            }
        }
    }

}
