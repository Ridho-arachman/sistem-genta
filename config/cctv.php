<?php

return [
    // Base URL HLS dari media server (MediaMTX), mis. http://mediamtx.local:8888.
    // URL stream diturunkan dari stream_key kamera.
    'hls_base' => env('CCTV_HLS_BASE'),

    'mediamtx' => [
        // Control API MediaMTX, mis. http://mediamtx.local:9997. Kosong = dinonaktifkan.
        'api' => env('CCTV_MEDIAMTX_API'),
        'username' => env('CCTV_MEDIAMTX_USER'),
        'password' => env('CCTV_MEDIAMTX_PASS'),
    ],

    // Timeout probe RTSP (detik) saat health check.
    'probe_timeout' => (int) env('CCTV_PROBE_TIMEOUT', 8),

    'ffprobe' => env('CCTV_FFPROBE', 'ffprobe'),
];
