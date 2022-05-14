<?php

use App\Models\WordToWord;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWordsToWordsTable extends Migration
{
    private const TABLE = 'words_to_words';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid')->unique();
            $table->bigInteger('parent_word_id')->unsigned();
            $table->bigInteger('word_id')->unsigned();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists(self::TABLE);
    }
}
