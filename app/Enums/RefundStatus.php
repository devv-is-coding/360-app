<?php

namespace App\Enums;

/**
 * Backed by the `refunds.status` tinyint column.
 *
 * A refund is a negotiation, ported faithfully: the customer requests with a
 * reason, a manager approves full or partial, or rejects with a reason, and
 * only then is it processed against the gateway.
 */
enum RefundStatus: int
{
    case Requested = 1;
    case Approved = 2;
    case Rejected = 3;
    case Processed = 4;
    case Failed = 5;

    /**
     * Get the human-readable label for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::Requested => __('Requested'),
            self::Approved => __('Approved'),
            self::Rejected => __('Rejected'),
            self::Processed => __('Processed'),
            self::Failed => __('Failed'),
        };
    }

    /**
     * Determine if the refund returns the booking's inventory to stock.
     *
     * Approval is what releases the slot; a rejected refund leaves both the
     * money and the inventory untouched.
     */
    public function releasesInventory(): bool
    {
        return in_array($this, [self::Approved, self::Processed], true);
    }

    /**
     * Determine if the refund reduces the booking's refunded total.
     */
    public function countsAsRefunded(): bool
    {
        return $this === self::Processed;
    }

    /**
     * Determine if the status requires a reason to be recorded.
     */
    public function requiresReason(): bool
    {
        return $this === self::Rejected;
    }
}
