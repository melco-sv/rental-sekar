<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan {{ $year }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; margin: 0; padding: 24px; }
        .header { text-align: center; border-bottom: 2px solid #4f46e5; padding-bottom: 14px; margin-bottom: 24px; }
        .header h1 { font-size: 18px; color: #4338ca; margin: 0 0 4px; }
        .header p { margin: 2px 0; color: #6b7280; font-size: 11px; }
        .summary { background: #eef2ff; border: 1px solid #c7d2fe; padding: 12px 20px; border-radius: 6px; margin-bottom: 20px; text-align: center; }
        .summary .label { font-size: 11px; color: #6b7280; margin-bottom: 4px; }
        .summary .value { font-size: 22px; font-weight: bold; color: #4338ca; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #4338ca; color: white; padding: 9px 12px; text-align: left; font-size: 11px; }
        td { padding: 8px 12px; border-bottom: 1px solid #e5e7eb; font-size: 11px; }
        tr:nth-child(even) { background: #f5f3ff; }
        .total-row { background: #eef2ff !important; font-weight: bold; border-top: 2px solid #4338ca; }
        .footer { margin-top: 24px; text-align: right; font-size: 10px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🚗 Rental Mobil Sekar</h1>
        <p>Laporan Keuangan Tahunan</p>
        <p>Periode: Januari – Desember {{ $year }}</p>
        <p>Dicetak: {{ now()->format('d F Y H:i') }}</p>
    </div>

    <div class="summary">
        <div class="label">Total Pendapatan {{ $year }}</div>
        <div class="value">Rp {{ number_format($yearlyRevenue, 0, ',', '.') }}</div>
    </div>

    @php
        $months = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    @endphp

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Bulan</th>
                <th>Jumlah Transaksi</th>
                <th>Pendapatan (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($monthlyData as $m => $data)
                <tr>
                    <td>{{ $m }}</td>
                    <td>{{ $months[$m] }}</td>
                    <td>{{ $data['count'] }}</td>
                    <td>{{ number_format($data['revenue'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2" style="text-align:right">TOTAL</td>
                <td>{{ array_sum(array_column($monthlyData, 'count')) }}</td>
                <td>{{ number_format($yearlyRevenue, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Laporan ini digenerate otomatis oleh Sistem Informasi Rental Mobil Sekar
    </div>
</body>
</html>
