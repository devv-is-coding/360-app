<?php

namespace App\Enums;

/**
 * Backed by the `bookings.fulfilment_status` tinyint column.
 *
 * The third status axis, and the one the legacy lacked entirely: it tracked
 * individual stop visits in `tour_visits` but had no booking-level notion of
 * whether the guest actually turned up.
 *
 * Orthogonal to both the approval and the payment axes - a paid, approved
 * booking can still be a no-show.
 */
enum FulfilmentStatus: int
{
    case NotStarted = 1;
    case CheckedIn = 2;
    case Completed = 3;
    case NoShow = 4;

    /**
     * Get the human-readable label for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::NotStarted => __('Not started'),
            self::CheckedIn => __('Checked in'),
            self::Completed => __('Completed'),
            self::NoShow => __('No show'),
        };
    }

    /**
     * Determine if the guest may still submit a review.
     *
     * Reviews are booking-gated: the guest must have actually been served.
     */
    public function allowsReview(): bool
    {
        return $this === self::Completed;
    }
}
