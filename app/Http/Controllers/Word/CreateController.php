<?php

namespace App\Http\Controllers\Word;

use Inertia\Inertia;
use App\Rules\WordExists;
use Illuminate\Http\Request;
use App\Services\WordService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\DictionaryController;

class CreateController extends DictionaryController
{
    public function index()
    {
        return Inertia::render('NewWord', [
            'randomWord' => $this->randomWord(),
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'isLoggedIn' => Auth::check(),
        ]);
    }

    public function store(Request $request)
    {
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

        $valid = $this->wordService->validateNewWordSubmission($payload);

        if (!$valid) {
            return redirect()->back()
                ->withErrors(['newWord' => 'There was an error creating your comment'])
                ->withInput();
        }

        $userIsTrusted = Auth::user() && Auth::user()->isTrusted;

        $newWord = $this->wordService->createWord($payload, !$userIsTrusted);

        if ($userIsTrusted) {
            return redirect()->route('word.comments', $newWord->slug);
        }

        return Inertia::render('NewWord', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'isLoggedIn' => Auth::check(),
            'success' => true,
        ]);
    }
}
