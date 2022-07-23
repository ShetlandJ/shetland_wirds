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
use App\Models\WordRelationType;
use App\Models\WordToLocation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Routing\Loader\Configurator\CollectionConfigurator;

class WordService
{
    public function findBy(?string $searchString = '', array $pagination = [], ?string $letter = null): Collection
    {
        $query = Word::query();

        $query->select('words.*');

        $query->leftJoin('word_definitions', 'words.id', '=', 'word_definitions.word_id');

        if ($searchString) {
            $query->whereLike($searchString);
        }

        if (isset($pagination['page'])) {
            $pageSize = 10;
            $query
                ->take($pageSize)
                ->skip(($pagination['page'] - 1) * $pageSize);
        }

        if ($letter) {
            $query->where('words.word', 'like', $letter . '%');
        }

        $query->where('words.pending', false);
        $query->where('words.rejected', false);

        $query->groupBy('words.id');

        $exactMatchWord = $this->findExactWordBySearch($searchString);
        if ($exactMatchWord) {
            $query->where('words.uuid', '!=', $exactMatchWord['id']);
        }

        return $query->get();
    }

    public function findAllUserWords(): Collection
    {
        $query = Word::query();

        $query->where('creator_id', Auth::user()->id);

        return $query->get()
            ->map(fn (Word $word) => $this->formatWord($word));
    }

    public function findExactWordBySearch(string $searchString): ?array
    {
        $foundWord = Word::where('word', $searchString)->first();

        if (!$foundWord) {
            return null;
        }

        return $this->formatWord($foundWord);
    }

    public function findByWordRaw(string $word): ?Word
    {
        $foundWord = Word::where('slug', $word)->first();

        if (!$foundWord) {
            return null;
        }

        return $foundWord;
    }

    public function findByWord(string $word): ?array
    {
        $foundWord = Word::where('slug', $word)->first();

        if (!$foundWord) {
            return null;
        }

        return $this->formatWord($foundWord);
    }

    public function findByUuid(string $uuid): ?array
    {
        $foundWord = Word::where('uuid', $uuid)->first();

        if (!$foundWord) {
            return null;
        }

        return $this->formatWord($foundWord);
    }

    public function findAllWordsWithPagination(string $searchString, array $pagination = [], ?string $letter = null): array
    {
        $words = $this->findBy($searchString, $pagination, $letter);

        $output = [];
        foreach ($words as $word) {
            $formattedWord = $this->formatWord($word);
            $formattedWord['see_also'] = $word->relatedWords->filter(function ($relatedWord) {
                return $relatedWord->word;
            })
            ->map(fn (WordToWord $w2w) => $this->formatWord($w2w->word));

            $output[] = $formattedWord;
        }

        return $output;
    }

