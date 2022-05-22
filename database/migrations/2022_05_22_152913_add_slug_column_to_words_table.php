<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSlugColumnToWordsTable extends Migration
{
    private const TABLE = 'words';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->string('slug')->unique()->nullable();
        });
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
}
