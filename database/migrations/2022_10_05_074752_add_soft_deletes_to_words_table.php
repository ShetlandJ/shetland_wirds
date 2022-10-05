<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeletesToWordsTable extends Migration
{
    private const TABLE = 'words';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
