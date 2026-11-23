<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dapur;
use App\Models\FcmToken;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MobileController extends Controller
{
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
     * Auto check-in via geofence trigger - now creates Laporan instead of Absensi.
     */
    public function autoCheckin(Request $request)
    {
        $validated = $request->validate([
            'dapur_id' => ['required', 'exists:dapurs,id'],
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'trigger' => ['required', 'in:entry,exit,dwell'],
        ]);

        if (! in_array($validated['trigger'], ['entry', 'exit'], true)) {
            return response()->json(['status' => 'ignored']);
        }

        // Transaksi: check-then-insert supaya geofence yang terkirim berulang tidak membuat laporan ganda.
        return DB::transaction(function () use ($validated) {
            $dapur = Dapur::find($validated['dapur_id']);

            $laporan = Laporan::where('created_by', Auth::id())
                ->where('dapur_id', $dapur->id)
                ->whereDate('created_at', today())
                ->lockForUpdate()->first();

            if ($validated['trigger'] === 'entry') {
                if ($laporan) {
                    return response()->json(['status' => 'already_checked_in']);
                }

                $laporan = Laporan::create([
                    'created_by' => Auth::id(),
                    'dapur_id' => $dapur->id,
                    'latitude' => $validated['lat'],
                    'longitude' => $validated['lng'],
                    'status' => 'diperiksa',
                ]);

                return response()->json([
                    'status' => 'checked_in',
                    'laporan_id' => $laporan->id,
                ]);
            }

            if (! $laporan || $laporan->status === 'selesai') {
                return response()->json(['status' => 'no_active_checkin']);
            }

            $laporan->update(['status' => 'selesai']);

            return response()->json([
                'status' => 'checked_out',
                'laporan_id' => $laporan->id,
            ]);
        });
    }
}
