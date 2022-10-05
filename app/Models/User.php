<?php

namespace App\Models;

use App\Models\Role;
use App\Models\Word;
use App\Models\Location;
use App\Models\UserToRole;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Jetstream\HasProfilePhoto;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'can_contact',
        'location_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
        'is_trusted',
        'location_uuid'
    ];

    public function words(): HasMany
    {
        return $this->hasMany(Word::class, 'creator_id', 'id');
    }

    public function roles(): HasManyThrough
    {
        return $this->hasManyThrough(
            Role::class,
            UserToRole::class,
            'user_id',
            'id',
            'id',
            'role_id'
        );
    }

    public function location(): HasOne
    {
        return $this->hasOne(Location::class, 'id', 'location_id');
    }

    public function getLocationUuidAttribute(): ?string
    {
        return $this->location && $this->location->uuid;
    }

    public function getIsTrustedAttribute(): bool
    {
        $isTrusted = false;

        foreach ($this->roles as $role) {

            if ($role->name === 'trusted' || $role->name === 'admin') {
                $isTrusted = true;
            }
        }

        return $isTrusted;
    }

    public function getInitialsAttribute(): string
    {
        return $this->generateInitials($this->name);
    }

    public function generateInitials(string $name): string
    {
        $words = explode(' ', $name);
        if (count($words) >= 2) {
            return mb_strtoupper(
                mb_substr($words[0], 0, 1, 'UTF-8') .
                mb_substr(end($words), 0, 1, 'UTF-8'),
            'UTF-8');
        }
        return $this->makeInitialsFromSingleWord($name);
    }

    protected function makeInitialsFromSingleWord(string $name) : string
    {
        preg_match_all('#([A-Z]+)#', $name, $capitals);
        if (count($capitals[1]) >= 2) {
            return mb_substr(implode('', $capitals[1]), 0, 2, 'UTF-8');
        }
        return mb_strtoupper(mb_substr($name, 0, 2, 'UTF-8'), 'UTF-8');
    }
}
