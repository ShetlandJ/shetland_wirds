<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWordDefinitionsTable extends Migration
{
    private const TABLE = 'word_definitions';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->longText('definition');
            $table->longText('example_sentence')->nullable();
            $table->string('type')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('word_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists(self::TABLE);
    }
}
