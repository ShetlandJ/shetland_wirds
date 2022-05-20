<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRecordingsTableAddType extends Migration
{
    private const TABLE = 'word_recordings';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->string('type')->default('word')->nullable()->after('user_id');
        });
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
