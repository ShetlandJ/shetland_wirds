<?php

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUuidColumnToUsersTable extends Migration
{
    private const TABLE = 'users';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->string('uuid')->unique()->after('id')->nullable();
        });

        User::all()->each(function (User $user) {
            $user->uuid = (string) Str::uuid();
            $user->save();
        });
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
}
