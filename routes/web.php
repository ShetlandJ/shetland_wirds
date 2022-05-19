<?php

use App\Models\Role;
use App\Models\Word;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Services\WordService;
use App\Http\Middleware\UserIsAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;

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

Route::get('/word/{word}', function (string $word) {
    return Inertia::render('Word', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'isLoggedIn' => Auth::check(),
        'word' => app(WordService::class)->findByWord($word),
    ]);
})->where('word', '.*')->name('word');

Route::post('/word', function (Request $request) {
    // get the post payload
    $payload = $request->all();

    $valid = app(WordService::class)->validateNewWordSubmission($payload);

    if ($valid) {
        app(WordService::class)->createWord($payload);
    }

    return redirect()->back();
})->name('word.new');

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
