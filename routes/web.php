<?php

use App\Models\Role;
use App\Models\User;
use App\Models\Word;
use Inertia\Inertia;
use App\Models\Comment;
use App\Rules\WordExists;
use App\Models\WordReport;
use App\Services\LogService;
use Illuminate\Http\Request;
use App\Models\WordRecording;
use App\Services\UserService;
use App\Services\WordService;
use App\Services\AdminService;
use App\Models\WordRelationType;
use App\Services\CommentService;
use App\Services\RevisionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Middleware\UserIsAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

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
    $pageTotal = request('perPage') ?? 20;
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
    $pageTotal = request('perPage') ?? 20;

    $pagination = [
        'page' => request('page') ?? 1,
        'perPage' => request('perPage') ?? 20,
        'total' => $total,
        'pages' => ceil($total / $pageTotal),
    ];

    $exactMatch = app(WordService::class)->findExactWordBySearch($searchTerm, true);
    $words = app(WordService::class)->findAllWordsWithPagination($searchTerm, $pagination);

    app(LogService::class)->createSearchLog(request(), $searchTerm);

    if ($exactMatch && count($words) === 0) {
        return redirect()->route('word.comments', ['word' => $exactMatch['slug']]);
    }

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'isLoggedIn' => Auth::check(),
        'exactMatch' => $exactMatch,
        'words' => $words,
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
    $pageTotal = request('perPage') ?? 20;
    $pagination = [
        'page' => request('page') ?? 1,
        'perPage' => request('perPage') ?? 20,
        'total' => $total,
        'pages' => ceil($total / $pageTotal),
    ];

    $words = app(WordService::class)->findAllWordsWithPagination($searchTerm, $pagination);
    $exactMatch = app(WordService::class)->findExactWordBySearch($searchTerm, true);
    app(LogService::class)->createSearchLog(request(), $searchTerm);

    if ($exactMatch && count($words) === 0) {
        return redirect()->route('word.comments', ['word' => $exactMatch['slug']]);
    }

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
    app(LogService::class)->createUserLog(request(), $fullWord->id);

    return Inertia::render('WordRecordings', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'isLoggedIn' => Auth::check(),
        'word' => $foundWord,
        'randomWord' => DB::table('words')->inRandomOrder()->first()->slug,
        'locations' => app(WordService::class)->getAllLocations(),
        'userHasPendingRecordings' => app(WordService::class)->userHasPendingRecording($fullWord),
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

    $filePath = sprintf('%s/%s', $slug, basename($file));
    if (App::environment('production')) {
        Storage::disk('s3')->put($slug . '/', request('userRecording'));
    } else {
        $filePath = sprintf('%s/%s', $slug, basename($file));
    }

    $fullWord = Word::where('slug', $slug)->first();

    $userIsTrusted = false;
    if (Auth::user() && Auth::user()->isTrusted) {
        $userIsTrusted = true;
    }

    app(WordService::class)->saveRecording($fullWord, $filePath, !$userIsTrusted);

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
    app(LogService::class)->createUserLog(request(), $fullWord->id);

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
    $pageTotal = request('perPage') ?? 20;

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

    app(LogService::class)->createUserLog(request(), $fullWord->id);

    $fullWord = Word::where('uuid', $foundWord['id'])->first();

    return redirect()->route('word.comments', $fullWord->slug);
})->name('word.base');

Route::get('/word/id/{uuid}', function (string $wordUuid) {
    $word = Word::where('uuid', $wordUuid)->first();
    if (!$word) {
        return redirect()->back();
    }

    app(LogService::class)->createUserLog(request(), $word->id);

    return redirect()->route('word.comments', $word->slug);
});

Route::get('/word/{word}/comments', function (string $word) {
    $foundWord = app(WordService::class)->findByWord($word);
    if (!$foundWord) {
        return redirect()->back();
    }

    $fullWord = Word::where('uuid', $foundWord['id'])->first();
    app(LogService::class)->createUserLog(request(), $fullWord->id);

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
    $payload = $request->all();

    // validator check if the word is already in the database
    $validator = Validator::make($payload, [
        'newWord' => ['required', new WordExists()],
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    $valid = app(WordService::class)->validateNewWordSubmission($payload);

    $userIsTrusted = false;

    if (Auth::user() && Auth::user()->isTrusted) {
        $userIsTrusted = true;
    }

    if ($valid) {
        $newWord = app(WordService::class)->createWord($payload, !$userIsTrusted);
    }

    if ($userIsTrusted) {
        return redirect()->route('word.comments', $newWord->slug);
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
                'reports' => app(WordService::class)->getReports(),
            ]);
        }

        return redirect()->to('/');
    })->name('dashboard');
});

