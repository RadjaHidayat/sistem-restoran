@extends('layouts.pelanggan')

@section('isi')
    @php
        $inisial = collect(explode(' ', $user->nama))->filter()->map(fn ($k) => mb_substr($k, 0, 1))->take(2)->implode('');
        $kelasInput = 'h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90';
        $kelasLabel = 'mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400';
        $kelasKartu = 'rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 dark:border-gray-800 dark:bg-white/[0.03]';
        // Buka jendela konfirmasi lagi kalau tadi ada kesalahan di form nonaktifkan akun
        $bukaModal = $errors->has('password_konfirmasi') || $errors->has('setuju');
    @endphp

    <h1 class="text-2xl font-semibold text-gray-800 dark:text-white/90">Profil Saya</h1>
    <p class="text-sm text-gray-500 dark:text-gray-400">Atur data diri, password, dan akunmu.</p>

    @if (session('success'))
        <div class="mt-5 rounded-xl border border-success-500 bg-success-50 px-4 py-3 text-sm text-success-700">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mt-5 rounded-xl border border-error-500 bg-error-50 px-4 py-3 text-sm text-error-700">{{ session('error') }}</div>
    @endif

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Kartu identitas -->
        <div class="{{ $kelasKartu }} h-fit text-center">
            <span class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-brand-500 text-2xl font-semibold text-white uppercase">{{ $inisial }}</span>
            <p class="mt-3 text-lg font-semibold text-gray-800 dark:text-white/90">{{ $user->nama }}</p>
            <p class="text-sm break-all text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
            <span class="mt-2 inline-block rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">Pelanggan</span>

            <div class="mt-5 grid grid-cols-2 gap-3 border-t border-gray-100 pt-5 dark:border-gray-800">
                <div>
                    <p class="text-lg font-semibold text-gray-800 dark:text-white/90">{{ $jumlahPesanan }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total pesanan</p>
                </div>
                <div>
                    <p class="text-lg font-semibold text-gray-800 dark:text-white/90">{{ $user->created_at ? $user->created_at->translatedFormat('M Y') : '-' }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Bergabung</p>
                </div>
            </div>
        </div>

        <div class="space-y-6 lg:col-span-2">
            <!-- Data diri -->
            <form method="POST" action="{{ route('akun.profil.simpan') }}" class="{{ $kelasKartu }}">
                @csrf
                @method('PUT')
                <h2 class="font-semibold text-gray-800 dark:text-white/90">Data diri</h2>
                <p class="mb-5 text-xs text-gray-500 dark:text-gray-400">Alamat dipakai otomatis saat memesan delivery.</p>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="{{ $kelasLabel }}">Nama lengkap</label>
                        <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" class="{{ $kelasInput }}">
                        @error('nama') <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="{{ $kelasLabel }}">No. HP</label>
                        <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" class="{{ $kelasInput }}">
                        @error('no_hp') <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="{{ $kelasLabel }}">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="{{ $kelasInput }}">
                        @error('email') <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p> @enderror
                        <p class="mt-1 text-xs text-gray-400">Kalau email diganti, login berikutnya memakai email baru.</p>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="{{ $kelasLabel }}">Alamat</label>
                        <textarea name="alamat" rows="2" class="{{ $kelasInput }} h-auto">{{ old('alamat', $user->alamat) }}</textarea>
                        @error('alamat') <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-5 flex justify-end">
                    <button type="submit" class="h-11 rounded-lg bg-brand-500 px-6 text-sm font-medium text-white hover:bg-brand-600">Simpan perubahan</button>
                </div>
            </form>

            <!-- Ganti password -->
            <form method="POST" action="{{ route('akun.password') }}" class="{{ $kelasKartu }}">
                @csrf
                @method('PUT')
                <h2 class="font-semibold text-gray-800 dark:text-white/90">Ganti password</h2>
                <p class="mb-5 text-xs text-gray-500 dark:text-gray-400">Minimal 6 karakter. Lupa password lama? Keluar dulu, lalu pakai "Lupa password?" di halaman masuk.</p>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                    <div>
                        <label class="{{ $kelasLabel }}">Password lama</label>
                        <input type="password" name="password_lama" autocomplete="current-password" class="{{ $kelasInput }}">
                        @error('password_lama') <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="{{ $kelasLabel }}">Password baru</label>
                        <input type="password" name="password" autocomplete="new-password" class="{{ $kelasInput }}">
                        @error('password') <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="{{ $kelasLabel }}">Ulangi password baru</label>
                        <input type="password" name="password_confirmation" autocomplete="new-password" class="{{ $kelasInput }}">
                    </div>
                </div>

                <div class="mt-5 flex justify-end">
                    <button type="submit" class="h-11 rounded-lg border border-gray-300 px-6 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">Ganti password</button>
                </div>
            </form>

            <!-- Nonaktifkan akun -->
            <div class="rounded-2xl border border-error-200 bg-white p-5 sm:p-6 dark:border-error-500/30 dark:bg-white/[0.03]" x-data="{ buka: @js($bukaModal) }">
                <h2 class="font-semibold text-error-600">Hapus / nonaktifkan akun</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Akun yang dinonaktifkan tidak bisa dipakai login lagi dan <b>tidak bisa diaktifkan kembali</b>.
                </p>
                <button type="button" @click="buka = true"
                    class="mt-4 h-11 rounded-lg border border-error-500 px-5 text-sm font-medium text-error-600 hover:bg-error-50 dark:hover:bg-error-500/10">
                    Nonaktifkan akun saya
                </button>

                <!-- Jendela konfirmasi -->
                <div x-show="buka" x-transition.opacity style="display: none"
                    class="fixed inset-0 z-50 flex items-end justify-center bg-gray-900/60 p-0 sm:items-center sm:p-4" @keydown.escape.window="buka = false">
                    <form method="POST" action="{{ route('akun.nonaktifkan') }}" @click.outside="buka = false"
                        class="max-h-[90vh] w-full overflow-y-auto rounded-t-3xl bg-white p-6 sm:max-w-lg sm:rounded-3xl dark:bg-gray-900">
                        @csrf
                        @method('DELETE')

                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-error-50 text-2xl dark:bg-error-500/15">⚠️</div>
                        <h3 class="mt-4 text-lg font-semibold text-gray-800 dark:text-white/90">Yakin ingin menonaktifkan akun?</h3>

                        <ul class="mt-3 list-disc space-y-1.5 pl-5 text-sm text-gray-600 dark:text-gray-400">
                            <li>Kamu <b>tidak bisa login</b> lagi dengan akun ini, dan akun ini <b>tidak bisa dipulihkan</b>.</li>
                            <li>Kalau ingin memesan lagi nanti, kamu harus <b>mendaftar akun baru</b> (boleh memakai email yang sama).</li>
                            <li>Reservasi yang masih berjalan akan <b>dibatalkan otomatis</b>.</li>
                            <li>Catatan transaksi pesananmu tetap disimpan restoran untuk keperluan laporan, tapi tidak bisa kamu buka lagi.</li>
                        </ul>

                        <div class="mt-5">
                            <label class="{{ $kelasLabel }}">Masukkan password untuk konfirmasi</label>
                            <input type="password" name="password_konfirmasi" autocomplete="current-password" class="{{ $kelasInput }}">
                            @error('password_konfirmasi') <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p> @enderror
                        </div>

                        <label class="mt-4 flex items-start gap-3 text-sm text-gray-700 dark:text-gray-300">
                            <input type="checkbox" name="setuju" value="1" class="mt-0.5 h-4 w-4 rounded border-gray-300">
                            Saya mengerti bahwa akun ini tidak bisa dipulihkan dan saya harus membuat akun baru untuk memesan lagi.
                        </label>
                        @error('setuju') <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p> @enderror

                        <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                            <button type="button" @click="buka = false"
                                class="h-11 rounded-lg border border-gray-300 px-5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">
                                Batal
                            </button>
                            <button type="submit" class="h-11 rounded-lg bg-error-500 px-5 text-sm font-medium text-white hover:bg-error-600">
                                Ya, nonaktifkan akun
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection