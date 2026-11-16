<?php

use App\Services\MediaMtxClient;
use Illuminate\Support\Facades\Http;

test('tidak melakukan apa pun saat control api belum dikonfigurasi', function () {
    config(['cctv.mediamtx.api' => null]);
    Http::fake();

    $client = new MediaMtxClient;

    expect($client->addPath('abc', 'rtsp://cam/1'))->toBeFalse()
        ->and($client->isReady('abc'))->toBeNull();

    Http::assertNothingSent();
});

test('mendaftarkan path sebagai pull on-demand', function () {
    config(['cctv.mediamtx.api' => 'http://mediamtx.test:9997']);
    Http::fake(['mediamtx.test:9997/*' => Http::response([], 200)]);

    expect((new MediaMtxClient)->addPath('abc', 'rtsp://cam/1'))->toBeTrue();

    Http::assertSent(fn ($request) => $request->url() === 'http://mediamtx.test:9997/v3/config/paths/replace/abc'
        && $request['source'] === 'rtsp://cam/1'
        && $request['sourceOnDemand'] === true);
});

test('mendaftarkan path mode push saat tanpa rtsp url', function () {
    config(['cctv.mediamtx.api' => 'http://mediamtx.test:9997']);
    Http::fake(['mediamtx.test:9997/*' => Http::response([], 200)]);

    (new MediaMtxClient)->addPath('abc');

    Http::assertSent(fn ($request) => $request['source'] === 'publisher'
        && $request['sourceOnDemand'] === false);
});

test('membaca status ready dan menganggap path hilang sebagai belum siap', function () {
    config(['cctv.mediamtx.api' => 'http://mediamtx.test:9997']);
    Http::fake([
        'mediamtx.test:9997/v3/paths/get/live' => Http::response(['ready' => true], 200),
        'mediamtx.test:9997/v3/paths/get/missing' => Http::response([], 404),
    ]);

    $client = new MediaMtxClient;

    expect($client->isReady('live'))->toBeTrue()
        ->and($client->isReady('missing'))->toBeFalse();
});
