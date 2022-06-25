<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Database\Seeders\UnabbreviateWordTypes;
use Illuminate\Database\Migrations\Migration;

class RunSeederUnabbreviateWordTypes extends Migration
{
    public function up()
    {
        (new UnabbreviateWordTypes())->run();
    }
}
