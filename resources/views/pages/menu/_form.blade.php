<div class="space-y-5">
    <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Kategori</label>
        <select name="id_kategori" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            <option value="">-- Pilih kategori --</option>
            @foreach ($kategori as $k)
                <option value="{{ $k->id_kategori }}" @selected(old('id_kategori', $menu->id_kategori ?? '') == $k->id_kategori)>
                    {{ $k->nama_kategori }}
                </option>
            @endforeach
        </select>
        @error('id_kategori')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nama Menu</label>
        <input type="text" name="nama_menu" value="{{ old('nama_menu', $menu->nama_menu ?? '') }}"
            placeholder="Contoh: Nasi Goreng Spesial" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
        @error('nama_menu')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Deskripsi</label>
        <textarea name="deskripsi" rows="3" placeholder="Opsional"
            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">{{ old('deskripsi', $menu->deskripsi ?? '') }}</textarea>
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Harga (Rp)</label>
        <input type="number" name="harga" min="0" step="500" value="{{ old('harga', isset($menu) ? (int) $menu->harga : '') }}"
            placeholder="Contoh: 25000" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
        @error('harga')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Foto (opsional, maks. 2 MB)</label>
        @if (!empty($menu?->foto))
            <img src="{{ asset('storage/' . $menu->foto) }}" alt="Foto sekarang" class="mb-3 h-24 w-24 rounded-lg object-cover">
        @endif
        <input type="file" name="foto" accept="image/*"
            class="block w-full text-sm text-gray-700 file:mr-4 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-brand-600 dark:text-gray-400" />
        @error('foto')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-400">
            <input type="checkbox" name="tersedia" value="1" class="h-4 w-4 accent-brand-500"
                @checked(old() ? old('tersedia') : ($menu->tersedia ?? true))>
            Menu tersedia (bisa dipesan)
        </label>
    </div>

    <div class="flex gap-3">
        <button type="submit" class="h-11 rounded-lg bg-brand-500 px-5 text-sm font-medium text-white hover:bg-brand-600">
            Simpan
        </button>
        <a href="{{ route('menu.index') }}"
            class="flex h-11 items-center rounded-lg border border-gray-300 px-5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">
            Batal
        </a>
    </div>
</div>