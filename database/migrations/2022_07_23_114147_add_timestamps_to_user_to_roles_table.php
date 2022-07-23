<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimestampsToUserToRolesTable extends Migration
{
    private const TABLE = 'users_to_roles';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }
}
