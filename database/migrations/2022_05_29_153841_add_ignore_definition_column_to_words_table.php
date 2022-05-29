<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIgnoreDefinitionColumnToWordsTable extends Migration
{
    private const TABLE = 'words';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->boolean('ignore_definition')->default(false)->after('rejected');
        });
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('ignore_definition');
        });
    }
}
