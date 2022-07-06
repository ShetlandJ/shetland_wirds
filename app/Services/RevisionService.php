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

    public function formatPayload(array $payload): array
    {
        $difference = [];
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

                // compare original and updated definition and record the difference
                // $contentAdded = substr($value['updated'], strlen($value['original']));

                $added = str_replace(str_split(strtolower($value['original'])), '', strtolower($value['updated']));
                $removed = str_replace(str_split(strtolower($value['updated'])), '', strtolower($value['original']));

                if ($added) {
                    $difference['definitions'][$key]['added'] = $added;
                } else {
                    $difference['definitions'][$key]['removed'] = $removed;
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
