<?php

namespace App\Services;

use DateTime;
use Exception;
use Carbon\Carbon;
use App\Models\Word;
use App\Models\Comment;
use App\Models\Location;
use App\Models\Revision;
use App\Models\WordToWord;
use Illuminate\Support\Str;
use App\Models\UserWordLike;
use App\Models\WordOfTheDay;
use App\Models\WordRecording;
use App\Models\WordDefinition;
use App\Models\WordRelationType;
use App\Models\WordToLocation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Routing\Loader\Configurator\CollectionConfigurator;

class AdminService
{
    public RevisionService $revisionService;

    public function __construct(RevisionService $revisionService)
    {
        $this->revisionService = $revisionService;
    }

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
            $newItem['slug'] = $item->word->slug;
            $newItem['scheduled_for'] = Carbon::parse($item->scheduled_for)->toDateTimeString();
            $newItem['creator'] = $item->creator ? $item->creator->name : 'System generated';
            $formattedList[] = $newItem;
        }

        return $formattedList;
    }

    public function searchAllWords(string $searchString)
    {
        $query = Word::query();

        $lowercasedSearchString = Str::lower($searchString);
        $query->where('words.word', 'like', "%{$lowercasedSearchString}%");

        $query->groupBy('words.id');

        $result = $query->get();

        $exactResult = [];
        $formattedResult = [];

        foreach ($result as $item) {
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

    public function updateWord(array $payload): Word
    {
        $word = Word::where('uuid', $payload['id'])->first();
        $originalWord = $word->word;

        $word->word = $payload['word'];

        $definitionsChanges = [];
        foreach ($payload['definitions'] as $definition) {
            $wordDefinition = WordDefinition::where('uuid', $definition['id'])->first();
            $definitionsChanges[$wordDefinition->uuid]['original'] = $wordDefinition->definition;
            if ($wordDefinition) {
                $wordDefinition->definition = $definition['definition'];
                $wordDefinition->save();
                $definitionsChanges[$wordDefinition->uuid]['updated'] = $wordDefinition->definition;
            }
        }

        foreach ($payload['newDefinitions'] as $definition) {
            $wordDefinition = new WordDefinition();
            $wordDefinition->uuid = (string) Str::uuid();
            $wordDefinition->word_id = $word->id;
            $wordDefinition->definition = $definition['definition'];
            $wordDefinition->save();
            $definitionsChanges[$wordDefinition->uuid]['original'] = '';
            $definitionsChanges[$wordDefinition->uuid]['updated'] = $wordDefinition->definition;
        }

        foreach ($payload['removedDefinitions'] as $definition) {
            $wordDefinition = WordDefinition::where('uuid', $definition)->first();
            $definitionsChanges[$wordDefinition->uuid]['original'] = $wordDefinition->definition;
            $wordDefinition->delete();
            $definitionsChanges[$wordDefinition->uuid]['updated'] = '';
        }

        foreach ($payload['removedRecordings'] as $recordingUuid) {
            $recording = WordRecording::where('uuid', $recordingUuid)->first();
            $recording->delete();
        }

        $word->touch();
        $word->save();

        $updatedWord = $word->word;

        $this->revisionService->create($word->word, [
            'originalWord' => $originalWord,
            'updatedWord' => $updatedWord,
            'definitionChanges' => $definitionsChanges,
        ], $payload['userId']);

        $wordToWords = WordToWord::where('word_id', $word->id)->get();
        foreach ($wordToWords as $wordToWord) {
            $wordToWord->delete();
        }

        if ($payload['wordLinks']) {
            foreach ($payload['wordLinks'] as $link) {
                $relationType = WordRelationType::where('name', 'synonym')->first();

                if (isset($payload['wordRelationType'])) {
                    $relationType = WordRelationType::where('name', $payload['wordRelationType'])->first();
                }

                $linkedWord = Word::where('uuid', $link['id'])->first();
                $wordToWord = new WordToWord();
                $wordToWord->uuid = (string) Str::uuid();
                $wordToWord->word_id = $word->id;
                $wordToWord->linked_word_id = $linkedWord->id;
                $wordToWord->type_id = $relationType->id;
                $wordToWord->save();
            }
        }

        return $word;
    }
}
