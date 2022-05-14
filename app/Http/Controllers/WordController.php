<?php

namespace App\Http\Controllers;

use App\Models\Word;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class WordController extends Controller
{
    public function searchWords(string $searchString): Collection
    {
        return Word::where('word', 'like', "%{$searchString}%")->get();
    }
}
