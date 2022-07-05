<?php

namespace App\Services;

use DateTime;
use Exception;
use Carbon\Carbon;
use App\Models\Word;
use App\Models\Comment;
use App\Models\Location;
use App\Models\WordToWord;
use Illuminate\Support\Str;
use App\Models\UserWordLike;
use App\Models\WordOfTheDay;
use App\Models\WordRecording;
use App\Models\WordDefinition;
use App\Models\WordToLocation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Routing\Loader\Configurator\CollectionConfigurator;

class AdminService
{
    public function getQueuedWords(?string $searchString = '', array $pagination = []): array
    {
        $query = WordOfTheDay::query();

        $query->select('word_of_the_day.*');

        $query->leftJoin('words', 'words.id', '=', 'word_of_the_day.word_id');
        $query->leftJoin('word_definitions', 'word_definitions.word_id', '=', 'words.id');

        if ($searchString) {
            $query->whereLike($searchString);
        }

        if (isset($pagination['page'])) {
            $pageSize = 10;
            $query
                ->take($pageSize)
                ->skip(($pagination['page'] - 1) * $pageSize);
        }

        $query->where('words.pending', false);
        $query->where('words.rejected', false);

        $query->where('word_of_the_day.scheduled_for', '>=', Carbon::today()->toDateTimeString());
        $query->orderBy('word_of_the_day.scheduled_for', 'asc');

        $list = $query->get();

        // format list
        $formattedList = [];
        foreach ($list as $item) {
            $newItem = [];
            $newItem['id'] = $item->uuid;
            $newItem['word'] = $item->word->word;
            $newItem['scheduled_for'] = Carbon::parse($item->scheduled_for)->toDateTimeString();
            $newItem['creator'] = $item->creator ? $item->creator->name : 'System generated';
            $formattedList[] = $newItem;
        }

        return $formattedList;
    }

    public function returnSearchedWordsList(string $searchString)
    {
        $query = Word::query();

        $lowercasedSearchString = Str::lower($searchString);
        $query->where('words.word', 'like', "%{$lowercasedSearchString}%");

        $query->groupBy('words.id');

        $result = $query->get();

        $exactResult = [];
        $formattedResult = [];

        $recentWordIds = app(WordService::class)->getRecentWordOfTheDayIds();

        foreach ($result as $item) {
            if (in_array($item->id, $recentWordIds)) {
                continue;
            }

            if ($item->word === $searchString) {
                $exactResult[] =
                ['id' => $item->uuid, 'word' => sprintf('%s (exact match)', $item->word)];
                continue;
            }


            $formattedResult[] = [
                'id' => $item->uuid,
                'word' => $item->word,
            ];
        }

        return array_merge($exactResult, $formattedResult);
    }
}
