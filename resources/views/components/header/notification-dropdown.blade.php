{{-- Lonceng notifikasi staf. Data diambil dari route('notifikasi') setiap 30 detik. --}}
<div class="contents"
    x-data="{
        item: [],
        jumlah: 0,
        dibaca: JSON.parse(localStorage.getItem('notif_dibaca') || '[]'),
        judulAsli: document.title,
        get adaBelumDilihat() {
            return this.item.some(i => ! this.dibaca.includes(i.kunci));
        },
        async muat() {
            try {
                const res = await fetch('{{ route('notifikasi') }}', { headers: { 'Accept': 'application/json' } });
                if (! res.ok) return;
                const data = await res.json();
                this.item = data.item;
                this.jumlah = data.jumlah;
                document.title = (this.jumlah > 0 ? '(' + this.jumlah + ') ' : '') + this.judulAsli;
            } catch (e) {
                // Server sedang tidak bisa dihubungi, coba lagi di putaran berikutnya
            }
        },
        tandaiDilihat() {
            this.dibaca = this.item.map(i => i.kunci);
            localStorage.setItem('notif_dibaca', JSON.stringify(this.dibaca));
        },
        warna(jenis) {
            return {
                baru: 'bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400',
                siap: 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-500',
                reservasi: 'bg-warning-50 text-warning-700 dark:bg-warning-500/15 dark:text-warning-500',
            }[jenis];
        },
        ikon(jenis) {
            return { baru: '🧾', siap: '🍽️', reservasi: '📅' }[jenis];
        }
    }"
    x-init="muat(); setInterval(() => muat(), 30000)">
    <x-ui.dropdown-menu>
        <x-ui.dropdown-menu.trigger as-child>
            <x-ui.button variant="outline" size="icon-lg" class="relative" aria-label="Notifikasi" x-on:click.capture="tandaiDilihat()">
                {{-- Angka jumlah --}}
                <span x-show="jumlah > 0" x-text="jumlah > 99 ? '99+' : jumlah" style="display: none"
                    class="absolute -end-1.5 -top-1.5 z-10 flex h-5 min-w-5 items-center justify-center rounded-full bg-error-500 px-1 text-[10px] font-semibold text-white"></span>
                {{-- Titik berkedip kalau ada yang belum dilihat --}}
                <span x-show="adaBelumDilihat" style="display: none"
                    class="absolute -start-0.5 -top-0.5 z-10 h-2 w-2 rounded-full bg-orange-400">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-orange-400 opacity-75"></span>
                </span>
                <x-svg.bell class="size-auto!" />
            </x-ui.button>
        </x-ui.dropdown-menu.trigger>

        <x-ui.dropdown-menu.content align="end" :side-offset="12" class="w-87.5 overflow-hidden p-3 sm:w-90.25 dark:bg-[#1E2635]">
            <div class="flex items-center justify-between pb-3">
                <h5 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Notifikasi</h5>
                <span class="text-xs text-gray-500 dark:text-gray-400" x-text="jumlah + ' perlu ditindaklanjuti'"></span>
            </div>

            <x-ui.dropdown-menu.separator class="my-0" />

            <div class="max-h-96 overflow-x-hidden overflow-y-auto custom-scrollbar">
                <template x-if="item.length === 0">
                    <p class="px-3 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                        Semua beres. Tidak ada yang perlu ditindaklanjuti.
                    </p>
                </template>

                <div class="flex flex-col pe-2">
                    <template x-for="i in item" :key="i.kunci">
                        <a :href="i.url" class="flex items-start gap-3 rounded-lg p-3 hover:bg-gray-100 dark:hover:bg-white/5">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-lg" :class="warna(i.jenis)" x-text="ikon(i.jenis)"></span>
                            <span class="block flex-1">
                                <span class="block text-sm font-medium text-gray-800 dark:text-white/90" x-text="i.judul"></span>
                                <span class="block text-xs text-gray-500 dark:text-gray-400" x-text="i.isi"></span>
                                <span class="mt-1 block text-xs text-gray-400" x-text="i.waktu"></span>
                            </span>
                            <span x-show="! dibaca.includes(i.kunci)" class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-brand-500"></span>
                        </a>
                    </template>
                </div>
            </div>

            <x-ui.button as="a" href="{{ route('pesanan.index') }}" variant="outline" size="default" class="mt-3 w-full">
                Buka halaman Pesanan
            </x-ui.button>
        </x-ui.dropdown-menu.content>
    </x-ui.dropdown-menu>
</div>