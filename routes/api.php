<?php

use App\Models\Word;
use Illuminate\Support\Str;
use App\Models\WordOfTheDay;
use Illuminate\Http\Request;
use App\Services\WordService;
use App\Services\AdminService;
use Illuminate\Support\Facades\Auth;
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

    Route::post('/wotd/new', function (Request $request) {
        $req = $request->input();

        if (!isset($req['word_id']) || !isset($req['schedule_date'])) {
            return ["data" => null];
        }

        $newWord = Word::where('uuid', $req['word_id'])->first();

        $wordOfTheDay = WordOfTheDay::create([
            'uuid' => (string) Str::uuid(),
            'word_id' => $newWord->id,
            'scheduled_for' => $req['schedule_date'],
            'creator_id' => $req['creator_id'] ?? null,
        ]);

        $wordOfTheDay->word_id = $newWord->id;
        $wordOfTheDay->save();

        return ["data" => null];
    });

    Route::get('/words', function (Request $request) {
        $req = $request->input();

        if (!isset($req['search'])) {
            return ["data" => null];
        }
        $searchString = $req['search'];

        return app(AdminService::class)->searchAllWords($searchString);
    });

    Route::get('/word', function (Request $request) {
        $req = $request->input();

        if (!isset($req['uuid'])) {
            return ["data" => null];
        }

        return app(WordService::class)->findByUuid($req['uuid']);
    });

    Route::post('/word', function (Request $request) {
        $payload = $request->input()['payload'];

        app(AdminService::class)->updateWord($payload);

        return ["data" => null];
    });

    Route::get('/locations', function (Request $request) {
        return ["data" => app(WordService::class)->getAllLocations()];
    });
});
