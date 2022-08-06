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
use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\Word\WordController;
use App\Http\Controllers\Words\WordsController;
use App\Http\Controllers\Search\SearchController;
use App\Http\Controllers\Word\CommentController;
use App\Http\Controllers\Word\CreateController;

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

Route::get('/', [HomeController::class, 'index'])
    ->name('home');

Route::get('/words', [WordsController::class, 'index'])
    ->name('words');

Route::get('/words/{letter}', [WordsController::class, 'letter'])
    ->where('letter', '.*')
    ->name('letter');

Route::get('/search', [SearchController::class, 'index'])
    ->name('search');

Route::post('/search', [SearchController::class, 'search'])
    ->name('search');

Route::group(['prefix' => 'word'], function () {
    Route::get('/id/{uuid}', [WordController::class, 'show'])
        ->name('word.show');

    Route::get('/{slug}', [WordController::class, 'base'])
        ->name('word.base');

    Route::post('/{slug}/like', [WordController::class, 'like'])
        ->name('word.like');

    Route::get('/{slug}/comments', [CommentController::class, 'index'])
        ->name('word.comments');
    Route::post('/{slug}/comments', [CommentController::class, 'store'])
        ->name('word.comments.new');
    Route::patch('/{slug}/comments', [CommentController::class, 'update'])
        ->name('word.comments.update');
    Route::delete('/{slug}/comments/{commentId}', [CommentController::class, 'delete'])
        ->name('word.comments.delete');

    Route::get('/{slug}/recordings', [WordController::class, 'recordingsIndex'])
        ->name('word.recordings');
    Route::post('/{slug}/recordings', [WordController::class, 'recordingsStore'])
        ->name('word.recordings.create');

    Route::get('/{slug}/locations', [WordController::class, 'locationsIndex'])
        ->name('word.locations');
    Route::post('/{slug}/locations', [WordController::class, 'locationsStore'])
        ->name('word.locations.new');
});

Route::get('/tutorial', function (Request $request) {
    return Inertia::render('Tutorial', [
        'isLoggedIn' => Auth::check(),
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'randomWord' => DB::table('words')->inRandomOrder()->first()->slug,
    ]);
});

Route::group(['prefix' => 'create'], function () {
    Route::get('/', [CreateController::class, 'index'])
        ->name('create');

    Route::post('/', [CreateController::class, 'store'])
        ->name('create');
});

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
    if (request('id') && request('roleUuid')) {
        app(UserService::class)->updateRole(request('id'), request('roleUuid'));
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
    $foundWord = app(WordService::class)->findBySlug($word);

    if (!$foundWord) {
        return redirect()->back();
    }

    $fullWord = Word::where('uuid', $foundWord['id'])->first();
    app(WordService::class)->reportWord($fullWord, request()->all());

    return redirect()->back();
})->where('word', '.*')->name('word.report');

// Latest
Route::get('/dashboard/latest/words', function () {
    return Inertia::render('AdminDashboard', [
        'latestWords' => app(WordService::class)->getLatestWords(),
        'isLoggedIn' => Auth::check(),
        'reports' => app(WordService::class)->getReports(),
    ]);
})->name('latest.words');

Route::get('/dashboard/latest/comments', function () {
    return Inertia::render('AdminDashboard', [
        'latestComments' => app(WordService::class)->getLatestComments(),
        'isLoggedIn' => Auth::check(),
        'reports' => app(WordService::class)->getReports(),
    ]);
})->name('latest.comments');

Route::get('/dashboard/latest/recordings', function () {
    return Inertia::render('AdminDashboard', [
        'latestRecordings' => app(WordService::class)->getLatestRecordings(),
        'isLoggedIn' => Auth::check(),
        'reports' => app(WordService::class)->getReports(),
    ]);
})->name('latest.recordings');