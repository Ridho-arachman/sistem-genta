<?php

return [
    // Base URL HLS dari media server (MediaMTX), mis. http://mediamtx.local:8888.
    // URL stream diturunkan dari stream_key kamera.
    'hls_base' => env('CCTV_HLS_BASE'),

    // Base URL WebRTC dari MediaMTX (mengaktifkan WHEP), mis. http://mediamtx.local:8889.
    // Saat kosong, profiler hanya memakai HLS.
    'webrtc_base' => env('CCTV_WEBRTC_BASE', 'http://127.0.0.1:8889'),

    'mediamtx' => [
        // Control API MediaMTX, mis. http://mediamtx.local:9997. Kosong = dinonaktifkan.
        'api' => env('CCTV_MEDIAMTX_API'),
        'username' => env('CCTV_MEDIAMTX_USER'),
        'password' => env('CCTV_MEDIAMTX_PASS'),
        // Alamat RTSP MediaMTX untuk path turunan (kamera dengan RTSP URL sama).
        'rtsp' => env('CCTV_MEDIAMTX_RTSP', 'rtsp://127.0.0.1:8554'),
    ],

    // Timeout probe RTSP (detik) saat health check.
    'probe_timeout' => (int) env('CCTV_PROBE_TIMEOUT', 8),

    'ffprobe' => env('CCTV_FFPROBE', 'ffprobe'),
];
