<?php

declare(strict_types=1);

namespace Domain\Users\Models;

use Database\Factories\UserFactory;
use Domain\Shared\Casts\AsDateTime;
use Domain\Users\Collections\UserCollection;
use Domain\Users\QueryBuilders\UserQueryBuilder;
use Domain\Users\States\UserState;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\CollectedBy;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Scout\Searchable;
use Spatie\ModelStates\HasStates;
use Spatie\Permission\Traits\HasRoles;

#[CollectedBy(UserCollection::class)]
#[UseEloquentBuilder(UserQueryBuilder::class)]
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory;
    use HasRoles;
    use HasStates;
    use HasUlids;
    use Notifiable;
    use Searchable;
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'settings',
        'state',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'state' => UserState::class,
            'settings' => AsArrayObject::class,
            'email_verified_at' => AsDateTime::class,
            'created_at' => AsDateTime::class,
            'updated_at' => AsDateTime::class,
            'deleted_at' => AsDateTime::class,
        ];
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    public function uniqueIds(): array
    {
        return ['ulid'];
    }

    public function getRouteKeyName(): string
    {
        return 'ulid';
    }

    public static function findFromUlid(User|string $value): ?User
    {
        if ($value instanceof User) {
            return $value;
        }

        return User::query()->firstWhere('ulid', $value);
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => (string) $this->getScoutKey(),
            'name' => (string) $this->name,
            'email' => (string) $this->email,
            'state' => (string) $this->state,
            'email_verified_at' => (int) $this->email_verified_at?->getTimestamp(),
            'created_at' => (int) $this->created_at->getTimestamp(),
            'updated_at' => (int) $this->updated_at->getTimestamp(),
            'deleted_at' => (int) $this->deleted_at?->getTimestamp(),
        ];
    }

    public function isAdmin(): bool
    {
        return $this->hasAnyRole('admin', 'super-admin');
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }

    protected function assignedRoles(): Attribute
    {
        return Attribute::make(
            get: fn (): Collection => $this->getRoleNames(),
        )->shouldCache();
    }

    protected function assignedPermissions(): Attribute
    {
        return Attribute::make(
            get: fn (): Collection => $this->getAllPermissions()->pluck('name'),
        )->shouldCache();
    }
}
