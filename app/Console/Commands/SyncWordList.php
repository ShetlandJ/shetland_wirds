<?php

namespace App\Console\Commands;

use App\Models\Word;
use App\Models\WordDefinition;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncWordList extends Command
{
    protected $signature = 'word:sync';
    protected $description = 'Sync words from Viveka\'s export';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $path = storage_path() . "/wordUpdates.csv"; // ie: /var/www/laravel/app/storage/json/filename.json

        $words = [];
        try {
            $counter = 0;
            if (($open = fopen($path, "r")) !== false) {
                while (($data = fgetcsv($open, 1000, ",")) !== false) {
                    if ($counter < 1) {
                        ++$counter;
                        continue;
                    }
                    [$word, $version, $definitions, $type] = $data;
                    $words[] = [
                        'word' => $word,
                        'version' => $version,
                        'definitions' => $definitions,
                        'type' => $type,
                    ];
                    ++$counter;
                }

                fclose($open);
            }
        } catch (\Exception $e) {
            logger($e->getMessage());
            return false;
        }

        DB::beginTransaction();

        try {
            foreach ($words as $word) {
                $this->createWord($word);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e->getMessage());
            $this->error($e->getMessage());
        }

        DB::commit();

        return true;
    }

    public function createWord(array $payload)
    {
        $foundWord = Word::where('word', $payload['word'])->first();

        if (!$foundWord) {
            $word = new Word();

            $slugExistsCount = Word::where('slug', Str::slug($payload['word']))->count();
            $word->word = $payload['word'];
            $word->uuid = (string) Str::uuid();
            $word->slug = $slugExistsCount
                ? sprintf('%s-%s', Str::slug($payload['word']), ($slugExistsCount + 1))
                : Str::slug($payload['word']);
            $word->creator_id = 0;
            $word->save();

            if (isset($payload['definitions']) && $payload['definitions'] !== '') {
                $word->definitions()->create([
                    'uuid' => (string) Str::uuid(),
                    'definition' => ltrim($payload['definitions']),
                    'example_sentence' => null,
                    'type' => $payload['type'] ?? null,
                ]);
            }
        } else {
            $foundDefinition = $foundWord->definitions()
                ->where('word_id', $foundWord->id)
                ->where('definition', $payload['definitions'])
                ->first();

            if (!$foundDefinition && isset($payload['definitions']) && $payload['definitions'] !== '') {
                $foundWord->definitions()->create([
                    'uuid' => (string) Str::uuid(),
                    'definition' => ltrim($payload['definitions']),
                    'example_sentence' => null,
                    'type' => $payload['type'] ?? null,
                ]);
            }
        }
    }
}
