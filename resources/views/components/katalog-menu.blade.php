{{--
    Katalog menu + keranjang. Dipakai di halaman pesan QR meja dan halaman Pesan Antar/Ambil.
    Contoh:
    <x-katalog-menu :menu-per-kategori="$menuPerKategori" :action="route('pesan.store', $meja->kode_qr)">
        (isi tambahan di atas daftar menu, boleh kosong)
    </x-katalog-menu>
--}}
@props([
    'menuPerKategori',
    'action',
    'judulTombol' => 'Pesan Sekarang',
    'keteranganBayar' => 'Pembayaran dilakukan di kasir.',
    'atas' => 'top-0',            // posisi menempel tab kategori (top-16 kalau ada bar atas)
    'grid' => 'sm:grid-cols-2',   // jumlah kolom kartu menu
    'lebar' => 'max-w-2xl',       // lebar bar keranjang di bawah
])

@php
    $semuaMenu = $menuPerKategori->flatten(1);
    $infoMenu = $semuaMenu->mapWithKeys(fn ($m) => [$m->id_menu => ['nama' => $m->nama_menu, 'harga' => (int) $m->harga]]);
    $qtyAwal = $semuaMenu->mapWithKeys(fn ($m) => [$m->id_menu => (int) old('jumlah.' . $m->id_menu, 0)]);
    $catatanAwal = $semuaMenu->mapWithKeys(fn ($m) => [$m->id_menu => (string) old('catatan.' . $m->id_menu, '')]);
    $idKategori = $menuPerKategori->keys()->mapWithKeys(fn ($k) => [$k => 'kat-' . \Illuminate\Support\Str::slug($k)]);
    // Nama menu huruf kecil per kategori, untuk pencarian
    $namaPerKategori = $menuPerKategori->map(fn ($daftar) => $daftar->map(fn ($m) => mb_strtolower($m->nama_menu))->values());
@endphp

