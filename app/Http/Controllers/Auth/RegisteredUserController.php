<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'phone'          => ['required', 'string', 'max:20'],
            'address'        => ['required', 'string', 'max:500'],
            'id_card_number' => ['nullable', 'string', 'max:20'],
            'password'       => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'name.required'    => 'Nama lengkap harus diisi.',
            'email.required'   => 'Email harus diisi.',
            'email.unique'     => 'Email sudah terdaftar.',
            'phone.required'   => 'Nomor telepon harus diisi.',
            'address.required' => 'Alamat harus diisi.',
        ]);

        $user = User::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'phone'          => $request->phone,
            'address'        => $request->address,
            'id_card_number' => $request->id_card_number,
            'role'           => 'customer',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('customer.dashboard');
    }
}
