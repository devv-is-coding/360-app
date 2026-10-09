<?php

namespace App\Enums;

/**
 * Backed by the `payments.method` tinyint column.
 *
 * The legacy stored this as a varchar and the dataset contains 'cash',
 * 'credit_card' and 'stripe'. Cash matters: the real flow in the data is staff
 * recording a cash deposit, then the customer settling the balance online.
 */
enum PaymentMethod: int
{
    case Cash = 1;
    case CreditCard = 2;
    case Stripe = 3;
    case BankTransfer = 4;

    /**
     * Get the human-readable label for the method.
     */
    public function label(): string
    {
        return match ($this) {
            self::Cash => __('Cash'),
            self::CreditCard => __('Credit card'),
            self::Stripe => __('Stripe'),
            self::BankTransfer => __('Bank transfer'),
        };
    }

    /**
     * Determine if staff record this payment by hand rather than a gateway.
     */
    public function isRecordedByStaff(): bool
    {
        return in_array($this, [self::Cash, self::BankTransfer], true);
    }

    /**
     * Determine if the payment is settled through Stripe.
     *
     * These are only ever confirmed from a signature-verified webhook, never
     * from a client callback.
     */
    public function isGatewaySettled(): bool
    {
        return in_array($this, [self::CreditCard, self::Stripe], true);
    }
}
