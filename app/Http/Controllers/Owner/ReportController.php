<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->year ?? now()->year;

        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $revenue = Payment::where('status', 'verified')
                ->whereHas('booking', fn ($q) => $q
                    ->where('status', 'completed')
                    ->whereMonth('created_at', $i)
                    ->whereYear('created_at', $year))
                ->sum('amount');

            $count = Booking::where('status', 'completed')
                ->whereMonth('created_at', $i)
                ->whereYear('created_at', $year)
                ->count();

            $monthlyData[$i] = [
                'revenue' => (float) $revenue,
                'count'   => $count,
            ];
        }

        $yearlyRevenue = array_sum(array_column($monthlyData, 'revenue'));

        $years = range(now()->year, now()->year - 4);

        return view('owner.reports.index', compact('monthlyData', 'yearlyRevenue', 'year', 'years'));
    }

    public function exportPdf(Request $request)
    {
        $year = $request->year ?? now()->year;

        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $revenue = Payment::where('status', 'verified')
                ->whereHas('booking', fn ($q) => $q
                    ->where('status', 'completed')
                    ->whereMonth('created_at', $i)
                    ->whereYear('created_at', $year))
                ->sum('amount');

            $count = Booking::where('status', 'completed')
                ->whereMonth('created_at', $i)
                ->whereYear('created_at', $year)
                ->count();

            $monthlyData[$i] = [
                'revenue' => (float) $revenue,
                'count'   => $count,
            ];
        }

        $yearlyRevenue = array_sum(array_column($monthlyData, 'revenue'));

        $pdf = Pdf::loadView('owner.reports.pdf', compact('monthlyData', 'yearlyRevenue', 'year'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('laporan-keuangan-' . $year . '.pdf');
    }
}
