# Panduan Pemakaian Panel Admin

Panduan ini untuk pemilik toko yang mengelola website sehari-hari lewat panel admin —
tidak perlu paham kode sama sekali. Kalau butuh dokumentasi teknis, lihat
`DEVELOPER.md`.

## 1. Login

Buka `/admin/login` (mis. `https://tokoanda.com/admin/login`), masukkan email dan
password akun admin Anda.

> **Penting:** akun admin default yang dibuat saat instalasi (`admin@herbalstore.test` /
> `password`) **harus diganti** sebelum website benar-benar dipakai publik. Minta
> developer Anda membuatkan akun baru lewat `php artisan tinker` atau menambah user
> langsung di database, lalu hapus/nonaktifkan akun default tersebut.

Setelah login, Anda akan masuk ke **Dashboard** — ringkasan jumlah produk dan kategori.
Menu di sisi kiri (bisa diciutkan lewat ikon di pojok atas menu, dan otomatis jadi menu
geser di HP): **Dashboard**, **Produk**, **Kategori**, **Pengaturan**, **Logout**.

## 2. Mengelola Produk

Menu **Produk** menampilkan semua produk dalam bentuk tabel yang bisa dicari
(kotak pencarian di pojok kanan atas tabel) dan diurutkan (klik judul kolom).

### Menambah produk baru

Klik tombol **Tambah Produk**, lalu isi:

| Field | Keterangan |
|---|---|
| Nama Produk | Wajib diisi. |
| Kategori | Pilih dari daftar kategori yang sudah dibuat (lihat bagian Kategori di bawah — buat kategorinya dulu kalau belum ada). |
| Deskripsi | Opsional, tampil di halaman detail produk. |
| Harga (Rp) | Wajib, angka saja (tanpa titik/koma) — website yang akan memformatnya jadi "Rp150.000" otomatis. |
| Stok | Wajib, angka. Kalau diisi `0`, produk akan menampilkan badge "Stok Habis" di katalog — **stok di sini hanya untuk ditampilkan**, tidak berkurang otomatis saat ada yang pesan (karena pemesanan masih terjadi di luar situs, lewat Shopee/TikTok/WhatsApp). |
| Gambar Produk | Opsional. Kalau dikosongkan, produk memakai **Gambar Default Produk** dari menu Pengaturan (atau ikon daun generik kalau itu juga belum diisi). Ukuran file maksimal 2MB. |
| Shopee URL | Opsional — isi kalau produk ini juga dijual di Shopee. Tombol "Shopee" di halaman katalog **hanya muncul kalau field ini diisi**. |
| TikTok URL | Sama seperti Shopee URL, untuk TikTok Shop. |
| Order Now URL | Opsional — link umum untuk channel lain (mis. Tokopedia, toko online lain). Sama seperti dua field di atas, tombolnya cuma muncul kalau diisi. |
| Aktifkan produk | Centang supaya produk ini tampil di katalog publik. Uncek untuk menyembunyikan produk sementara tanpa menghapusnya (mis. sedang kosong / bukan dijual lagi). |
| Tandai sebagai Top Pick | Centang supaya produk ini ikut tampil di slider "Pilihan Terbaik" di halaman depan. |

Tombol **Chat Admin (WhatsApp)** selalu muncul di setiap produk secara otomatis, tidak
perlu diisi per produk — nomor dan template pesannya diatur satu kali di menu
**Pengaturan** (lihat bagian 4).

Klik **Simpan**. Untuk mengubah produk yang sudah ada, klik ikon pensil di baris
produk tersebut pada tabel; untuk menghapus, klik ikon tempat sampah.

### Kategori produk tidak bisa dihapus?

Kategori yang masih punya produk di dalamnya tidak bisa dihapus — pindahkan dulu semua
produknya ke kategori lain (lewat edit produk satu-satu), baru kategori itu bisa
dihapus.

## 3. Mengelola Kategori

