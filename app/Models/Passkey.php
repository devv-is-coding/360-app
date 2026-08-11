<?php

namespace App\Models;

use Laravel\Passkeys\Passkey as BasePasskey;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

/**
 * Passkeys belong to central users, so they must resolve against the central
 * connection even while tenancy has swapped the default connection to a tenant.
 *
 * Registered via Passkeys::usePasskeyModel() in AppServiceProvider.
 */
class Passkey extends BasePasskey
{
    use CentralConnection;
}
