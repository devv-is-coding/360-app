<?php

namespace App\Models;

use Database\Factories\TenantFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

/**
 * Multi-database tenancy requires a tenant model implementing TenantWithDatabase;
 * the package's stock model does not, which is why this app-level subclass exists.
 *
 * HasDatabase supplies the per-tenant database name and connection, HasDomains the
 * domain lookup used by InitializeTenancyByDomain.
 *
 * @property string $id
 */
class Tenant extends BaseTenant implements TenantWithDatabase
{
    /** @use HasFactory<TenantFactory> */
    use HasDatabase, HasDomains, HasFactory;
}
