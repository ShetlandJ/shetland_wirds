<?php

namespace Database\Seeders;

use App\Models\WordDefinition;
use Illuminate\Database\Seeder;

class UnabbreviateWordTypes extends Seeder
{
    public function run()
    {
        // Convert all word types from abbreviations in the word_definitions table into full words
        $wordDefinitions = WordDefinition::all();

        foreach ($wordDefinitions as $wordDefinition) {
            if (isset(WordDefinition::WORD_TYPE_ABBR_MAP[$wordDefinition->type])) {
                $wordDefinition->type = WordDefinition::WORD_TYPE_ABBR_MAP[$wordDefinition->type];
                $wordDefinition->save();
            }
        }
    }
}
