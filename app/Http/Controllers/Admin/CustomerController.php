<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = User::where('role', 'customer')
            ->when($request->search, fn ($q, $search) =>
                $q->where(fn ($sub) =>
                    $sub->where('name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%")
                )
            )
            ->withCount('bookings')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $user)
    {
        abort_if(! $user->isCustomer(), 404);

        $user->load('bookings.vehicle');

        return view('admin.customers.show', [
            'user'         => $user,
            'bookingCount' => $user->bookings->count(),
        ]);
    }
}
