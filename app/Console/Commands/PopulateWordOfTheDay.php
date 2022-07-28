<?php

namespace App\Console\Commands;

use App\Models\Word;
use App\Models\WordDefinition;
use App\Models\WordOfTheDay;
use App\Services\WordService;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PopulateWordOfTheDay extends Command
{
    protected $signature = 'word:add-word-of-the-day';
    protected $description = 'Add a word of the day';

    protected WordService $wordService;

    public function __construct()
    {
        parent::__construct();
        $this->wordService = app(WordService::class);
    }

    public function handle()
    {
        $recentWordIds = $this->wordService->getRecentWordOfTheDayIds();

        $newWordOfTheDay = Word::whereNotIn('id', $recentWordIds)
            ->whereNotIn('word', Word::WORDS_TO_EXCLUDE)
            ->inRandomOrder()
            ->first();

        app(WordService::class)->createWordOfTheDay($newWordOfTheDay);
    }
}
