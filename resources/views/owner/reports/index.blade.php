@extends('layouts.owner')

@section('title', 'Laporan Keuangan')

@section('content')
<div class="flex items-center justify-between mb-5">
    <h2 class="text-xl font-bold text-gray-900">Laporan Keuangan</h2>
    <a href="{{ route('owner.reports.pdf', ['year' => $year]) }}" target="_blank"
       class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700 transition font-medium">📄 Export PDF</a>
</div>

{{-- Year Filter --}}
<form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-5">
    <div class="flex gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Tahun</label>
            <select name="year" class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                @foreach($years as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-indigo-700 transition">Tampilkan</button>
    </div>
</form>

{{-- Summary --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-5">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-gray-500 text-sm">Total Pendapatan {{ $year }}</p>
            <p class="text-3xl font-bold text-indigo-600 mt-1">Rp {{ number_format($yearlyRevenue, 0, ',', '.') }}</p>
        </div>
        <div class="text-5xl">💰</div>
    </div>
</div>

{{-- Monthly Table --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-900">Rincian Pendapatan Per Bulan {{ $year }}</h3>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
            <tr>
                <th class="px-5 py-3 text-left">Bulan</th>
                <th class="px-5 py-3 text-left">Jumlah Transaksi</th>
                <th class="px-5 py-3 text-left">Pendapatan</th>
                <th class="px-5 py-3 text-left">Grafik</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @php
                $months = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                $maxRevenue = max(array_column($monthlyData, 'revenue') ?: [1]);
            @endphp
            @foreach($monthlyData as $m => $data)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-medium text-gray-900">{{ $months[$m] }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $data['count'] }} transaksi</td>
                    <td class="px-5 py-3 font-semibold {{ $data['revenue'] > 0 ? 'text-indigo-600' : 'text-gray-400' }}">
                        Rp {{ number_format($data['revenue'], 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-3">
                        @if($data['revenue'] > 0)
                            <div class="bg-gray-200 rounded-full h-2 w-32">
                                <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ ($data['revenue'] / $maxRevenue) * 100 }}%"></div>
                            </div>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="bg-indigo-50 font-bold">
                <td class="px-5 py-3 text-indigo-900">TOTAL</td>
                <td class="px-5 py-3 text-indigo-900">{{ array_sum(array_column($monthlyData, 'count')) }} transaksi</td>
                <td class="px-5 py-3 text-indigo-600 text-base">Rp {{ number_format($yearlyRevenue, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>
@endsection
