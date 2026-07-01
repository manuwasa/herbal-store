# Dokumentasi Developer

Dokumen ini menjelaskan cara kerja proyek ini di level kode — untuk developer yang
melanjutkan, memperbaiki, atau memperluas situs ini. Untuk instalasi awal, lihat
`README.md`. Untuk panduan memakai panel admin sehari-hari (bukan kode), lihat
`USER-GUIDE.md`.

## Prinsip Desain yang Wajib Dipahami Dulu

Sebelum menambah kode apa pun, pahami batasan yang **disengaja**, bukan kelalaian:

- **Katalog saja, bukan e-commerce.** Tidak ada cart, checkout, atau tabel `orders`.
  Setiap produk punya sampai 4 tombol aksi (Shopee/TikTok/Order Now/WhatsApp) yang
  semuanya mengarahkan pembeli **keluar** dari situs. Kalau tugas Anda adalah menambah
  fitur checkout/pembayaran, baca `ROADMAP-MARKETPLACE.md` dulu — itu perubahan
  arsitektur besar, bukan penambahan kecil.
- **Tidak ada Node.js/npm/Vite di mana pun dalam proyek ini.** CSS dikompilasi lewat
  Tailwind Standalone CLI (binary `bin/tailwindcss.exe`, sudah di-commit ke repo).
  Jangan menambah `package.json` atau dependensi npm apa pun.
- **Tidak ada aset dari CDN.** jQuery, DataTables, dan font Fraunces semuanya
  di-self-host di `public/vendor/` dan `public/fonts/`. Kalau menambah library baru,
  download filenya dan commit ke repo — jangan `<script src="https://...">`.
- **Satu role admin, tanpa RBAC.** Tidak ada level admin/staff/superadmin. Siapa pun
  yang punya akun di tabel `users` bisa akses semua menu `/admin/*`.
- **Reusable untuk niche lain.** Tidak ada yang hardcode "herbal" di luar konten —
  semua branding lewat `Setting`. Kalau menambah fitur, jaga agar tetap generik
  (lihat pola `Product`/`Category`/`Setting` yang sudah ada).

## Stack

- Laravel 12, PHP 8.2, MySQL.
- Tailwind CSS v4 (via Standalone CLI, bukan PostCSS/Vite).
- jQuery + DataTables 2.x untuk tabel di panel admin saja (situs publik tidak pakai
  jQuery sama sekali — JS publik di `public/js/app.js` adalah vanilla JS murni).
- Auth admin dibuat manual (`app/Http/Controllers/Auth/LoginController.php` + middleware
  `auth` bawaan Laravel) — bukan Breeze/Fortify/Jetstream.
- Tidak ada automated test suite yang berarti — `tests/` masih isi contoh default
  Laravel. Verifikasi selama ini dilakukan manual di browser (lihat bagian Verifikasi
  di bawah).

## Struktur Direktori yang Perlu Diketahui

```
app/
  Http/Controllers/
    HomeController.php          # halaman depan (/)
    ProductController.php       # katalog publik (/katalog, /katalog/{slug})
    Admin/
      DashboardController.php   # halaman awal setelah login admin
      ProductController.php     # CRUD produk
      CategoryController.php    # CRUD kategori
      SettingController.php     # form pengaturan tunggal (bukan CRUD, cuma edit)
    Auth/LoginController.php    # login/logout admin, tangan-buatan
  Models/
    Product.php, Category.php, Setting.php
  Observers/ProductObserver.php # hapus file gambar lama saat produk diupdate/dihapus
  Services/WhatsAppLinkBuilder.php

resources/
  css/app.css                   # satu file CSS untuk seluruh situs (publik + admin)
  views/
    home.blade.php, catalog/*.blade.php     # "theme surface" publik
    admin/**                                # semua view panel admin
    components/                             # komponen Blade yang dipakai bareng
      product-card.blade.php, action-buttons.blade.php,
      navbar.blade.php, footer.blade.php, icon.blade.php,
      layouts/app.blade.php, layouts/admin.blade.php, admin/sidebar.blade.php

public/
  css/app.css, js/app.js, js/admin.js   # hasil compile, ini yang benar-benar dimuat browser
  vendor/jquery/, vendor/datatables/    # self-hosted, bukan CDN
  fonts/fraunces-variable.woff2

routes/web.php     # route publik (require routes/admin.php di baris terakhir)
routes/admin.php   # semua route /admin/*
```

## Model Data

### `Category`
`name`, `slug` (unique), `is_active`. Relasi `hasMany(Product)`. Scope `active()`.

