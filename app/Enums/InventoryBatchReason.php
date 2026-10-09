<?php

namespace App\Enums;

/**
 * Backed by the `inventory_batches.reason` tinyint column.
 *
 * Batches are an append-only ledger of additions, so a mistake is corrected by
 * writing a negative Correction batch rather than by editing or deleting the
 * original. The legacy carried this intent only as free text in `notes`, with
 * values like "Extra slots for holiday season".
 */
enum InventoryBatchReason: int
{
    case InitialStock = 1;
    case SeasonalAddition = 2;
    case Correction = 3;
    case Maintenance = 4;
    case Returned = 5;

    /**
     * Get the human-readable label for the reason.
     */
    public function label(): string
    {
        return match ($this) {
            self::InitialStock => __('Initial stock'),
            self::SeasonalAddition => __('Seasonal addition'),
            self::Correction => __('Correction'),
            self::Maintenance => __('Withdrawn for maintenance'),
            self::Returned => __('Returned to stock'),
        };
    }

    /**
     * Determine if this reason is expected to carry a negative quantity.
     */
    public function reducesCapacity(): bool
    {
        return in_array($this, [self::Correction, self::Maintenance], true);
    }
}
