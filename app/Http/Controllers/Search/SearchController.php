<?php

namespace App\Http\Controllers\Search;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Services\WordService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DictionaryController;

class SearchController extends DictionaryController
{
    public function index()
    {
        $searchTerm = '';

        if (request('searchTerm')) {
            $searchTerm = request('searchTerm');
        }

        $exactMatch = $this->wordService->findExactWordBySearch($searchTerm, true);
        $words = $this->wordService->findAllWordsWithPagination($searchTerm, $this->pagination());

        $this->logService->createSearchLog(request(), $searchTerm);

        if ($exactMatch && count($words) === 0) {
            return redirect()->route('word.comments', ['word' => $exactMatch['slug']]);
        }

        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'isLoggedIn' => Auth::check(),
            'exactMatch' => $exactMatch,
            'words' => $words,
            'pagination' => count($words) < 20 ? null : $this->pagination(),
            'searchString' => $searchTerm,
            'randomWord' => $this->randomWord(),
        ]);
    }

    public function search()
    {
        $searchTerm = '';

        if (request('searchTerm')) {
            $searchTerm = request('searchTerm');
        }
        if (request('wordToLike')) {
            $this->wordService->handleLike(request('wordToLike'));
        }

        $words = $this->wordService->findAllWordsWithPagination($searchTerm, $this->pagination());
        $exactMatch = $this->wordService->findExactWordBySearch($searchTerm, true);
        $this->logService->createSearchLog(request(), $searchTerm);

        if ($exactMatch && count($words) === 0) {
            return redirect()->route('word.comments', ['slug' => $exactMatch['slug']]);
        }

        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'isLoggedIn' => Auth::check(),
            'words' => $words,
            'exactMatch' => $exactMatch,
            'pagination' => count($words) < 20 ? null : $this->pagination(),
            'searchString' => $searchTerm,
            'randomWord' => $this->randomWord(),
        ]);
    }
}
