<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Domain as BaseDomain;

/**
 * Exists only to rename the timestamp columns; registered as tenancy.domain_model.
 */
class Domain extends BaseDomain
{
    const CREATED_AT = 'created_on';

    const UPDATED_AT = 'updated_on';
}
