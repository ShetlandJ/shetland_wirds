<?php

namespace App\Services;

use DateTime;
use Exception;
use Carbon\Carbon;
use App\Models\Word;
use App\Models\Comment;
use App\Models\WordToWord;
use Illuminate\Support\Str;
use App\Models\UserWordLike;
use App\Models\WordRecording;
use App\Models\WordDefinition;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Routing\Loader\Configurator\CollectionConfigurator;

class WordService
{
    public function findBy(?string $searchString = '', array $pagination = []): Collection
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

        $query->where('words.pending', false);
        $query->where('words.rejected', false);

        $query->groupBy('words.id');

        return $query->get();
    }

    public function findAllUserWords(): Collection
    {
        $query = Word::query();

        $query->where('creator_id', Auth::user()->id);

        return $query->get()
            ->map(fn (Word $word) => $this->formatWord($word));
    }

    public function findByWord(string $word): ?array
    {
        // dd($word);
        $foundWord = Word::where('slug', $word)->first();

        if (!$foundWord) {
            return null;
        }

        return $this->formatWord($foundWord);
    }

    public function findAllWordsWithPagination(string $searchString, array $pagination = []): array
    {
        $words = $this->findBy($searchString, $pagination);

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
        ];
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
        return Word::create([
            'uuid' => (string) Str::uuid(),
            'word' => $payload['newWord'],
            'creator_id' => Auth::id() ?? null,
            'pending' => true,
        ]);
    }

    public function findAllPendingWords(): Collection
    {
        return Word::where('pending', true)
            ->where('rejected', false)
            ->get()
            ->map(fn (Word $word) => $this->formatWord($word));
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
}
