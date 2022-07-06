<?php

namespace App\Services;

use DateTime;
use Exception;
use Carbon\Carbon;
use App\Models\Word;
use App\Models\Comment;
use App\Models\Location;
use App\Models\WordToWord;
use Illuminate\Support\Str;
use App\Models\UserWordLike;
use App\Models\WordOfTheDay;
use App\Models\WordRecording;
use App\Models\WordDefinition;
use App\Models\WordToLocation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Routing\Loader\Configurator\CollectionConfigurator;

class CommentService
{
    public function update(Comment $comment, string $newText)
    {
        $comment->comment = $newText;
        $comment->save();
    }

    public function delete(Comment $comment): bool
    {
        if ($comment->parent) {
            foreach ($comment->childComments as $childComment) {
                $childComment->delete();
            }
        }

        return $comment->delete();
    }
}
