@extends('layouts.customer')

@section('title', 'Edit Profil')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Profil Saya</h2>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Profile Info --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-900 mb-5 text-lg">Informasi Pribadi</h3>

        <form action="{{ route('customer.profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            @if(session('success') && !session('password_changed'))
                <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-lg mb-4 text-sm">{{ session('success') }}</div>
            @endif

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none @error('name') border-red-500 @enderror">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none @error('email') border-red-500 @enderror">
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                    <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor KTP</label>
                    <input type="text" name="id_card_number" value="{{ old('id_card_number', $user->id_card_number) }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <textarea name="address" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none resize-none">{{ old('address', $user->address) }}</textarea>
                </div>
            </div>

            <button type="submit" class="mt-5 w-full bg-blue-600 text-white py-2.5 rounded-xl font-medium hover:bg-blue-700 transition">
                Simpan Perubahan
            </button>
        </form>
    </div>

    {{-- Password Change --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-900 mb-5 text-lg">Ganti Password</h3>

        <form action="{{ route('customer.profile.password') }}" method="POST">
            @csrf
            @method('PUT')

            @if(session('success') && session('password_changed'))
                <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-lg mb-4 text-sm">{{ session('success') }}</div>
            @endif

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini <span class="text-red-500">*</span></label>
                    <input type="password" name="current_password"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none @error('current_password') border-red-500 @enderror">
                    @error('current_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru <span class="text-red-500">*</span></label>
                    <input type="password" name="password"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none @error('password') border-red-500 @enderror">
                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    <p class="text-xs text-gray-400 mt-1">Minimal 8 karakter.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
            </div>

            <button type="submit" class="mt-5 w-full bg-gray-800 text-white py-2.5 rounded-xl font-medium hover:bg-gray-900 transition">
                Ganti Password
            </button>
        </form>
    </div>
</div>
@endsection
