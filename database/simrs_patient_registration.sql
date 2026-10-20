-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Erstellungszeit: 20. Okt 2026 um 09:53
-- Server-Version: 10.4.32-MariaDB
-- PHP-Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `simrs_patient_registration`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `clinic_id` int(11) DEFAULT NULL,
  `clinic` varchar(120) NOT NULL,
  `doctor` varchar(150) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `appointment_no` varchar(40) NOT NULL,
  `status` varchar(30) NOT NULL,
  `reminder_status` varchar(30) DEFAULT 'Terkirim',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `clinic_id`, `clinic`, `doctor`, `appointment_date`, `appointment_time`, `appointment_no`, `status`, `reminder_status`, `created_at`) VALUES
(1, 1, 1, 'Poli Penyakit Dalam', 'dr. Andi Pratama, Sp.PD', '2026-09-24', '09:00:00', 'APT-26001', 'confirmed', 'Terkirim', '2026-09-23 12:26:24'),
(2, 2, 4, 'Poli Obstetri & Ginekologi', 'dr. Dina Maharani, Sp.OG', '2026-09-24', '10:00:00', 'APT-26002', 'confirmed', 'Terkirim', '2026-09-23 12:26:24'),
(3, 4, 2, 'Poli Jantung', 'dr. Bima Wijaya, Sp.JP', '2026-09-25', '13:00:00', 'APT-26003', 'confirmed', 'Terkirim', '2026-09-23 12:26:24'),
(7, 6, 54, 'Poli Saraf', 'dr. Eka Permata, Sp.S', '2026-09-26', '14:00:00', 'APT-26004', 'pending', 'Terkirim', '2026-09-23 12:37:43'),
(8, 65, 1, 'Poli Penyakit Dalam', 'dr. Andi Pratama, Sp.PD', '2026-09-27', '11:00:00', 'APT-26005', 'confirmed', 'Terkirim', '2026-09-23 12:37:43'),
(9, 66, 2, 'Poli Jantung', 'dr. Bima Wijaya, Sp.JP', '2026-09-27', '15:00:00', 'APT-26006', 'pending', 'Terkirim', '2026-09-23 12:37:43');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(60) NOT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `table_name`, `record_id`, `details`, `created_at`) VALUES
(1, 1, 'LOGIN', 'users', 1, 'Seed akun demo dan audit log awal', '2026-09-23 12:28:47'),
(2, 1, 'CREATE', 'patients', 1, 'Registrasi pasien baru contoh', '2026-09-23 12:28:47'),
(3, 1, 'VERIFY_INSURANCE', 'insurance_verifications', 1, 'Verifikasi BPJS contoh', '2026-09-23 12:28:47'),
(4, 1, 'LOGIN', 'users', 1, 'Login berhasil', '2026-09-23 12:30:07'),
(5, 1, 'SYSTEM_READY', 'users', 1, 'Konfigurasi Patient Registration aktif di port 3307', '2026-09-23 12:37:44'),
(6, 1, 'CREATE', 'patients', 1, 'Registrasi pasien contoh', '2026-09-23 12:37:44'),
(7, 1, 'LOGIN', 'users', 1, 'Login berhasil', '2026-09-23 12:39:02'),
(8, 1, 'CREATE_PATIENT', 'patients', 117, 'Pasien baru RM202600069 • registrasi Poli Jantung', '2026-09-23 12:40:36'),
(9, 1, 'CREATE_VISIT', 'visits', 16, 'Kunjungan pertama • antrean A022', '2026-09-23 12:40:36');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `clinics`
--