<form method="POST" action="{{ $action }}"
    x-data="{
        menu: @js($infoMenu),
        qty: @js($qtyAwal),
        catatan: @js($catatanAwal),
        cari: '',
        keranjang: false,
        kategoriAktif: @js($idKategori->first()),
        get idDipilih() {
            return Object.keys(this.qty).filter(id => this.qty[id] > 0);
        },
        get jumlahItem() {
            return this.idDipilih.reduce((t, id) => t + this.qty[id], 0);
        },
        get total() {
            return this.idDipilih.reduce((t, id) => t + this.qty[id] * this.menu[id].harga, 0);
        },
        tambah(id) {
            this.qty[id] = Math.min(50, (this.qty[id] || 0) + 1);
        },
        kurang(id) {
            this.qty[id] = Math.max(0, (this.qty[id] || 0) - 1);
            if (this.jumlahItem === 0) this.keranjang = false;
        },
        cocok(nama) {
            return nama.includes(this.cari.trim().toLowerCase());
        },
        rupiah(n) {
            return 'Rp ' + n.toLocaleString('id-ID');
        },
        lompat(id) {
            this.kategoriAktif = id;
            const el = document.getElementById(id);
            window.scrollTo({ top: el.getBoundingClientRect().top + window.scrollY - 140, behavior: 'smooth' });
        },
        pantauGulir() {
            let aktif = this.kategoriAktif;
            this.$root.querySelectorAll('[data-kategori]').forEach(el => {
                if (el.getBoundingClientRect().top < 160) aktif = el.id;
            });
            this.kategoriAktif = aktif;
        }
    }"
    @scroll.window.throttle.100ms="pantauGulir()">
    @csrf

    {{ $slot }}

    @if ($semuaMenu->isEmpty())
        <p class="px-5 py-16 text-center text-gray-500">Maaf, belum ada menu yang tersedia saat ini.</p>
    @else
        <!-- Cari + tab kategori (menempel di atas saat digulir) -->
        <div class="sticky {{ $atas }} z-30 -mx-4 border-b border-gray-200 bg-gray-50/95 px-4 pt-3 pb-2 backdrop-blur sm:mx-0 sm:rounded-b-2xl dark:border-gray-800 dark:bg-gray-950/95">
            <div class="relative">
                <input type="text" x-model="cari" placeholder="Cari menu, contoh: nasi goreng"
                    class="h-11 w-full rounded-xl border border-gray-200 bg-white pr-4 pl-10 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden dark:border-gray-800 dark:bg-gray-900 dark:text-white/90">
                <svg class="pointer-events-none absolute top-1/2 left-3.5 size-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z" />
                </svg>
            </div>
            <div class="no-scrollbar mt-2 flex gap-2 overflow-x-auto" x-show="cari === ''">
                @foreach ($idKategori as $nama => $id)
                    <button type="button" @click="lompat('{{ $id }}')"
                        :class="kategoriAktif === '{{ $id }}' ? 'bg-brand-500 text-white border-brand-500' : 'bg-white text-gray-600 border-gray-200 dark:bg-gray-900 dark:text-gray-400 dark:border-gray-800'"
                        class="shrink-0 rounded-full border px-4 py-1.5 text-sm font-medium">
                        {{ $nama }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Daftar menu per kategori -->
        @foreach ($menuPerKategori as $namaKategori => $daftarMenu)
            <section id="{{ $idKategori[$namaKategori] }}" data-kategori class="pt-6"
                x-show="cari === '' || @js($namaPerKategori[$namaKategori]).some(n => cocok(n))">
                <h2 class="mb-3 text-base font-semibold text-gray-800 dark:text-white/90">
                    {{ $namaKategori }}
                    <span class="text-sm font-normal text-gray-400">· {{ $daftarMenu->count() }}</span>
                </h2>

                <div class="grid grid-cols-1 gap-3 {{ $grid }}">
                    @foreach ($daftarMenu as $item)
                        @php $id = $item->id_menu; @endphp
                        <div x-show="cocok(@js(mb_strtolower($item->nama_menu)))"
                            :class="qty[{{ $id }}] > 0 ? 'border-brand-400 ring-1 ring-brand-400' : 'border-gray-200 dark:border-gray-800'"
                            class="flex gap-3 rounded-2xl border bg-white p-3 transition dark:bg-white/[0.03]">
                            @if ($item->foto)
                                <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->nama_menu }}" loading="lazy"
                                    class="h-24 w-24 shrink-0 rounded-xl object-cover">
                            @else
                                <div class="flex h-24 w-24 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-3xl font-semibold text-brand-400 dark:bg-brand-500/10">
                                    {{ mb_substr($item->nama_menu, 0, 1) }}
                                </div>
                            @endif

                            <div class="flex min-w-0 flex-1 flex-col">
                                <p class="font-medium text-gray-800 dark:text-white/90">{{ $item->nama_menu }}</p>
                                @if ($item->deskripsi)
                                    <p class="mt-0.5 line-clamp-2 text-xs text-gray-500 dark:text-gray-400">{{ $item->deskripsi }}</p>
                                @endif

                                <div class="mt-auto flex items-center justify-between gap-2 pt-2">
                                    <span class="text-sm font-semibold text-gray-800 dark:text-white/90">Rp {{ number_format($item->harga, 0, ',', '.') }}</span>

                                    <!-- Tombol Tambah, berubah jadi - jumlah + setelah dipilih -->
                                    <button type="button" x-show="! qty[{{ $id }}]" @click="tambah({{ $id }})"
                                        class="h-9 rounded-full border border-brand-500 px-4 text-sm font-semibold text-brand-600 hover:bg-brand-50 dark:text-brand-400 dark:hover:bg-brand-500/10">
                                        + Tambah
                                    </button>
                                    <div x-show="qty[{{ $id }}] > 0" style="display: none" class="flex items-center gap-1 rounded-full bg-brand-500 p-1 text-white">
                                        <button type="button" @click="kurang({{ $id }})" class="flex h-7 w-7 items-center justify-center rounded-full text-lg leading-none hover:bg-white/20" aria-label="Kurangi">&minus;</button>
                                        <span class="w-6 text-center text-sm font-semibold" x-text="qty[{{ $id }}]"></span>
                                        <button type="button" @click="tambah({{ $id }})" class="flex h-7 w-7 items-center justify-center rounded-full text-lg leading-none hover:bg-white/20" aria-label="Tambah">+</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Data yang dikirim ke server -->
                            <input type="hidden" name="jumlah[{{ $id }}]" :value="qty[{{ $id }}] || 0">
                            <input type="hidden" name="catatan[{{ $id }}]" :value="catatan[{{ $id }}] || ''">
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach

        <p x-show="cari !== '' && ! Object.values(menu).some(m => cocok(m.nama.toLowerCase()))" style="display: none"
            class="py-12 text-center text-sm text-gray-500">Menu "<span x-text="cari"></span>" tidak ditemukan.</p>
    @endif

    <!-- Bar keranjang di bawah layar -->
    <div class="fixed inset-x-0 bottom-0 z-40 border-t border-gray-200 bg-white px-4 pt-3 pb-[max(0.75rem,env(safe-area-inset-bottom))] dark:border-gray-800 dark:bg-gray-900">
        <div class="mx-auto {{ $lebar }}">
            <button type="button" @click="keranjang = true" :disabled="jumlahItem === 0"
                class="flex h-14 w-full items-center justify-between rounded-2xl bg-brand-500 px-5 text-white transition hover:bg-brand-600 disabled:bg-gray-200 disabled:text-gray-500 dark:disabled:bg-gray-800">
                <span class="flex items-center gap-3 text-sm">
                    <span class="flex h-8 min-w-8 items-center justify-center rounded-full bg-white/20 px-2 font-semibold" x-text="jumlahItem"></span>
                    <span x-text="jumlahItem === 0 ? 'Belum ada menu dipilih' : 'Lihat keranjang'"></span>
                </span>
                <span class="text-base font-semibold" x-text="rupiah(total)"></span>
            </button>
        </div>
    </div>

    <!-- Keranjang (muncul dari bawah) -->
    <div x-show="keranjang" style="display: none" class="fixed inset-0 z-50 flex items-end justify-center bg-gray-900/60 sm:items-center sm:p-4"
        x-transition.opacity @keydown.escape.window="keranjang = false">
        <div @click.outside="keranjang = false" x-show="keranjang" x-transition:enter="transition duration-200" x-transition:enter-start="translate-y-full sm:translate-y-0 sm:scale-95"
            class="flex max-h-[85vh] w-full flex-col rounded-t-3xl bg-white sm:max-w-lg sm:rounded-3xl dark:bg-gray-900">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4 dark:border-gray-800">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Keranjang</h3>
                <button type="button" @click="keranjang = false" class="text-2xl leading-none text-gray-400 hover:text-gray-600" aria-label="Tutup">&times;</button>
            </div>

            <div class="flex-1 space-y-4 overflow-y-auto px-5 py-4">
                <template x-for="id in idDipilih" :key="id">
                    <div class="border-b border-gray-100 pb-4 last:border-0 dark:border-gray-800">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-medium text-gray-800 dark:text-white/90" x-text="menu[id].nama"></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400" x-text="rupiah(menu[id].harga * qty[id])"></p>
                            </div>
                            <div class="flex items-center gap-1 rounded-full border border-gray-200 p-1 dark:border-gray-700">
                                <button type="button" @click="kurang(id)" class="flex h-7 w-7 items-center justify-center rounded-full text-lg text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5">&minus;</button>
                                <span class="w-6 text-center text-sm font-semibold text-gray-800 dark:text-white/90" x-text="qty[id]"></span>
                                <button type="button" @click="tambah(id)" class="flex h-7 w-7 items-center justify-center rounded-full text-lg text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5">+</button>
                            </div>
                        </div>
                        <input type="text" x-model="catatan[id]" maxlength="255" placeholder="Catatan (opsional), contoh: tidak pedas"
                            class="mt-2 h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden dark:border-gray-700 dark:text-white/90">
                    </div>
                </template>
            </div>

            <div class="border-t border-gray-100 px-5 pt-4 pb-[max(1rem,env(safe-area-inset-bottom))] dark:border-gray-800">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Total (<span x-text="jumlahItem"></span> item)</span>
                    <span class="text-xl font-semibold text-gray-800 dark:text-white/90" x-text="rupiah(total)"></span>
                </div>
                <p class="mt-1 text-xs text-gray-400">{{ $keteranganBayar }}</p>
                <button type="submit" class="mt-4 h-12 w-full rounded-2xl bg-brand-500 text-sm font-semibold text-white hover:bg-brand-600">
                    {{ $judulTombol }}
                </button>
            </div>
        </div>
    </div>
</form>