<div class="space-y-5">
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Pelanggan</label>
            <select name="id_user" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                <option value="">-- Pilih pelanggan --</option>
                @foreach ($pelanggan as $p)
                    <option value="{{ $p->id_user }}" @selected(old('id_user', $reservasi->id_user ?? '') == $p->id_user)>
                        {{ $p->nama }}{{ $p->no_hp ? ' (' . $p->no_hp . ')' : '' }}
                    </option>
                @endforeach
            </select>
            @error('id_user')
                <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Jumlah Orang</label>
            <input type="number" name="jumlah_orang" min="1" value="{{ old('jumlah_orang', $reservasi->jumlah_orang ?? '') }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
            @error('jumlah_orang')
                <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tanggal</label>
            <input type="date" name="tanggal" value="{{ old('tanggal', $reservasi->tanggal ?? '') }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
            @error('tanggal')
                <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Jam</label>
            <input type="time" name="jam" value="{{ old('jam', isset($reservasi) ? substr($reservasi->jam, 0, 5) : '') }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
            @error('jam')
                <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Meja (opsional)</label>
            <select name="id_meja" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                <option value="">-- Pilih nanti --</option>
                @foreach ($meja as $m)
                    <option value="{{ $m->id_meja }}" @selected(old('id_meja', $reservasi->id_meja ?? '') == $m->id_meja)>
                        Meja {{ $m->nomor_meja }} ({{ $m->kapasitas }} orang) - {{ $m->status }}
                    </option>
                @endforeach
            </select>
            @error('id_meja')
                <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
            @enderror
        </div>

        @isset($reservasi)
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Status</label>
                <select name="status" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    @foreach (['menunggu' => 'Menunggu', 'dikonfirmasi' => 'Dikonfirmasi', 'selesai' => 'Selesai (tamu sudah datang)', 'dibatalkan' => 'Dibatalkan'] as $nilai => $label)
                        <option value="{{ $nilai }}" @selected(old('status', $reservasi->status) == $nilai)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status')
                    <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                @enderror
            </div>
        @endisset
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Catatan (opsional)</label>
        <textarea name="catatan" rows="2" placeholder="Contoh: ulang tahun, minta dekat jendela" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">{{ old('catatan', $reservasi->catatan ?? '') }}</textarea>
    </div>

    <div class="flex gap-3">
        <button type="submit" class="h-11 rounded-lg bg-brand-500 px-5 text-sm font-medium text-white hover:bg-brand-600">
            Simpan
        </button>
        <a href="{{ route('reservasi.index') }}"
            class="flex h-11 items-center rounded-lg border border-gray-300 px-5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">
            Batal
        </a>
    </div>
</div>