<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

/**
 * API tokens belong to central users, so they must resolve against the central
 * connection even while tenancy has swapped the default connection to a tenant.
 *
 * Registered via Sanctum::usePersonalAccessTokenModel() in AppServiceProvider.
 *
 * Sanctum reads and writes `created_at`, `last_used_at` and `expires_at` by name,
 * so those are aliased onto the `_on` columns below.
 *
 * @property CarbonInterface|null $last_used_on
 * @property CarbonInterface|null $expires_on
 * @property CarbonInterface|null $created_on
 * @property CarbonInterface|null $updated_on
 */
class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use CentralConnection;

    const CREATED_AT = 'created_on';

    const UPDATED_AT = 'updated_on';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'abilities' => 'json',
        'last_used_on' => 'datetime',
        'expires_on' => 'datetime',
    ];

    /**
     * @return Attribute<CarbonInterface|null, never>
     */
    protected function createdAt(): Attribute
    {
        return Attribute::get(fn (): ?CarbonInterface => $this->created_on)->withoutObjectCaching();
    }

    /**
     * @return Attribute<CarbonInterface|null, mixed>
     */
    protected function lastUsedAt(): Attribute
    {
        return Attribute::make(
            get: fn (): ?CarbonInterface => $this->last_used_on,
            set: fn (mixed $value): array => ['last_used_on' => $value],
        )->withoutObjectCaching();
    }

    /**
     * @return Attribute<CarbonInterface|null, mixed>
     */
    protected function expiresAt(): Attribute
    {
        return Attribute::make(
            get: fn (): ?CarbonInterface => $this->expires_on,
            set: fn (mixed $value): array => ['expires_on' => $value],
        )->withoutObjectCaching();
    }
}
