@php
    $colors = [
        'pending'   => 'bg-yellow-100 text-yellow-700',
        'confirmed' => 'bg-blue-100 text-blue-700',
        'active'    => 'bg-green-100 text-green-700',
        'completed' => 'bg-gray-100 text-gray-700',
        'cancelled' => 'bg-red-100 text-red-700',
    ];
    $labels = [
        'pending'   => 'Menunggu Konfirmasi',
        'confirmed' => 'Dikonfirmasi',
        'active'    => 'Aktif',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];
    $color = $colors[$status] ?? 'bg-gray-100 text-gray-700';
    $label = $labels[$status] ?? ucfirst($status);
@endphp
<span class="px-2 py-1 rounded-full text-xs font-medium {{ $color }}">{{ $label }}</span>
