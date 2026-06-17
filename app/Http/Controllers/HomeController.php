<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;

class HomeController extends Controller
{
    public function index()
    {
        // Redirect user yang sudah login langsung ke dashboard role-nya
        if (auth()->check()) {
            return match (auth()->user()->role) {
                'admin'  => redirect()->route('admin.dashboard'),
                'owner'  => redirect()->route('owner.dashboard'),
                default  => redirect()->route('customer.dashboard'),
            };
        }

        $vehicles = Vehicle::whereIn('status', ['available', 'rented'])->latest()->take(6)->get();

        return view('welcome', compact('vehicles'));
    }
}
