<?php

use App\Models\Word;
use App\Models\WordOfTheDay;
use Illuminate\Http\Request;
use App\Services\AdminService;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware([config('jetstream.auth_session')])->group(function () {
    Route::get('/wotd', function (Request $request) {
        $req = $request->input();

        if (!isset($req['search'])) {
            return ["data" => null];
        }
        $searchString = $req['search'];

        return app(AdminService::class)->returnSearchedWordsList($searchString);
    });

    Route::post('/wotd/replace', function (Request $request) {
        $req = $request->input();

        if (!isset($req['replacement_word_id']) || !isset($req['id'])) {
            return ["data" => null];
        }

        $newWord = Word::where('uuid', $req['replacement_word_id'])->first();

        $wordOfTheDay = WordOfTheDay::where('uuid', $req['id'])->first();

        $wordOfTheDay->word_id = $newWord->id;
        $wordOfTheDay->save();

        return ["data" => null];
    });
});
