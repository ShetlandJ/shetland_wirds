<?php

use Database\Seeders\SeedDefaultLocations;
use Illuminate\Database\Migrations\Migration;

class RunSeederSeedDefaultLocations extends Migration
{
    public function up()
    {
        (new SeedDefaultLocations)->run();
    }
}
