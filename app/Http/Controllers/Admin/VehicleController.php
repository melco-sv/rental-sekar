<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreVehicleRequest;
use App\Http\Requests\Admin\UpdateVehicleRequest;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $vehicles = Vehicle::withTrashed()
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->search, fn ($q) => $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('plate_number', 'like', '%' . $request->search . '%'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        return view('admin.vehicles.create');
    }

    public function store(StoreVehicleRequest $request)
    {
        $data = $request->validated();
        unset($data['photos']);

        $uploadedPhotos = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $uploadedPhotos[] = $file->store('vehicles', 'public');
            }
        }

        $data['photos'] = $uploadedPhotos ?: null;
        $data['photo']  = $uploadedPhotos[0] ?? null;

        Vehicle::create($data);

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    public function edit(Vehicle $vehicle)
    {
        return view('admin.vehicles.edit', compact('vehicle'));
    }

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle)
    {
        $data = $request->safe()->except(['new_photos', 'remove_photos']);

        // Start from existing photos
        $currentPhotos = $vehicle->photos ?? [];

        // Remove photos that were marked for deletion
        $toRemove = $request->input('remove_photos', []);
        foreach ($toRemove as $path) {
            Storage::disk('public')->delete($path);
            $currentPhotos = array_values(array_filter($currentPhotos, fn ($p) => $p !== $path));
        }

        // Add new uploaded photos
        if ($request->hasFile('new_photos')) {
            foreach ($request->file('new_photos') as $file) {
                $currentPhotos[] = $file->store('vehicles', 'public');
            }
        }

        $data['photos'] = $currentPhotos ?: null;
        $data['photo']  = $currentPhotos[0] ?? null;

        $vehicle->update($data);

        return redirect()->route('admin.vehicles.edit', $vehicle)
            ->with('success', 'Kendaraan berhasil diperbarui.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Kendaraan berhasil dihapus.');
    }

    public function restore($id)
    {
        Vehicle::withTrashed()->findOrFail($id)->restore();

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Kendaraan berhasil dipulihkan.');
    }
}
