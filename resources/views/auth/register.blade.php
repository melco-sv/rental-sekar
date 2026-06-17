<x-guest-layout>
    <div class="text-center mb-5">
        <h2 class="text-2xl font-bold text-white mb-1">Buat Akun Baru 🚀</h2>
        <p class="text-purple-300 text-sm">Bergabung & mulai sewa kendaraan</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-3.5">
        @csrf

        {{-- Name & Phone --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <div>
                <label class="block text-xs font-semibold text-purple-300 mb-1.5 uppercase tracking-wider">Nama <span class="text-red-400">*</span></label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-purple-400 text-sm">👤</span>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus
                           class="w-full pl-10 pr-3 py-3 rounded-2xl text-sm text-white placeholder-purple-400 input-glow @error('name') ring-2 ring-red-500 @enderror"
                           style="background: rgba(255,255,255,0.08); border: 1px solid rgba(139,92,246,0.3);"
                           placeholder="Nama lengkap">
                </div>
                @error('name')<p class="text-red-400 text-xs mt-1">⚠️ {{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-purple-300 mb-1.5 uppercase tracking-wider">Telepon <span class="text-red-400">*</span></label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-purple-400 text-sm">📱</span>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required
                           class="w-full pl-10 pr-3 py-3 rounded-2xl text-sm text-white placeholder-purple-400 input-glow @error('phone') ring-2 ring-red-500 @enderror"
                           style="background: rgba(255,255,255,0.08); border: 1px solid rgba(139,92,246,0.3);"
                           placeholder="08xxxxxxxxxx">
                </div>
                @error('phone')<p class="text-red-400 text-xs mt-1">⚠️ {{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Email --}}
        <div>
            <label class="block text-xs font-semibold text-purple-300 mb-1.5 uppercase tracking-wider">Email <span class="text-red-400">*</span></label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-purple-400 text-sm">📧</span>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full pl-10 pr-3 py-3 rounded-2xl text-sm text-white placeholder-purple-400 input-glow @error('email') ring-2 ring-red-500 @enderror"
                       style="background: rgba(255,255,255,0.08); border: 1px solid rgba(139,92,246,0.3);"
                       placeholder="email@kamu.com">
            </div>
            @error('email')<p class="text-red-400 text-xs mt-1">⚠️ {{ $message }}</p>@enderror
        </div>

        {{-- Address (REQUIRED) --}}
        <div>
            <label class="block text-xs font-semibold text-purple-300 mb-1.5 uppercase tracking-wider">Alamat <span class="text-red-400">*</span></label>
            <div class="relative">
                <span class="absolute left-3.5 top-3 text-purple-400 text-sm">📍</span>
                <textarea name="address" rows="2" required
                          class="w-full pl-10 pr-3 py-3 rounded-2xl text-sm text-white placeholder-purple-400 input-glow resize-none @error('address') ring-2 ring-red-500 @enderror"
                          style="background: rgba(255,255,255,0.08); border: 1px solid rgba(139,92,246,0.3);"
                          placeholder="Alamat lengkap kamu...">{{ old('address') }}</textarea>
            </div>
            @error('address')<p class="text-red-400 text-xs mt-1">⚠️ {{ $message }}</p>@enderror
        </div>

        {{-- KTP (OPTIONAL) --}}
        <div>
            <label class="block text-xs font-semibold text-purple-300 mb-1.5 uppercase tracking-wider">
                Nomor KTP <span class="text-purple-500 normal-case text-xs">(opsional)</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-purple-400 text-sm">🪪</span>
                <input type="text" name="id_card_number" value="{{ old('id_card_number') }}"
                       class="w-full pl-10 pr-3 py-3 rounded-2xl text-sm text-white placeholder-purple-400 input-glow"
                       style="background: rgba(255,255,255,0.08); border: 1px solid rgba(139,92,246,0.3);"
                       placeholder="16 digit nomor KTP (bisa diisi nanti)">
            </div>
            @error('id_card_number')<p class="text-red-400 text-xs mt-1">⚠️ {{ $message }}</p>@enderror
        </div>

        {{-- Password --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <div x-data="{ show: false }">
                <label class="block text-xs font-semibold text-purple-300 mb-1.5 uppercase tracking-wider">Password <span class="text-red-400">*</span></label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-purple-400 text-sm">🔒</span>
                    <input :type="show ? 'text' : 'password'" name="password" required
                           class="w-full pl-10 pr-10 py-3 rounded-2xl text-sm text-white placeholder-purple-400 input-glow @error('password') ring-2 ring-red-500 @enderror"
                           style="background: rgba(255,255,255,0.08); border: 1px solid rgba(139,92,246,0.3);"
                           placeholder="Min 8 karakter">
                    <button type="button" @click="show=!show" class="absolute right-3 top-1/2 -translate-y-1/2 text-purple-400 text-xs">
                        <span x-text="show?'🙈':'👁️'"></span>
                    </button>
                </div>
                @error('password')<p class="text-red-400 text-xs mt-1">⚠️ {{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-purple-300 mb-1.5 uppercase tracking-wider">Konfirmasi <span class="text-red-400">*</span></label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-purple-400 text-sm">🔑</span>
                    <input type="password" name="password_confirmation" required
                           class="w-full pl-10 pr-3 py-3 rounded-2xl text-sm text-white placeholder-purple-400 input-glow"
                           style="background: rgba(255,255,255,0.08); border: 1px solid rgba(139,92,246,0.3);"
                           placeholder="Ulangi password">
                </div>
            </div>
        </div>

        <button type="submit" class="ripple w-full py-3.5 rounded-2xl text-sm font-bold text-white transition-all duration-300 hover:-translate-y-0.5 mt-1"
                style="background: linear-gradient(135deg, #7C3AED, #2563EB); box-shadow: 0 8px 32px rgba(124,58,237,0.4);">
            Daftar Sekarang 🚀
        </button>

        <p class="text-center text-purple-300 text-sm pt-1">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-bold text-purple-200 hover:text-white transition-colors">Masuk ✨</a>
        </p>
    </form>
</x-guest-layout>
