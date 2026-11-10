# Sistem Layanan Kuliner dan Restoran

Proyek akhir [nama mata kuliah], kelompok [nomor/nama kelompok].

Anggota:
- [INDRA OKTA NUGRAHA] ([123250188])
- [SATTARI RADJASYAH HIDAYAT] ([123250190])
- [Nama 3] ([NIM])

## Fitur
- Login, register pelanggan, logout, dan hak akses per role (admin, kasir, koki, pelanggan)
- Kelola kategori, menu (dengan upload foto), meja, dan pengguna
- Pesanan dine in / takeaway / delivery, pembayaran dengan kembalian otomatis
- Reservasi meja oleh kasir maupun pelanggan
- Pemesanan mandiri lewat QR di meja, dan cetak kartu QR per meja
- Dashboard: pendapatan hari ini, grafik 7 hari, menu terlaris
- Filter status/tanggal dan paginasi

## Teknologi
Laravel 12, MySQL, Tailwind CSS 4, Alpine.js, Vite, template [TailAdmin Laravel](https://github.com/TailAdmin/tailadmin-laravel) (lisensi MIT), bacon/bacon-qr-code.

## Cara instalasi (Laragon)
1. Clone repo ke `C:\laragon\www\restoran`
2. Install library:
   ```
   composer install
   npm install
   ```
3. Buat file `.env`:
   ```
   copy .env.example .env
   php artisan key:generate
   ```
4. Import database: buka HeidiSQL, pilih **File > Run SQL file...**, lalu pilih `database/sql/restoran.sql`.
   **Jangan** jalankan `php artisan migrate`.
5. Hubungkan folder foto:
   ```
   php artisan storage:link
   ```
6. Jalankan:
   ```
   npm run dev
   ```
   Lalu buka http://restoran.test

## Akun demo
| Role | Email | Password |
|---|---|---|
| Admin | admin@restoran.test | admin123 |
| Kasir | kasir@restoran.test | kasir123 |
| Koki | koki@restoran.test | koki1234 |
| Pelanggan | [email pelanggan demo] | [password] |