### `Product`
`category_id` (FK, `restrictOnDelete` — kategori tidak bisa dihapus kalau masih ada
produknya), `name`, `slug`, `description`, `price` (decimal 12,2), `image_path`,
`stock` (integer, **hanya tampilan** — badge "Stok Habis" di kartu produk, tidak pernah
dikurangi otomatis karena tidak ada order), `shopee_url`/`tiktok_url`/`order_now_url`
(semua nullable — masing-masing tombol di `action-buttons.blade.php` cuma muncul kalau
field-nya terisi), `is_active`, `is_top_pick`, plus `SoftDeletes`.

Scope: `active()`, `topPick()`. Helper boolean: `hasShopeeLink()`, `hasTiktokLink()`,
`hasOrderNowLink()` (dipakai di `action-buttons.blade.php` alih-alih cek `!!$product->shopee_url`
langsung di Blade).

`#[ObservedBy(ProductObserver::class)]` — saat `image_path` diganti (update) atau produk
dihapus permanen (`forceDeleted`, bukan soft-delete biasa), file lama di
`storage/app/public/products/` otomatis dihapus. Kalau menambah field upload gambar baru
di model lain, ikuti pola observer ini, jangan hapus file manual di controller.

### `Setting`
Satu baris konfigurasi untuk seluruh situs, diakses lewat `Setting::current()`
(`firstOrCreate([])` — otomatis bikin baris kosong kalau belum ada, jadi tidak perlu
seeding manual sebelum pakai). Field-nya mengelompok jadi: WhatsApp
(`whatsapp_number`, `whatsapp_message_template`), branding (`site_name`,
`site_tagline`, `site_description`, `logo_path`, `favicon_path`,
`product_placeholder_image_path`, `footer_text`), banner beranda (`banner_image_path`,
`banner_heading`, `banner_subheading`, `banner_badge_text`), kontak footer
(`contact_email`, `contact_phone`, `contact_address`, `instagram_url`, `facebook_url`,
`tiktok_profile_url`, `youtube_url`) — semuanya nullable dan cuma dirender kalau
terisi (pola "optional, tampil kalau diisi" yang sama dipakai di seluruh proyek: link
sosial, tombol produk, dst).

**Pola penting:** kalau menambah field konfigurasi baru untuk seluruh situs, tambahkan
ke `Setting` (migration + `$fillable`), bukan bikin tabel/model konfigurasi baru.

## Alur Publik

`routes/web.php` → `HomeController@index` (banner + Top Pick slider + 8 produk
terbaru + daftar kategori), `ProductController@index` (`/katalog`, filter
`?category=` + `?search=`), `ProductController@show` (`/katalog/{product:slug}` —
route model binding lewat kolom `slug`, bukan `id`).

`components/product-card.blade.php` dipakai di grid katalog maupun slider Top Pick —
root elemennya pakai `$attributes->merge(['class' => '...'])` supaya caller (mis.
`home.blade.php`) bisa menambah class tambahan (dipakai untuk animasi `reveal`, lihat
bagian JS di bawah) tanpa mengubah komponennya.

`components/action-buttons.blade.php` — 4 tombol, dipakai dari `product-card` (mode
`:compact="true"`) dan halaman detail produk (mode penuh). WhatsApp dibangun lewat
`WhatsAppLinkBuilder::forProduct($product)` yang mengisi template pesan dari
`Setting::current()->whatsapp_message_template` dengan placeholder `{product}`/`{url}`,
lalu di-`rawurlencode()` ke `https://wa.me/{nomor}?text=...`.

## Panel Admin

`routes/admin.php`: grup `guest` untuk halaman login, grup `auth` + prefix `admin` +
name prefix `admin.` untuk semua yang lain. `Route::resource('produk', ProductController::class)`
dan `Route::resource('kategori', CategoryController::class)` — nama route pakai bahasa
Indonesia (`admin.produk.index`, dst) lewat `->parameters(['produk' => 'product'])`
supaya URL & nama route berbahasa Indonesia tapi tetap bind ke model `Product` (bukan
`produk`) di controller.

Semua tabel index (`admin/products/index.blade.php`, `admin/categories/index.blade.php`)
pakai DataTables client-side (data dikirim penuh sekali render, difilter/disortir di
browser — bukan server-side processing, karena skalanya kecil). Inisialisasinya di
`public/js/admin.js`.

`SettingController` **bukan** CRUD biasa — cuma `edit`/`update`, karena `Setting` selalu
satu baris (`Setting::current()`). Upload gambar (logo/favicon/banner/placeholder produk)
ditangani lewat loop generik di `update()`:

```php
foreach (['logo', 'favicon', 'banner_image', 'product_placeholder_image'] as $field) {
    if ($request->hasFile($field)) {
        $data["{$field}_path"] = $request->file($field)->store('settings', 'public');
    }
    unset($data[$field]);
}
```

