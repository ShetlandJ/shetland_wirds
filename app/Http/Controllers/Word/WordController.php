<?php

namespace App\Http\Controllers\Word;

use App\Models\Word;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Services\WordService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\DictionaryController;

class WordController extends DictionaryController
{
    public function like()
    {
        if (!Auth::check()) {
            return redirect()->back();
        }

        if (request('wordToLike')) {
            $this->wordService->handleLike(request('wordToLike'));
        }

        return redirect()->back();
    }

    public function recordingsIndex(string $slug)
    {
        $foundWord = $this->wordService->findBySlug($slug);
        if (!$foundWord) {
            return redirect()->back();
        }

        $fullWord = Word::where('uuid', $foundWord['id'])->first();
        $this->logService->createUserLog(request(), $fullWord->id);

        return Inertia::render('WordRecordings', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'isLoggedIn' => Auth::check(),
            'word' => $foundWord,
            'randomWord' => $this->randomWord(),
            'locations' => $this->wordService->getAllLocations(),
            'userHasPendingRecordings' => $this->wordService->userHasPendingRecording($fullWord),
            'userSelectedLocations' => $this->wordService->getUserLocationsForWordUuids($fullWord),
            'success' => false,
        ]);
    }

    public function recordingsStore(string $slug)
    {
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

        $foundWord = $this->wordService->findBySlug($slug);

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

        $this->wordService->saveRecording($fullWord, $filePath, !$userIsTrusted);

        return Inertia::render('WordRecordings', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'isLoggedIn' => Auth::check(),
            'word' => $foundWord,
            'randomWord' => $this->randomWord(),
            'locations' => $this->wordService->getAllLocations(),
            'userSelectedLocations' => $this->wordService->getUserLocationsForWordUuids($fullWord),
            'success' => true,
        ]);
    }

    public function locationsIndex(string $slug)
    {
        $foundWord = $this->wordService->findBySlug($slug);
        if (!$foundWord) {
            return redirect()->back();
        }

        $fullWord = Word::where('uuid', $foundWord['id'])->first();
        $this->logService->createUserLog(request(), $fullWord->id);

        return Inertia::render('WordLocations', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'isLoggedIn' => Auth::check(),
            'word' => $foundWord,
            'randomWord' => $this->randomWord(),
            'locations' => $this->wordService->getAllLocations(),
            'userSelectedLocations' => $this->wordService->getUserLocationsForWordUuids($fullWord),
            'locationData' => $this->wordService->getPercentageDistributionWordLocations($fullWord),
        ]);
    }

    public function locationsStore(string $slug)
    {
        if (!Auth::check()) {
            return redirect()->back();
        }

        $foundWord = Word::where('slug', $slug)->first();
        $fullWord = $this->wordService->findBySlug($foundWord->slug);

        $locations = request('locations');

        $this->wordService->addLocationsToWordLinks($foundWord, $locations);

        return Inertia::render('WordLocations', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'isLoggedIn' => Auth::check(),
            'word' => $fullWord,
            'randomWord' => $this->randomWord(),
            'locations' => $this->wordService->getAllLocations(),
            'userSelectedLocations' => $this->wordService->getUserLocationsForWordUuids($foundWord),
        ]);
    }
}
