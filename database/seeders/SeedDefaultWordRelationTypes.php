<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Models\WordRelationType;

class SeedDefaultWordRelationTypes extends Seeder
{
    public function run()
    {
        foreach (WordRelationType::DEFAULT_TYPES as $key => $wordRelationType) {
            WordRelationType::create([
                'uuid' => (string) Str::uuid(),
                'name' => $key,
                'title' => $wordRelationType,
            ]);
        }
    }
}
