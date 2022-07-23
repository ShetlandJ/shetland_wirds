<?php

namespace App\Services;

use App\Models\User;
use App\Models\Revision;
use App\Models\Role;
use App\Models\UserToRole;
use Illuminate\Support\Str;
use App\Services\WordService;
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
            'id' => $user->uuid,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $roles,
            'word_count' => $user->words->count(),
            'created_at' => $user->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function updateRole(string $userId, string $roleId): void
    {
        $user = User::where('uuid', $userId)->first();
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
}
