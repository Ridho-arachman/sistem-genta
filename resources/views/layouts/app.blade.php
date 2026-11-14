@extends('layouts.base', ['pageTitle' => $pageTitle ?? config('app.name'), 'bodyClass' => 'bg-white'])

@section('header')
<x-navbar-guest />
@endsection
