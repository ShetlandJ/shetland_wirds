<?php

namespace App\Http\Controllers\Word;

use App\Models\Word;
use Inertia\Inertia;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DictionaryController;

class CommentController extends DictionaryController
{
    public function index(string $slug)
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

    public function store(string $slug)
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

    public function update(string $slug)
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

    public function delete(string $slug, string $commentUuid)
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
}
