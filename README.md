# Herbal Store

Website toko herbal dengan katalog **dan** marketplace penuh: setiap produk punya tombol aksi
eksternal (Shopee, TikTok, Order Now, Chat Admin WhatsApp) **dan** keranjang + checkout internal
dengan pembayaran online (Midtrans), ongkir otomatis (Biteship), stok multi-cabang, peran admin
(Pemilik / Staf Cabang), serta laporan penjualan.

> **Catatan sejarah:** proyek ini bermula sebagai katalog murni "link keluar" tanpa cart/checkout.
> Fitur marketplace ditambahkan kemudian (lihat `ROADMAP-MARKETPLACE.md`). Tombol eksternal
> Shopee/TikTok/WhatsApp tetap ada, berdampingan dengan tombol "Tambah ke Keranjang".

Branding tetap mudah dipakai ulang untuk niche lain — ganti konten di halaman **Pengaturan**
(nama, logo, favicon, banner, kontak) dan sesuaikan `resources/views/home.blade.php` serta
`resources/views/catalog/`.

## Stack

- Laravel 12 + PHP 8.2, MySQL
- **Tidak ada Node.js/npm/Vite.** Tailwind CSS dikompilasi lewat [Tailwind Standalone CLI](https://tailwindcss.com/blog/standalone-cli)
  — binary-nya (~100MB+, karena membundel runtime JS sendiri) **tidak disimpan di git**
  karena melebihi batas ukuran file GitHub. Diunduh otomatis sekali ke `bin/tailwindcss.exe`
  lewat `composer run get-tailwindcss` (lihat langkah 5 di bawah).
- jQuery + DataTables untuk tabel admin (self-hosted di `public/vendor/`, tidak lewat CDN).
- Login admin dibuat manual (bukan Breeze) — tidak ada registrasi publik. Akun admin pertama
  dibuat lewat seeder; akun tambahan dikelola dari panel admin sendiri (menu **Pengguna**).
  Login dilindungi rate limiting (maksimal 5 percobaan/menit per kombinasi email+IP).

## Setup Lokal (Laragon)

1. `composer install`
2. Copy `.env.example` ke `.env`, sesuaikan `DB_DATABASE` jika perlu, lalu `php artisan key:generate`.
3. Buat database MySQL `herbal_store` (lewat HeidiSQL/phpMyAdmin bawaan Laragon, atau `mysql -u root -e "CREATE DATABASE herbal_store"`).
4. `php artisan migrate --seed` — akan membuat 1 akun admin default:
   - Email: `admin@herbalstore.test`
   - Password: `password`

   **Ganti password ini sebelum deploy ke production.** Bisa lewat menu **Pengguna** di
   panel admin setelah login (edit akun ini, atau buat akun baru lalu hapus yang default).
5. `composer run get-tailwindcss` — unduh sekali binary Tailwind CLI sesuai OS Anda ke
   `bin/tailwindcss.exe` (deteksi otomatis Windows/macOS/Linux, x64/arm64). Cuma perlu
   dijalankan sekali per komputer; kalau filenya sudah ada, perintah ini otomatis di-skip.
   Kalau gagal (mis. tidak ada koneksi internet), instruksi unduh manual akan ditampilkan.
6. `composer run build-css` untuk compile CSS pertama kali (lihat bagian di bawah).
7. `php artisan storage:link` — jika gagal (symlink butuh Developer Mode aktif di Windows), coba
   `php artisan storage:link --relative`, atau jalankan terminal sebagai Administrator.
8. Buka `http://herbal-store.test` (setelah reload Laragon) atau `php artisan serve`.

Langkah 1–7 di atas juga bisa dijalankan sekaligus lewat `composer run setup`.

## Compile Ulang CSS

Setiap kali menambah class Tailwind baru di file Blade (atau di `public/js/*.js`), jalankan:

```
bin/tailwindcss.exe -i resources/css/app.css -o public/css/app.css --minify
```

Atau lewat Composer: `composer run build-css`. Tidak butuh `npm install` sama sekali —
`bin/tailwindcss.exe` sendiri didapat lewat `composer run get-tailwindcss` (lihat Setup
Lokal di atas), bukan lewat npm.

## Struktur Penting

- `resources/views/components/action-buttons.blade.php` — 4 tombol order (Shopee/TikTok/Order Now/WhatsApp),
  dipakai bareng di card katalog dan halaman detail produk.
- `app/Services/WhatsAppLinkBuilder.php` — bikin link `wa.me` dari nomor & template pesan di Pengaturan.
- `app/Models/Setting.php` — satu baris data (`Setting::current()`) untuk semua branding, banner, dan kontak footer.
- `routes/admin.php` — semua route `/admin/*`, dilindungi middleware `auth` bawaan Laravel.

## Admin Panel

`/admin/login` — kelola Produk, Kategori, Pesanan, Cabang, Transfer Stok, Pengguna, Laporan,
Riwayat Transaksi, dan Pengaturan.

Ada **dua peran** (`app/Enums/UserRole.php`): **Pemilik** melihat/mengelola semua; **Staf Cabang**
dibatasi ke cabangnya sendiri (hanya Pesanan/Transfer Stok/Laporan/Riwayat cabangnya; tidak bisa
akses Produk/Kategori/Cabang/Pengguna/Pengaturan). Penegakan: middleware `role:` untuk gerbang
per-rute (owner-only), scope `Order::scopeVisibleTo()` untuk filter daftar per cabang, dan
`OrderPolicy` untuk otorisasi per-pesanan.

Pembayaran (Midtrans), ongkir otomatis (Biteship), dan proteksi bot (reCAPTCHA v3) dikonfigurasi
di menu **Pengaturan** — semua kunci rahasia disimpan terenkripsi dan tidak pernah dirender ke HTML.

## Dokumentasi Lengkap

- **[DEVELOPER.md](DEVELOPER.md)** — dokumentasi teknis: arsitektur, struktur kode,
  konvensi, dan jebakan yang perlu diketahui sebelum menambah fitur. Untuk developer.
- **[USER-GUIDE.md](USER-GUIDE.md)** — panduan pemakaian panel admin sehari-hari
  (kelola Produk/Kategori/Pengguna/Pengaturan). Untuk pemilik toko, tidak perlu paham kode.
- **[DEPLOYMENT.md](DEPLOYMENT.md)** — cara deploy ke production, baik shared hosting
  cPanel maupun VPS, sampai troubleshooting masalah umum setelah deploy.
- **[ROADMAP-MARKETPLACE.md](ROADMAP-MARKETPLACE.md)** — peta jalan kalau situs ini
  nanti mau dikembangkan jadi toko online sungguhan (payment gateway, ongkir otomatis,
  multi-vendor). Untuk pemilik toko maupun developer.
