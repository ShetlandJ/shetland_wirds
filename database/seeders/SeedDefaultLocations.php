<?php

namespace Database\Seeders;

use Ramsey\Uuid\Uuid;
use App\Models\Location;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class SeedDefaultLocations extends Seeder
{
    public function run()
    {
        foreach (Location::LOCATIONS as $location) {
            Location::create([
                'uuid' => Str::uuid(),
                'name' => $location,
            ]);
        }
    }
}
