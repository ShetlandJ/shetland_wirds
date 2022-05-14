<?php

namespace App\Services;

use App\Models\Word;
use Illuminate\Support\Str;
use App\Models\UserWordLike;
use App\Models\WordToWord;
use Illuminate\Support\Facades\Auth;

class WordService
{
    public function findBy(string $searchString)
    {
        $query = Word::query();

        $query = $query->where('words.word', 'like', "%{$searchString}%");

        return $query->get();
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
            'is_liked' => $word->isLikedByUser,
            'likes' => $word->likes->count(),
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
}
