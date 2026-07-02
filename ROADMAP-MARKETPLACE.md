# Dari Katalog ke Marketplace — Panduan Pengembangan Lanjutan

> **✅ SUDAH DIIMPLEMENTASIKAN (branch `feature/marketplace`).** Tahap 1–3 dari peta jalan
> ini — cart, checkout tamu, order, pembayaran Midtrans, ongkir otomatis Biteship — plus
> multi-cabang, stok per-cabang, peran admin (Pemilik/Staf Cabang), dan laporan penjualan
> **sudah dibangun**. Dokumen ini dipertahankan sebagai catatan desain/riwayat keputusan;
> untuk cara kerja aktual lihat `DEVELOPER.md`. Yang **belum** dibangun (sengaja ditunda):
> gateway/kurir kedua (Xendit/Duitku/RajaOngkir — abstraksinya sudah ada), split-fulfillment
> satu order lintas cabang, booking resi/label otomatis via API kurir, refund parsial, dan
> multi-vendor (Tahap 4 di bawah).

Dokumen ini untuk **pemilik toko** dan **developer** yang nanti melanjutkan proyek ini,
kalau suatu saat situs ini mau dikembangkan dari sekadar katalog (link keluar ke
Shopee/TikTok/WhatsApp) menjadi toko online sungguhan: ada keranjang, checkout,
pembayaran online, dan hitung ongkir otomatis.

Ini **bukan** rencana implementasi siap pakai — ini peta jalan supaya siapa pun yang
melanjutkan tahu apa saja yang berubah, seberapa besar pekerjaannya, dan urutan yang
masuk akal untuk mengerjakannya.

---

## Bagian 1 — Untuk Pemilik Toko

### Kondisi sekarang vs kalau jadi marketplace

| | Sekarang (katalog) | Kalau jadi marketplace |
|---|---|---|
| Pembeli pesan lewat | Klik tombol Shopee/TikTok/Order Now/WhatsApp, transaksi selesai di luar situs | Isi keranjang, checkout, bayar langsung di situs ini |
| Uang masuk ke | Rekening/saldo Shopee, TikTok Shop, dll (ditangani platform tsb) | Payment gateway (Midtrans/Xendit) → masuk ke rekening Anda, situs Anda yang tanggung jawab |
| Ongkos kirim | Dihitung manual oleh admin lewat chat WhatsApp | Dihitung otomatis dari alamat + berat produk |
| Yang harus Anda urus sendiri | Hampir tidak ada — Shopee/TikTok yang urus pembayaran, komplain, refund | Rekonsiliasi pembayaran, resi pengiriman, refund, komplain, keamanan data pelanggan |
| Biaya tambahan | Tidak ada | Biaya transaksi payment gateway (biasanya ~1.5–3% per transaksi), biaya API ongkir (biasanya berbayar per lookup atau berlangganan) |

**Ini keputusan bisnis, bukan cuma teknis.** Poin penting sebelum minta developer mulai:

1. **Tanggung jawab bertambah.** Begitu ada tombol "Bayar" di situs sendiri, Anda yang
   bertanggung jawab atas keamanan pembayaran, data pelanggan (alamat, nomor HP), dan
   proses refund/komplain — bukan lagi Shopee/TikTok.
2. **Butuh akun bisnis di payment gateway.** Midtrans/Xendit mewajibkan verifikasi
   bisnis (NIB/dokumen usaha) sebelum bisa menerima pembayaran asli, bukan cuma mode
   testing/sandbox.
3. **Ongkir otomatis butuh data yang sekarang belum ada**: berat tiap produk, dan alamat
   asal toko yang presisi (biasanya sampai level kecamatan) — ini perlu diisi ulang untuk
   semua produk yang sudah ada.
4. **Tidak harus sekaligus.** Bagian 3 di bawah membagi ini jadi 3 tahap yang bisa
   dikerjakan satu-satu — bisa berhenti di tahap manapun sesuai kebutuhan (misalnya cukup
   sampai "checkout + transfer manual" tanpa payment gateway otomatis).
