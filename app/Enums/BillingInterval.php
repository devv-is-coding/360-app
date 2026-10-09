<?php

namespace App\Enums;

use Carbon\CarbonInterface;

/**
 * Backed by the `subscriptions.interval` tinyint column.
 *
 * How often the platform bills an agency. Stored as a count of months rather
 * than a free-form period string, so advancing a billing date is arithmetic
 * instead of parsing.
 */
enum BillingInterval: int
{
    case Monthly = 1;
    case Quarterly = 2;
    case Annual = 3;

    /**
     * Get the human-readable label for the interval.
     */
    public function label(): string
    {
        return match ($this) {
            self::Monthly => __('Monthly'),
            self::Quarterly => __('Quarterly'),
            self::Annual => __('Annual'),
        };
    }

    /**
     * Get the number of months this interval spans.
     */
    public function months(): int
    {
        return match ($this) {
            self::Monthly => 1,
            self::Quarterly => 3,
            self::Annual => 12,
        };
    }

    /**
     * Get the date one interval after the given one.
     *
     * Carbon clamps a shorter month, so a period starting on the 31st advances
     * to the 28th or 30th rather than overflowing into the next month.
     */
    public function advance(CarbonInterface $from): CarbonInterface
    {
        return $from->copy()->addMonthsNoOverflow($this->months());
    }
}
