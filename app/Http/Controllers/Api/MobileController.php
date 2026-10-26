<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Dapur;
use App\Models\FcmToken;
use App\Models\User;
use App\Services\GeolocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MobileController extends Controller
{
    public function __construct(
        protected GeolocationService $geolocation
    ) {}

    /**
     * Store FCM token for push notifications.
     */
    public function storeFcmToken(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'platform' => ['required', 'in:android,ios,web'],
        ]);

        // Update or create FCM token for current user
        FcmToken::updateOrCreate(
            ['user_id' => Auth::id(), 'token' => $validated['token']],
            [
                'platform' => $validated['platform'],
                'last_used_at' => now(),
            ]
        );

        // Clean up old tokens (keep max 3 per user)
        $oldTokens = FcmToken::where('user_id', Auth::id())
            ->orderBy('last_used_at', 'desc')
            ->offset(3)
            ->limit(PHP_INT_MAX)
            ->pluck('id');

        FcmToken::whereIn('id', $oldTokens)->delete();

        return response()->json(['status' => 'ok']);
    }

    /**
     * Sync pending locations from background geolocation.
     */
    public function syncLocations(Request $request)
    {
        $validated = $request->validate([
            'locations' => ['required', 'array'],
            'locations.*.lat' => ['required', 'numeric', 'between:-90,90'],
            'locations.*.lng' => ['required', 'numeric', 'between:-180,180'],
            'locations.*.accuracy' => ['nullable', 'numeric', 'min:0'],
            'locations.*.timestamp' => ['required', 'integer'],
            'locations.*.speed' => ['nullable', 'numeric', 'min:0'],
        ]);

        $synced = 0;
        $errors = [];

        foreach ($validated['locations'] as $loc) {
            try {
                // Find dapur within range (geofence check)
                $dapur = $this->findDapurNearLocation($loc['lat'], $loc['lng']);

                if ($dapur) {
                    // Check if user already has recent absensi for this dapur
                    $existing = Absensi::where('user_id', Auth::id())
                        ->where('dapur_id', $dapur->id)
                        ->whereDate('created_at', today())
                        ->lockForUpdate()->first();

                    if (! $existing) {
                        // Auto check-in if within geofence
                        Absensi::create([
                            'user_id' => Auth::id(),
                            'dapur_id' => $dapur->id,
                            'check_in_at' => now(),
                            'lat' => $loc['lat'],
                            'lng' => $loc['lng'],
                            'status' => 'hadir',
                        ]);
                        $synced++;
                    }
                }
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        return response()->json([
            'synced' => $synced,
            'errors' => $errors,
        ]);
    }

    /**
     * Get geofences for all dapurs (for background geolocation).
     */
    public function getGeofences(Request $request)
    {
        $dapurs = Dapur::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereNotNull('geofence_radius')
            ->get(['id', 'nama', 'latitude', 'longitude', 'geofence_radius']);

        $geofences = $dapurs->map(function ($dapur) {
            return [
                'id' => "dapur_{$dapur->id}",
                'identifier' => "dapur_{$dapur->id}",
                'latitude' => (float) $dapur->latitude,
                'longitude' => (float) $dapur->longitude,
                'radius' => (float) ($dapur->geofence_radius ?? 100),
                'notifyOnEntry' => true,
                'notifyOnExit' => true,
                'notifyOnDwell' => false,
            ];
        });

        return response()->json([
            'geofences' => $geofences,
        ]);
    }

    /**
     * Auto check-in via geofence trigger.
     */
    public function autoCheckin(Request $request)
    {
        $validated = $request->validate([
            'dapur_id' => ['required', 'exists:dapurs,id'],
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'trigger' => ['required', 'in:entry,exit,dwell'],
        ]);

        $dapur = Dapur::find($validated['dapur_id']);

        if ($validated['trigger'] === 'entry') {
            // Check if already checked in today
            $existing = Absensi::where('user_id', Auth::id())
                ->where('dapur_id', $dapur->id)
                ->whereDate('created_at', today())
                ->lockForUpdate()->first();

            if (! $existing) {
                $absensi = Absensi::create([
                    'user_id' => Auth::id(),
                    'dapur_id' => $dapur->id,
                    'check_in_at' => now(),
                    'lat' => $validated['lat'],
                    'lng' => $validated['lng'],
                    'status' => 'hadir',
                ]);

                return response()->json([
                    'status' => 'checked_in',
                    'absensi_id' => $absensi->id,
                ]);
            }

            return response()->json(['status' => 'already_checked_in']);
        }

        if ($validated['trigger'] === 'exit') {
            // Auto check-out on exit
            $absensi = Absensi::where('user_id', Auth::id())
                ->where('dapur_id', $dapur->id)
                ->whereDate('created_at', today())
                ->whereNull('check_out_at')
                ->lockForUpdate()->first();

            if ($absensi) {
                $absensi->update(['check_out_at' => now()]);

                return response()->json([
                    'status' => 'checked_out',
                    'absensi_id' => $absensi->id,
                ]);
            }

            return response()->json(['status' => 'no_active_checkin']);
        }

        return response()->json(['status' => 'ignored']);
    }

    /**
     * Find dapur within geofence radius of given location.
     */
    private function findDapurNearLocation(float $lat, float $lng): ?Dapur
    {
        $dapurs = Dapur::whereNotNull("latitude")
            ->whereNotNull("longitude")
            ->whereNotNull("geofence_radius")
            ->get();

        return $this->geolocation->findNearest($dapurs, $lat, $lng);
    }
}