<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveDefinitionAndExampleSentenceFromWordsTable extends Migration
{
    private const TABLE = 'words';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('translation');
            $table->dropColumn('example_sentence');
            $table->dropColumn('external_id');
            $table->dropColumn('type');
        });
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->longText('translation')->nullable();
            $table->longText('example_sentence')->nullable();
            $table->string('external_id')->nullable();
            $table->string('type')->nullable();
        });
    }
}
