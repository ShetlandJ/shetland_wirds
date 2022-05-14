<?php

use Database\Seeders\SeedDefaultDictionary;
use Illuminate\Database\Migrations\Migration;

class RunSeederSeedDefaultDictionary extends Migration
{
    public function up()
    {
        (new SeedDefaultDictionary)->run();
    }
}
