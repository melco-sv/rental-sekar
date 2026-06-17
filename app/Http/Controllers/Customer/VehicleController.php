<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $filterStart = $request->filter_start ?: null;
        $filterEnd   = $request->filter_end   ?: null;

        // Validasi: jika hanya satu tanggal diisi, abaikan keduanya
        if (!$filterStart || !$filterEnd) {
            $filterStart = null;
            $filterEnd   = null;
        }

        $query = Vehicle::query();

        if ($filterStart && $filterEnd) {
            // ── Mode filter tanggal ──────────────────────────────────────────
            // Tampilkan SEMUA non-maintenance yang tidak punya booking konflik
            $busyIds = Booking::whereIn('status', ['pending', 'confirmed', 'active'])
                ->where(function ($q) use ($filterStart, $filterEnd) {
                    $q->whereBetween('start_date', [$filterStart, $filterEnd])
                      ->orWhereBetween('end_date', [$filterStart, $filterEnd])
                      ->orWhere(fn ($q2) => $q2
                          ->where('start_date', '<=', $filterStart)
                          ->where('end_date', '>=', $filterEnd)
                      );
                })
                ->pluck('vehicle_id');

            $query->where('status', '!=', 'maintenance')
                  ->whereNotIn('id', $busyIds);
        } else {
            // ── Mode default: tampilkan available + rented ──────────────────
            $query->whereIn('status', ['available', 'rented'])
                  ->with(['bookings' => fn ($q) => $q
                      ->whereIn('status', ['pending', 'confirmed', 'active'])
                      ->where('end_date', '>=', today())
                      ->orderBy('end_date', 'desc')
                      ->select('id', 'vehicle_id', 'start_date', 'end_date', 'status')
                  ]);
        }

        $vehicles = $query
            ->when($request->search, fn ($q) => $q->where('name', 'like', '%' . $request->search . '%'))
            ->when($request->type,   fn ($q) => $q->where('type', $request->type))
            ->paginate(9)
            ->withQueryString();

        $types = Vehicle::whereIn('status', ['available', 'rented'])
            ->select('type')->distinct()->pluck('type');

        return view('customer.vehicles.index', compact(
            'vehicles',
            'types',
            'filterStart',
            'filterEnd'
        ));
    }

    public function show(Vehicle $vehicle)
    {
        if ($vehicle->trashed()) {
            abort(404);
        }

        $bookedRanges = $vehicle->bookings()
            ->whereIn('status', ['pending', 'confirmed', 'active'])
            ->where('end_date', '>=', today())
            ->orderBy('start_date')
            ->get(['start_date', 'end_date', 'status']);

        $latestEnd = $bookedRanges->max('end_date');
        $nextAvailableDate = $latestEnd
            ? Carbon::parse($latestEnd)->addDay()
            : null;

        $similarVehicles = Vehicle::whereIn('status', ['available', 'rented'])
            ->where('type', $vehicle->type)
            ->where('id', '!=', $vehicle->id)
            ->take(3)
            ->get();

        return view('customer.vehicles.show', compact(
            'vehicle',
            'bookedRanges',
            'nextAvailableDate',
            'similarVehicles'
        ));
    }
}
