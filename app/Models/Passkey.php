<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Laravel\Passkeys\Passkey as BasePasskey;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

/**
 * Passkeys belong to central users, so they must resolve against the central
 * connection even while tenancy has swapped the default connection to a tenant.
 *
 * Registered via Passkeys::usePasskeyModel() in AppServiceProvider.
 *
 * @property CarbonInterface|null $last_used_on
 * @property CarbonInterface|null $created_on
 * @property CarbonInterface|null $updated_on
 */
class Passkey extends BasePasskey
{
    use CentralConnection;

    const CREATED_AT = 'created_on';

    const UPDATED_AT = 'updated_on';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'credential' => 'json',
            'last_used_on' => 'datetime',
        ];
    }

    /**
     * Alias for the passkeys package, which hardcodes the `_at` name.
     *
     * @return Attribute<CarbonInterface|null, mixed>
     */
    protected function lastUsedAt(): Attribute
    {
        return Attribute::make(
            get: fn (): ?CarbonInterface => $this->last_used_on,
            set: fn (mixed $value): array => ['last_used_on' => $value],
        )->withoutObjectCaching();
    }
}
