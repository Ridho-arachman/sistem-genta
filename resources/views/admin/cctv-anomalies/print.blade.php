@extends('admin.layouts.app', ['pageTitle' => 'Cetak Laporan Anomali'])

@section('content')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $filename ?? 'Laporan Anomali CCTV' }} - {{ config('app.name') }}</title>
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
        .badge-tinggi { background: #fee2e2; color: #991b1b; }
        .badge-sedang { background: #fef3c7; color: #92400e; }
        .badge-rendah { background: #f3f4f6; color: #374151; }
        .badge-baru { background: #fee2e2; color: #991b1b; }
        .badge-diproses { background: #fef3c7; color: #92400e; }
        .badge-selesai { background: #dcfce7; color: #166534; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; }
        @media print { body { padding: 0; } .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>GENTA Pangan Banten</h1>
        <p>Laporan Anomali CCTV</p>
    </div>

    <div class="info">
        <div>Periode: {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : 'Semua' }} - {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : 'Sekarang' }}</div>
        <div>Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>CCTV</th>
                <th>Dapur</th>
                <th>Area/Kamera</th>
                <th>Deskripsi</th>
                <th>Severity</th>
                <th>Status</th>
                <th>Dilaporkan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($anomalies as $index => $anomaly)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $anomaly->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $anomaly->cctv->display_name }}</td>
                <td>{{ $anomaly->cctv->dapur->nama }}</td>
                <td>{{ $anomaly->cctv->section ?? '-' }} / Cam {{ $anomaly->cctv->camera_number ?? '-' }}</td>
                <td>{{ $anomaly->description }}</td>
                <td><span class="badge badge-{{ $anomaly->severity }}">{{ ucfirst($anomaly->severity) }}</span></td>
                <td><span class="badge badge-{{ $anomaly->status }}">{{ ucfirst($anomaly->status) }}</span></td>
                <td>{{ $anomaly->admin->name }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak otomatis oleh sistem GENTA Pangan Banten</p>
    </div>

    <script>
        window.onload = function() { window.print(); };
    </script>
</body>
</html>
@endsection