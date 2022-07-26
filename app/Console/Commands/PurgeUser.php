<?php

namespace App\Console\Commands;

use App\Models\Comment;
use App\Models\SearchLog;
use App\Models\User;
use App\Models\UserLog;
use App\Models\UserWordLike;
use App\Models\Word;
use Illuminate\Support\Str;
use App\Models\WordDefinition;
use App\Models\WordReport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PurgeUser extends Command
{
    protected $signature = 'user:purge {userId}';
    protected $description = 'Remove all references to a user';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $userId = $this->argument('userId');
        $user = User::find($userId);
        if (!$user) {
            $this->error("User not found");
            return;
        }

        // find all words submitted by this user and set the creator_id column to 0
        $words = Word::where('creator_id', $userId)->get();
        foreach ($words as $word) {
            $word->creator_id = 0;
            $word->save();
        }

        // find all word definitions submitted by this user and set the user_id column to null
        $wordDefinitions = WordDefinition::where('user_id', $userId)->get();
        foreach ($wordDefinitions as $wordDefinition) {
            $wordDefinition->user_id = null;
            $wordDefinition->save();
        }

        // find all comments created by this user (and all child comments) and remove them
        $comments = Comment::where('user_id', $userId)->get();
        foreach ($comments as $comment) {
            foreach ($comment->childComments as $childComment) {
                $childComment->delete();
            }
            $comment->delete();
        }

        // find all user word likes and remove them
        $userWordLikes = UserWordLike::where('user_id', $userId)->get();
        foreach ($userWordLikes as $userWordLike) {
            $userWordLike->delete();
        }

        // find all UserLogs and remove them
        $userLogs = UserLog::where('user_id', $userId)->get();
        foreach ($userLogs as $userLog) {
            $userLog->delete();
        }

        // find all SearchLogs for user and remove them
        $searchLogs = SearchLog::where('user_id', $userId)->get();
        foreach ($searchLogs as $searchLog) {
            $searchLog->delete();
        }

        // find all WordReports and remove them
        $wordReports = WordReport::where('user_id', $userId)->get();
        foreach ($wordReports as $wordReport) {
            $wordReport->delete();
        }

        $user->delete();
        $this->info("User deleted");
    }

}
