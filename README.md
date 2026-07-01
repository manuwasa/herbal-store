# Herbal Store

Website katalog produk herbal. Bukan e-commerce — setiap produk punya sampai 4 tombol aksi
(Shopee, TikTok, Order Now, Chat Admin WhatsApp) yang mengarahkan pembeli ke channel eksternal.
Tidak ada keranjang, checkout, atau pembayaran di website ini.

Dibangun agar mudah dipakai ulang untuk katalog produk lain — cukup ganti konten di halaman
**Pengaturan** (nama, logo, favicon, banner, kontak) dan sesuaikan tampilan di `resources/views/home.blade.php`
serta `resources/views/catalog/`, tanpa perlu menyentuh data model atau panel admin.

## Stack

- Laravel 12 + PHP 8.2, MySQL
- **Tidak ada Node.js/npm/Vite.** Tailwind CSS dikompilasi lewat [Tailwind Standalone CLI](https://tailwindcss.com/blog/standalone-cli)
  (`bin/tailwindcss.exe`, sudah ada di repo — tidak perlu diinstal ulang).
- jQuery + DataTables untuk tabel admin (self-hosted di `public/vendor/`, tidak lewat CDN).
- Login admin dibuat manual (bukan Breeze) — tidak ada registrasi publik, akun admin dibuat lewat seeder/tinker.

## Setup Lokal (Laragon)

1. `composer install`
2. Copy `.env.example` ke `.env`, sesuaikan `DB_DATABASE` jika perlu, lalu `php artisan key:generate`.
3. Buat database MySQL `herbal_store` (lewat HeidiSQL/phpMyAdmin bawaan Laragon, atau `mysql -u root -e "CREATE DATABASE herbal_store"`).
4. `php artisan migrate --seed` — akan membuat 1 akun admin default:
   - Email: `admin@herbalstore.test`
   - Password: `password`

   **Ganti password ini sebelum deploy ke production.**
5. `php artisan storage:link` — jika gagal (symlink butuh Developer Mode aktif di Windows), coba
   `php artisan storage:link --relative`, atau jalankan terminal sebagai Administrator.
6. Buka `http://herbal-store.test` (setelah reload Laragon) atau `php artisan serve`.

## Compile Ulang CSS

Setiap kali menambah class Tailwind baru di file Blade, jalankan:

```
bin/tailwindcss.exe -i resources/css/app.css -o public/css/app.css --minify
```

Atau lewat Composer: `composer run build-css`. Tidak butuh `npm install` sama sekali.

## Struktur Penting

- `resources/views/components/action-buttons.blade.php` — 4 tombol order (Shopee/TikTok/Order Now/WhatsApp),
  dipakai bareng di card katalog dan halaman detail produk.
- `app/Services/WhatsAppLinkBuilder.php` — bikin link `wa.me` dari nomor & template pesan di Pengaturan.
- `app/Models/Setting.php` — satu baris data (`Setting::current()`) untuk semua branding, banner, dan kontak footer.
- `routes/admin.php` — semua route `/admin/*`, dilindungi middleware `auth` bawaan Laravel.

## Admin Panel

`/admin/login` — kelola Produk, Kategori, dan Pengaturan (branding, banner, WhatsApp, kontak footer).
Tidak ada role/level admin — siapa pun yang punya akun bisa akses semua menu.

## Dokumentasi Lengkap

- **[DEVELOPER.md](DEVELOPER.md)** — dokumentasi teknis: arsitektur, struktur kode,
  konvensi, dan jebakan yang perlu diketahui sebelum menambah fitur. Untuk developer.
- **[USER-GUIDE.md](USER-GUIDE.md)** — panduan pemakaian panel admin sehari-hari
  (kelola Produk/Kategori/Pengaturan). Untuk pemilik toko, tidak perlu paham kode.
- **[DEPLOYMENT.md](DEPLOYMENT.md)** — cara deploy ke production, baik shared hosting
  cPanel maupun VPS, sampai troubleshooting masalah umum setelah deploy.
- **[ROADMAP-MARKETPLACE.md](ROADMAP-MARKETPLACE.md)** — peta jalan kalau situs ini
  nanti mau dikembangkan jadi toko online sungguhan (payment gateway, ongkir otomatis,
  multi-vendor). Untuk pemilik toko maupun developer.