Menu **Kategori** — sama seperti Produk, ada tabel dengan tombol **Tambah Kategori**,
serta ikon edit/hapus per baris. Field-nya hanya **Nama Kategori** dan status
**Aktif** (kategori yang tidak aktif tidak muncul di filter kategori pada halaman
katalog publik, tapi produk di dalamnya tetap ada — sembunyikan produknya satu-satu
lewat "Aktifkan produk" kalau memang ingin disembunyikan juga).

## 4. Pengaturan

Menu **Pengaturan** adalah satu halaman untuk semua konfigurasi tampilan dan kontak
situs — semua perubahan di sini langsung terlihat di halaman publik setelah disimpan,
tanpa perlu bantuan developer.

### Branding
- **Nama Website** — muncul di logo teks (kalau belum upload logo gambar) dan judul tab browser.
- **Tagline** — kalimat pendek pendamping nama website.
- **Deskripsi Website (SEO)** — teks yang muncul saat link website dibagikan atau dicari di Google. Tidak tampil langsung di halaman.
- **Logo** — gambar logo di kiri atas navbar. Kalau dikosongkan, navbar memakai ikon daun bulat dengan inisial nama website.
- **Favicon** — ikon kecil di tab browser.
- **Gambar Default Produk** — foto pengganti untuk produk yang belum sempat diisi fotonya sendiri.
- **Teks Footer** — kalimat pendek di footer, di bawah nama toko.

### Banner Beranda
- **Teks Badge (opsional)** — label kecil di atas judul besar banner, mis. "100% Herbal Alami". Kosongkan untuk menyembunyikannya sepenuhnya.
- **Judul Banner** dan **Sub Judul Banner** — headline utama halaman depan.
- **Gambar Banner** — foto besar di sisi kanan banner. Kalau dikosongkan, sisi itu akan kosong (bagian teks banner tetap tampil normal).

### Kontak & WhatsApp
- **Nomor WhatsApp Admin** — format internasional tanpa tanda `+` atau spasi, contoh: `6281234567890`. Ini nomor yang dituju semua tombol "Chat Admin" di seluruh situs.
- **Template Pesan WhatsApp** — teks pesan yang otomatis terisi saat pembeli klik tombol Chat Admin. Pakai `{product}` untuk nama produk dan `{url}` untuk link ke halaman produk tsb, contoh:
  `Halo, saya mau tanya produk {product} ({url})`
- **Email**, **Telepon**, **Alamat** — tampil di footer, masing-masing hanya muncul kalau diisi.

### Sosial Media (Footer)
Instagram/Facebook/TikTok Profile/YouTube URL — masing-masing tombol di footer hanya
muncul kalau URL-nya diisi. Kosongkan field yang tidak dipakai, jangan diisi tanda `-`
atau semacamnya (field yang diisi teks apa pun akan dianggap "ada" dan tombolnya tetap
muncul, tapi linknya tidak akan valid).

## 5. Tips Praktis

- **Ukuran gambar**: logo dan favicon sebaiknya gambar persegi/landscape kecil (di
  bawah 1MB). Gambar banner dan produk boleh lebih besar (maksimal 2MB) tapi tetap
  disarankan dikompres dulu supaya halaman cepat dibuka pengunjung.
- **Semua link (Shopee/TikTok/Order Now/media sosial) harus alamat lengkap**, diawali
  `https://` — kalau ditempel dari browser biasanya sudah otomatis lengkap.
- **Uji coba tombol WhatsApp** setiap habis mengubah nomor atau template pesan di
  Pengaturan — buka salah satu halaman produk di situs publik dan klik tombolnya,
  pastikan nomor dan isi pesannya sudah benar sebelum dibagikan ke pembeli.
- Kalau nanti ingin website ini dikembangkan jadi toko online sungguhan (bisa checkout
  dan bayar langsung di situs, hitung ongkir otomatis), itu proyek pengembangan
  tersendiri yang butuh developer — lihat `ROADMAP-MARKETPLACE.md` untuk gambaran
  besarnya.
