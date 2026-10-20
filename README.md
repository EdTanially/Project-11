
## Persyaratan

Sebelum menjalankan aplikasi, siapkan:

- PHP dan Apache melalui XAMPP.
- MariaDB yang dijalankan secara terpisah melalui Command Prompt.
- phpMyAdmin untuk pengelolaan database.
- Browser web.
- Git (opsional, untuk pengembangan).

## Konfigurasi Server Database

Proyek ini menggunakan MariaDB yang dijalankan secara terpisah dari layanan database pada XAMPP.

Konfigurasi koneksi database lokal:

| Pengaturan | Nilai |
|---|---|
| Database server | MariaDB |
| Host | `127.0.0.1` |
| Port | `3307` |
| Username | `root` (sesuaikan jika berbeda) |
| Password | Sesuai konfigurasi lokal |
| Database | Sesuai modul yang dijalankan |

**Penting:** Port database yang digunakan pada lingkungan pengembangan ini adalah `3307`, bukan port default `3306`. Pastikan MariaDB berjalan dan aplikasi PHP menggunakan port `3307` saat membuat koneksi database.

Port tersebut dapat berbeda pada komputer lain. Jika menggunakan konfigurasi yang berbeda, sesuaikan port pada konfigurasi koneksi aplikasi dan phpMyAdmin.

## Instalasi dan Konfigurasi

1. Clone atau unduh repository ini.
2. Tempatkan folder masing-masing modul di direktori web server lokal, misalnya `htdocs`.
3. Jalankan Apache melalui XAMPP.
4. Jalankan MariaDB melalui Command Prompt menggunakan konfigurasi port `3307`.
5. Pastikan MariaDB menerima koneksi melalui host `127.0.0.1` dan port `3307`.
6. Buat database untuk masing-masing modul sesuai konfigurasi aplikasi.
7. Impor skrip SQL yang tersedia di folder `database/` melalui phpMyAdmin atau command line.
8. Periksa konfigurasi koneksi database pada masing-masing modul.
9. Pastikan host, port, nama database, username, dan password sesuai dengan lingkungan lokal.
10. Buka aplikasi melalui browser.

Contoh alamat lokal:

- Patient Registration: `http://localhost/patient-registration/`
- CPOE: `http://localhost/cpoe/`
- Nursing: `http://localhost/nursing/`

Alamat tersebut merupakan contoh. Sesuaikan dengan nama folder proyek yang sebenarnya.

## Konfigurasi Koneksi PHP

Pastikan setiap modul menggunakan konfigurasi koneksi database yang sesuai.

Contoh konfigurasi PDO untuk MariaDB:

```php
$host = '127.0.0.1';
$port = '3307';
$dbname = 'nama_database';
$username = 'root';
$password = '';

$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

$pdo = new PDO($dsn, $username, $password);
```

Ganti `nama_database` dengan nama database yang digunakan oleh modul terkait. Jika akun database menggunakan password, isi variabel `$password` sesuai konfigurasi lokal.

Contoh di atas hanya digunakan sebagai referensi. Sesuaikan dengan struktur kode koneksi yang sudah digunakan pada masing-masing modul.

## Pengelolaan Database

Setiap modul dapat menggunakan database yang terpisah. Pastikan aplikasi terhubung ke database yang benar dan lakukan backup sebelum mengubah struktur atau data.

Jangan mengunggah password, kredensial, atau data pasien asli ke repository GitHub. Gunakan data dummy atau sintetis untuk demonstrasi dan pengujian.
