<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterWordsToWordsTable extends Migration
{
    private const TABLE = 'words_to_words';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->renameColumn('word_id', 'linked_word_id');
            $table->renameColumn('parent_word_id', 'word_id');
            $table->unsignedBigInteger('type_id')->after('word_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->renameColumn('linked_word_id', 'word_id');
            $table->renameColumn('word_id', 'parent_word_id');

            $table->dropColumn('type_id');
        });
    }
}
