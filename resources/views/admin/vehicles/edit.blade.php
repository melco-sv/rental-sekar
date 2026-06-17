@extends('layouts.admin')
@section('title', 'Edit Kendaraan')

@section('content')
<div class="mb-5">
    <a href="{{ route('admin.vehicles.index') }}" class="text-purple-600 hover:underline text-sm">← Kembali</a>
    <h2 class="text-xl font-bold text-gray-900 mt-1">Edit: {{ $vehicle->name }}</h2>
</div>

@php $existingPhotos = $vehicle->allPhotos(); @endphp

<div class="max-w-2xl">
    <form action="{{ route('admin.vehicles.update', $vehicle) }}" method="POST" enctype="multipart/form-data"
          class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 space-y-5"
          x-data="editPhotoManager({{ json_encode($existingPhotos) }})">
        @csrf
        @method('PUT')

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-2xl p-4">
                <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        {{-- Basic Fields --}}
        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Nama Kendaraan *</label>
                <input type="text" name="name" value="{{ old('name', $vehicle->name) }}" required
                       class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-sm focus:ring-2 focus:ring-purple-400 outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Tipe *</label>
                <input type="text" name="type" value="{{ old('type', $vehicle->type) }}" required
                       class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-sm focus:ring-2 focus:ring-purple-400 outline-none">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Plat Nomor *</label>
                <input type="text" name="plate_number" value="{{ old('plate_number', $vehicle->plate_number) }}" required
                       class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-sm font-mono focus:ring-2 focus:ring-purple-400 outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Harga Per Hari (Rp) *</label>
                <input type="number" name="price_per_day" value="{{ old('price_per_day', $vehicle->price_per_day) }}" min="0" required
                       class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-sm focus:ring-2 focus:ring-purple-400 outline-none">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Status *</label>
            <select name="status" class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-sm focus:ring-2 focus:ring-purple-400 outline-none">
                <option value="available" {{ old('status', $vehicle->status) == 'available' ? 'selected' : '' }}>✅ Tersedia</option>
                <option value="rented" {{ old('status', $vehicle->status) == 'rented' ? 'selected' : '' }}>🚗 Disewa</option>
                <option value="maintenance" {{ old('status', $vehicle->status) == 'maintenance' ? 'selected' : '' }}>🔧 Perawatan</option>
            </select>
        </div>

        {{-- ===== EXISTING PHOTOS MANAGEMENT ===== --}}
        <div class="border border-gray-100 rounded-2xl p-4">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">📸 Foto Saat Ini</label>
                    <p class="text-xs text-gray-400 mt-0.5">Klik tombol <span class="text-red-500 font-semibold">🗑️ Hapus</span> di bawah foto untuk menghapusnya</p>
                </div>
                {{-- Counter for photos to be removed --}}
                <div x-show="toRemove.length > 0" x-cloak
                     class="px-3 py-1 rounded-full text-xs font-bold text-red-700 bg-red-50 border border-red-200">
                    <span x-text="toRemove.length"></span> akan dihapus
                </div>
            </div>

            {{-- Photos Grid --}}
            <template x-if="currentPhotos.length > 0">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <template x-for="(photo, i) in currentPhotos" :key="photo">
                        <div class="relative rounded-2xl overflow-hidden border-2 transition-all duration-200"
                             :class="toRemove.includes(photo)
                                ? 'border-red-400 opacity-60'
                                : (i === 0 ? 'border-purple-400' : 'border-gray-200')">

                            {{-- Photo --}}
                            <div class="aspect-video">
                                <img :src="'/storage/' + photo"
                                     class="w-full h-full object-cover">
                            </div>

                            {{-- Labels & Actions --}}
                            <div class="p-2 bg-gray-50 flex items-center justify-between gap-2">
                                {{-- Label --}}
                                <div>
                                    <template x-if="i === 0">
                                        <span class="px-2 py-0.5 rounded-full text-xs font-bold text-purple-700 bg-purple-100">
                                            🏠 Eksterior
                                        </span>
                                    </template>
                                    <template x-if="i > 0">
                                        <span class="px-2 py-0.5 rounded-full text-xs font-bold text-blue-700 bg-blue-100">
                                            🪑 Interior
                                        </span>
                                    </template>
                                </div>

                                {{-- Delete / Undo button --}}
                                <template x-if="!toRemove.includes(photo)">
                                    <button type="button"
                                            @click="toRemove.push(photo)"
                                            class="flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 transition-all duration-200 hover:scale-105">
                                        🗑️ Hapus
                                    </button>
                                </template>
                                <template x-if="toRemove.includes(photo)">
                                    <button type="button"
                                            @click="toRemove = toRemove.filter(p => p !== photo)"
                                            class="flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-bold text-green-600 bg-green-50 hover:bg-green-100 border border-green-200 transition-all duration-200">
                                        ↩️ Batal
                                    </button>
                                </template>
                            </div>

                            {{-- "Will be deleted" overlay --}}
                            <template x-if="toRemove.includes(photo)">
                                <div class="absolute inset-0 bg-red-500/20 flex items-center justify-center pointer-events-none"
                                     style="top: 0; bottom: 42px;">
                                    <div class="bg-red-600 text-white text-xs font-black px-3 py-1 rounded-full rotate-[-10deg] shadow-lg">
                                        AKAN DIHAPUS
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </template>

            <template x-if="currentPhotos.length === 0">
                <div class="text-center py-6 text-gray-400">
                    <p class="text-3xl mb-2">📷</p>
                    <p class="text-sm">Belum ada foto untuk kendaraan ini.</p>
                </div>
            </template>

            {{-- Hidden inputs for photos to remove — sent to server --}}
            <template x-for="path in toRemove" :key="path">
                <input type="hidden" name="remove_photos[]" :value="path">
            </template>

            {{-- Warning when all photos will be deleted --}}
            <template x-if="toRemove.length > 0 && toRemove.length === currentPhotos.length">
                <div class="mt-3 bg-amber-50 border border-amber-200 rounded-xl p-3 flex items-center gap-2">
                    <span class="text-lg">⚠️</span>
                    <p class="text-amber-800 text-xs font-medium">
                        Semua foto akan dihapus. Pastikan kamu upload foto baru di bawah.
                    </p>
                </div>
            </template>
        </div>

        {{-- ===== ADD NEW PHOTOS ===== --}}
        <div x-data="photoUploader()" @drop.prevent="handleDrop($event)" @dragover.prevent>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">
                ➕ Tambah Foto Baru
                <span class="normal-case text-gray-400 font-normal ml-1">(opsional — akan ditambahkan ke foto yang tersisa)</span>
            </label>

            <div class="border-2 border-dashed border-gray-200 rounded-2xl p-5 text-center cursor-pointer hover:border-purple-400 transition-colors"
                 :class="previews.length > 0 ? 'border-purple-300 bg-purple-50/30' : ''"
                 @click="$refs.photoInput.click()">
                <input type="file" name="new_photos[]" multiple accept="image/jpeg,image/jpg,image/png"
                       class="hidden" x-ref="photoInput"
                       @change="handleFiles($event.target.files)">
                <template x-if="previews.length === 0">
                    <div>
                        <div class="text-3xl mb-1">🖼️</div>
                        <p class="text-sm font-semibold text-gray-600">Klik atau drag & drop foto baru</p>
                        <p class="text-xs text-gray-400 mt-0.5">JPG, PNG · Maks 2MB per foto · Hingga 6 foto</p>
                    </div>
                </template>
                <template x-if="previews.length > 0">
                    <p class="text-xs text-purple-600 font-semibold">
                        <span x-text="previews.length"></span> foto baru siap diupload
                        <span class="text-gray-400 font-normal ml-1">— klik untuk tambah lagi</span>
                    </p>
                </template>
            </div>

            {{-- New photos preview --}}
            <div x-show="previews.length > 0" x-cloak class="mt-3 grid grid-cols-3 sm:grid-cols-6 gap-2">
                <template x-for="(p, i) in previews" :key="i">
                    <div class="relative rounded-xl overflow-hidden border-2 border-blue-300">
                        <div class="aspect-square">
                            <img :src="p" class="w-full h-full object-cover">
                        </div>
                        <div class="p-1 bg-blue-50 flex items-center justify-between">
                            <span class="text-xs font-bold text-blue-600">NEW</span>
                            <button type="button" @click.stop="removePreview(i)"
                                    class="text-xs text-red-500 hover:text-red-700 font-bold">✕</button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Deskripsi</label>
            <textarea name="description" rows="3"
                      class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-sm focus:ring-2 focus:ring-purple-400 outline-none resize-none">{{ old('description', $vehicle->description) }}</textarea>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-6 py-2.5 rounded-2xl font-bold text-white text-sm transition-all hover:-translate-y-0.5"
                    style="background: linear-gradient(135deg, #7C3AED, #2563EB);">
                💾 Simpan Perubahan
            </button>
            <a href="{{ route('admin.vehicles.index') }}" class="border border-gray-200 text-gray-600 px-6 py-2.5 rounded-2xl font-medium text-sm hover:bg-gray-50 transition-all">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function editPhotoManager(initial) {
    return {
        currentPhotos: initial,
        toRemove: [],
    };
}

function photoUploader() {
    return {
        previews: [],
        files: [],
        handleFiles(newFiles) {
            Array.from(newFiles).forEach(f => {
                if (this.files.length >= 6) return;
                if (!f.type.match(/image\/(jpeg|jpg|png)/)) return;
                this.files.push(f);
                const reader = new FileReader();
                reader.onload = e => this.previews.push(e.target.result);
                reader.readAsDataURL(f);
            });
            this.syncInput();
        },
        removePreview(i) {
            this.previews.splice(i, 1);
            this.files.splice(i, 1);
            this.syncInput();
        },
        handleDrop(e) { this.handleFiles(e.dataTransfer.files); },
        syncInput() {
            const input = this.$refs.photoInput;
            const dt = new DataTransfer();
            this.files.forEach(f => dt.items.add(f));
            input.files = dt.files;
        }
    };
}
</script>
@endpush
