# Geolocation Service Documentation

## Overview
Custom geolocation service implementing Haversine formula for distance calculation and geofence validation used in absensi (attendance) system.

## Service Class

### `app/Services/GeolocationService.php`
```php
class GeolocationService
{
    const EARTH_RADIUS_KM = 6371;
    const EARTH_RADIUS_METERS = 6371000;
    const DEFAULT_GEOFENCE_RADIUS_METERS = 100;
    const MAX_GPS_ACCURACY_METERS = 50;

    /**
     * Calculate distance between two coordinates using Haversine formula
     * 
     * @param float $lat1 Latitude of point 1 (degrees)
     * @param float $lon1 Longitude of point 1 (degrees)
     * @param float $lat2 Latitude of point 2 (degrees)
     * @param float $lon2 Longitude of point 2 (degrees)
     * @return float Distance in meters
     */
    public static function calculateDistance(
        float $lat1, float $lon1,
        float $lat2, float $lon2
    ): float {
        // Validate coordinates
        if (!self::validateCoordinate($lat1, $lon1) || 
            !self::validateCoordinate($lat2, $lon2)) {
            throw new InvalidArgumentException('Invalid coordinates');
        }

        // Convert to radians
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        // Haversine formula
        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;
        
        $a = sin($dLat / 2) ** 2 + 
             cos($lat1) * cos($lat2) * sin($dLon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        $distanceKm = self::EARTH_RADIUS_KM * $c;
        return $distanceKm * 1000; // Return in meters
    }

    /**
     * Check if coordinate is within geofence radius
     */
    public static function isWithinGeofence(
        float $userLat, float $userLng,
        float $targetLat, float $targetLng,
        int $radiusMeters = self::DEFAULT_GEOFENCE_RADIUS_METERS
    ): bool {
        $distance = self::calculateDistance($userLat, $userLng, $targetLat, $targetLng);
        return $distance <= $radiusMeters;
    }

    /**
     * Validate coordinate format and range
     */
    public static function validateCoordinate(float $lat, float $lng): bool
    {
        return $lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180;
    }

    /**
     * Get geofence radius for dapur (configurable per dapur)
     */
    public static function getGeofenceRadius(Dapur $dapur): int
    {
        return $dapur->geofence_radius ?? self::DEFAULT_GEOFENCE_RADIUS_METERS;
    }

    /**
     * Validate GPS accuracy is acceptable
     */
    public static function isAccuracyAcceptable(float $accuracyMeters): bool
    {
        return $accuracyMeters <= self::MAX_GPS_ACCURACY_METERS;
    }

    /**
     * Format distance for display
     */
    public static function formatDistance(float $meters): string
    {
        if ($meters < 1000) {
            return round($meters) . ' m';
        }
        return round($meters / 1000, 1) . ' km';
    }

    /**
     * Calculate bearing between two points (degrees)
     */
    public static function calculateBearing(
        float $lat1, float $lon1,
        float $lat2, float $lon2
    ): float {
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $dLon = $lon2 - $lon1;
        
        $y = sin($dLon) * cos($lat2);
        $x = cos($lat1) * sin($lat2) - 
             sin($lat1) * cos($lat2) * cos($dLon);
        
        $bearing = rad2deg(atan2($y, $x));
        return ($bearing + 360) % 360; // Normalize to 0-360
    }

    /**
     * Get cardinal direction from bearing
     */
    public static function getCardinalDirection(float $bearing): string
    {
        $directions = [
            'N', 'NNE', 'NE', 'ENE',
            'E', 'ESE', 'SE', 'SSE',
            'S', 'SSW', 'SW', 'WSW',
            'W', 'WNW', 'NW', 'NNW'
        ];
        $index = round($bearing / 22.5) % 16;
        return $directions[$index];
    }
}
```

## Haversine Formula Explanation

### Mathematical Formula
```
a = sin²(Δφ/2) + cos(φ1) × cos(φ2) × sin²(Δλ/2)
c = 2 × atan2(√a, √(1−a))
d = R × c

Where:
- φ = latitude in radians
- λ = longitude in radians
- R = Earth radius (6371 km)
- d = distance
```

### Why Haversine?
- **Accuracy**: Good for short to medium distances (< 1000 km)
- **Performance**: Simple trigonometric operations
- **No dependencies**: Pure PHP implementation
- **Suitable for geofencing**: Meter-level precision at 100m scale

### Limitations
- Assumes spherical Earth (not ellipsoid)
- Error < 0.5% for distances < 100 km
- Not suitable for surveying-grade precision

## Usage in Absensi Controller

### Check-in Validation
```php
// In Pengawas\AbsensiController::store()
$distance = GeolocationService::calculateDistance(
    $data['latitude'], $data['longitude'],
    $dapur->latitude, $dapur->longitude
);

$geofenceRadius = GeolocationService::getGeofenceRadius($dapur);
$isWithinGeofence = $distance <= $geofenceRadius;

// Also check GPS accuracy
if (!GeolocationService::isAccuracyAcceptable($data['accuracy'])) {
    return back()->with('error', 'Akurasi GPS terlalu rendah (>50m). Coba di area terbuka.');
}
```

### Bearing Calculation (Future)
```php
// Direction from user to dapur
$bearing = GeolocationService::calculateBearing(
    $userLat, $userLng,
    $dapur->latitude, $dapur->longitude
);
$direction = GeolocationService::getCardinalDirection($bearing);
// Returns: "NE", "S", "W", etc.
```

## Configuration

