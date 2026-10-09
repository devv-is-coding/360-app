<?php

namespace App\Enums;

/**
 * Backed by the `options.availability_mode` tinyint column.
 *
 * The mode lives on the option rather than the item, which keeps options
 * genuinely generic and lets one hotel sell both a per-night room and a
 * fixed-date New Year's package.
 *
 * Nightly is the headline fix: the legacy had no date-ranged availability at
 * all, so booking a room consumed its slot permanently.
 */
enum AvailabilityMode: int
{
    /** A fixed departure window with a finite number of seats - tours. */
    case Capacity = 1;

    /** Per-night availability over a guest-chosen range - hotels. */
    case Nightly = 2;

    /** No inventory tracked at all - visas. */
    case Unlimited = 3;

    /**
     * Get the human-readable label for the mode.
     */
    public function label(): string
    {
        return match ($this) {
            self::Capacity => __('Fixed capacity'),
            self::Nightly => __('Per night'),
            self::Unlimited => __('Unlimited'),
        };
    }

    /**
     * Determine if booking this option writes inventory allocation rows.
     */
    public function consumesInventory(): bool
    {
        return $this !== self::Unlimited;
    }

    /**
     * Determine if the guest chooses the dates.
     *
     * Capacity copies its dates from the item's departure window; Nightly takes
     * the guest's range; Unlimited has none.
     */
    public function isGuestDated(): bool
    {
        return $this === self::Nightly;
    }

    /**
     * Determine if the date range is half-open - the last day is not consumed.
     *
     * Nightly is half-open [starts_on, ends_on): the check-out day is not a
     * consumed night, which is what makes back-to-back bookings work. Capacity
     * is inclusive of both ends. Never compare these dates by hand.
     */
    public function usesHalfOpenRange(): bool
    {
        return $this === self::Nightly;
    }
}
