@extends('layouts.admin')
@section('title', 'Tambah Kendaraan')

@section('content')
<div class="mb-5">
    <a href="{{ route('admin.vehicles.index') }}" class="text-purple-600 hover:underline text-sm">← Kembali</a>
    <h2 class="text-xl font-bold text-gray-900 mt-1">Tambah Kendaraan Baru</h2>
</div>

<div class="max-w-2xl">
    <form action="{{ route('admin.vehicles.store') }}" method="POST" enctype="multipart/form-data"
          class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 space-y-5">
        @csrf

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-2xl p-4">
                <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Nama Kendaraan *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-sm focus:ring-2 focus:ring-purple-400 outline-none @error('name') border-red-400 @enderror">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Tipe *</label>
                <input type="text" name="type" value="{{ old('type') }}" placeholder="MPV, SUV, Hatchback..." required
                       class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-sm focus:ring-2 focus:ring-purple-400 outline-none">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Plat Nomor *</label>
                <input type="text" name="plate_number" value="{{ old('plate_number') }}" placeholder="B 1234 ABC" required
                       class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-sm font-mono focus:ring-2 focus:ring-purple-400 outline-none @error('plate_number') border-red-400 @enderror">
                @error('plate_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Harga Per Hari (Rp) *</label>
                <input type="number" name="price_per_day" value="{{ old('price_per_day') }}" min="0" required
                       class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-sm focus:ring-2 focus:ring-purple-400 outline-none @error('price_per_day') border-red-400 @enderror">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Status *</label>
            <select name="status" class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-sm focus:ring-2 focus:ring-purple-400 outline-none">
                <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>✅ Tersedia</option>
                <option value="rented" {{ old('status') == 'rented' ? 'selected' : '' }}>🚗 Disewa</option>
                <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>🔧 Perawatan</option>
            </select>
        </div>

        {{-- Multi-photo upload --}}
        <div x-data="photoUploader()" @drop.prevent="handleDrop($event)" @dragover.prevent>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">
                Foto Kendaraan
                <span class="normal-case text-purple-500 ml-1">(Maks 6 foto — Foto pertama = Eksterior utama)</span>
            </label>

            {{-- Drop Zone --}}
            <div class="border-2 border-dashed border-gray-200 rounded-2xl p-6 text-center cursor-pointer hover:border-purple-400 transition-colors"
                 :class="previews.length > 0 ? 'border-purple-300 bg-purple-50/30' : ''"
                 @click="$refs.photoInput.click()">
                <input type="file" name="photos[]" multiple accept="image/jpeg,image/jpg,image/png"
                       class="hidden" x-ref="photoInput"
                       @change="handleFiles($event.target.files)">

                <template x-if="previews.length === 0">
                    <div>
                        <div class="text-4xl mb-2">📷</div>
                        <p class="text-sm font-semibold text-gray-600">Klik atau drag & drop foto</p>
                        <p class="text-xs text-gray-400 mt-1">JPG, PNG · Maks 2MB per foto · Hingga 6 foto</p>
                        <p class="text-xs text-purple-500 mt-1 font-medium">⭐ Foto pertama = tampilan utama (Eksterior)</p>
                    </div>
                </template>
                <template x-if="previews.length > 0">
                    <div>
                        <p class="text-xs text-purple-600 font-semibold mb-1">
                            <span x-text="previews.length"></span> foto dipilih
                            <span class="text-gray-400 font-normal ml-1">— klik untuk tambah lagi</span>
                        </p>
                    </div>
                </template>
            </div>

            {{-- Preview Grid --}}
            <div x-show="previews.length > 0" x-cloak class="mt-3 grid grid-cols-3 sm:grid-cols-6 gap-2">
                <template x-for="(p, i) in previews" :key="i">
                    <div class="relative group rounded-xl overflow-hidden aspect-square border-2"
                         :class="i === 0 ? 'border-purple-500' : 'border-gray-200'">
                        <img :src="p" class="w-full h-full object-cover">
                        <template x-if="i === 0">
                            <div class="absolute top-1 left-1 px-1.5 py-0.5 rounded text-white text-xs font-bold"
                                 style="background:rgba(124,58,237,0.8)">EXT</div>
                        </template>
                        <template x-if="i > 0">
                            <div class="absolute top-1 left-1 px-1.5 py-0.5 rounded text-white text-xs font-bold"
                                 style="background:rgba(59,130,246,0.8)">INT</div>
                        </template>
                        <button type="button" @click.stop="removePreview(i)"
                                class="absolute top-1 right-1 w-5 h-5 bg-red-500 text-white rounded-full text-xs items-center justify-center hidden group-hover:flex transition-all">✕</button>
                        <div class="absolute bottom-1 right-1 text-white text-xs font-bold"
                             style="text-shadow: 0 1px 2px rgba(0,0,0,0.8)" x-text="(i+1)"></div>
                    </div>
                </template>
            </div>

            @error('photos.*')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Deskripsi</label>
            <textarea name="description" rows="3" placeholder="Deskripsi singkat kendaraan..."
                      class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-sm focus:ring-2 focus:ring-purple-400 outline-none resize-none">{{ old('description') }}</textarea>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-6 py-2.5 rounded-2xl font-bold text-white text-sm transition-all hover:-translate-y-0.5"
                    style="background: linear-gradient(135deg, #7C3AED, #2563EB);">Simpan Kendaraan</button>
            <a href="{{ route('admin.vehicles.index') }}" class="border border-gray-200 text-gray-600 px-6 py-2.5 rounded-2xl font-medium text-sm hover:bg-gray-50 transition-all">Batal</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function photoUploader() {
    return {
        previews: [],
        files: [],
        handleFiles(newFiles) {
            const maxCount = 6;
            const arr = Array.from(newFiles);
            arr.forEach(f => {
                if (this.files.length >= maxCount) return;
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
        handleDrop(e) {
            this.handleFiles(e.dataTransfer.files);
        },
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
