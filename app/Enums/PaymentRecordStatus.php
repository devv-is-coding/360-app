<?php

namespace App\Enums;

/**
 * Backed by the `payments.status` tinyint column.
 *
 * This is the state of one payment row, not of the booking - the booking's
 * money axis lives in BookingPaymentStatus and is derived from the sum of these.
 *
 * The legacy dataset only ever used 2, 4 and 5, and those values are preserved.
 * Pending (1) and Failed (3) fill the gaps the legacy left implicit.
 */
enum PaymentRecordStatus: int
{
    case Pending = 1;
    case Succeeded = 2;
    case Failed = 3;
    case Refunded = 4;
    case PartiallyRefunded = 5;

    /**
     * Get the human-readable label for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => __('Pending'),
            self::Succeeded => __('Succeeded'),
            self::Failed => __('Failed'),
            self::Refunded => __('Refunded'),
            self::PartiallyRefunded => __('Partially refunded'),
        };
    }

    /**
     * Determine if this payment counts toward the booking's paid amount.
     */
    public function countsAsPaid(): bool
    {
        return in_array($this, [self::Succeeded, self::Refunded, self::PartiallyRefunded], true);
    }
}
