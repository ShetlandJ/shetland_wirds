<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToAFewTables extends Migration
{
    public function up()
    {
        Schema::table('word_definitions', function (Blueprint $table) {
            $table->index('word_id');
        });

        Schema::table('words', function (Blueprint $table) {
            $table->index('uuid');
            $table->index('slug');
        });
    }

    public function down()
    {
        Schema::table('word_definitions', function (Blueprint $table) {
            $table->dropIndex('word_id');
        });

        Schema::table('words', function (Blueprint $table) {
            $table->dropIndex('uuid');
            $table->dropIndex('slug');
        });
    }
}
