<?php

namespace App\Enums;

/**
 * Backed by the `items.item_type` tinyint column.
 *
 * The legacy values are preserved deliberately (1 = Tour, 2 = Visa, 3 = Hotel)
 * so ported rows keep their meaning. One polymorphic catalog across all three
 * types is what lets a single booking engine serve the whole catalog.
 */
enum ItemType: int
{
    case Tour = 1;
    case Visa = 2;
    case Hotel = 3;

    /**
     * Get the human-readable label for the type.
     */
    public function label(): string
    {
        return match ($this) {
            self::Tour => __('Tour'),
            self::Visa => __('Visa'),
            self::Hotel => __('Hotel'),
        };
    }

    /**
     * Determine if the type has an ordered route of stops.
     *
     * Only tours do, which is what gates the itinerary map - a visa or a hotel
     * renders no map at all rather than an empty one.
     */
    public function allowsItinerary(): bool
    {
        return $this === self::Tour;
    }

    /**
     * Get the profile table that carries this type's typed attributes.
     */
    public function profileTable(): string
    {
        return match ($this) {
            self::Tour => 'tour_profiles',
            self::Visa => 'visa_profiles',
            self::Hotel => 'hotel_profiles',
        };
    }
}
