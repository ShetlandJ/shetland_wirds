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

class ConvertToMp3 extends Command
{
    protected $signature = 'audio:convert';
    protected $description = 'Convert all audio from webm to mp3';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $recordings = WordRecording::where('filename', 'like', '%webm')->get();

        foreach ($recordings as $recording) {
            try {
                $filename = str_replace('storage/', '', $recording->filename);
                $baseName = str_replace('storage/', '', $filename);
                $baseName = str_replace('.webm', '', $baseName);
                $newFileName = $baseName . '.mp3';

                FFMpeg::fromDisk($this->getAssetPath())
                ->open($filename)
                ->export()
                ->toDisk($this->getAssetPath())
                ->inFormat(new Mp3)
                ->save($newFileName);

                // log success
                $this->info('Converted ' . $filename . ' to ' . $newFileName);
            } catch (\Exception $e) {
                // log failure
                $this->error('Failed to convert ' . $filename . ' to ' . $newFileName);
                $this->error($e->getMessage());
                continue;
            }
        }
    }

    public function getAssetPath(): string
    {
        if (App::environment('production')) {
            return sprintf('https://%s.amazonaws.com/', 'spaektionary-recordings.s3.eu-west-1/');
        }

        return 'public';
    }
}