    public function formatWord(Word $word): array
    {
        return [
            'id' => $word->uuid,
            'word' => $word->word,
            'slug' => $word->slug,
            'definitions' => $word->formattedDefinitions,
            'is_liked' => (bool) $word->isLikedByUser,
            'likes' => $word->likes->count(),
            'pending' => $word->pending,
            'rejected' => (bool) $word->rejected,
            'reason' => $word->reason,
            'creator_name' => $word->creator ? $word->creator->name : 'Unregistered',
            'comments'=> $this->getComments($word)->values()->all(),
            'recordings' => $this->getRecordings($word),
            'linked_words' => $this->getLinkedWords($word),
            'updated_at' => $word->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    public function getLinkedWords(Word $word): array
    {
        $parents = WordToWord::select(
            'words_to_words.*',
            'word_relation_types.uuid as type_uuid',
            'word_relation_types.title as type_title'
        )
            ->where('linked_word_id', $word->id)
            ->join('word_relation_types', 'word_relation_types.id', '=', 'words_to_words.type_id')
            ->get();

        $payload = [];

        if ($parents->count() > 0) {
        foreach ($parents as $parent) {
            if ($parent->word->id !== $word->id) {
                $payload[$parent->word->id]['id'] = $parent->word->uuid;
                $payload[$parent->word->id]['word'] = $parent->word->word;
                $payload[$parent->word->id]['slug'] = $parent->word->slug;
                $payload[$parent->word->id]['type_id'] = $parent->type_uuid ?? null;
                $payload[$parent->word->id]['type'] = $parent->type_title ?? null;
            }

            $children = WordToWord::select(
                'words_to_words.*',
                'word_relation_types.uuid as type_uuid',
                'word_relation_types.title as type_title'
            )
                ->where('word_id', $parent->word_id)
                ->join('word_relation_types', 'word_relation_types.id', '=', 'words_to_words.type_id')
                ->get();

            foreach ($children as $child) {
                if ($child->linkedWord->id === $word->id) {
                    continue;
                }

                $payload[$child->linked_word_id] = [
                    'id' => $child->linkedWord->uuid,
                    'word' => $child->linkedWord->word,
                    'slug' => $child->linkedWord->slug,
                    'type_id' => $child->type_uuid ?? null,
                    'type' => $child->type_title ?? null,
                ];
            }
        }
    } else {
        $children = WordToWord::select(
            'words_to_words.*',
            'word_relation_types.uuid as type_uuid',
            'word_relation_types.title as type_title'
        )
            ->where('word_id', $word->id)
            ->join('word_relation_types', 'word_relation_types.id', '=', 'words_to_words.type_id')
            ->get();

        foreach ($children as $child) {
            if ($child->linkedWord->id === $word->id) {
                continue;
            }

            $payload[$child->linked_word_id] = [
                'id' => $child->linkedWord->uuid,
                'word' => $child->linkedWord->word,
                'slug' => $child->linkedWord->slug,
                'type_id' => $child->type_uuid ?? null,
                'type' => $child->type_title ?? null,
            ];
        }
    }

        return array_values($payload);
    }

    public function getRecordings(Word $word): Collection
    {
        return $word->activeRecordings->map(fn (WordRecording $recording) => [
            'id' => $recording->uuid,
            'url' => asset($recording->filename),
            'speaker_name' => $recording->speaker ? $recording->speaker->name : 'Unregistered',
            'created_at' => $this->formatDate($recording->created_at),
            'type' => $recording->type,
        ]);
    }

    public function formatComment(Comment $comment)
    {
        $formattedComment = [
            'id' => $comment->uuid,
            'parent_comment_id' => $comment->parent ? $comment->parent->uuid : null,
            'word_id' => $comment->word->uuid,
            'author_id' => $comment->author->uuid,
            'author' => $comment->author->name,
            'author_initials' => $comment->author->initials,
            'created_at' => $this->formatDate($comment->created_at),
            'message' => $comment->comment,
        ];

        if ($comment->childComments) {
            $formattedComment['child_comments'] = $comment->childComments->map(fn (Comment $comment) => $this->formatComment($comment));
        }

        return $formattedComment;
    }

    public function getComments($word): Collection
    {
        if (!$word->comments) {
            return collect();
        }

        return $word->comments
            ->filter(fn (Comment $comment) => !$comment->parent)
            ->map(fn (Comment $comment) => $this->formatComment($comment));
    }

    public function handleLike(string $word): void
    {
        $word = Word::where('word', $word)->first();

        if (!$word) {
            return;
        }

        if ($word->isLikedByUser) {
            UserWordLike::where('user_id', Auth::id())->where('word_id', $word->id)->delete();
        } else {
            UserWordLike::create([
                'user_id' => Auth::id(),
                'word_id' => $word->id,
            ]);
        }
    }

    public function validateNewWordSubmission(array $payload): bool
    {
        if (isset($payload['newWord'])) {
            $wordExists = Word::where('word', $payload['newWord'])->exists();
            if ($wordExists) {
                return false;
            }
        }

        if (!isset($payload['translation'])) {
            return false;
        }

        if (isset($payload['translation'])) {
            if (strlen($payload['translation']) < 3) {
                return false;
            }
        }

        return true;
    }

    public function createWord(array $payload): Word
    {
        $newWord = Word::create([
            'uuid' => (string) Str::uuid(),
            'word' => $payload['newWord'],
            'creator_id' => Auth::id() ?? null,
            'pending' => true,
            'slug' => Str::slug($payload['newWord'])
        ]);

        $newWord->definitions()->create([
            'uuid' => (string) Str::uuid(),
            'word_id' => $newWord->id,
            'definition' => $payload['translation'],
            'example_sentence' => $payload['example_sentence'],
            'pending' => true,
            'type' => $payload['word_type'],
        ]);

        return $newWord;
    }

    public function findAllPendingWords(): Collection
    {
        return Word::where('pending', true)
            ->where('rejected', false)
            ->get()
            ->map(fn (Word $word) => $this->formatWord($word));
    }

    public function findAllPendingDefinitions(): Collection
    {
        return WordDefinition::where('pending', true)
            ->get()
            ->map(function (WordDefinition $definition) {
                $word = $definition->word;
                return [
                    'id' => $definition->uuid,
                    'word_id' => $word->uuid,
                    'word' => $word->word,
                    'definition' => $definition->definition,
                    'example_sentence' => $definition->example_sentence,
                    'user' => $definition->user->name ?? 'Unregistered',
                    'pending' => true,
                    'created_at' => $this->formatDate($definition->created_at)
                ];
            });
    }

    public function findAllRejectedWords(): Collection
    {
        return Word::where('rejected', true)
            ->get()
            ->map(fn (Word $word) => $this->formatWord($word));
    }

    public function approveWord(string $wordUuid): ?Word
    {
        $word = Word::where('uuid', $wordUuid)->first();

        if (!$word) {
            return null;
        }

        $word->rejected = false;
        $word->reason = null;
        $word->pending = false;
        $word->save();

        return $word;
    }

    public function rejectWord(string $wordUuid, ?string $rejectReason): ?Word
    {
        $word = Word::where('uuid', $wordUuid)->first();

        if (!$word) {
            return null;
        }

        $word->pending = true;
        $word->rejected = true;
        $word->reason = $rejectReason;
        $word->save();

        return $word;
    }

    public function getAdminDashboardMetrics(): array
    {
        $allWords = Word::all();

        $headlineMetrics = [
            ['name' => 'Total pending words', 'value' => $allWords->where('pending', true)->where('rejected', false)->count(), 'type' => 'pending'],
            ['name' => 'Total rejected words', 'value' => $allWords->where('rejected', true)->count(), 'type' => 'rejected'],
        ];

        $recentMetrics = [
            [
                'name' => 'Words added by users in the last 30 days',
                'value' => Word::where('created_at', '>=', now()->subDays(30))->userAdded()->approved()->count(),
                'type' => 'words'
            ],
            ['name' => 'Likes in the last 30 days', 'value' => UserWordLike::where('created_at', '>=', now()->subDays(30))->count(), 'type' => 'likes'],
            ['name' => 'Comments in the last 30 days', 'value' => Comment::where('created_at', '>=', now()->subDays(30))->count(), 'type' => 'comments'],
        ];

        $allTimeMetrics = [
            ['name' => 'Total words', 'value' => $allWords->count(), 'type' => 'total'],
            ['name' => 'Most liked word', 'value' => $allWords->sortByDesc('likes_count')->first()->word, 'type' => 'likes'],
        ];

        return [
            "headline" => $headlineMetrics,
            "recent" => $recentMetrics,
            "allTime" => $allTimeMetrics,
        ];
    }

    public function createComment(string $commentText, Word $word, ?Comment $comment = null): Comment
    {
        $comment = Comment::create([
            'uuid' => (string) Str::uuid(),
            'word_id' => $word->id,
            'user_id' => Auth::id(),
            'comment' => $commentText,
            'comment_id' => $comment ? $comment->id : null,
        ]);

        return $comment;
    }

    public function saveRecording(Word $word, string $fileName): WordRecording
    {
        $recording = new WordRecording();

        $recording->word_id = $word->id;
        $recording->user_id = Auth::id();
        $recording->uuid = (string) Str::uuid();
        $recording->type = 'word';
        $recording->filename = $fileName;
        $recording->pending = true;

        $recording->save();

        return $recording;
    }

    protected function formatDate(string $date): string
    {
        if (empty($date)) {
            return null;
        }

        if (! $date instanceof Carbon) {
            $date = new Carbon($date);
        }

        return $date->toIso8601String();
    }

    public function findWithoutDefinitions(): array
    {
        return Word::query()
            ->select('words.*')
            ->leftJoin('word_definitions', 'words.id', '=', 'word_definitions.word_id')
            ->inRandomOrder()
            ->limit(6)
            ->whereNull('word_definitions.id')
            ->where('words.ignore_definition', false)
            ->get()
            ->map(fn (Word $word) => $this->formatWord($word))
            ->values()
            ->all();
    }


    public function findWithoutExampleSentences(): array
    {
        return Word::query()
            ->select('words.*')
            ->join('word_definitions', 'words.id', '=', 'word_definitions.word_id')
            ->inRandomOrder()
            ->limit(6)
            ->whereNull('word_definitions.example_sentence')
            ->where('words.ignore_definition', false)
            ->get()
            ->map(fn (Word $word) => $this->formatWord($word))
            ->values()
            ->all();
    }

    public function ignoreWord(string $wordUuid): Word
    {
        $word = Word::where('uuid', $wordUuid)->first();

        if (!$word) {
            return null;
        }

        $word->ignore_definition = true;
        $word->save();

        return $word;
    }

    public function addDefinition(
        string $wordUuid,
        string $definition,
        ?string $exampleSentence
    ): Word {
        $word = Word::where('uuid', $wordUuid)->first();

        if (!$word) {
            return null;
        }

        $word->ignore_definition = false;
        $word->save();

        $definition = WordDefinition::create([
            'uuid' => (string) Str::uuid(),
            'word_id' => $word->id,
            'definition' => $definition,
            'pending' => true,
            'example_sentence' => $exampleSentence,
            'user_id' => Auth::id() ?? null,
            'pending' => true,
        ]);

        return $word;
    }

    public function getPendingRecordings(): array
    {
        $recordings = WordRecording::where('pending', true)
            ->get();

        return $recordings->map(fn (WordRecording $recording) => $this->formatRecording($recording))->values()->all();
    }

    public function formatRecording(WordRecording $recording)
    {
        return [
            'id' => $recording->uuid,
            'url' => asset($recording->filename),
            'type' => $recording->type,
            'created_at' => $recording->created_at->toIso8601String(),
            'user' => $recording->user ? $recording->user->name : 'Unregistered',
            'speaker_name' => $recording->speaker ? $recording->speaker->name : 'Unregistered',
            'created_at' => $this->formatDate($recording->created_at),
            'word' => $recording->word ? $this->formatWord($recording->word) : null,
        ];
    }

    public function approveRecording(string $recordingUuid): ?WordRecording
    {
        $recording = WordRecording::where('uuid', $recordingUuid)->first();

        if (!$recording) {
            return null;
        }

        $recording->pending = false;
        $recording->save();

        return $recording;
    }

    public function deleteRecording(string $recordingUuid): bool
    {
        $recording = WordRecording::where('uuid', $recordingUuid)->first();
        if (!$recording) {
            return null;
        }

        File::delete($recording->filename);

        return $recording->delete();
    }

    public function approveDefinition(string $definitionUuid): ?WordDefinition
    {
        $definition = WordDefinition::where('uuid', $definitionUuid)->first();

        if (!$definition) {
            return null;
        }

        $definition->pending = false;
        $definition->save();

        return $definition;
    }

    public function deleteDefinition(string $definitionUuid): bool
    {
        $definition = WordDefinition::where('uuid', $definitionUuid)->first();
        if (!$definition) {
            return null;
        }

        return $definition->delete();
    }

    public function getAllLocations(): array
    {
        return Location::all()->map(fn (Location $location) => $this->formatLocation($location))->values()->all();
    }

    public function formatLocation(Location $location): array
    {
        return [
            'id' => $location->uuid,
            'name' => $location->name,
        ];
    }

    public function addLocationsToWordLinks(Word $word, array $locationUuids): void
    {
        $fullLocations = Location::whereIn('uuid', $locationUuids)->get();

        $links = WordToLocation::where('word_id', $word->id)
            ->where('user_id', Auth::id())
            ->get();

        foreach ($links as $link) {
            $link->delete();
        }

        foreach ($fullLocations as $location) {
            WordToLocation::create([
                'uuid' => (string) Str::uuid(),
                'word_id' => $word->id,
                'location_id' => $location->id,
                'user_id' => Auth::id()
            ]);
        }
    }

    public function getUserLocationsForWordUuids(Word $word): array
    {
        $locations = WordToLocation::where('user_id', Auth::id())
            ->where('word_id', $word->id)
            ->get();

        return $locations->map(fn (WordToLocation $location) => $location->location->uuid)->all();
    }

    public function getAllWordLocationLinks(Word $word): Collection
    {
        return WordToLocation::where('word_id', $word->id)->get();
    }

    public function getPercentageDistributionWordLocations(Word $word): array
    {
        $wordLocations = $this->getAllWordLocationLinks($word);

        // get counts for each location
        $counts = [];

        // Need a total number of selected locations
        $total = $wordLocations->count();
        // Need the number of times that location has been selected
        // need to present it like ['word' => 'string', 'count' => 0]
        foreach ($wordLocations as $wordLocation) {
            if (!isset($counts[$wordLocation->location->uuid])) {
                $counts[$wordLocation->location->uuid]['count'] = 0;
                $counts[$wordLocation->location->uuid]['location'] = $wordLocation->location->name;
                $counts[$wordLocation->location->uuid]['location_id'] = $wordLocation->location->uuid;
            }
            $counts[$wordLocation->location->uuid]['count']++;
        }

        // calculate the percentage
        $percentages = [];
        foreach ($counts as $key => $count) {
            $percentages[$key]['percentage'] = round(($count['count'] / $total) * 100);
            $percentages[$key]['count'] = $count['count'];
            $percentages[$key]['location'] = $count['location'];
        }

        return (array) array_values($percentages);
    }

    public function getRecentWordOfTheDayIds(): array
    {
        return WordOfTheDay::select('*')
            ->orderBy('created_at', 'desc')
            ->take(90)
            ->get()
            ->pluck('id')
            ->values()
            ->all();
    }

    public function createWordOfTheDay(Word $word): WordOfTheDay
    {
        $wordsOfTheDay = WordOfTheDay::select('scheduled_for')
            ->orderBy('scheduled_for', 'asc')
            ->get();

        $scheduledFor = Carbon::today();

        foreach ($wordsOfTheDay as $key => $wordOfTheDay) {
            if (isset($wordsOfTheDay[$key+1])) {
                $diff = $wordsOfTheDay[$key+1]->scheduled_for->diffInDays($wordOfTheDay->scheduled_for);

                if ($diff > 1) {
                    $scheduledFor = $wordOfTheDay->scheduled_for->addDay();
                } else {
                    $scheduledFor = $wordsOfTheDay[$key+1]->scheduled_for->addDay();
                }
            }
        }

        return WordOfTheDay::create([
            'uuid' => (string) Str::uuid(),
            'word_id' => $word->id,
            'creator_id' => Auth::id() ?? null,
            'scheduled_for' => $scheduledFor,
        ]);
    }

    public function getFeaturedWord(): ?array
    {
        // get word of the day where scheduled_for is between the start and end of today
        $wotd = WordOfTheDay::whereBetween('scheduled_for', [Carbon::today(), Carbon::tomorrow()])
            ->first();

        if ($wotd) {
            return $this->formatWord($wotd->word);
        }

        return null;
    }

    public function userHasPendingRecording(Word $word): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return (bool) WordRecording::where('word_id', $word->id)
            ->where('user_id', Auth::id())
            ->where('pending', 1)
            ->first();
    }
}
