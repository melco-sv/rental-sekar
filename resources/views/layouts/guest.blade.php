<!DOCTYPE html>
<html lang="id" style="color-scheme: light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen" style="background: #0f0c29;">

    {{-- Animated Background --}}
    <div class="fixed inset-0 z-0">
        <div class="absolute inset-0 animate-gradient" style="background: linear-gradient(-45deg, #0f0c29, #302b63, #24243e, #0f0c29); background-size: 400% 400%;"></div>

        {{-- Blobs --}}
        <div class="blob absolute w-96 h-96 opacity-20 -top-20 -left-20"
             style="background: linear-gradient(135deg, #7C3AED, #2563EB);"></div>
        <div class="blob-2 absolute w-72 h-72 opacity-15 bottom-10 right-10"
             style="background: linear-gradient(135deg, #EC4899, #F97316); animation-delay: 2s;"></div>
        <div class="blob absolute w-56 h-56 opacity-10 top-1/2 left-1/2"
             style="background: linear-gradient(135deg, #06B6D4, #7C3AED); animation-delay: 4s;"></div>

        {{-- Grid --}}
        <div class="absolute inset-0 opacity-5"
             style="background-image: linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 50px 50px;"></div>
    </div>

    <div class="relative z-10 min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            {{-- Logo --}}
            <div class="text-center mb-8 animate-slide-up">
                <a href="{{ route('home') }}" class="inline-flex flex-col items-center gap-2 group">
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-3xl mb-1 group-hover:scale-110 transition-transform duration-300"
                         style="background: linear-gradient(135deg, #7C3AED, #2563EB); box-shadow: 0 8px 32px rgba(124,58,237,0.4);">
                        🚗
                    </div>
                    <span class="text-2xl font-bold text-white">{{ config('app.name') }}</span>
                    <span class="text-xs text-purple-300 font-medium tracking-widest uppercase">Rental Kendaraan</span>
                </a>
            </div>

            {{-- Card --}}
            <div class="glass rounded-3xl p-8 animate-slide-up animation-delay-100"
                 style="box-shadow: 0 25px 80px rgba(0,0,0,0.4);">
                {{ $slot }}
            </div>

            <p class="text-center text-purple-400 text-xs mt-6 animate-fade-in animation-delay-300">
                &copy; {{ date('Y') }} {{ config('app.name') }} — All rights reserved
            </p>
        </div>
    </div>
</body>
</html>
