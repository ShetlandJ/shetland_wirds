<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWordRecordingsTable extends Migration
{
    private const TABLE = 'word_recordings';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('word_id');
            $table->unsignedBigInteger('user_id');
            $table->string('filename');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists(self::TABLE);
    }
}
