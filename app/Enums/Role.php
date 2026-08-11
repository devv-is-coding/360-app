<?php

namespace App\Enums;

/**
 * Backed by the `users.role` tinyint column.
 *
 * Note that SuperAdmin is 0, which is also the column's default - a user created
 * without an explicit role is therefore a super admin. Always set the role
 * explicitly when creating users.
 */
enum Role: int
{
    case SuperAdmin = 0;
    case Admin = 1;
    case Manager = 2;
    case Customer = 3;

    /**
     * Get the human-readable label for the role.
     */
    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => __('Super admin'),
            self::Admin => __('Admin'),
            self::Manager => __('Manager'),
            self::Customer => __('Customer'),
        };
    }

    /**
     * Determine if the role has full platform access.
     */
    public function isSuperAdmin(): bool
    {
        return $this === self::SuperAdmin;
    }
}
