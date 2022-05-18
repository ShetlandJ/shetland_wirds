<?php

use Database\Seeders;
use Database\Seeders\SeedRoles;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RunSeederSeedRoles extends Migration
{
    public function up()
    {
        (new SeedRoles())->run();
    }
}
