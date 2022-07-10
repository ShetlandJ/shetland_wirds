<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Database\Seeders\SeedDefaultWordRelationTypes;

class RunSeederSeedDefaultWordRelationTypes extends Migration
{
    public function up()
    {
        (new SeedDefaultWordRelationTypes)->run();
    }
}
