<?php

namespace App\Exports;

use App\Models\Booking;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BookingsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function __construct(
        protected ?int $month = null,
        protected ?int $year  = null
    ) {}

    public function collection()
    {
        return Booking::with(['user', 'vehicle', 'payments', 'extensions'])
            ->where('status', 'completed')
            ->when($this->month, fn ($q) => $q->whereMonth('created_at', $this->month))
            ->when($this->year,  fn ($q) => $q->whereYear('created_at', $this->year))
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'No', 'ID', 'Pelanggan', 'Kendaraan',
            'Tgl Mulai', 'Tgl Selesai', 'Jumlah Hari',
            'Ada Perpanjangan', '+Hari', '+Biaya Perpanjangan (Rp)',
            'Total Keseluruhan (Rp)',
        ];
    }

    public function map($booking): array
    {
        static $no = 0;
        $no++;

        $confirmedExts = $booking->extensions->where('status', 'confirmed');
        $extDays       = $confirmedExts->sum('additional_days');
        $extPrice      = $confirmedExts->sum('additional_price');

        return [
            $no,
            $booking->id,
            $booking->user->name,
            $booking->vehicle->name . ' (' . $booking->vehicle->plate_number . ')',
            $booking->start_date->format('d/m/Y'),
            $booking->end_date->format('d/m/Y'),
            $booking->total_days . ' hari',
            $confirmedExts->count() > 0 ? 'Ya (' . $confirmedExts->count() . 'x)' : 'Tidak',
            $extDays > 0 ? '+' . $extDays . ' hari' : '-',
            $extPrice > 0 ? number_format($extPrice, 0, ',', '.') : '-',
            number_format($booking->total_price, 0, ',', '.'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
