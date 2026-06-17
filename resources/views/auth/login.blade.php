<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-white mb-1">Selamat Datang 👋</h2>
        <p class="text-purple-300 text-sm">Masuk ke akun kamu</p>
    </div>

    <x-auth-session-status class="mb-4 text-green-400 text-sm text-center" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-xs font-semibold text-purple-300 mb-1.5 uppercase tracking-wider">Email</label>
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-purple-400">📧</span>
                <input id="email" type="email" name="email" :value="old('email')" required autofocus
                       class="w-full pl-11 pr-4 py-3 rounded-2xl text-sm text-white placeholder-purple-400 input-glow transition-all duration-200
                              @error('email') border-red-500 @enderror"
                       style="background: rgba(255,255,255,0.08); border: 1px solid rgba(139,92,246,0.3);"
                       placeholder="email@kamu.com">
            </div>
            @error('email')
                <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">⚠️ {{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-purple-300 mb-1.5 uppercase tracking-wider">Password</label>
            <div class="relative" x-data="{ show: false }">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-purple-400">🔒</span>
                <input id="password" :type="show ? 'text' : 'password'" name="password" required
                       class="w-full pl-11 pr-12 py-3 rounded-2xl text-sm text-white placeholder-purple-400 input-glow transition-all duration-200"
                       style="background: rgba(255,255,255,0.08); border: 1px solid rgba(139,92,246,0.3);"
                       placeholder="••••••••">
                <button type="button" @click="show = !show"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-purple-400 hover:text-purple-200 transition-colors text-sm">
                    <span x-text="show ? '🙈' : '👁️'"></span>
                </button>
            </div>
            @error('password')
                <p class="text-red-400 text-xs mt-1.5">⚠️ {{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember" class="w-4 h-4 rounded border-purple-500 text-purple-600 bg-transparent">
                <span class="text-purple-300 text-xs">Ingat saya</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-xs text-purple-400 hover:text-purple-200 transition-colors">Lupa password?</a>
            @endif
        </div>

        <button type="submit" class="ripple w-full py-3 rounded-2xl text-sm font-bold text-white transition-all duration-300 hover:-translate-y-0.5"
                style="background: linear-gradient(135deg, #7C3AED, #2563EB); box-shadow: 0 8px 32px rgba(124,58,237,0.4);">
            <span class="relative z-10">Masuk ✨</span>
        </button>

        <div class="relative my-4">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-white/10"></div>
            </div>
            <div class="relative flex justify-center">
                <span class="px-3 text-xs text-purple-400" style="background: transparent;">atau</span>
            </div>
        </div>

        <p class="text-center text-purple-300 text-sm">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-bold text-purple-200 hover:text-white transition-colors">Daftar sekarang 🚀</a>
        </p>
    </form>
</x-guest-layout>
