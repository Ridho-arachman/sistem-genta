<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $filename ?? 'Laporan Absensi' }} - {{ config('app.name') }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: white; color: black; padding: 20px; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #E50000; padding-bottom: 15px; }
        .header h1 { color: #E50000; font-size: 20px; font-weight: 700; }
        .header p { color: #666; font-size: 12px; margin-top: 5px; }
        .info { display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 11px; color: #333; }
        table { width: 100%; border-collapse: collapse; font-size: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background: #E50000; color: white; font-weight: 600; }
        tr:nth-child(even) { background: #f9f9f9; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 10px; font-size: 9px; font-weight: 600; }
        .badge-hadir { background: #dcfce7; color: #166534; }
        .badge-izin { background: #fef3c7; color: #92400e; }
        .badge-cuti { background: #f3f4f6; color: #374151; }
        .badge-tidak_hadir { background: #fee2e2; color: #991b1b; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; }
        @media print { body { padding: 0; } .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>GENTA Pangan Banten</h1>
        <p>Laporan Absensi Pengawas</p>
    </div>

    <div class="info">
        <div>Periode: {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : 'Semua' }} - {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : 'Sekarang' }}</div>
        <div>Dapur: {{ $dapurId ? \App\Models\Dapur::find($dapurId)?->nama ?? 'Semua' : 'Semua' }}</div>
        <div>Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Pengawas</th>
                <th>Dapur</th>
                <th>Check-in</th>
                <th>Check-out</th>
                <th>Status</th>
                <th>Lokasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($absensis as $index => $absensi)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $absensi->created_at->format('d/m/Y') }}</td>
                <td>{{ $absensi->user->name }}</td>
                <td>{{ $absensi->dapur->nama }}</td>
                <td>{{ $absensi->check_in_at ? $absensi->check_in_at->format('H:i:s') : '-' }}</td>
                <td>{{ $absensi->check_out_at ? $absensi->check_out_at->format('H:i:s') : '-' }}</td>
                <td><span class="badge badge-{{ $absensi->status }}">{{ ucfirst($absensi->status) }}</span></td>
                <td>{{ $absensi->lat && $absensi->lng ? $absensi->lat . ', ' . $absensi->lng : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak otomatis oleh sistem GENTA Pangan Banten</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>