<?php

namespace App\Http\Controllers\Words;

use App\Http\Controllers\DictionaryController;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Services\WordService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class WordsController extends DictionaryController
{
    public function index()
    {
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'isLoggedIn' => Auth::check(),
            'words' => $this->wordService->findAllWordsWithPagination('', $this->pagination()),
            'randomWord' => $this->randomWord(),
            'pagination' => $this->pagination(),
        ]);
    }

    public function letter(Request $request, string $letter)
    {
        $words = $this->wordService->findAllWordsWithPagination($letter, $this->pagination('', $letter), $letter);

        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'isLoggedIn' => Auth::check(),
            'letter' => $letter,
            'words' => $words,
            'pagination' => count($words) < 20 ? null : $this->pagination('', $letter),
            'randomWord' => $this->randomWord(),
        ]);
    }
}
