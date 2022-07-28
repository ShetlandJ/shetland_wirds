<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\DictionaryController;
use Inertia\Inertia;
use App\Services\WordService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class HomeController extends DictionaryController
{
    public function index()
    {
        return Inertia::render('Home', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'isLoggedIn' => Auth::check(),
            'randomWord' => $this->randomWord(),
            'featuredWord' => $this->wordService->getFeaturedWord(),
        ]);
    }
}
