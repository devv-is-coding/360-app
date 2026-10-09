<?php

namespace App\Enums;

/**
 * Backed by the `bookings.payment_status` tinyint column.
 *
 * This is the money axis, orthogonal to the manager's decision in
 * `bookings.approval_status`.
 *
 * The legacy values are preserved, including the gap: 5 was never used, and
 * PartiallyRefunded stays at 6 so ported rows keep their meaning.
 *
 * This status is always *derived* from the booking's amounts and never
 * assigned, so it cannot contradict the payment ledger. The pure function that
 * derives it arrives with the payment work, together with its table-driven test.
 */
enum BookingPaymentStatus: int
{
    case Unpaid = 1;
    case PartiallyPaid = 2;
    case Paid = 3;
    case Refunded = 4;
    case PartiallyRefunded = 6;

    /**
     * Get the human-readable label for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::Unpaid => __('Unpaid'),
            self::PartiallyPaid => __('Partially paid'),
            self::Paid => __('Paid'),
            self::Refunded => __('Refunded'),
            self::PartiallyRefunded => __('Partially refunded'),
        };
    }

    /**
     * Determine if any money has been received against the booking.
     */
    public function hasPayments(): bool
    {
        return $this !== self::Unpaid;
    }

    /**
     * Determine if the booking has been settled in full.
     */
    public function isSettled(): bool
    {
        return $this === self::Paid;
    }
}
