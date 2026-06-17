<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; margin: 0; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #2563eb; padding-bottom: 12px; margin-bottom: 20px; }
        .header h1 { font-size: 18px; color: #1d4ed8; margin: 0 0 4px; }
        .header p { margin: 2px 0; color: #555; font-size: 11px; }
        .summary { display: flex; gap: 20px; margin-bottom: 16px; }
        .summary-box { background: #eff6ff; border: 1px solid #bfdbfe; padding: 10px 16px; border-radius: 6px; flex: 1; }
        .summary-box .label { font-size: 10px; color: #6b7280; margin-bottom: 2px; }
        .summary-box .value { font-size: 16px; font-weight: bold; color: #1d4ed8; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { background: #1d4ed8; color: white; padding: 8px 10px; text-align: left; font-size: 11px; }
        td { padding: 7px 10px; border-bottom: 1px solid #e5e7eb; font-size: 11px; }
        tr:nth-child(even) { background: #f9fafb; }
        .total-row { background: #eff6ff !important; font-weight: bold; border-top: 2px solid #2563eb; }
        .footer { margin-top: 24px; text-align: right; font-size: 10px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🚗 Rental Mobil Sekar</h1>
        <p>Laporan Transaksi Pemesanan</p>
        @if($month || $year)
            <p>Periode:
                @if($month) {{ ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][$month] }} @endif
                {{ $year }}
            </p>
        @endif
        <p>Dicetak: {{ now()->format('d F Y H:i') }}</p>
    </div>

    <div class="summary">
        <div class="summary-box">
            <div class="label">Total Transaksi</div>
            <div class="value">{{ $bookings->count() }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Total Pendapatan</div>
            <div class="value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Pelanggan</th>
                <th>Kendaraan</th>
                <th>Tgl Mulai</th>
                <th>Tgl Selesai</th>
                <th>Hari</th>
                <th>Perpanjangan</th>
                <th>Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bookings as $i => $booking)
                @php
                    $extConfirmed = $booking->extensions->where('status','confirmed');
                    $extDays      = $extConfirmed->sum('additional_days');
                    $extPrice     = $extConfirmed->sum('additional_price');
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $booking->user->name }}</td>
                    <td>{{ $booking->vehicle->name }}</td>
                    <td>{{ $booking->start_date->format('d/m/Y') }}</td>
                    <td>{{ $booking->end_date->format('d/m/Y') }}</td>
                    <td>{{ $booking->total_days }}</td>
                    <td style="color: {{ $extDays > 0 ? '#7C3AED' : '#9CA3AF' }}; font-size:11px;">
                        {{ $extDays > 0 ? '+' . $extDays . ' hari (+Rp ' . number_format($extPrice, 0, ',', '.') . ')' : '—' }}
                    </td>
                    <td><strong>{{ number_format($booking->total_price, 0, ',', '.') }}</strong></td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="7" style="text-align:right">TOTAL PENDAPATAN</td>
                <td>{{ number_format($totalRevenue, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini digenerate otomatis oleh Sistem Informasi Rental Mobil Sekar
    </div>
</body>
</html>
