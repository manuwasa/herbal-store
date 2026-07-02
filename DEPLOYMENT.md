# Panduan Deploy ke Production

Dua skenario dibahas di sini: **shared hosting cPanel** (paling umum dipakai untuk
toko kecil di Indonesia — Niagahoster, Rumahweb, dll) dan **VPS** (server sendiri
dengan akses root penuh). Baca bagian **Persiapan** dulu (berlaku untuk keduanya),
lalu lanjut ke bagian sesuai jenis hosting Anda.

## Persiapan (Berlaku untuk Semua Jenis Hosting)

### Syarat server
- PHP **8.2 atau lebih baru**, dengan ekstensi: `mbstring`, `openssl`, `pdo_mysql`,
  `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, **`gd`** (dibutuhkan untuk
  validasi upload gambar produk/logo/banner di panel admin — tanpa ini, upload gambar
  akan gagal validasi meski PHP-nya sendiri jalan normal).
- MySQL 8 / MariaDB 10.3+.
- Composer terinstal (di VPS Anda yang install; di shared hosting cPanel biasanya
  sudah tersedia lewat menu **Setup Node.js/PHP App** atau **Terminal**, tergantung provider).

### Siapkan sebelum mulai
1. **Buat database MySQL** untuk aplikasi ini beserta user & passwordnya (lewat
   phpMyAdmin di cPanel, atau `mysql` CLI di VPS). Catat nama database, user, password.
2. **Siapkan domain/subdomain** yang akan dipakai, dan pastikan DNS-nya sudah
   mengarah ke server sebelum lanjut ke langkah HTTPS.
3. **Compile CSS dari lokal** kalau ada perubahan Tailwind yang belum di-build:
   `bin/tailwindcss.exe -i resources/css/app.css -o public/css/app.css --minify`
   di komputer development Anda, lalu commit/upload hasilnya (`public/css/app.css`)
   bersama kode — server production **tidak perlu** punya binary Tailwind CLI sama
   sekali, cukup file hasil compile-nya.
4. Siapkan isi `.env` production (jangan pernah pakai nilai dari `.env` lokal apa
   adanya):
   ```
   APP_NAME="Nama Toko Anda"
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://domain-anda.com

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_database
   DB_USERNAME=user_database
   DB_PASSWORD=password_database
   ```
   `APP_KEY` akan digenerate di langkah deploy (jangan disalin dari `.env` lokal —
   setiap environment harus punya key sendiri). `APP_DEBUG=false` **wajib** di
   production supaya error tidak menampilkan stack trace `Illuminate\...` ke publik.

---

## Opsi A: Shared Hosting (cPanel)

Tantangan utama di shared hosting: Laravel harus diakses lewat folder `public/`-nya,
padahal cPanel biasanya mengarahkan domain langsung ke `public_html`. Pilih salah satu
pendekatan di bawah tergantung fitur yang tersedia di provider Anda.

### Upload kode

- Kalau provider menyediakan **Terminal/SSH** di cPanel: paling mudah lewat
  `git clone` langsung di server, lalu `composer install --no-dev --optimize-autoloader`
  di server itu juga.
- Kalau **tidak ada Terminal/SSH** (banyak paket murah tidak menyertakan ini): jalankan
  `composer install --no-dev --optimize-autoloader` **di komputer lokal**, lalu upload
  seluruh folder proyek (termasuk folder `vendor/` hasil install tadi) lewat File
  Manager cPanel atau FTP. Ini satu-satunya cara kalau server tidak kasih akses
  command line sama sekali.

### Mengarahkan domain ke folder `public/`

**Kalau cPanel Anda punya opsi "Document Root" saat menambah domain/subdomain**
(banyak provider modern sudah mendukung ini di menu **Domains**): upload seluruh
proyek ke luar `public_html` (mis. `~/herbal-store/`), lalu set document root domain
tsb langsung ke `~/herbal-store/public`. Ini pendekatan paling bersih — tidak perlu
mengubah file apa pun.

**Kalau tidak bisa mengubah document root** (domain utama akun cPanel biasanya
terkunci ke `public_html`): 
1. Upload seluruh proyek ke folder **di luar** `public_html`, misalnya `~/herbal-app/`.
2. Salin (bukan pindahkan) **isi** folder `~/herbal-app/public/` ke `public_html/`.
3. Edit `public_html/index.php` hasil salinan tadi — ubah 2 baris `require` di
   dalamnya supaya menunjuk ke lokasi baru:
   ```php
   require __DIR__.'/../herbal-app/vendor/autoload.php';
   $app = require_once __DIR__.'/../herbal-app/bootstrap/app.php';
   ```
   (sesuaikan `../herbal-app` dengan path folder proyek Anda yang sebenarnya relatif
   terhadap `public_html`.)
4. Setiap kali ada file baru di folder `public/` (jarang terjadi, tapi misalnya
   menambah gambar statis baru), ulangi langkah salin di poin 2.

### Setup aplikasi

Lewat Terminal cPanel kalau tersedia (kalau tidak ada, sebagian provider punya menu
**"Cron Job"** yang bisa dipakai sekali untuk menjalankan perintah `artisan` di bawah
sebagai pengganti terminal — jalankan sekali lalu hapus cron-nya):

```bash
cp .env.example .env   # lalu edit isinya sesuai bagian Persiapan di atas
php artisan key:generate --force
php artisan migrate --force
php artisan storage:link
```

Kalau `storage:link` gagal dengan error terkait symlink (beberapa shared hosting
menonaktifkan fungsi `symlink()` demi keamanan), coba `php artisan storage:link --relative`.
Kalau tetap gagal, hubungi support hosting untuk mengaktifkan `symlink()`, atau
pertimbangkan pindah ke VPS — tanpa symlink ini, gambar produk/logo/banner yang
diupload lewat panel admin tidak akan bisa diakses publik.

### Permission folder

Pastikan folder `storage/` dan `bootstrap/cache/` bisa ditulis oleh PHP (biasanya
`755`, kadang perlu `775` tergantung konfigurasi provider):
```bash
chmod -R 755 storage bootstrap/cache
```

### PHP version & SSL

- Pilih PHP 8.2+ lewat menu **MultiPHP Manager** di cPanel untuk domain ini.
- Aktifkan HTTPS lewat **SSL/TLS Status** → **AutoSSL** (gratis, otomatis perpanjang),
  atau plugin Let's Encrypt kalau AutoSSL tidak tersedia di paket Anda.

---

## Opsi B: VPS (Ubuntu + Nginx)

Asumsi: Ubuntu 22.04/24.04, akses root/sudo, domain sudah mengarah ke IP server.

### Install stack

```bash
sudo apt update
sudo apt install -y nginx mysql-server php8.2-fpm php8.2-mysql php8.2-mbstring \
    php8.2-xml php8.2-bcmath php8.2-curl php8.2-gd php8.2-zip git unzip
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### Ambil kode & install dependency

