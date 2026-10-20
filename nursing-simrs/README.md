# Nursing SIMRS — Hospital Interface

Web application untuk modul dokumentasi keperawatan berbasis PHP + MySQL/MariaDB.

## Konfigurasi
- Host database: `127.0.0.1`
- Port database: `3307`
- Database: `nursing_simrs`
- User: `root`
- Password: kosong (sesuai konfigurasi XAMPP default)

## Akses
1. Import `database.sql` ke MySQL/MariaDB bila database belum tersedia.
2. Simpan folder `nursing-simrs` di dalam `htdocs`.
3. Jalankan Apache dan MySQL/MariaDB pada port 3307.
4. Buka `http://localhost/nursing-simrs/`.

## Akun
- Perawat: `perawat` / `perawat123`
- Bidan: `bidan` / `bidan123`
- Dokter: `dokter` / `dokter123`
- Farmasi: `farmasi` / `farmasi123`
- Admin: `admin` / `admin123`

Antarmuka menggunakan session login, role-based navigation, tombol logout pada sidebar dan profil pengguna, serta audit trail aktivitas.