Route::get('/dashboard/word-admin', function () {
    return Inertia::render('AdminDashboard', [
        'wordRelationTypes' => WordRelationType::all()->toArray(),
        'isLoggedIn' => Auth::check(),
        'reports' => app(WordService::class)->getReports(),
    ]);
})->name('word-admin');

Route::get('/dashboard/approve', function () {
    return Inertia::render('AdminDashboard', [
        'pendingWords' => app(WordService::class)->findAllPendingWords(),
        'isLoggedIn' => Auth::check(),
        'reports' => app(WordService::class)->getReports(),
    ]);
})->name('approval');

Route::post('/dashboard/approve', function () {
    if (request('wordToApprove')) {
        app(WordService::class)->approveWord(request('wordToApprove'));
    }

    return Inertia::render('AdminDashboard', [
        'pendingWords' => app(WordService::class)->findAllPendingWords(),
        'isLoggedIn' => Auth::check(),
        'reports' => app(WordService::class)->getReports(),
    ]);
})->name('approve');

Route::get('/dashboard/new-definitions', function () {
    return Inertia::render('AdminDashboard', [
        'definitions' => app(WordService::class)->findAllPendingDefinitions(),
        'isLoggedIn' => Auth::check(),
        'reports' => app(WordService::class)->getReports(),
    ]);
})->name('new-definitions');


Route::get('/dashboard/wotd', function () {
    return Inertia::render('AdminDashboard', [
        'wordQueue' => app(AdminService::class)->getQueuedWords(),
        'isLoggedIn' => Auth::check(),
        'reports' => app(WordService::class)->getReports(),
    ]);
})->name('wotd');

Route::get('/dashboard/rejected', function () {
    return Inertia::render('AdminDashboard', [
        'words' => app(WordService::class)->findAllRejectedWords(),
        'isLoggedIn' => Auth::check(),
        'reports' => app(WordService::class)->getReports(),
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
        'pendingRecordings' => app(WordService::class)->getPendingRecordings(),
        'isLoggedIn' => Auth::check(),
        'reports' => app(WordService::class)->getReports(),
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
        'reports' => app(WordService::class)->getReports(),
        'isLoggedIn' => Auth::check(),
    ]);
})->name('revisions');

Route::get('/dashboard/users', function () {
    return Inertia::render('AdminDashboard', [
        'users' => app(UserService::class)->findAll(),
        'roles' => Role::all()->map(function ($role) {
            return [
                'name' => $role->name,
                'id' => $role->uuid,
            ];
        }),
        'isLoggedIn' => Auth::check(),
        'reports' => app(WordService::class)->getReports(),
    ]);
})->name('users');

Route::patch('/dashboard/users', function () {
    if (request('userUuid') && request('roleUuid')) {
        app(UserService::class)->updateRole(request('userUuid'), request('roleUuid'));
    }

    return redirect()->back();
})->name('users.update');

Route::get('/dashboard/reports', function () {
    return Inertia::render('AdminDashboard', [
        'reports' => app(WordService::class)->getReports(),
        'isLoggedIn' => Auth::check(),
    ]);
})->name('reports');

Route::post('/dashboard/reports/{reportId}', function (string $reportId) {
    $report = WordReport::where('uuid', $reportId)->first();

    if (!$report) {
        return redirect()->back();
    }

    app(WordService::class)->resolveReport($report);

    return redirect()->back();
})->name('reports.resolve');

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

Route::post('/report/{word}', function (string $word) {
    return Inertia::render('FAQ', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'randomWord' => DB::table('words')->inRandomOrder()->first()->slug,
        'isLoggedIn' => Auth::check(),
    ]);
})->name('faq');

Route::post('/report/{word}', function (string $word) {
    $foundWord = app(WordService::class)->findByWord($word);

    if (!$foundWord) {
        return redirect()->back();
    }

    $fullWord = Word::where('uuid', $foundWord['id'])->first();
    app(WordService::class)->reportWord($fullWord, request()->all());

    return redirect()->back();
})->where('word', '.*')->name('word.report');