### Environment Variables
```env
# .env
GEOFENCE_DEFAULT_RADIUS=100
GPS_MAX_ACCURACY=50
```

### Config File (`config/geolocation.php`)
```php
return [
    'default_geofence_radius' => env('GEOFENCE_DEFAULT_RADIUS', 100),
    'max_gps_accuracy' => env('GPS_MAX_ACCURACY', 50),
    'earth_radius_km' => 6371,
];
```

## Testing

### Unit Tests (`tests/Unit/GeolocationServiceTest.php`)
```php
use App\Services\GeolocationService;

test('calculate_distance_returns_correct_meters', function () {
    // Jakarta to Bandung ~ 140 km
    $distance = GeolocationService::calculateDistance(
        -6.2088, 106.8456,  // Jakarta
        -6.9175, 107.6191   // Bandung
    );
    
    expect($distance)->toBeGreaterThan(130000)
        ->and($distance)->toBeLessThan(150000);
});

test('calculate_distance_same_point_returns_zero', function () {
    $distance = GeolocationService::calculateDistance(
        -6.123456, 106.123456,
        -6.123456, 106.123456
    );
    
    expect($distance)->toBe(0);
});

test('is_within_geofence_returns_true_when_close', function () {
    $result = GeolocationService::isWithinGeofence(
        -6.123500, 106.123500,  // 50m away
        -6.123456, 106.123456,
        100  // 100m radius
    );
    
    expect($result)->toBeTrue();
});

test('is_within_geofence_returns_false_when_far', function () {
    $result = GeolocationService::isWithinGeofence(
        -6.128000, 106.128000,  // ~500m away
        -6.123456, 106.123456,
        100  // 100m radius
    );
    
    expect($result)->toBeFalse();
});

test('validate_coordinate_rejects_invalid', function () {
    expect(GeolocationService::validateCoordinate(91, 0))->toBeFalse();
    expect(GeolocationService::validateCoordinate(-91, 0))->toBeFalse();
    expect(GeolocationService::validateCoordinate(0, 181))->toBeFalse();
    expect(GeolocationService::validateCoordinate(0, -181))->toBeFalse();
    
    expect(GeolocationService::validateCoordinate(0, 0))->toBeTrue();
    expect(GeolocationService::validateCoordinate(-6.123, 106.123))->toBeTrue();
});

test('format_distance_returns_readable_string', function () {
    expect(GeolocationService::formatDistance(50))->toBe('50 m');
    expect(GeolocationService::formatDistance(1500))->toBe('1.5 km');
    expect(GeolocationService::formatDistance(100000))->toBe('100.0 km');
});

test('calculate_bearing_returns_correct_direction', function () {
    // North
    $bearing = GeolocationService::calculateBearing(0, 0, 1, 0);
    expect($bearing)->toBeApproximately(0, 1);
    
    // East
    $bearing = GeolocationService::calculateBearing(0, 0, 0, 1);
    expect($bearing)->toBeApproximately(90, 1);
    
    // South
    $bearing = GeolocationService::calculateBearing(0, 0, -1, 0);
    expect($bearing)->toBeApproximately(180, 1);
    
    // West
    $bearing = GeolocationService::calculateBearing(0, 0, 0, -1);
    expect($bearing)->toBeApproximately(270, 1);
});
```

## Integration with Dapur Model

### Dapur Model Accessor
```php
// In Dapur.php
public function getGeofenceRadiusAttribute(): int
{
    return $this->geofence_radius ?? 100;
}

public function isWithinGeofence(float $lat, float $lng): bool
{
    return GeolocationService::isWithinGeofence(
        $lat, $lng,
        $this->latitude, $this->longitude,
        $this->geofence_radius
    );
}

public function distanceFrom(float $lat, float $lng): float
{
    return GeolocationService::calculateDistance(
        $lat, $lng,
        $this->latitude, $this->longitude
    );
}
```

## Performance Considerations

### Caching
```php
// Cache distance calculations for repeated checks
$cacheKey = "distance:{$userLat}:{$userLng}:{$dapurId}";
$distance = Cache::remember($cacheKey, 300, function () use ($userLat, $userLng, $dapur) {
    return GeolocationService::calculateDistance($userLat, $userLng, $dapur->latitude, $dapur->longitude);
});
```

### Database Indexing
```php
// Add spatial index if using PostGIS
// $table->point('location')->index(); // Requires PostGIS

// For MySQL, index lat/lng separately
$table->index(['latitude', 'longitude']);
```

## Future Enhancements

### PostGIS Integration
```php
// For production with high volume
// Use PostGIS ST_Distance_Sphere for better performance
DB::select("
    SELECT ST_Distance_Sphere(
        point(?, ?), 
        point(latitude, longitude)
    ) as distance FROM dapurs WHERE id = ?
", [$lng, $lat, $dapurId]);
```

### Configurable Per-Role Geofence
```php
// Different radius for different roles
const GEOFENCE_RADIUS = [
    'pengawas' => 100,
    'kepala_dapur' => 50,
    'admin' => 1000,
];
```

### Offline Geofence Validation
```javascript
// Client-side validation in mobile app
function isWithinGeofence(userLat, userLng, dapurLat, dapurLng, radius = 100) {
    const R = 6371000;
    const φ1 = userLat * Math.PI/180;
    const φ2 = dapurLat * Math.PI/180;
    const Δφ = (dapurLat - userLat) * Math.PI/180;
    const Δλ = (dapurLng - userLng) * Math.PI/180;
    
    const a = Math.sin(Δφ/2)**2 + 
              Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ/2)**2;
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    
    return R * c <= radius;
}
```