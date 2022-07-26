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
use App\Services\UserService;
use App\Services\WordService;
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

        app(UserService::class)->completelyDeleteUser($user);
        $this->info("User deleted");
    }

}
