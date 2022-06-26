<?php

use App\Models\Role;
use App\Models\Word;
use Inertia\Inertia;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Services\WordService;
use App\Http\Middleware\UserIsAdmin;
use App\Models\WordRecording;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $total = app(WordService::class)->findBy()->count();
    $pageTotal = request('perPage') ?? 10;
    $pagination = [
        'page' => request('page') ?? 1,
        'perPage' => request('perPage') ?? 10,
        'total' => $total,
        'pages' => ceil($total / $pageTotal),
    ];

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'isLoggedIn' => Auth::check(),
        'phpVersion' => PHP_VERSION,
        'words' => app(WordService::class)->findAllWordsWithPagination('', $pagination),
        'randomWord' => DB::table('words')->inRandomOrder()->first()->slug,
        'pagination' => $pagination,
    ]);
})->name('home');

Route::get('/search', function () {
    $searchTerm = '';

    if (request('searchTerm')) {
        $searchTerm = request('searchTerm');
    }

    $total = app(WordService::class)->findBy($searchTerm)->count();
    $pageTotal = request('perPage') ?? 10;

    $pagination = [
        'page' => request('page') ?? 1,
        'perPage' => request('perPage') ?? 10,
        'total' => $total,
        'pages' => ceil($total / $pageTotal),
    ];

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'isLoggedIn' => Auth::check(),
        'words' => app(WordService::class)->findAllWordsWithPagination($searchTerm, $pagination),
        'pagination' => $pagination,
        'searchString' => $searchTerm,
    ]);
})->name('search');

Route::post('/search', function () {
    $searchTerm = '';

    if (request('searchTerm')) {
        $searchTerm = request('searchTerm');
    }
    if (request('wordToLike')) {
        app(WordService::class)->handleLike(request('wordToLike'));
    }

    $total = app(WordService::class)->findBy($searchTerm)->count();
    $pageTotal = request('perPage') ?? 10;
    $pagination = [
        'page' => request('page') ?? 1,
        'perPage' => request('perPage') ?? 10,
        'total' => $total,
        'pages' => ceil($total / $pageTotal),
    ];

    $words = app(WordService::class)->findAllWordsWithPagination($searchTerm, $pagination);

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'isLoggedIn' => Auth::check(),
        'words' => $words,
        'pagination' => $pagination,
        'searchString' => $searchTerm,
    ]);
})->name('search');

Route::post('/word/{word}/newRecording', function (string $word) {
    if (!Auth::check()) {
        return redirect()->back();
    }

    $path = "/public/$word";

    if (!Storage::exists($path)) {
        Storage::disk('local')->makeDirectory($path);
    }

    $file = Storage::disk('local')->put(
        $path,
        request('userRecording'),
    );

    $foundWord = Word::where('word', $word)->first();

    $filePath = sprintf('storage/%s/%s', $word, basename($file));

    app(WordService::class)->saveRecording($foundWord, $filePath);
    return redirect()->back();
})->where('word', '.*')->name('uploadFile');

Route::get('/word/{word}/', function (string $word) {
    $foundWord = app(WordService::class)->findByWord($word);
    if (!$foundWord) {
        return redirect()->back();
    }

    $fullWord = Word::where('uuid', $foundWord['id'])->first();
    $recording = WordRecording::where('word_id', $fullWord['id'])->where('created_at', '>', now()->subSeconds(5))->first();

    return Inertia::render('Word', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'isLoggedIn' => Auth::check(),
        'word' => $foundWord,
        'recordingJustSubmitted' => (bool) $recording,
        'tab' => request('tab'),
    ]);
})->where('word', '.*')->name('word');

Route::get('/words/{letter}/', function (string $letter) {
    $total = app(WordService::class)->findBy('', [], $letter)->count();
    $pageTotal = request('perPage') ?? 10;

    $page = request('page') ?? 1;

    $pagination = [
        'page' => (int) $page,
        'perPage' => request('perPage') ?? 10,
        'total' => $total,
        'pages' => ceil($total / $pageTotal),
    ];
    $words = app(WordService::class)->findAllWordsWithPagination($letter, $pagination, $letter);

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'isLoggedIn' => Auth::check(),
        'letter' => $letter,
        'words' => $words,
        'pagination' => $pagination,
    ]);
})->where('letter', '.*')->name('letter');

Route::post('/word/{word}/like', function (string $word) {
    if (!Auth::check()) {
        return redirect()->back();
    }

    if (request('wordToLike')) {
        app(WordService::class)->handleLike(request('wordToLike'));
    }

    return redirect()->back();
})->where('word', '.*')->name('wordLike');

Route::post('/word/{word}', function (string $word) {
    if (!Auth::check()) {
        return redirect()->back();
    }

    if (request('text')) {
        $foundWord = Word::where('word', $word)->first();

        $comment = null;

        if (request('comment_id')) {
            $comment = Comment::where('uuid', request('comment_id'))->first();
        }

        app(WordService::class)->createComment(request('text'), $foundWord, $comment);
    }

    return redirect()->back();
})->where('word', '.*')->name('newComment');

Route::get('/create', function () {
    return Inertia::render('NewWord', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'isLoggedIn' => Auth::check(),
        'randomWord' => DB::table('words')->inRandomOrder()->first()->slug,
    ]);
})->name('create');

