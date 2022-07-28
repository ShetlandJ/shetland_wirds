<?php

namespace App\Http\Controllers;

use App\Models\Word;
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

    public function __construct(
        WordService $wordService,
        LogService $logService
    ) {
        $this->wordService = $wordService;
        $this->logService = $logService;
    }

    public function randomWord(): string
    {
        return $this->wordService->getRandomWordSlug();
    }

    public function pagination(): array
    {
        $total = $this->wordService->findBy()->count();
        $pageTotal = request('perPage') ?? 20;

        return [
            'page' => request('page') ?? 1,
            'perPage' => request('perPage') ?? 20,
            'total' => $total,
            'pages' => ceil($total / $pageTotal),
        ];
    }
}
