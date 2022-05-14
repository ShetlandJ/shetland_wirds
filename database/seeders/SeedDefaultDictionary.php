<?php

namespace Database\Seeders;

use App\Models\Word;
use App\Models\WordToWord;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeedDefaultDictionary extends Seeder
{
    public function run()
    {
        $path = storage_path() . "/shet_words.json"; // ie: /var/www/laravel/app/storage/json/filename.json

        $words = json_decode(file_get_contents($path), true);

        foreach ($words as $word) {
            $newWord = new Word();

            $newWord->uuid = (string) Str::uuid();
            $newWord->word = $word['word'];
            $newWord->translation = $word['translation'] ?? null;
            $newWord->example_sentence = $word['example_sentence'] ?? null;
            $newWord->type = $word['type'] ?? null;
            $newWord->external_id = $word['external_id'] ?? null;
            $newWord->creator_id = 0;
            $newWord->save();
        }

        $allWords = Word::all();
        foreach ($words as $word) {
            $parentWord = $allWords->where('word', $word['word'])->first();

            if (isset($word['see_also'])) {
                $seeAlsoWords = explode(',', $word['see_also']);

                foreach ($seeAlsoWords as $seeAlsoWord) {
                    $foundWord = Word::where('word', $seeAlsoWord)->first();

                    if (!$foundWord) {
                        logger('Word not found: ' . $seeAlsoWord . ' parent word: ' . $word['word']);
                    } else {
                        $wordToWord = new WordToWord();

                        $wordToWord->uuid = (string) Str::uuid();
                        $wordToWord->parent_word_id = $foundWord->id;
                        $wordToWord->word_id = $parentWord->id;
                        $wordToWord->save();
                    }
                }
            }
        }
    }
}