Route::post('/create', function (Request $request) {
    // get the post payload
    $payload = $request->all();

    $valid = app(WordService::class)->validateNewWordSubmission($payload);

    if ($valid) {
        app(WordService::class)->createWord($payload);
    }

    return Inertia::render('NewWord', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'isLoggedIn' => Auth::check(),
        'success' => true,
    ]);
})->name('create');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        if (Auth::id() && Auth::user()->roles->where('name', Role::ROLE_ADMIN)->first()) {
            return Inertia::render('AdminDashboard', [
                'isLoggedIn' => Auth::check(),
                'metrics' => app(WordService::class)->getAdminDashboardMetrics(),
            ]);
        }

        return Inertia::render('Dashboard');
    })->name('dashboard');
});

Route::get('/dashboard/my-words', function () {
    return Inertia::render('AdminDashboard', [
        'words' => app(WordService::class)->findAllUserWords(),
        'isLoggedIn' => Auth::check(),
    ]);
})->name('my-words');

Route::get('/dashboard/approve', function () {
    return Inertia::render('AdminDashboard', [
        'words' => app(WordService::class)->findAllPendingWords(),
        'isLoggedIn' => Auth::check(),
    ]);
})->name('approval');

Route::post('/dashboard/approve', function () {
    if (request('wordToApprove')) {
        app(WordService::class)->approveWord(request('wordToApprove'));
    }

    return redirect()->back();
})->name('approve');


Route::get('/dashboard/new-definitions', function () {
    return Inertia::render('AdminDashboard', [
        'definitions' => app(WordService::class)->findAllPendingDefinitions(),
        'isLoggedIn' => Auth::check(),
    ]);
})->name('new-definitions');


Route::get('/dashboard/rejected', function () {
    return Inertia::render('AdminDashboard', [
        'words' => app(WordService::class)->findAllRejectedWords(),
        'isLoggedIn' => Auth::check(),
    ]);
})->name('rejected');

Route::post('/dashboard/reject', function () {
    if (request('wordToReject')) {
        app(WordService::class)->rejectWord(request('wordToReject'), request('rejectReason'));
    }

    return redirect()->back();
})->name('reject');

Route::get('/dashboard/recordings', function () {
    return Inertia::render('AdminDashboard', [
        'recordings' => app(WordService::class)->getPendingRecordings(),
        'isLoggedIn' => Auth::check(),
    ]);
})->name('recordings');

Route::post('/dashboard/recordings/{recordingUuid}', function (string $recordingUuid) {
    if (request('recordingUuid')) {
        app(WordService::class)->approveRecording(request('recordingUuid'));
    }

    return redirect()->back();
})->name('recording.approve');

Route::delete('/dashboard/recordings/{recordingUuid}', function (string $recordingUuid) {
    if (request('recordingUuid')) {
        app(WordService::class)->deleteRecording(request('recordingUuid'));
    }

    return redirect()->back();
})->name('recording.delete');

Route::post('/dashboard/definitions/{definitionUuid}', function (string $definitionUuid) {
    if (request('definitionUuid')) {
        app(WordService::class)->approveDefinition(request('definitionUuid'));
    }

    return redirect()->back();
})->name('definition.approve');

Route::delete('/dashboard/definitions/{definitionUuid}', function (string $definitionUuid) {
    if (request('definitionUuid')) {
        app(WordService::class)->deleteDefinition(request('definitionUuid'));
    }

    return redirect()->back();
})->name('definition.delete');

Route::get('/help-us', function () {
    $wordsWithoutDefinitions = app(WordService::class)->findWithoutDefinitions();
    $definitionsWithoutExampleSentences = app(WordService::class)->findWithoutExampleSentences();

    return Inertia::render('HelpUs', [
        'isLoggedIn' => Auth::check(),
        'missingDefinitions' => $wordsWithoutDefinitions,
        'missingExampleSentences' => $definitionsWithoutExampleSentences,
    ]);
})->name('help-us');

Route::get('/help-us', function () {
    $wordsWithoutDefinitions = app(WordService::class)->findWithoutDefinitions();
    $definitionsWithoutExampleSentences = app(WordService::class)->findWithoutExampleSentences();

    return Inertia::render('HelpUs', [
        'isLoggedIn' => Auth::check(),
        'missingDefinitions' => $wordsWithoutDefinitions,
        'missingExampleSentences' => $definitionsWithoutExampleSentences,
    ]);
})->name('help-us');

Route::post('/help-us', function () {
    $wordUuid = request('wordId');
    $newDefinition = request('definition');
    $exampleSentence = request('example_sentence');

    if ($wordUuid && $newDefinition) {
        app(WordService::class)->addDefinition($wordUuid, $newDefinition, $exampleSentence);
    }

    return redirect()->back();
})->name('help-us-new');

Route::post('/help-us-ignore', function () {
    $wordId = request('wordId');

    if ($wordId) {
        app(WordService::class)->ignoreWord($wordId);
    }

    return redirect()->back();
})->name('help-us-ignore');

Route::get('/about', function () {
    return Inertia::render('About', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'randomWord' => DB::table('words')->inRandomOrder()->first()->slug,
        'isLoggedIn' => Auth::check(),
    ]);
})->name('about');
