<?php

use App\Models\Role;
use App\Models\Word;
use Inertia\Inertia;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Models\WordRecording;
use App\Services\WordService;
use Illuminate\Support\Facades\DB;
use App\Http\Middleware\UserIsAdmin;
use App\Models\WordRelationType;
use App\Services\AdminService;
use App\Services\CommentService;
use App\Services\RevisionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

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

    $randomWord = DB::table('words')->inRandomOrder()->first()->slug;

    return Inertia::render('Home', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'isLoggedIn' => Auth::check(),
        'phpVersion' => PHP_VERSION,
        'randomWord' => $randomWord,
        'featuredWord' => app(WordService::class)->getFeaturedWord(),
    ]);
})->name('home');

Route::get('/words', function () {
    $total = app(WordService::class)->findBy()->count();
    $pageTotal = request('perPage') ?? 10;
    $pagination = [
        'page' => request('page') ?? 1,
        'perPage' => request('perPage') ?? 20,
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
})->name('words');

Route::get('/search', function () {
    $searchTerm = '';

    if (request('searchTerm')) {
        $searchTerm = request('searchTerm');
    }

    $total = app(WordService::class)->findBy($searchTerm)->count();
    $pageTotal = request('perPage') ?? 10;

    $pagination = [
        'page' => request('page') ?? 1,
        'perPage' => request('perPage') ?? 20,
        'total' => $total,
        'pages' => ceil($total / $pageTotal),
    ];

    $exactMatch = app(WordService::class)->findExactWordBySearch($searchTerm, true);

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'isLoggedIn' => Auth::check(),
        'exactMatch' => $exactMatch,
        'words' => app(WordService::class)->findAllWordsWithPagination($searchTerm, $pagination),
        'pagination' => $pagination,
        'searchString' => $searchTerm,
        'randomWord' => DB::table('words')->inRandomOrder()->first()->slug,
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
        'perPage' => request('perPage') ?? 20,
        'total' => $total,
        'pages' => ceil($total / $pageTotal),
    ];

    $words = app(WordService::class)->findAllWordsWithPagination($searchTerm, $pagination);
    $exactMatch = app(WordService::class)->findExactWordBySearch($searchTerm, true);

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'isLoggedIn' => Auth::check(),
        'words' => $words,
        'exactMatch' => $exactMatch,
        'pagination' => $pagination,
        'searchString' => $searchTerm,
        'randomWord' => DB::table('words')->inRandomOrder()->first()->slug,
    ]);
})->name('search');

Route::get('/word/{word}/recordings', function (string $word) {
    $foundWord = app(WordService::class)->findByWord($word);
    if (!$foundWord) {
        return redirect()->back();
    }

    $fullWord = Word::where('uuid', $foundWord['id'])->first();

    return Inertia::render('WordRecordings', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'isLoggedIn' => Auth::check(),
        'word' => $foundWord,
        'randomWord' => DB::table('words')->inRandomOrder()->first()->slug,
        'locations' => app(WordService::class)->getAllLocations(),
        'userSelectedLocations' => app(WordService::class)->getUserLocationsForWordUuids($fullWord),
        'success' => false,
    ]);
})->where('word', '.*')->name('word.recordings');

Route::post('/word/{slug}/recordings', function (string $slug) {
    if (!Auth::check()) {
        return redirect()->back();
    }

    $path = "/public/$slug";

    if (!Storage::exists($path)) {
        Storage::disk('local')->makeDirectory($path);
    }

    $file = Storage::disk('local')->put(
        $path,
        request('userRecording'),
    );

    $foundWord = app(WordService::class)->findByWord($slug);

    $filePath = sprintf('storage/%s/%s', $slug, basename($file));

    $fullWord = Word::where('slug', $slug)->first();
    app(WordService::class)->saveRecording($fullWord, $filePath);

    return Inertia::render('WordRecordings', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'isLoggedIn' => Auth::check(),
        'word' => $foundWord,
        'randomWord' => DB::table('words')->inRandomOrder()->first()->slug,
        'locations' => app(WordService::class)->getAllLocations(),
        'userSelectedLocations' => app(WordService::class)->getUserLocationsForWordUuids($fullWord),
        'success' => true,
    ]);
})->where('word', '.*')->name('word.recordings.create');

