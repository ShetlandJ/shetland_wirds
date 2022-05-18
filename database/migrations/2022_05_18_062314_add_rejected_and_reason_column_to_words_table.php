<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRejectedAndReasonColumnToWordsTable extends Migration
{
    private const TABLE = 'words';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->boolean('rejected')->default(false)->after('creator_id');
            $table->longText('reason')->nullable()->after('creator_id');
        });
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('rejected');
            $table->dropColumn('reason');
        });
    }
}
