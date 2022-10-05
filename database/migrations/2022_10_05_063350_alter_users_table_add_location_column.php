<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersTableAddLocationColumn extends Migration
{
    private const TABLE = 'users';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->string('location_id')->after('can_contact')->nullable();
        });
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('location_id');
        });
    }
}
