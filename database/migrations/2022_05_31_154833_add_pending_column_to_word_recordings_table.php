<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPendingColumnToWordRecordingsTable extends Migration
{
    private const TABLE = 'word_recordings';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->boolean('pending')->default(false)->after('filename');
        });
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('pending');
        });
    }
}
