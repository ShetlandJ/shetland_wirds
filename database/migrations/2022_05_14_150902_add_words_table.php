<?php

use App\Models\Word;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWordsTable extends Migration
{
    private const TABLE = 'words';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid')->unique();
            $table->string('word');
            $table->longText('translation');
            $table->bigInteger('external_id')->nullable();
            $table->longText('example_sentence')->nullable();
            $table->string('type')->nullable();
            $table->bigInteger('creator_id')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists(self::TABLE);
    }
}