5. **Tombol Shopee/TikTok/WhatsApp tidak harus dihapus.** Bisa tetap tampil berdampingan
   dengan tombol "Beli di sini" — pembeli yang lebih nyaman pakai Shopee tetap bisa,
   sambil pelan-pelan memindahkan transaksi ke situs sendiri.

---

## Bagian 2 — Untuk Developer: Arsitektur Saat Ini

Konteks yang perlu dipahami sebelum menambah apa pun (lihat juga `README.md`):

- **Tidak ada state transaksi sama sekali.** `Product`, `Category`, `Setting` adalah
  satu-satunya model. Tidak ada cart/session, tidak ada tabel `orders`. Setiap "aksi
  beli" (`resources/views/components/action-buttons.blade.php`) cuma link `<a>` keluar
  (Shopee/TikTok/Order Now) atau link `wa.me` yang dibuat oleh
  `app/Services/WhatsAppLinkBuilder.php`.
- **`Product.stock`** (lihat `database/migrations/..._create_products_table.php`) saat
  ini cuma dipakai untuk badge "Stok Habis" — bukan sumber kebenaran, tidak pernah
  dikurangi otomatis, karena tidak ada yang benar-benar memesan di situs ini.
- **`Setting::current()`** (`app/Models/Setting.php`) adalah pola singleton satu baris
  untuk semua konfigurasi situs — pola ini bisa dipakai lagi untuk menyimpan kredensial
  payment gateway & konfigurasi ongkir, bukan bikin sistem konfigurasi baru.
- **Tidak ada akun pelanggan.** Tabel `users` isinya cuma admin (satu role, tanpa RBAC —
  lihat `routes/admin.php`). Ada CRUD untuk mengelola *banyak* akun admin lewat menu
  Pengguna (`Admin\UserController`), tapi semuanya tetap punya akses yang sama persis —
  ini bukan RBAC, cuma multi-user. Kalau multi-vendor (Tahap 4) benar-benar dikerjakan,
  konsep role/permission per akun harus dibangun baru, bukan diperluas dari sini.
  Checkout nanti butuh cara menyimpan data pemesan meski belum tentu ada sistem login
  pelanggan (lihat opsi guest checkout di bawah).
- **Stack sengaja minimal**: tidak ada Vite/npm, semua CSS lewat Tailwind Standalone
  CLI, JS publik cuma vanilla di `public/js/app.js`. Kalau menambah payment gateway,
  sebagian besar vendor (Midtrans Snap, Xendit) menyediakan `<script>` yang biasanya
  dimuat dari CDN mereka — ini **melanggar aturan "no CDN" yang berlaku untuk seluruh
  proyek ini sejauh ini**, jadi ini keputusan yang perlu diambil sadar-sadar (lihat
  catatan di bagian Payment Gateway di bawah).

---

## Bagian 3 — Peta Jalan Pengembangan

### Tahap 1: Cart, Checkout, dan Order (tanpa payment gateway dulu)

Fondasi yang harus ada sebelum tahap 2 & 3 bisa dibangun — ini yang paling banyak
mengubah arsitektur, karena situs ini pertama kalinya perlu menyimpan state transaksi.

**Model/tabel baru:**
- `orders` — nomor order, data pemesan (nama, telepon, alamat — simpan sebagai kolom
  langsung di sini untuk order histori, jangan cuma referensi ke tabel address yang bisa
  berubah), subtotal, ongkir, total, status (`pending` → `paid`/`unpaid` →
  `processing` → `shipped` → `completed` / `cancelled`), catatan pembeli.
- `order_items` — snapshot `product_id`, nama & harga produk **pada saat order dibuat**
  (jangan cuma foreign key ke `Product`, karena harga produk bisa berubah setelahnya).
- Cart bisa **session-based** (tidak perlu tabel `carts` di database) selama belum ada
  akun pelanggan — lebih sederhana dan cukup untuk toko satu penjual seperti ini.

