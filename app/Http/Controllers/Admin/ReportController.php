<?php

namespace App\Http\Controllers\Admin;

use App\Exports\BookingsExport;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->month;
        $year  = $request->year ?? now()->year;

        // Booking selesai + eager load SEMUA payments (bukan hanya payment pertama)
        $bookings = Booking::with(['user', 'vehicle', 'payments'])
            ->where('status', 'completed')
            ->when($month, fn ($q) => $q->whereMonth('created_at', $month))
            ->when($year, fn ($q) => $q->whereYear('created_at', $year))
            ->latest()
            ->get();

        // Total dari verified payments milik completed bookings (sinkron dengan tabel)
        $totalRevenue = Payment::where('status', 'verified')
            ->whereHas('booking', fn ($q) => $q
                ->where('status', 'completed')
                ->when($month, fn ($q) => $q->whereMonth('created_at', $month))
                ->whereYear('created_at', $year))
            ->sum('amount');

        return view('admin.reports.index', compact('bookings', 'totalRevenue', 'month', 'year'));
    }

    public function exportPdf(Request $request)
    {
        $month = $request->month;
        $year  = $request->year ?? now()->year;

        $bookings = Booking::with(['user', 'vehicle', 'payments'])
            ->where('status', 'completed')
            ->when($month, fn ($q) => $q->whereMonth('created_at', $month))
            ->when($year, fn ($q) => $q->whereYear('created_at', $year))
            ->latest()
            ->get();

        // Total dari verified payments milik completed bookings (sinkron dengan admin index)
        $totalRevenue = Payment::where('status', 'verified')
            ->whereHas('booking', fn ($q) => $q
                ->where('status', 'completed')
                ->when($month, fn ($q) => $q->whereMonth('created_at', $month))
                ->whereYear('created_at', $year))
            ->sum('amount');

        $pdf = Pdf::loadView('admin.reports.pdf', compact('bookings', 'totalRevenue', 'month', 'year'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-transaksi-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $month = $request->month;
        $year  = $request->year ?? now()->year;

        return Excel::download(
            new BookingsExport($month ? (int) $month : null, (int) $year),
            'laporan-transaksi-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}
