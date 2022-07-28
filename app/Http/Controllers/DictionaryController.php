<?php

namespace App\Http\Controllers;

use App\Models\Word;
use App\Services\CommentService;
use App\Services\LogService;
use App\Services\WordService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DictionaryController extends Controller
{
    public WordService $wordService;
    public LogService $logService;
    public CommentService $commentService;

    public function __construct(
        WordService $wordService,
        LogService $logService,
        CommentService $commentService
    ) {
        $this->wordService = $wordService;
        $this->logService = $logService;
        $this->commentService = $commentService;
    }

    public function randomWord(): string
    {
        return $this->wordService->getRandomWordSlug();
    }

    public function pagination(string $searchTerm = '', string $letter = ''): array
    {
        $total = $this->wordService->findBy($searchTerm, [], $letter)->count();
        $pageTotal = request('perPage') ?? 20;

        return [
            'page' => request('page') ?? 1,
            'perPage' => request('perPage') ?? 20,
            'total' => $total,
            'pages' => ceil($total / $pageTotal),
        ];
    }
}