Route::get('/word/{word}/locations', function (string $word) {
    $foundWord = app(WordService::class)->findByWord($word);
    if (!$foundWord) {
        return redirect()->back();
    }

    $fullWord = Word::where('uuid', $foundWord['id'])->first();

    return Inertia::render('WordLocations', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'isLoggedIn' => Auth::check(),
        'word' => $foundWord,
        'randomWord' => DB::table('words')->inRandomOrder()->first()->slug,
        'locations' => app(WordService::class)->getAllLocations(),
        'userSelectedLocations' => app(WordService::class)->getUserLocationsForWordUuids($fullWord),
        'locationData' => app(WordService::class)->getPercentageDistributionWordLocations($fullWord),
    ]);
})->where('word', '.*')->name('word.locations');

Route::post('/word/{slug}/locations', function (string $slug) {
    if (!Auth::check()) {
        return redirect()->back();
    }

    $foundWord = Word::where('slug', $slug)->first();
    $fullWord = app(WordService::class)->findByWord($foundWord->slug);

    $locations = request('locations');

    app(WordService::class)->addLocationsToWordLinks($foundWord, $locations);

    return Inertia::render('WordLocations', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'isLoggedIn' => Auth::check(),
        'word' => $fullWord,
        'randomWord' => DB::table('words')->inRandomOrder()->first()->slug,
        'locations' => app(WordService::class)->getAllLocations(),
        'userSelectedLocations' => app(WordService::class)->getUserLocationsForWordUuids($foundWord),
    ]);
})->where('word', '.*')->name('word.locations.new');

Route::get('/words/{letter}/', function (string $letter) {
    $total = app(WordService::class)->findBy('', [], $letter)->count();
    $pageTotal = request('perPage') ?? 10;

    $page = request('page') ?? 1;

    $pagination = [
        'page' => (int) $page,
        'perPage' => request('perPage') ?? 20,
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
        'randomWord' => DB::table('words')->inRandomOrder()->first()->slug,
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

Route::get('/word/{word}', function (string $word) {
    $foundWord = app(WordService::class)->findByWord($word);
    if (!$foundWord) {
        return redirect()->back();
    }
    $fullWord = Word::where('uuid', $foundWord['id'])->first();

    return redirect()->route('word.comments', $fullWord->slug);
});

Route::get('/word/id/{uuid}', function (string $wordUuid) {
    $word = Word::where('uuid', $wordUuid)->first();
    if (!$word) {
        return redirect()->back();
    }
    return redirect()->route('word.comments', $word->slug);
});

Route::get('/word/{word}/comments', function (string $word) {
    $foundWord = app(WordService::class)->findByWord($word);
    if (!$foundWord) {
        return redirect()->back();
    }

    $fullWord = Word::where('uuid', $foundWord['id'])->first();

    return Inertia::render('WordComments', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'isLoggedIn' => Auth::check(),
        'word' => $foundWord,
        'randomWord' => DB::table('words')->inRandomOrder()->first()->slug,
        'locations' => app(WordService::class)->getAllLocations(),
        'userSelectedLocations' => app(WordService::class)->getUserLocationsForWordUuids($fullWord),
    ]);
})->where('word', '.*')->name('word.comments');

Route::post('/word/{slug}/comments', function (string $slug) {
    if (!Auth::check()) {
        return redirect()->back();
    }

    // $foundWord
    $foundWord = Word::where('slug', $slug)->first();

    if (request('text')) {
        $comment = null;

        if (request('comment_id')) {
            $comment = Comment::where('uuid', request('comment_id'))->first();
        }

        app(WordService::class)->createComment(request('text'), $foundWord, $comment);
    }
    $fullWord = app(WordService::class)->findByWord($foundWord->slug);

    return Inertia::render('WordComments', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'isLoggedIn' => Auth::check(),
        'word' => $fullWord,
        'randomWord' => DB::table('words')->inRandomOrder()->first()->slug,
        'locations' => [],
        'userSelectedLocations' => []
    ]);
})->where('word', '.*')->name('word.comments.new');

Route::patch('/word/{word}/comments', function (string $word) {
    if (!Auth::check()) {
        return redirect()->back();
    }

    $commentId = request('childCommentId');

    if (!$commentId) {
        return redirect()->back();
    }

    $foundWord = Word::where('word', $word)->first();
    $comment = Comment::where('uuid', $commentId)->first();

    if ($comment->author->id !== Auth::id()) {
        return redirect()->back();
    }

    $text = request('text');

    app(CommentService::class)->update($comment, $text);

    $fullWord = app(WordService::class)->findByWord($foundWord->slug);

    return Inertia::render('WordComments', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'isLoggedIn' => Auth::check(),
        'word' => $fullWord,
        'randomWord' => DB::table('words')->inRandomOrder()->first()->slug,
        'locations' => [],
        'userSelectedLocations' => []
    ]);
})->where('word', '.*')->name('word.comments.update');

