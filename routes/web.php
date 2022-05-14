<?php

use App\Models\Word;
use App\Services\WordService;
use Inertia\Inertia;
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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/search', function () {
    $searchTerm = request('searchTerm');
    $words = [];

    if ($searchTerm && $searchTerm !== '') {
        $words = Word::where('word', 'like', "%{$searchTerm}%")->get();
    }

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'words' => $words,
    ]);
})->name('search');

Route::post('/search', function () {
    $searchTerm = request('searchTerm');
    if (request('wordToLike')) {
        app(WordService::class)->handleLike(request('wordToLike'));
    }

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'words' => app(WordService::class)->findAllWords($searchTerm),
    ]);
})->name('search');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});
