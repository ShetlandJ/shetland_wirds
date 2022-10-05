<?php

namespace App\Http\Controllers\Word;

use App\Models\Word;
use Inertia\Inertia;
use App\Models\Comment;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\DictionaryController;

class WordController extends DictionaryController
{
    public function like(?string $slug)
    {
        if (!Auth::check()) {
            return redirect()->back();
        }

        if ($slug) {
            $this->wordService->handleLike($slug);
        }
        return redirect()->back();
    }

    public function show(string $uuid)
    {
        $word = Word::where('uuid', $uuid)->first();
        if (!$word) {
            return redirect()->back();
        }

        $this->logService->createUserLog(request(), $word->id);

        return redirect()->route('word.comments', $word->slug);
    }

    public function base(string $slug)
    {
        $foundWord = $this->wordService->findBySlug($slug);
        if (!$foundWord) {
            return redirect()->back();
        }

        $fullWord = Word::where('uuid', $foundWord['id'])->first();

        $this->logService->createUserLog(request(), $fullWord->id);

        $fullWord = Word::where('uuid', $foundWord['id'])->first();

        return redirect()->route('word.comments', $fullWord->slug);
    }

    public function commentsIndex(string $slug)
    {
        $foundWord = $this->wordService->findBySlug($slug);
        if (!$foundWord) {
            return redirect()->back();
        }

        $fullWord = Word::where('uuid', $foundWord['id'])->first();
        $this->logService->createUserLog(request(), $fullWord->id);

        return Inertia::render('WordComments', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'isLoggedIn' => Auth::check(),
            'word' => $foundWord,
            'randomWord' => $this->randomWord(),
            'locations' => $this->wordService->getAllLocations(),
            'userSelectedLocations' => $this->wordService->getUserLocationsForWordUuids($fullWord),
        ]);
    }

    public function commentsStore(string $slug)
    {
        if (!Auth::check()) {
            return redirect()->back();
        }

        $foundWord = Word::where('slug', $slug)->first();

        if (request('text')) {
            $comment = null;

            if (request('comment_id')) {
                $comment = Comment::where('uuid', request('comment_id'))->first();
            }

            $this->wordService->createComment(request('text'), $foundWord, $comment);
        }

        $fullWord = $this->wordService->findBySlug($foundWord->slug);

        return Inertia::render('WordComments', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'isLoggedIn' => Auth::check(),
            'word' => $fullWord,
            'randomWord' => $this->randomWord(),
            'locations' => [],
            'userSelectedLocations' => []
        ]);
    }

    public function commentsEdit(string $slug)
    {
        if (!Auth::check()) {
            return redirect()->back();
        }

        $commentId = request('childCommentId');

        if (!$commentId) {
            return redirect()->back();
        }

        $foundWord = Word::where('slug', $slug)->first();
        $comment = Comment::where('uuid', $commentId)->first();


        if ($comment->author->id !== Auth::id()) {
            return redirect()->back();
        }

        $text = request('text');

        $this->commentService->update($comment, $text);

        $fullWord = $this->wordService->findBySlug($foundWord->slug);

        return Inertia::render('WordComments', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'isLoggedIn' => Auth::check(),
            'word' => $fullWord,
            'randomWord' => $this->randomWord(),
            'locations' => [],
            'userSelectedLocations' => []
        ]);
    }

    public function commentsDelete(string $slug, string $commentUuid)
    {
        if (!Auth::check()) {
            return redirect()->back();
        }

        if (!$commentUuid) {
            return redirect()->back();
        }

        $comment = Comment::where('uuid', $commentUuid)->first();

        if ($comment->author->id !== Auth::id()) {
            return redirect()->back();
        }

        $this->commentService->delete($comment);

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

        $recording = $this->wordService->saveRecording($fullWord, $filePath, !$userIsTrusted);

        $this->wordService->saveAsMp3($recording);

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