Route::delete('/word/{word}/comments/{commentId}', function (string $word, string $commentId) {
    if (!Auth::check()) {
        return redirect()->back();
    }

    if (!$commentId) {
        return redirect()->back();
    }

    $foundWord = Word::where('word', $word)->first();
    $comment = Comment::where('uuid', $commentId)->first();

    if ($comment->author->id !== Auth::id()) {
        return redirect()->back();
    }

    app(CommentService::class)->delete($comment);

    return redirect()->back();
})->where('word', '.*')->name('word.comments.delete');

Route::get('/create', function (Request $request) {
    return Inertia::render('NewWord', [
        'randomWord' => DB::table('words')->inRandomOrder()->first()->slug,
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'isLoggedIn' => Auth::check(),
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

        return redirect()->to('/');
    })->name('dashboard');
});

Route::get('/dashboard/word-admin', function () {
    return Inertia::render('AdminDashboard', [
        'wordRelationTypes' => WordRelationType::all()->toArray(),
        'isLoggedIn' => Auth::check(),
    ]);
})->name('word-admin');

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

    return Inertia::render('AdminDashboard', [
        'words' => app(WordService::class)->findAllPendingWords(),
        'isLoggedIn' => Auth::check(),
    ]);
})->name('approve');

Route::get('/dashboard/new-definitions', function () {
    return Inertia::render('AdminDashboard', [
        'definitions' => app(WordService::class)->findAllPendingDefinitions(),
        'isLoggedIn' => Auth::check(),
    ]);
})->name('new-definitions');


Route::get('/dashboard/wotd', function () {
    return Inertia::render('AdminDashboard', [
        'wordQueue' => app(AdminService::class)->getQueuedWords(),
        'isLoggedIn' => Auth::check(),
    ]);
})->name('wotd');

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

Route::get('/dashboard/revisions', function () {
    return Inertia::render('AdminDashboard', [
        'revisions' => app(RevisionService::class)->findAll(),
        'isLoggedIn' => Auth::check(),
    ]);
})->name('revisions');


Route::get('/help-us', function () {
    $wordsWithoutDefinitions = app(WordService::class)->findWithoutDefinitions();
    $definitionsWithoutExampleSentences = app(WordService::class)->findWithoutExampleSentences();

    return Inertia::render('HelpUs', [
        'randomWord' => DB::table('words')->inRandomOrder()->first()->slug,
        'isLoggedIn' => Auth::check(),
        'missingDefinitions' => $wordsWithoutDefinitions,
        'missingExampleSentences' => $definitionsWithoutExampleSentences,
    ]);
})->name('help-us');

Route::get('/help-us', function () {
    $wordsWithoutDefinitions = app(WordService::class)->findWithoutDefinitions();
    $definitionsWithoutExampleSentences = app(WordService::class)->findWithoutExampleSentences();

    return Inertia::render('HelpUs', [
        'randomWord' => DB::table('words')->inRandomOrder()->first()->slug,
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

Route::get('/faq', function () {
    return Inertia::render('FAQ', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'randomWord' => DB::table('words')->inRandomOrder()->first()->slug,
        'isLoggedIn' => Auth::check(),
    ]);
})->name('faq');
