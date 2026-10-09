<?php

namespace App\Enums;

/**
 * Backed by the `media.media_type` tinyint column.
 *
 * The legacy stored this as the varchar 'image'; every row in the dataset was
 * an image, but the column allowed anything.
 */
enum MediaType: int
{
    case Image = 1;
    case Video = 2;
    case Document = 3;

    /**
     * Get the human-readable label for the media type.
     */
    public function label(): string
    {
        return match ($this) {
            self::Image => __('Image'),
            self::Video => __('Video'),
            self::Document => __('Document'),
        };
    }

    /**
     * Determine if this type may be used as an item's featured image.
     */
    public function canBeFeatured(): bool
    {
        return $this === self::Image;
    }
}
