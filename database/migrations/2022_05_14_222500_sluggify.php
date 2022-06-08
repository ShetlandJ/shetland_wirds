<?php

use App\Models\Word;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Sluggify extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Word::all()->each(function ($word) {
            if (!$word->slug) {
                $slugExistsCount = Word::where('slug', Str::slug($word->word))->count();

                $word->slug = $slugExistsCount
                    ? sprintf('%s-%s', Str::slug($word->word), ($slugExistsCount + 1))
                    : Str::slug($word->word);

                $word->save();
            }
        });
    }

    public function down()
    {
        //
    }
}
