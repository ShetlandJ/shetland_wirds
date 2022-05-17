<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RunSeederSeedRoles extends Migration
{
    public function up()
    {
        (new SeedRoles())->run();
    }
}
