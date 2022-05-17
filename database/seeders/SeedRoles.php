<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class SeedRoles extends Seeder
{
    public function run()
    {
        Role::create([
            'uuid' => Str::uuid(),
            'name' => Role::ROLE_ADMIN,
            'description' => Role::ROLE_ADMIN_DESC,
        ]);

        Role::create([
            'uuid' => Str::uuid(),
            'name' => Role::ROLE_TRUSTED,
            'description' => Role::ROLE_TRUSTED_DESC,
        ]);

        Role::create([
            'uuid' => Str::uuid(),
            'name' => Role::ROLE_USER,
            'description' => Role::ROLE_USER_DESC,
        ]);
    }
}
