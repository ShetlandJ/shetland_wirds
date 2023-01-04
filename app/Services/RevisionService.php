<?php

namespace App\Services;

use App\Models\Revision;
use App\Models\User;
use Illuminate\Support\Str;
use App\Services\WordService;
use Illuminate\Support\Facades\Auth;

class RevisionService
{
    public WordService $wordService;

    public function __construct(WordService $wordService)
    {
        $this->wordService = $wordService;
    }

    public function findAll(): array
    {
        $revisions = Revision::orderBy('created_at', 'desc')->get();

        $payload = [];

        foreach ($revisions as $revision) {
            $payload[] = [
                'id' => $revision->uuid,
                'word' => $revision->word,
                'user' => $revision->user->name ?? '',
                'revisions' => $this->formatRevisions(json_decode($revision->revisions)),
                'created_at' => $revision->created_at->format('Y-m-d H:i:s'),
            ];
        }

        return $payload;
    }

    public function formatRevisions(object $revisions)
    {
        $payload = [];

        $payload['word']['original'] = $revisions->word->word_original ?? '';
        $payload['word']['updated'] = $revisions->word->word_updated ?? '';
        $payload['word']['changed'] = false;
        if (isset($revisions->word)) {
            $payload['word']['changed'] = $revisions->word->word_original !== $revisions->word->word_updated;
        }

        if (isset($revisions->definitions)) {
            foreach ($revisions->definitions as $definition) {
                $payload['definitions'][] = [
                    'original' => $definition->definition_original ?? '',
                    'updated' => $definition->definition_updated ?? '',
                ];
            }
        }

        return $payload;
    }

    public function formatPayload(array $payload): array
    {
        $difference = [];

        if (isset($payload['newWord']) && $payload['newWord']) {
            $difference['originalWord'] = '';
            $difference['updatedWord'] = $payload['newWord'];
        } else {
            // check if originalWord and updatedWord are different
            if ($payload['originalWord'] !== $payload['updatedWord']) {
                $difference['word'] = [];
                $difference['word']['word_original'] = $payload['originalWord'];
                $difference['word']['word_updated'] = $payload['updatedWord'];
            }

            // loop through definitionChanges and keep any differences
            $difference['definitions'] = [];
            foreach ($payload['definitionChanges'] as $key => $value) {
                if ($value['original'] !== $value['updated']) {
                    $difference['definitions'][$key] = [];
                    $difference['definitions'][$key]['definition_original'] = $value['original'];
                    $difference['definitions'][$key]['definition_updated'] = $value['updated'];
                }
            }
        }

        return $difference;
    }

    public function create(string $updatedWordText, array $payload, ?string $userUuid): Revision
    {
        $revision = new Revision();

        $revision->uuid = (string) Str::uuid();
        $revision->word = $updatedWordText;

        $foundWord = $this->wordService->findByWordRaw($updatedWordText);
        if ($foundWord) {
            $revision->word_id = $foundWord->id;
        } else {
            $revision->word_id = null;
        }

        $revision->revisions = json_encode($this->formatPayload($payload));

        $revision->user_id = $userUuid ? User::where('uuid', $userUuid)->first()->id : null;

        $revision->save();

        return $revision;
    }
}
