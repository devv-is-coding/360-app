<?php

namespace App\Enums;

/**
 * Backed by the `refunds.type` tinyint column.
 *
 * The legacy stored this as the varchar 'full' or 'partial' in a column on the
 * booking itself, which permitted exactly one refund per booking, ever.
 */
enum RefundType: int
{
    case Full = 1;
    case Partial = 2;

    /**
     * Get the human-readable label for the type.
     */
    public function label(): string
    {
        return match ($this) {
            self::Full => __('Full refund'),
            self::Partial => __('Partial refund'),
        };
    }
}
