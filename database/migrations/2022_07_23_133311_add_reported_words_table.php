<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReportedWordsTable extends Migration
{
    private const TABLE = 'reported_words';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('word_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('reason_type')->nullable();
            $table->string('reason')->nullable();
            $table->string('email')->nullable();
            $table->boolean('resolved')->default(false);
            $table->unsignedBigInteger('resolved_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists(self::TABLE);
    }
}