CREATE TABLE `clinics` (
  `id` int(11) NOT NULL,
  `code` varchar(30) DEFAULT NULL,
  `name` varchar(120) DEFAULT NULL,
  `floor` varchar(50) DEFAULT NULL,
  `status` varchar(30) DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `name` varchar(120) DEFAULT NULL,
  `specialty` varchar(100) DEFAULT NULL,
  `clinic_id` int(11) DEFAULT NULL,
  `license_no` varchar(60) DEFAULT NULL,
  `status` varchar(30) DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `insurance`
--

CREATE TABLE `insurance` (
  `id` int(11) NOT NULL,
  `name` varchar(120) DEFAULT NULL,
  `type` varchar(60) DEFAULT NULL,
  `status` varchar(30) DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `insurance_verifications`
--

CREATE TABLE `insurance_verifications` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `payer` varchar(60) NOT NULL,
  `member_no` varchar(80) DEFAULT NULL,
  `status` varchar(50) NOT NULL,
  `eligibility` varchar(100) DEFAULT 'Terverifikasi',
  `note` text DEFAULT NULL,
  `verified_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `insurance_verifications`
--

INSERT INTO `insurance_verifications` (`id`, `patient_id`, `payer`, `member_no`, `status`, `eligibility`, `note`, `verified_at`) VALUES
(1, 1, 'BPJS', '0001876543210', 'AKTIF', 'Layanan Rawat Jalan', 'Peserta aktif', '2026-09-23 12:26:24'),
(2, 2, 'BPJS', '0001876543211', 'AKTIF', 'Layanan Rawat Jalan', 'FKTP sesuai', '2026-09-23 12:26:24'),
(3, 3, 'Asuransi', 'INS-2026-7788', 'AKTIF', 'Rawat Jalan', 'Plafon tersedia', '2026-09-23 12:26:24'),
(4, 4, 'Pribadi', '-', 'SELF PAY', 'Pembayaran Mandiri', '', '2026-09-23 12:26:24'),
(5, 5, 'BPJS', '0001876543214', 'AKTIF', 'Layanan Rawat Jalan', 'Tidak ada tunggakan', '2026-09-23 12:26:24'),
(6, 1, 'BPJS', '0001876543210', 'AKTIF', 'Layanan Rawat Jalan', 'Peserta aktif dan sesuai layanan', '2026-09-23 12:37:42'),
(7, 2, 'BPJS', '0001876543211', 'AKTIF', 'Layanan Rawat Jalan', 'FKTP sesuai', '2026-09-23 12:37:42'),
(8, 3, 'Asuransi', 'INS-2026-7788', 'AKTIF', 'Rawat Jalan', 'Plafon tersedia', '2026-09-23 12:37:42'),
(9, 4, 'Pribadi', '-', 'SELF PAY', 'Pembayaran Mandiri', '', '2026-09-23 12:37:43'),
(10, 5, 'BPJS', '0001876543214', 'AKTIF', 'Layanan Rawat Jalan', 'Tidak ada tunggakan', '2026-09-23 12:37:43'),
(11, 6, 'BPJS', '0001876543216', 'AKTIF', 'Layanan Rawat Jalan', 'Peserta aktif', '2026-09-23 12:37:43'),
(12, 7, 'Pribadi', '-', 'SELF PAY', 'Pembayaran Mandiri', '', '2026-09-23 12:37:43'),
(13, 65, 'BPJS', '0001876543220', 'AKTIF', 'Layanan Rawat Jalan', 'Peserta aktif', '2026-09-23 12:37:43');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `master_clinics`
--

CREATE TABLE `master_clinics` (
  `id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(120) NOT NULL,
  `schedule` varchar(120) DEFAULT 'Senin–Jumat 08:00–16:00',
  `active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `master_clinics`
--

INSERT INTO `master_clinics` (`id`, `code`, `name`, `schedule`, `active`) VALUES
(1, 'PL01', 'Poli Penyakit Dalam', 'Senin–Jumat 08:00–16:00', 1),
(2, 'JG01', 'Poli Jantung', 'Senin–Jumat 08:00–15:00', 1),
(3, 'AN01', 'Poli Anak', 'Senin–Jumat 08:00–15:00', 1),
(4, 'OB01', 'Poli Obstetri & Ginekologi', 'Senin–Jumat 08:00–15:00', 1),
(5, 'IGD01', 'Instalasi Gawat Darurat', '24 Jam', 1),
(6, 'LAB01', 'Laboratorium', 'Senin–Sabtu 07:00–18:00', 1),
(7, 'RAD01', 'Radiologi', 'Senin–Sabtu 08:00–17:00', 1),
(54, 'SP01', 'Poli Saraf', 'Senin–Jumat 08:00–15:00', 1),
(55, 'MA01', 'Poli Mata', 'Senin–Jumat 08:00–15:00', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `master_doctors`
--

CREATE TABLE `master_doctors` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `specialty` varchar(100) NOT NULL,
  `clinic_id` int(11) DEFAULT NULL,
  `schedule` varchar(120) DEFAULT 'Senin–Jumat',
  `active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `master_doctors`
--

INSERT INTO `master_doctors` (`id`, `name`, `specialty`, `clinic_id`, `schedule`, `active`) VALUES
(1, 'dr. Andi Pratama, Sp.PD', 'Spesialis Penyakit Dalam', 1, 'Senin–Jumat', 1),
(2, 'dr. Bima Wijaya, Sp.JP', 'Spesialis Jantung & Pembuluh Darah', 2, 'Senin–Jumat', 1),
(3, 'dr. Citra Lestari, Sp.A', 'Spesialis Anak', 3, 'Senin–Jumat', 1),
(4, 'dr. Dina Maharani, Sp.OG', 'Spesialis Obstetri & Ginekologi', 4, 'Senin–Jumat', 1),
(5, 'dr. Eka Permata, Sp.S', 'Spesialis Saraf', 54, 'Senin–Jumat', 1),
(6, 'dr. Fajar Santoso, Sp.M', 'Spesialis Mata', 55, 'Senin–Jumat', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `medical_record_no` varchar(30) NOT NULL,
  `nik` varchar(30) NOT NULL,
  `name` varchar(150) NOT NULL,
  `dob` date NOT NULL,
  `gender` enum('L','P') NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `blood_type` varchar(5) DEFAULT NULL,
  `marital_status` varchar(30) DEFAULT NULL,
  `emergency_contact` varchar(150) DEFAULT NULL,
  `duplicate_flag` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `patients`
--

INSERT INTO `patients` (`id`, `medical_record_no`, `nik`, `name`, `dob`, `gender`, `phone`, `address`, `blood_type`, `marital_status`, `emergency_contact`, `duplicate_flag`, `created_at`) VALUES
(1, 'RM20260001', '3174015501010001', 'Ahmad Fauzan', '1985-02-14', 'L', '081234567801', 'Jl. Melati No. 12, Tangerang', 'O', 'Menikah', 'Siti Fauziah', 0, '2026-09-23 12:26:22'),
(2, 'RM20260002', '3174016207020002', 'Siti Nurhaliza', '1992-07-02', 'P', '081234567802', 'Jl. Mawar No. 8, Tangerang', 'A', 'Menikah', 'Budi Santoso', 0, '2026-09-23 12:26:23'),
(3, 'RM20260003', '3174011003050003', 'Rizky Maulana', '2005-03-10', 'L', '081234567803', 'Perumahan Citra, Tangerang', 'B', 'Belum Menikah', 'Hendra Maulana', 0, '2026-09-23 12:26:23'),
(4, 'RM20260004', '3174014511120004', 'Maria Cecilia', '1988-12-05', 'P', '081234567804', 'Jl. Pahlawan No. 22, Tangerang', 'AB', 'Menikah', 'Anton Wijaya', 0, '2026-09-23 12:26:23'),
(5, 'RM20260005', '3174016008060005', 'Dewi Lestari', '2000-08-20', 'P', '081234567805', 'Jl. Kenanga No. 4, Tangerang', 'O', 'Belum Menikah', 'Rina Lestari', 0, '2026-09-23 12:26:23'),
(6, 'RM20260006', '3174017209090006', 'Fajar Hidayat', '1978-09-12', 'L', '081234567806', 'Jl. Anggrek No. 17, Tangerang', 'A', 'Menikah', 'Ani Hidayat', 0, '2026-09-23 12:26:23'),
(7, 'RM20260007', '3174013004110007', 'Nadia Putri', '2011-04-30', 'P', '081234567807', 'Jl. Flamboyan No. 6, Tangerang', 'B', 'Belum Menikah', 'Rudi Putra', 0, '2026-09-23 12:26:23'),
(8, 'RM20260008', '3174012210010008', 'Yosef Kurniawan', '1990-10-22', 'L', '081234567808', 'Jl. Wijaya No. 2, Tangerang', 'O', 'Menikah', 'Maya Kurniawan', 0, '2026-09-23 12:26:23'),
(65, 'RM20260009', '3174014310030009', 'Clara Regina', '1995-03-10', 'P', '081234567809', 'Jl. Cendana No. 11, Tangerang', 'A', 'Belum Menikah', 'Rina Regina', 0, '2026-09-23 12:37:41'),
(66, 'RM20260010', '3174015405060010', 'Bagus Setiawan', '1982-06-15', 'L', '081234567810', 'Jl. Pinus No. 7, Tangerang', 'O', 'Menikah', 'Lia Setiawan', 0, '2026-09-23 12:37:41'),
(67, 'RM20260011', '3174016712080011', 'Kevin Jonathan', '1998-12-28', 'L', '081234567811', 'Jl. Bougenville No. 5, Tangerang', 'B', 'Belum Menikah', 'Maria Jonathan', 0, '2026-09-23 12:37:41'),
(68, 'RM20260012', '3174012209010012', 'Rani Wulandari', '1975-09-22', 'P', '081234567812', 'Jl. Dahlia No. 14, Tangerang', 'AB', 'Menikah', 'Tono Wulandari', 0, '2026-09-23 12:37:41'),
(117, 'RM202600069', '327390483745', 'Edward', '2020-06-17', 'L', '8127984901', '-', 'AB', 'Belum Menikah', '-', 0, '2026-09-23 12:40:36');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `registrations`
--

CREATE TABLE `registrations` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `visit_date` date NOT NULL,
  `clinic` varchar(120) DEFAULT NULL,
  `doctor` varchar(120) DEFAULT NULL,
  `visit_type` varchar(40) DEFAULT NULL,
  `queue_no` varchar(20) DEFAULT NULL,
  `payer` varchar(120) DEFAULT NULL,
  `status` varchar(30) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(80) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `role` varchar(60) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `full_name`, `role`, `is_active`, `created_at`) VALUES
(1, 'registrar', 'registration@hospital.test', '$2y$10$7sFFSfNleu3CAbA40GXUieQYPGMjsgfdeWybuF0YQjl18VsIYgp8m', 'Petugas Loket Registrasi', 'Petugas Registrasi', 1, '2026-09-23 12:26:19'),
(2, 'rm', 'rm@hospital.test', '$2y$10$hLI8nkulwmbUW9KEvGwavOOF0Z8juKPq0.W7I40GWLFd6uponCGt2', 'Petugas Administrasi Registrasi', 'Petugas Rekam Medis', 1, '2026-09-23 12:26:20'),
(3, 'doctor', 'doctor@hospital.test', '$2y$10$xA4S59NJ30LZ8Jp9MEE/6OcbcnFVfeE7KB.hc1ZQiOaUQXAJqvWk2', 'dr. Andi Pratama, Sp.PD', 'Dokter', 1, '2026-09-23 12:26:21'),
(4, 'nurse', 'nurse@hospital.test', '$2y$10$j8lu1eB7KTRg.7QWEj3h6uK/xbu6d4Hw4MOmJyHRd4ez2mtpj1ATS', 'Ns. Siti Rahma, S.Kep', 'Perawat/Bidan', 1, '2026-09-23 12:26:21'),
(5, 'admin', 'admin@hospital.test', '$2y$10$brBhxkhhODjygy4tO45j6.8pXrb9qq007cCQTgxLKk.MGP/1Lmkei', 'Administrator SIMRS', 'Administrator SIMRS', 1, '2026-09-23 12:26:21'),
(6, 'management', 'management@hospital.test', '$2y$10$oVeMkpQnmjkHlGfJaD5c9ujb7K1v9LqKAbeDn1r5.ynVobwVcBtwC', 'Manajemen Rumah Sakit', 'Manajemen', 1, '2026-09-23 12:26:21');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `visits`
--

CREATE TABLE `visits` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `clinic_id` int(11) DEFAULT NULL,
  `clinic` varchar(120) NOT NULL,
  `doctor` varchar(150) DEFAULT NULL,
  `queue_no` varchar(20) NOT NULL,
  `service_type` varchar(40) DEFAULT 'Rawat Jalan',
  `status` varchar(30) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `visits`
--

INSERT INTO `visits` (`id`, `patient_id`, `clinic_id`, `clinic`, `doctor`, `queue_no`, `service_type`, `status`, `notes`, `created_at`) VALUES
(1, 1, 1, 'Poli Penyakit Dalam', 'dr. Andi Pratama, Sp.PD', 'A001', 'Rawat Jalan', 'Selesai', 'Kontrol rutin', '2026-09-23 12:26:23'),
(2, 2, 4, 'Poli Obstetri & Ginekologi', 'dr. Dina Maharani, Sp.OG', 'B012', 'Rawat Jalan', 'Menunggu', 'Kontrol kehamilan', '2026-09-23 12:26:23'),
(3, 3, 3, 'Poli Anak', 'dr. Citra Lestari, Sp.A', 'C006', 'Rawat Jalan', 'Dipanggil', 'Demam 2 hari', '2026-09-23 12:26:23'),
(4, 4, 2, 'Poli Jantung', 'dr. Bima Wijaya, Sp.JP', 'A018', 'Rawat Jalan', 'Terdaftar', 'Kontrol hipertensi', '2026-09-23 12:26:23'),
(5, 5, 1, 'Poli Penyakit Dalam', 'dr. Andi Pratama, Sp.PD', 'A019', 'Rawat Jalan', 'Selesai', 'Pemeriksaan berkala', '2026-09-23 12:26:23'),
(6, 1, 1, 'Poli Penyakit Dalam', 'dr. Andi Pratama, Sp.PD', 'A001', 'Rawat Jalan', 'Selesai', 'Kontrol rutin', '2026-09-23 12:37:41'),
(7, 2, 4, 'Poli Obstetri & Ginekologi', 'dr. Dina Maharani, Sp.OG', 'B012', 'Rawat Jalan', 'Menunggu', 'Kontrol kehamilan', '2026-09-23 12:37:41'),
(8, 3, 3, 'Poli Anak', 'dr. Citra Lestari, Sp.A', 'C006', 'Rawat Jalan', 'Dipanggil', 'Demam', '2026-09-23 12:37:41'),
(9, 4, 2, 'Poli Jantung', 'dr. Bima Wijaya, Sp.JP', 'A018', 'Rawat Jalan', 'Terdaftar', 'Kontrol hipertensi', '2026-09-23 12:37:42'),
(10, 5, 1, 'Poli Penyakit Dalam', 'dr. Andi Pratama, Sp.PD', 'A019', 'Rawat Jalan', 'Selesai', 'Pemeriksaan berkala', '2026-09-23 12:37:42'),
(11, 6, 54, 'Poli Saraf', 'dr. Eka Permata, Sp.S', 'A021', 'Rawat Jalan', 'Menunggu', 'Keluhan pusing', '2026-09-23 12:37:42'),
(12, 7, 3, 'Poli Anak', 'dr. Citra Lestari, Sp.A', 'C008', 'Rawat Jalan', 'Terdaftar', 'Kontrol tumbuh kembang', '2026-09-23 12:37:42'),
(13, 8, 55, 'Poli Mata', 'dr. Fajar Santoso, Sp.M', 'D004', 'Rawat Jalan', 'Selesai', 'Pemeriksaan mata', '2026-09-23 12:37:42'),
(14, 65, 1, 'Poli Penyakit Dalam', 'dr. Andi Pratama, Sp.PD', 'A024', 'Rawat Jalan', 'Menunggu', 'Kontrol diabetes', '2026-09-23 12:37:42'),
(15, 66, 2, 'Poli Jantung', 'dr. Bima Wijaya, Sp.JP', 'B021', 'Rawat Jalan', 'Terdaftar', 'Kontrol tekanan darah', '2026-09-23 12:37:42'),
(16, 117, 2, 'Poli Jantung', 'dr. Setiawan', 'A022', 'Rawat Jalan', 'Terdaftar', 'Registrasi pasien baru', '2026-09-23 12:40:36');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `appointment_no` (`appointment_no`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `clinic_id` (`clinic_id`);

--
-- Indizes für die Tabelle `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indizes für die Tabelle `clinics`
--
ALTER TABLE `clinics`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indizes für die Tabelle `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `insurance`
--
ALTER TABLE `insurance`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `insurance_verifications`
--
ALTER TABLE `insurance_verifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indizes für die Tabelle `master_clinics`
--
ALTER TABLE `master_clinics`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indizes für die Tabelle `master_doctors`
--
ALTER TABLE `master_doctors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `clinic_id` (`clinic_id`);

--
-- Indizes für die Tabelle `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `medical_record_no` (`medical_record_no`),
  ADD KEY `idx_nik` (`nik`),
  ADD KEY `idx_name_dob` (`name`,`dob`);

--
-- Indizes für die Tabelle `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indizes für die Tabelle `visits`
--
ALTER TABLE `visits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `clinic_id` (`clinic_id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT für Tabelle `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT für Tabelle `clinics`
--
ALTER TABLE `clinics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `insurance`
--
ALTER TABLE `insurance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `insurance_verifications`
--
ALTER TABLE `insurance_verifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT für Tabelle `master_clinics`
--
ALTER TABLE `master_clinics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=131;

--
-- AUTO_INCREMENT für Tabelle `master_doctors`
--
ALTER TABLE `master_doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT für Tabelle `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=166;

--
-- AUTO_INCREMENT für Tabelle `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT für Tabelle `visits`
--
ALTER TABLE `visits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`clinic_id`) REFERENCES `master_clinics` (`id`) ON DELETE SET NULL;

--
-- Constraints der Tabelle `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints der Tabelle `insurance_verifications`
--
ALTER TABLE `insurance_verifications`
  ADD CONSTRAINT `insurance_verifications_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`);

--
-- Constraints der Tabelle `master_doctors`
--
ALTER TABLE `master_doctors`
  ADD CONSTRAINT `master_doctors_ibfk_1` FOREIGN KEY (`clinic_id`) REFERENCES `master_clinics` (`id`) ON DELETE SET NULL;

--
-- Constraints der Tabelle `visits`
--
ALTER TABLE `visits`
  ADD CONSTRAINT `visits_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `visits_ibfk_2` FOREIGN KEY (`clinic_id`) REFERENCES `master_clinics` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
