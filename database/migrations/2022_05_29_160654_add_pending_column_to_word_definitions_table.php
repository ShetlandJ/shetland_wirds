<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPendingColumnToWordDefinitionsTable extends Migration
{
    private const TABLE = 'word_definitions';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->boolean('pending')->default(false)->after('word_id');
        });
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('pending');
        });
    }
}