**Perubahan ke model yang sudah ada:**
- `Product` perlu `weight` (gram) untuk tahap 3 nanti — tambahkan dari awal supaya tidak
  perlu isi ulang data dua kali.
- `Product.stock` perlu jadi sumber kebenaran sungguhan: dikurangi saat order dibuat
  (atau saat dibayar, tergantung kebijakan), idealnya via event/observer seperti pola
  `ProductObserver` yang sudah ada untuk cleanup file gambar.

**Keputusan yang perlu diambil di awal tahap ini:**
- **Guest checkout vs akun pelanggan.** Rekomendasi: mulai dari guest checkout (isi
  nama/telepon/alamat per order, tanpa login) — lebih cepat dibangun dan lazim di
  marketplace Indonesia. Akun pelanggan (riwayat order, alamat tersimpan) bisa menyusul
  belakangan sebagai peningkatan, bukan syarat.
- **Konfirmasi pembayaran manual dulu**: checkout menghasilkan order berstatus
  `pending`, pembeli transfer manual + upload bukti transfer, admin konfirmasi manual di
  panel admin. Ini menunda kebutuhan payment gateway ke tahap 2, tapi order/cart/checkout
  flow-nya sudah bisa langsung dipakai dan diuji.

**Sisi admin:** halaman baru `Admin\OrderController` (index dengan status filter, show
detail, update status) — pola CRUD-nya sama seperti `Admin\ProductController` yang
sudah ada, tabelnya bisa pakai DataTables seperti yang lain.

### Tahap 2: Payment Gateway

Setelah ada tabel `orders` yang solid dari Tahap 1, ganti "upload bukti transfer manual"
dengan pembayaran otomatis.

**Pilihan populer untuk pasar Indonesia:** Midtrans atau Xendit — keduanya mendukung
QRIS, transfer virtual account, e-wallet (GoPay/OVO/Dana), dan kartu kredit dalam satu
integrasi, jadi tidak perlu integrasi manual per metode pembayaran.
Dokumentasi resmi: https://docs.midtrans.com/ dan https://developers.xendit.co/.

**Yang perlu ditambahkan:**
- `Setting` tambah field kredensial (`midtrans_server_key`, `midtrans_client_key`,
  `midtrans_is_production`, atau setara untuk Xendit) — **jangan pernah simpan kredensial
  ini di kode/`.env` yang di-commit**, tetap ikuti pola `Setting::current()` yang sudah
  ada supaya bisa diganti dari panel admin tanpa deploy ulang, TAPI pastikan field ini
  hanya bisa dibaca lewat server-side, tidak pernah ditampilkan di HTML publik.
- Endpoint **webhook** (mis. `POST /webhooks/midtrans`) yang menerima notifikasi status
  pembayaran dari gateway — endpoint ini **harus dikecualikan dari proteksi CSRF**
  (`bootstrap/app.php` → `$middleware->validateCsrfTokens(except: [...])`, sama seperti
  pola tambahan middleware admin yang sudah ada di file itu) dan **wajib verifikasi
  signature** dari payload webhook, jangan percaya begitu saja isi request-nya.
- Redirect/popup pembayaran (Midtrans Snap adalah widget JS yang di-embed) — ini satu
  pengecualian sadar terhadap aturan "no CDN" proyek ini, karena skrip Snap **wajib**
  dimuat dari domain Midtrans (bukan boleh di-self-host) agar data kartu tidak pernah
  menyentuh server sendiri (syarat kepatuhan PCI). Dokumentasikan pengecualian ini secara
  eksplisit di README kalau nanti ditambahkan, supaya tidak dikira pelanggaran aturan yang
  tidak disengaja.
- Jangan pernah menyimpan nomor kartu kredit pelanggan sendiri di database — itu sebabnya
  alur di atas selalu lewat redirect/widget resmi gateway, bukan form kartu buatan sendiri.

### Tahap 3: Ongkir Otomatis

Baru masuk akal dikerjakan setelah alamat pembeli (Tahap 1) dan berat produk sudah ada.

