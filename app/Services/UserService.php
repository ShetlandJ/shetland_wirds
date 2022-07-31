<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use App\Models\Word;
use App\Models\Comment;
use App\Models\UserLog;
use App\Models\Revision;
use App\Models\SearchLog;
use App\Models\UserToRole;
use App\Models\WordReport;
use Illuminate\Support\Str;
use App\Models\UserWordLike;
use App\Services\WordService;
use App\Models\WordDefinition;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    private const BLOCKED_EMAILS = [
        'james@jastewart.co.uk',
    ];

    public function findAll(): array
    {
        return User::all()->map(fn (User $user) => $this->formatUser($user))->all();
    }

    public function formatUser(User $user): array
    {
        $roles = [];

        foreach ($user->roles as $role) {
            $roles[] = [
                'name' => $role->name,
                'id' => $role->uuid,
            ];
        }

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $roles,
            'word_count' => $user->words->count(),
            'created_at' => $user->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function updateRole(int $userId, string $roleId): void
    {
        $user = User::where('id', $userId)->first();
        $role = Role::where('uuid', $roleId)->first();

        if (!$user || !$role) {
            throw new \Exception('User or role not found');
        }

        // check if user has the role already
        if ($user->roles->contains($role)) {
            // check if role->name is admin and user's email is not in BLOCKED_EMAILS
            if ($role->name === 'admin' && in_array($user->email, self::BLOCKED_EMAILS)) {
                // if user has the role, but email is in BLOCKED_EMAILS, ignore the request
                return;
            }

            $link = $user->roles()->where('role_id', $role->id)->first();

            $link->delete();
        } else {
            $utr = new UserToRole();
            $utr->user_id = $user->id;
            $utr->role_id = $role->id;
            $utr->save();
        }

        // if ($user->roles)
    }

    public function completelyDeleteUser(User $user): void
    {
        $userId = $user->id;

        $user = User::where('id', $userId)->first();

        if ($user->email === 'james@jastewart.co.uk') {
            throw new \Exception('Cannot delete');
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

    }
}