```bash
sudo mkdir -p /var/www/herbal-store
sudo chown $USER:$USER /var/www/herbal-store
git clone <url-repo-anda> /var/www/herbal-store
cd /var/www/herbal-store
composer install --no-dev --optimize-autoloader
cp .env.example .env   # lalu edit isinya sesuai bagian Persiapan di atas
php artisan key:generate --force
php artisan migrate --force
php artisan storage:link
```

### Kepemilikan & permission

```bash
sudo chown -R www-data:www-data /var/www/herbal-store
sudo chmod -R 755 /var/www/herbal-store/storage /var/www/herbal-store/bootstrap/cache
```

### Konfigurasi Nginx

Buat `/etc/nginx/sites-available/herbal-store`:

```nginx
server {
    listen 80;
    server_name domain-anda.com;
    root /var/www/herbal-store/public;

    index index.php;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Aktifkan dan reload:
```bash
sudo ln -s /etc/nginx/sites-available/herbal-store /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

### HTTPS

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d domain-anda.com
```
Certbot otomatis mengubah konfigurasi Nginx di atas untuk redirect ke HTTPS dan
mengatur perpanjangan sertifikat otomatis.

### Firewall

```bash
sudo ufw allow 22    # SSH — jangan lupa, atau Anda akan terkunci dari server sendiri
sudo ufw allow 80
sudo ufw allow 443
sudo ufw enable
```

---

## Setelah Deploy (Berlaku untuk Semua Jenis Hosting)

1. **Cache konfigurasi** untuk performa (opsional tapi disarankan):
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
   **Catatan penting:** kalau nanti mengubah isi `.env` di server, `config:cache` harus
   dijalankan ulang (`php artisan config:cache`) — kalau tidak, Laravel tetap memakai
   nilai `.env` yang lama karena sudah di-cache. Kalau lupa dan aplikasi berperilaku
   aneh setelah ganti `.env`, jalankan `php artisan config:clear` untuk reset dulu.
2. **Ganti password admin default** — jangan biarkan `admin@herbalstore.test` /
   `password` aktif di production. Login sekali dengan akun default, buat akun admin
   baru lewat menu **Pengguna** di panel admin, lalu hapus akun default tersebut (lihat
   langkah lengkapnya di `USER-GUIDE.md` bagian 4 — tidak butuh `tinker` atau akses
   database langsung lagi).
3. **Cek langsung di browser**: halaman depan tampil, gambar produk/logo/banner muncul
   (ini membuktikan `storage:link` berhasil), form login admin bisa dipakai, dan
   tombol WhatsApp/Shopee/TikTok mengarah ke tujuan yang benar.
4. Simpan `.env` production di tempat aman (password manager/vault) — file ini tidak
   pernah ikut ke Git, jadi kalau server hilang/reset, ini satu-satunya salinan
   kredensial database & `APP_KEY` Anda.
5. **WAJIB untuk marketplace — jalankan queue worker.** Webhook pembayaran Midtrans
   memproses status pesanan lewat **queued job** (driver `database`). Tanpa worker yang
   jalan, pesanan yang dibayar **tidak akan pernah** berubah jadi "paid" dan stok tidak
   berkurang. Jalankan `php artisan queue:work --tries=3` sebagai proses persisten:
   - VPS: buat service **Supervisor** (atau systemd) yang menjaga `php artisan queue:work`
     tetap hidup dan auto-restart.
   - Shared hosting tanpa proses persisten: pakai **cron** tiap menit
     `php artisan queue:work --stop-when-empty` sebagai gantinya.
   Jalankan ulang worker (`php artisan queue:restart`) setiap deploy kode baru.
6. **Konfigurasi gateway di panel admin, bukan `.env`.** Kunci Midtrans (client/server),
   Biteship API key, dan reCAPTCHA diisi di menu **Pengaturan** (disimpan terenkripsi di DB).
   Aktifkan "Payment Gateway" dan "Ongkir Otomatis" hanya setelah kunci produksi terisi &
   teruji di sandbox. Tiap **cabang** juga perlu diisi *area asal pengiriman* (menu Cabang)
   agar ongkir otomatis akurat — kalau kosong, checkout tetap jalan tapi ongkir "diatur via WhatsApp".
7. **Timezone & HTTPS.** `config/app.php` sudah di-set `Asia/Jakarta` (memengaruhi batas
   kedaluwarsa pembayaran 2 jam & batas "hari" di laporan). Pastikan HTTPS aktif dan set
   `SESSION_SECURE_COOKIE=true` sebelum menerima pembayaran & data pelanggan asli.

## Update Kode di Kemudian Hari

Setiap kali ada perubahan kode yang perlu di-deploy ulang:

```bash
git pull                                    # atau upload ulang file yang berubah
composer install --no-dev --optimize-autoloader   # kalau ada dependency baru
php artisan migrate --force                 # kalau ada migration baru
php artisan config:clear && php artisan config:cache   # kalau .env berubah atau pakai cache
```
Kalau ada perubahan class Tailwind, build ulang CSS di lokal dan upload
`public/css/app.css` yang baru — jangan lupakan langkah ini, halaman akan tampil
tanpa styling terbaru kalau file CSS-nya tidak ikut ter-update di server.

## Troubleshooting Umum

| Gejala | Kemungkinan penyebab |
|---|---|
| Halaman putih / error 500 tanpa detail | `APP_DEBUG=false` menyembunyikan detail error dari publik (memang seharusnya begitu) — cek detail sebenarnya di `storage/logs/laravel.log` di server. |
| Error terkait `APP_KEY`/enkripsi | `php artisan key:generate --force` belum pernah dijalankan di server ini, atau `.env` tertimpa/hilang saat upload ulang. |
| Gambar produk/logo/banner tidak muncul (ikon rusak) | `php artisan storage:link` belum berhasil — cek apakah `public/storage` di server benar-benar berupa symlink (bukan folder kosong). Umum terjadi di shared hosting yang menonaktifkan `symlink()`. |
| Perubahan `.env` tidak berefek | Konfigurasi sudah di-cache lewat `config:cache` sebelumnya — jalankan `php artisan config:clear` lalu `config:cache` lagi. |
| Upload gambar gagal terus padahal file valid | Cek ekstensi PHP `gd` terpasang di server (dibutuhkan validasi rule `image`), dan cek `upload_max_filesize`/`post_max_size` di `php.ini` tidak lebih kecil dari batas yang divalidasi aplikasi (2MB untuk gambar produk/banner). |
| Login admin menampilkan "Too Many Requests" / error 429 | Rate limiter bawaan (`throttle:login`, 5 percobaan/menit per kombinasi email+IP — lihat `DEVELOPER.md`) sedang aktif, biasanya karena salah password berulang kali. Normal, bukan bug — tunggu sekitar satu menit lalu coba lagi. |
