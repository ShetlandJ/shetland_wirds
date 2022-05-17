<?php

namespace App\Models;

use App\Models\User;
use App\Models\Word;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Role extends Model
{
    protected $table = 'roles';

    public const ROLE_ADMIN = 'admin';
    public const ROLE_ADMIN_DESC = 'Admins have full access to all resources.';

    public const ROLE_TRUSTED = 'trusted';
    public const ROLE_TRUSTED_DESC = 'This user can add new words without having to wait for the approval process';

    public const ROLE_USER = 'user';
    public const ROLE_USER_DESC = 'Users can add new words and track their submissions';

    protected $fillable = [
        'uuid',
        'name',
        'description',
    ];
}
