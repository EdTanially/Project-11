# CareFlow Hospital Information System — UTS Realistic Edition

Academic simulation of an integrated Hospital Information System (HIS/SIMRS) with role-based access control, EMR/SOAP, nursing documentation, CPOE, LIS, RIS, pharmacy, billing, audit trail, and system administration.

## Demo accounts
All demo accounts use password `password`.

| Role | Email | Fokus |
|---|---|---|
| Administrator / IT | admin@hospital.test | User & Access, System Health |
| Dokter | doctor@hospital.test | Appointment, SOAP/EMR, CPOE, e-prescribing |
| Perawat | nurse@hospital.test | Queue, vital signs, nursing notes |
| Apoteker | pharmacy@hospital.test | Prescription verification/dispensing, inventory |
| Kasir | cashier@hospital.test | Invoice, payment |
| Pasien | patient@hospital.test | Appointment, own vital, own prescription, own billing |
| Pendaftaran | registration@hospital.test | Patient registration, MRN, scheduling |
| Rekam Medis | rekammedis@hospital.test | Read-only chart review & completeness |
| Laboratorium | lab@hospital.test | Lab worklist & result verification |
| Radiologi | radiology@hospital.test | Imaging worklist & reporting |
| Manajemen | management@hospital.test | Aggregate KPI |
| Auditor | auditor@hospital.test | Read-only audit trail |

## Realistic clinical workflow
1. Pendaftaran membuat/menemukan MRN dan episode kunjungan.
2. Appointment menghubungkan pasien dengan dokter dan jadwal pelayanan.
3. Perawat menangani queue/vital signs dan menulis nursing note.
4. Dokter membuka clinical encounter dari appointment miliknya.
5. Dokter mengisi CC, Subjective, Objective, Assessment, Plan, diagnosis, dan allergy.
6. SOAP dapat disimpan sebagai draft; setelah selesai dokter melakukan finalisasi/tanda tangan.
7. SOAP yang sudah signed tidak dapat diedit ulang. Koreksi/penambahan dibuat sebagai addendum yang mencatat author dan timestamp.
8. Dokter membuat CPOE dari encounter miliknya. Laboratory/Radiology memproses worklist dan merilis hasil.
9. Dokter membuat e-prescription dari encounter. Apoteker memverifikasi lalu dispensing; stok obat berkurang saat dispensing.
10. Kasir membuat invoice dan mencatat pembayaran.
11. Rekam Medis melakukan review read-only; Auditor melihat audit trail; Manajemen menerima KPI agregat.

## Prinsip akses
Administrator/IT tidak otomatis memiliki akses untuk mengedit SOAP, nursing note, hasil lab, laporan radiologi, resep, atau billing. Administrator mengelola akun dan kesehatan sistem.

Clinical data menggunakan dua lapisan kontrol:
- Role-based access: modul yang dapat dibuka berdasarkan profesi.
- Record ownership/context: dokter hanya mengedit encounter miliknya; pasien hanya melihat datanya sendiri; unit lab/radiologi hanya mengerjakan order unitnya.

Catatan klinis final bersifat append-only pada level aplikasi: SOAP signed dikunci, kemudian koreksi dicatat sebagai addendum. Nursing note dibuat sebagai catatan historis dan tidak memiliki tombol edit/delete.

## Role access matrix

| Role | Boleh mengubah | Read-only utama | Tidak memiliki akses |
|---|---|---|---|
| Administrator / IT | User & Access, System Health | — | SOAP, nursing notes, vital, CPOE, lab, radiology, resep, billing |
| Dokter | Appointment sendiri, SOAP sendiri sebelum signed, CPOE sendiri, resep sendiri | Vital, nursing note, lab, radiology | Inventory, billing write, user administration |
| Perawat | Queue (triaged/queued), vital signs, nursing notes | SOAP, CPOE, lab, radiology | Resep, billing, user administration |
| Apoteker | Verifikasi/dispensing resep, inventory | Data pasien minimal terkait resep | SOAP editing, nursing notes, billing |
| Kasir | Invoice & payment | — | Clinical record, prescription, nursing |
| Pendaftaran | Patient registration, appointment | Patient master | Clinical documentation |
| Rekam Medis | — | SOAP, nursing, vital, lab, radiology | Clinical editing |
| Laboratorium | Lab result verification | Order dan data identitas minimal | SOAP editing, nursing, billing |
| Radiologi | Radiology report | Order dan data identitas minimal | SOAP editing, nursing, billing |
| Manajemen | — | Aggregate KPI | Patient-level clinical documentation |
| Auditor | — | Audit trail | Editing seluruh data operasional/klinis |
| Pasien | Booking appointment sendiri | Hasil/riwayat milik sendiri | Data pasien lain dan internal workflow |

## Modul
Dashboard role-specific · Pendaftaran & Antrean · Master Patient Index · Appointment · SOAP/EMR · Addendum · Nursing Notes · Vital Signs · CPOE · Laboratory · Radiology · E-Prescribing · Pharmacy Inventory · Billing/Cashier · Role-based KPI · Audit Trail · User & Access · System Health.

## Instalasi XAMPP / VS Code
1. Extract folder `hospital-system` ke `D:\HIS\htdocs\hospital-system`.
2. Jalankan MariaDB/MySQL. CareFlow otomatis mencoba port `3307` lalu `3306`; port khusus bisa diatur dengan `CAREFLOW_DB_PORT`.
3. Buka `http://localhost/hospital-system/auth/login.php`.
4. Schema compatibility akan memastikan tabel penting tersedia pada database lama; aplikasi tidak melakukan DROP database otomatis.
5. Untuk clean installation, import `database/hospital.sql` satu kali lewat phpMyAdmin.
6. Setelah login Administrator, buka `System Health`.

## Catatan akademik
Ini adalah simulasi untuk UTS/akademik, bukan perangkat lunak klinis untuk penggunaan pasien nyata. Threshold vital dan data demo digunakan untuk menunjukkan konsep SIMRS/HIS dan bukan instruksi diagnosis atau terapi.