**Pilihan API:** RajaOngkir (https://rajaongkir.com/) paling dikenal tapi memakai ID
kota/kecamatan sendiri yang perlu di-mapping dari alamat bebas-teks; Biteship
(https://biteship.com/) dan Komerce adalah alternatif lebih baru dengan API pencarian
area yang lebih fleksibel. Bandingkan cakupan kurir (JNE/J&T/SiCepat/dll) dan harga
sebelum memilih — ini keputusan yang sebaiknya dicek ulang saat tahap ini benar-benar
dikerjakan, karena harga dan cakupan API pihak ketiga berubah dari waktu ke waktu.

**Yang perlu ditambahkan:**
- `Setting` tambah alamat asal toko (kota/kecamatan asal, dalam format yang sesuai
  provider ongkir yang dipilih) dan kredensial API key provider tsb.
- Saat checkout: total berat = jumlah `product.weight * quantity` semua item di
  keranjang, dikirim ke API ongkir bersama kota tujuan dari alamat pembeli → tampilkan
  pilihan kurir & harga sebelum pembeli konfirmasi order.
- Ini biasanya dipanggil lewat AJAX di halaman checkout (submit alamat → tampilkan
  pilihan ongkir tanpa reload halaman) — satu-satunya tempat di proyek ini yang butuh
  request AJAX ke luar sejauh ini, jadi perlu endpoint kecil khusus
  (mis. `POST /checkout/ongkir`) yang memanggil API provider dari sisi server (jangan
  panggil API ongkir langsung dari browser, supaya API key tidak pernah terekspos ke
  publik).

### (Opsional, jauh lebih besar) Tahap 4: Multi-Vendor Marketplace Sungguhan

Kalau yang dimaksud "marketplace" nantinya bukan cuma satu toko dengan
checkout/pembayaran sendiri, tapi banyak penjual berbeda berjualan di platform yang
sama (seperti Shopee/Tokopedia) — ini lompatan arsitektur yang jauh lebih besar dari
Tahap 1–3, dan sebaiknya dianggap proyek terpisah, bukan lanjutan langsung:

- Perlu akun penjual (vendor) dengan produk milik masing-masing, bukan satu `Product`
  list milik pemilik tunggal — ini kembali ke kompleksitas RBAC/multi-tenant yang
  sengaja dihindari sejak awal proyek ini (lihat riwayat keputusan di `README.md`).
- Perlu split pembayaran/settlement per vendor, komisi platform, dan pemisahan order per
  vendor dalam satu keranjang pembeli.
- Rekomendasi: selesaikan dan jalankan dulu Tahap 1–3 sebagai toko satu-penjual,
  baru evaluasi apakah benar-benar perlu multi-vendor — banyak kasus "marketplace" yang
  dimaksud pemilik toko sebenarnya cukup terlayani oleh Tahap 1–3 saja.

---

## Ringkasan Urutan & Perkiraan Kompleksitas

| Tahap | Kompleksitas relatif | Syarat sebelum mulai |
|---|---|---|
| 1. Cart/Checkout/Order (manual transfer) | Besar — mengubah arsitektur dari stateless ke stateful | Tidak ada, bisa mulai kapan saja |
| 2. Payment Gateway | Sedang — sebagian besar cuma integrasi API + webhook | Tahap 1 selesai (butuh tabel `orders`) |
| 3. Ongkir Otomatis | Sedang — API sederhana, tapi butuh data alamat presisi | Tahap 1 selesai (butuh alamat pembeli + berat produk) |
| 4. Multi-Vendor | Sangat besar — proyek terpisah | Tahap 1–3 sudah stabil dan benar-benar dibutuhkan |

Tahap 2 dan 3 tidak saling bergantung satu sama lain — bisa dikerjakan dalam urutan
manapun setelah Tahap 1 selesai, tergantung mana yang lebih mendesak (mis. kalau
sekarang ongkir masih dihitung manual dengan cukup lancar lewat WhatsApp, payment
gateway mungkin lebih prioritas untuk dikerjakan lebih dulu).
