<?php

namespace App\Services;

use Exception;
use App\Models\Word;
use App\Models\WordToWord;
use Illuminate\Support\Str;
use App\Models\UserWordLike;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Routing\Loader\Configurator\CollectionConfigurator;

class WordService
{
    public function findBy(string $searchString)
    {
        $query = Word::query();

        $lowercasedSearchString = Str::lower($searchString);

        $query->where('word', 'like', "%{$lowercasedSearchString}%");

        return $query->get();
    }

    public function findByWord(string $word): ?array
    {
        $foundWord = Word::where('word', $word)->first();

        if (!$foundWord) {
            return null;
        }

        return $this->formatWord($foundWord);
    }

    public function findAllWords(string $searchString): array
    {
        $words = $this->findBy($searchString);

        $output = [];
        foreach ($words as $word) {
            $output[] = [
                'id' => $word->uuid,
                'word' => $word->word,
                'translation' => $word->translation,
                'example_sentence' => $word->example_sentence,
                'is_liked' => $word->isLikedByUser,
                'likes' => $word->likes->count(),
                'external_id' => $word->external_id,
                'see_also' => $word->relatedWords->filter(function ($relatedWord) {
                    return $relatedWord->word;
                })
                ->map(function (WordToWord $w2w) {
                    return $this->formatWord($w2w->word);
                }),
            ];
        }

        return $output;
    }

    public function formatWord(Word $word): array
    {
        return [
            'id' => $word->uuid,
            'word' => $word->word,
            'translation' => $word->translation,
            'example_sentence' => $word->example_sentence,
            'is_liked' => (bool) $word->isLikedByUser,
            'likes' => $word->likes->count(),
            'rejected' => (bool) $word->rejected,
            'reason' => $word->reason,
        ];
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
            'translation' => $payload['translation'],
            'example_sentence' => isset($payload['example_sentence']) ? $payload['example_sentence'] : null,
            'external_id' => null,
            'creator_id' => Auth::id() ?? null,
            'pending' => true,
            'type' => isset($payload['word_type']) ? $payload['word_type'] : null,
        ]);
    }

    public function findAllPendingWords(): Collection
    {
        return Word::where('pending', true)
        ->where('rejected', false)
        ->get()
            ->map(function (Word $word) {
                return $this->formatWord($word);
            });
    }

    public function findAllRejectedWords(): Collection
    {
        return Word::where('rejected', true)
        ->get()
            ->map(function (Word $word) {
                return $this->formatWord($word);
            });
    }

    public function approveWord(string $wordUuid): ?Word
    {
        $word = Word::where('uuid', $wordUuid)->first();

        if (!$word) {
            return null;
        }

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
}
