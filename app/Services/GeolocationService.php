<?php

namespace App\Services;

class GeolocationService
{
    private const EARTH_RADIUS = 6371000; // meters

    /**
     * Calculate distance between two coordinates using Haversine formula.
     */
    public function distance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin(deg2rad($lng2 - $lng1) / 2) * sin(deg2rad($lng2 - $lng1) / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return self::EARTH_RADIUS * $c;
    }

    /**
     * Check if a point is within radius of a center point.
     */
    public function isWithinRadius(float $lat1, float $lng1, float $lat2, float $lng2, float $radius): bool
    {
        return $this->distance($lat1, $lng1, $lat2, $lng2) <= $radius;
    }

    /**
     * Find nearest dapur from a given location.
     */
    public function findNearest(\Illuminate\Support\Collection $dapurs, float $lat, float $lng): ?object
    {
        return $dapurs
            ->filter(fn($d) => $d->latitude && $d->longitude)
            ->map(fn($d) => [
                'dapur' => $d,
                'distance' => $this->distance($lat, $lng, (float)$d->latitude, (float)$d->longitude),
            ])
            ->sortBy('distance')
            ->first()['dapur'] ?? null;
    }
}
