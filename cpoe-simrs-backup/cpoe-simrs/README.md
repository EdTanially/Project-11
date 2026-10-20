# CPOE SIMRS — Order Management

Prototype aplikasi PHP + MySQL (XAMPP) untuk modul CPOE Order Management. Struktur dan alur fitur mengikuti SOP dan use case yang diberikan.

## Fitur utama
- Login + RBAC untuk 7 role: Dokter, Perawat/Bidan, Petugas Laboratorium, Petugas Radiologi, Petugas Farmasi, Petugas Gizi, Administrator SIMRS.
- Dashboard command center berwarna teal.
- Pembuatan order: Laboratorium, Radiologi, Farmasi, Tindakan Medis, Konsultasi, Diet.
- Validasi field wajib.
- Alert alergi obat berbasis data alergi pasien + deteksi duplikasi order aktif.
- Distribusi order ke unit tujuan.
- Work queue unit untuk dokumentasi pelaksanaan/hasil.
- Perubahan/pembatalan dengan alasan dan audit trail.
- Status order: Menunggu Validasi, Dikirim, Diproses, Dijadwalkan, Selesai, Perlu Revisi, Dibatalkan.
- Audit trail.
- Master pasien demo.

## Instalasi XAMPP
1. Copy folder `cpoe-simrs` ke `C:\xampp\htdocs\`.
2. Start Apache dan MySQL dari XAMPP Control Panel.
3. Buka phpMyAdmin → Import → `database.sql`.
4. Pastikan koneksi database pada `config/config.php` sesuai XAMPP kamu.
5. Buka `http://localhost/cpoe-simrs/`.

## Demo akun
- dokter / dokter123
- perawat / perawat123
- lab / lab123
- radiologi / radiologi123
- farmasi / farmasi123
- gizi / gizi123
- admin / admin123

## Catatan
Ini merupakan prototype akademik/demonstrasi. Untuk produksi rumah sakit, tambahkan HTTPS wajib, password_hash/password_verify, session hardening, CSRF policy yang lebih ketat, authorization per resource, encryption, backup, logging terpusat, approval/signature elektronik yang sah, integrasi HIS/EMR, serta kebijakan keamanan institusi.
