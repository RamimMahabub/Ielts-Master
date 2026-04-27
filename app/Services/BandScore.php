<?php

namespace App\Services;

/**
 * IELTS raw-score → band conversion.
 * Tables are official approximations; tweak in one place if needed.
 */
class BandScore
{
    /** Listening (Academic + General share the same scale). */
    public static function listening(int $raw): float
    {
        return match (true) {
            $raw >= 39 => 9.0,
            $raw >= 37 => 8.5,
            $raw >= 35 => 8.0,
            $raw >= 32 => 7.5,
            $raw >= 30 => 7.0,
            $raw >= 26 => 6.5,
            $raw >= 23 => 6.0,
            $raw >= 18 => 5.5,
            $raw >= 16 => 5.0,
            $raw >= 13 => 4.5,
            $raw >= 10 => 4.0,
            $raw >= 8  => 3.5,
            $raw >= 6  => 3.0,
            $raw >= 4  => 2.5,
            default    => 2.0,
        };
    }

    /** Reading — Academic table. */
    public static function readingAcademic(int $raw): float
    {
        return match (true) {
            $raw >= 39 => 9.0,
            $raw >= 37 => 8.5,
            $raw >= 35 => 8.0,
            $raw >= 33 => 7.5,
            $raw >= 30 => 7.0,
            $raw >= 27 => 6.5,
            $raw >= 23 => 6.0,
            $raw >= 19 => 5.5,
            $raw >= 15 => 5.0,
            $raw >= 13 => 4.5,
            $raw >= 10 => 4.0,
            $raw >= 8  => 3.5,
            $raw >= 6  => 3.0,
            $raw >= 4  => 2.5,
            default    => 2.0,
        };
    }

    /** Reading — General Training table. */
    public static function readingGeneral(int $raw): float
    {
        return match (true) {
            $raw >= 40 => 9.0,
            $raw >= 39 => 8.5,
            $raw >= 37 => 8.0,
            $raw >= 36 => 7.5,
            $raw >= 34 => 7.0,
            $raw >= 32 => 6.5,
            $raw >= 30 => 6.0,
            $raw >= 27 => 5.5,
            $raw >= 23 => 5.0,
            $raw >= 19 => 4.5,
            $raw >= 15 => 4.0,
            $raw >= 12 => 3.5,
            $raw >= 9  => 3.0,
            $raw >= 6  => 2.5,
            default    => 2.0,
        };
    }

    /**
     * Overall band — average of 4 modules rounded to nearest 0.5
     * using the official IELTS rule: 0.25→up to 0.5, 0.75→up to next whole.
     */
    public static function overall(?float $l, ?float $r, ?float $w, ?float $s): ?float
    {
        $bands = array_filter([$l, $r, $w, $s], fn ($b) => $b !== null);
        if (count($bands) < 4) {
            return null;
        }
        $avg = array_sum($bands) / 4;
        return self::roundToHalfBand($avg);
    }

    public static function roundToHalfBand(float $value): float
    {
        $rounded = round($value * 2) / 2;
        // IELTS rule: e.g. 6.125 → 6.0, 6.25 → 6.5, 6.75 → 7.0
        return $rounded;
    }
}
