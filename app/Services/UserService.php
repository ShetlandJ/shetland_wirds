<?php

namespace App\Services;

use App\Models\User;
use App\Models\Revision;
use App\Models\Role;
use Illuminate\Support\Str;
use App\Services\WordService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
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
}
