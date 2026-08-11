<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

/**
 * API tokens belong to central users, so they must resolve against the central
 * connection even while tenancy has swapped the default connection to a tenant.
 *
 * Registered via Sanctum::usePersonalAccessTokenModel() in AppServiceProvider.
 */
class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use CentralConnection;
}
