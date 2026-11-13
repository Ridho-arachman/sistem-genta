@props([])

{{--
    Shell tabel standar halaman admin (table w-full + thead opsional).

    <x-admin-table>
        <x-slot:head>
            <th>NAMA</th>
            <th>PERAN</th>
        </x-slot:head>
        @foreach ($users as $user)
            <tr>...</tr>
        @endforeach
    </x-admin-table>
--}}
<table class="table w-full" {{ $attributes }}>
    @isset($head)
        <thead>
            <tr>{{ $head }}</tr>
        </thead>
    @endisset
    <tbody>
        {{ $slot }}
    </tbody>
</table>