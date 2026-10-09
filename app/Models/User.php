<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Role;
use Carbon\CarbonInterface;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

/**
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string $first_name
 * @property string|null $middle_name
 * @property string $last_name
 * @property string|null $contact_number
 * @property string|null $profile_picture
 * @property Role $role
 * @property string|null $tenant_id
 * @property bool $is_active
 * @property Carbon|null $email_verified_on
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_on
 * @property string|null $remember_token
 * @property Carbon|null $created_on
 * @property Carbon|null $updated_on
 * @property Carbon|null $deleted_on
 * @property-read string $full_name
 * @property-read Tenant|null $tenant
 */
#[Fillable([
    'username',
    'email',
    'password',
    'first_name',
    'middle_name',
    'last_name',
    'contact_number',
    'profile_picture',
    'role',
    'tenant_id',
    'is_active',
])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements PasskeyUser
{
    /**
     * Users live in the central database, so this model must stay on the central
     * connection even while tenancy has swapped the default connection to a tenant.
     *
     * @use HasFactory<UserFactory>
     */
    use CentralConnection, HasFactory, Notifiable, PasskeyAuthenticatable, SoftDeletes, TwoFactorAuthenticatable;

    const CREATED_AT = 'created_on';

    const UPDATED_AT = 'updated_on';

    const DELETED_AT = 'deleted_on';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_on' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_on' => 'datetime',
            'deleted_on' => 'datetime',
            'role' => Role::class,
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the user's full name, omitting the middle name when it is not set.
     *
     * @return Attribute<string, never>
     */
    protected function fullName(): Attribute
    {
        return Attribute::get(fn (): string => collect([$this->first_name, $this->middle_name, $this->last_name])
            ->filter()
            ->implode(' '));
    }

    /**
     * Alias for the framework's MustVerifyEmail, which hardcodes the `_at` name.
     *
     * @return Attribute<CarbonInterface|null, mixed>
     */
    protected function emailVerifiedAt(): Attribute
    {
        return Attribute::make(
            get: fn (): ?CarbonInterface => $this->email_verified_on,
            set: fn (mixed $value): array => ['email_verified_on' => $value],
        )->withoutObjectCaching();
    }

    /**
     * Alias for Fortify, which hardcodes the `_at` name.
     *
     * @return Attribute<CarbonInterface|null, mixed>
     */
    protected function twoFactorConfirmedAt(): Attribute
    {
        return Attribute::make(
            get: fn (): ?CarbonInterface => $this->two_factor_confirmed_on,
            set: fn (mixed $value): array => ['two_factor_confirmed_on' => $value],
        )->withoutObjectCaching();
    }

    /**
     * Get the tenant an admin, manager or staff member belongs to.
     *
     * @return BelongsTo<Tenant, $this>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Determine if the user has full platform access.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role->isSuperAdmin();
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        $initials = Str::initials($this->first_name.' '.$this->last_name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }
}