Kalau menambah field upload gambar baru di Settings, tambahkan namanya ke array ini
saja — jangan tulis ulang blok if/store manual per field.

## Frontend: Build & JS

- **Compile CSS**: `bin/tailwindcss.exe -i resources/css/app.css -o public/css/app.css --minify`
  (atau `composer run build-css`) — wajib dijalankan ulang setiap kali menambah class
  Tailwind baru di file Blade/JS, karena Tailwind men-scan file lewat directive
  `@source` di `resources/css/app.css`. **Penting:** `@source` mencakup
  `public/js/*.js` secara eksplisit (bukan default Tailwind) — kalau menambah file JS
  publik baru yang memuat nama class Tailwind, pastikan pathnya ikut ter-scan atau
  tambahkan `@source` baru.
- **Cache-busting**: link CSS/JS di layout pakai
  `?v={{ filemtime(public_path('css/app.css')) }}` — otomatis berubah tiap kali file
  di-build ulang, tidak perlu sistem hash manual karena tidak ada bundler.
- **`public/js/app.js`** (publik, vanilla JS, satu `DOMContentLoaded` listener): animasi
  scroll-reveal (`IntersectionObserver` pada elemen `.reveal`), animasi masuk hero
  (`.hero-reveal`, dipicu langsung lewat `requestAnimationFrame` karena selalu di atas
  layar), navbar mengecil saat scroll, dan slider Top Pick (`scrollByCard()`,
  `updateButtons()`, auto-advance dengan jeda saat hover/sentuh/fokus keyboard, dan
  otomatis berhenti kalau `prefers-reduced-motion: reduce`).
- **`public/js/admin.js`** (admin, jQuery + vanilla): inisialisasi DataTables, toggle
  sidebar mobile/desktop, pemindahan search box DataTables ke slot header.

## Jebakan CSS yang Sudah Pernah Terjadi — Baca Sebelum Menambah CSS

**CSS yang tidak berada di dalam `@layer` (unlayered) SELALU menang atas aturan di
dalam `@layer components`, berapa pun specificity-nya atau urutan sumbernya** — ini
aturan cascade-layer di spec CSS, bukan bug. Ini sudah 3 kali menyebabkan bug susah
dilacak di proyek ini:

1. Override alignment kolom DataTables tidak jalan — CSS bawaan DataTables tidak
   berada di `@layer` mana pun, sedangkan override kita ada di `@layer components`.
2. Class toggle sidebar (collapse/mobile) tidak berefek — karena class utility Tailwind
   sendiri (`@layer utilities`, dihasilkan otomatis oleh Tailwind) juga menang atas
   `@layer components` kita.
3. Pagination custom Laravel bentrok sama style bawaan `dark:` yang unlayered.

**Solusinya, dan pola yang harus diikuti ke depan:**
- Kalau perlu override style dari library pihak ketiga yang unlayered (DataTables,
  dll), tulis override-nya juga **di luar** blok `@layer` mana pun di `app.css` (lihat
  bagian bawah `app.css`, ditandai komentar jelas).
- Kalau perlu toggle state lewat JS (sidebar, navbar shrink, dll), **toggle class
  utility Tailwind asli langsung dari JS** (`classList.toggle('py-2', ...)`), jangan
  bikin class custom di `@layer components` untuk itu — class custom akan selalu kalah
  kalau elemennya juga punya class utility Tailwind lain yang menyentuh properti yang
  sama.
- Class baru yang namanya unik (tidak bentrok dengan nama utility Tailwind apa pun,
  misalnya `.reveal`, `.hero-reveal`, `.pulse-soft`) aman ditaruh di `@layer
  components` — jebakan ini cuma berlaku kalau ada elemen unlayered lain yang menyasar
  properti CSS yang sama.

## Verifikasi

Tidak ada automated test suite yang dipakai aktif. Verifikasi perubahan dengan
menjalankan `php artisan serve` lokal dan cek manual di browser — untuk perubahan
visual/interaktif, proyek ini terbiasa diverifikasi lewat Playwright (screenshot +
pengecekan computed style/console error), tapi Playwright **bukan** dependensi proyek,
cuma dipakai ad-hoc saat development untuk verifikasi, tidak pernah di-commit.

## Pengembangan Lanjutan

Untuk menambah fitur besar (payment gateway, ongkir otomatis, multi-vendor
marketplace), baca **`ROADMAP-MARKETPLACE.md`** dulu — dokumen itu menjelaskan
perubahan arsitektur apa saja yang dibutuhkan dan urutan pengerjaan yang disarankan,
supaya tidak mulai dari nol menganalisis ulang batasan-batasan yang sudah didokumentasikan
di sini.
