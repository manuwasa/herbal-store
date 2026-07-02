# Panduan Pemakaian Panel Admin

Panduan ini untuk pemilik toko yang mengelola website sehari-hari lewat panel admin —
tidak perlu paham kode sama sekali. Kalau butuh dokumentasi teknis, lihat
`DEVELOPER.md`.

## 1. Login

Buka `/admin/login` (mis. `https://tokoanda.com/admin/login`), masukkan email dan
password akun admin Anda.

> **Penting:** akun admin default yang dibuat saat instalasi (`admin@herbalstore.test` /
> `password`) **harus diganti** sebelum website benar-benar dipakai publik. Sekarang ini
> bisa dilakukan sendiri tanpa developer, lewat menu **Pengguna** — lihat bagian 4 di
> bawah untuk caranya.

Kalau salah memasukkan password **5 kali dalam waktu 1 menit**, sistem akan menahan
percobaan login berikutnya sebentar (pesan "Too Many Requests") sebagai proteksi dari
tebak-tebak password oleh orang lain. Ini normal — tunggu sekitar satu menit lalu coba
lagi dengan password yang benar.

Setelah login, Anda akan masuk ke **Dashboard** — ringkasan jumlah produk dan kategori.
Menu di sisi kiri (bisa diciutkan lewat ikon di pojok atas menu, dan otomatis jadi menu
geser di HP): **Dashboard**, **Produk**, **Kategori**, **Pengguna**, **Pengaturan**,
**Logout**.

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
**Pengaturan** (lihat bagian 5).

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

## 4. Mengelola Pengguna (Akun Admin)

Menu **Pengguna** mengelola siapa saja yang bisa login ke panel admin ini. Semua akun di
sini punya akses yang sama persis — tidak ada perbedaan "admin utama" vs "staf", semua
bisa mengelola Produk/Kategori/Pengaturan/Pengguna lainnya.

### Menambah pengguna baru

Klik **Tambah Pengguna**, isi Nama, Email (harus unik, belum dipakai akun lain), Password,
dan Konfirmasi Password, lalu **Simpan**.

### Mengubah pengguna

Klik ikon pensil pada baris pengguna yang dituju. Kolom **Password** dan **Konfirmasi
Password** boleh dikosongkan kalau tidak ingin mengganti password — isi hanya kalau
memang ingin menggantinya.

### Menghapus pengguna

Klik ikon tempat sampah pada baris pengguna. Dua hal yang **tidak diperbolehkan** sistem
demi keamanan:
- **Anda tidak bisa menghapus akun yang sedang Anda pakai untuk login saat ini** —
  ikon hapusnya bahkan tidak akan muncul pada baris akun Anda sendiri.
- **Pengguna terakhir yang tersisa tidak bisa dihapus** — supaya panel admin tidak
  pernah kehilangan akses total tanpa ada satu akun pun yang bisa login.

Untuk mengganti akun default (`admin@herbalstore.test`) dengan akun milik Anda sendiri:
1. Login dengan akun default seperti biasa.
2. Buka menu **Pengguna** → **Tambah Pengguna**, buat akun baru dengan email dan
   password milik Anda.
3. **Logout**, lalu login lagi memakai akun baru tersebut.
4. Buka menu **Pengguna** lagi, lalu hapus akun `admin@herbalstore.test` — sekarang bisa
   dihapus karena bukan lagi akun yang sedang Anda pakai, dan bukan lagi satu-satunya
   akun yang ada.

## 5. Pengaturan

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

## 6. Tips Praktis

- **Ukuran gambar**: logo dan favicon sebaiknya gambar persegi/landscape kecil (di
  bawah 1MB). Gambar banner dan produk boleh lebih besar (maksimal 2MB) tapi tetap
  disarankan dikompres dulu supaya halaman cepat dibuka pengunjung.
- **Semua link (Shopee/TikTok/Order Now/media sosial) harus alamat lengkap**, diawali
  `https://` — kalau ditempel dari browser biasanya sudah otomatis lengkap.
- **Uji coba tombol WhatsApp** setiap habis mengubah nomor atau template pesan di
  Pengaturan — buka salah satu halaman produk di situs publik dan klik tombolnya,
  pastikan nomor dan isi pesannya sudah benar sebelum dibagikan ke pembeli.
- **Uji coba tombol WhatsApp** setiap habis mengubah nomor atau template pesan.

## 7. Fitur Marketplace (Checkout, Pembayaran, Ongkir, Cabang)

Situs kini bisa menerima pesanan & pembayaran langsung, bukan cuma link keluar. Menu-menu baru:

- **Pesanan** — semua order masuk. Klik detail untuk memproses: `Dibayar` → **Proses** →
  **Tandai Dikirim** (isi kurir + resi) → **Tandai Selesai**. Ada tombol **Batalkan** (kalau
  sudah dibayar, sistem otomatis coba refund ke Midtrans; kalau metode bayarnya tak bisa
  refund via API, order tetap dibatalkan dan ditandai "refund gagal" agar Anda refund manual
  di dashboard Midtrans). Badge angka di menu Pesanan = jumlah pesanan dibayar yang belum diproses.
- **Cabang** — kelola lokasi/gudang. Tiap cabang punya stok sendiri, nomor WhatsApp sendiri,
  dan *area asal pengiriman* (untuk hitung ongkir). Saat pembeli checkout, sistem otomatis
  memilih cabang terdekat yang stoknya cukup.
- **Transfer Stok** — satu-satunya cara mengubah stok. "Stok Baru" = barang masuk ke sistem;
  atau pindah antar cabang. Ada tombol input massal (dari halaman Cabang) untuk isi stok awal
  banyak produk sekaligus.
- **Laporan** — pendapatan & produk terlaris per rentang tanggal, bisa diekspor CSV.
- **Riwayat Transaksi** — daftar semua percobaan pembayaran (seperti mutasi rekening).

**Peran pengguna:** akun **Pemilik** melihat semua cabang; akun **Staf Cabang** hanya melihat
pesanan/stok/laporan cabangnya sendiri dan tidak bisa buka Produk/Kategori/Cabang/Pengguna/Pengaturan.

**Menyalakan checkout:** isi kunci Midtrans/Biteship di **Pengaturan**, isi area asal tiap
**Cabang**, lalu centang "Aktifkan checkout" — tombol keranjang baru muncul di situs setelah itu.
Sebelum diaktifkan, situs berperilaku seperti katalog biasa (hanya tombol Shopee/TikTok/WhatsApp).
