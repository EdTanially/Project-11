-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Erstellungszeit: 20. Okt 2026 um 09:50
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
-- Datenbank: `cpoe_simrs`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(80) NOT NULL,
  `entity` varchar(80) NOT NULL,
  `entity_id` int(10) UNSIGNED DEFAULT NULL,
  `detail` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `detail`, `created_at`) VALUES
(1, 1, 'LOGIN', 'session', NULL, 'Login berhasil', '2026-09-15 13:33:39'),
(2, 3, 'LOGIN', 'session', NULL, 'Login berhasil', '2026-09-15 13:34:13'),
(3, 3, 'LOGIN', 'session', NULL, 'Login berhasil', '2026-09-15 13:34:29'),
(4, 5, 'LOGIN', 'session', NULL, 'Login berhasil', '2026-09-15 13:40:34'),
(5, 1, 'LOGIN', 'session', NULL, 'Login berhasil', '2026-09-15 13:43:43'),
(6, 1, 'LOGIN', 'session', NULL, 'Login berhasil', '2026-09-15 13:50:02'),
(7, 1, 'LOGIN', 'session', NULL, 'Login berhasil', '2026-09-15 14:07:02'),
(8, 1, 'LOGIN', 'session', NULL, 'Login berhasil', '2026-09-15 14:07:13'),
(9, 3, 'LOGIN', 'session', NULL, 'Login berhasil', '2026-09-15 14:07:25'),
(10, 1, 'LOGIN', 'session', NULL, 'Login berhasil', '2026-09-15 14:08:57'),
(11, 3, 'LOGIN', 'session', NULL, 'Login berhasil', '2026-09-15 14:09:26'),
(12, 5, 'LOGIN', 'session', NULL, 'Login berhasil', '2026-09-15 14:09:53'),
(13, 7, 'LOGIN', 'session', NULL, 'Login berhasil', '2026-09-15 14:10:28'),
(14, 1, 'LOGIN', 'session', NULL, 'Login berhasil', '2026-09-15 14:12:20'),
(15, 3, 'LOGIN', 'session', NULL, 'Login berhasil', '2026-09-15 14:17:56'),
(16, 5, 'LOGIN', 'session', NULL, 'Login berhasil', '2026-09-15 14:19:35'),
(17, 7, 'LOGIN', 'session', NULL, 'Login berhasil', '2026-09-15 14:20:52'),
(18, 1, 'LOGIN', 'session', NULL, 'Login berhasil', '2026-09-23 15:01:22'),
(19, 3, 'LOGIN', 'session', NULL, 'Login berhasil', '2026-09-23 15:01:39'),
(20, 5, 'LOGIN', 'session', NULL, 'Login berhasil', '2026-09-23 15:01:53');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `orders`
--

CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `patient_id` int(10) UNSIGNED NOT NULL,
  `doctor_id` int(10) UNSIGNED NOT NULL,
  `type` enum('Laboratorium','Radiologi','Farmasi','Tindakan Medis','Konsultasi','Diet') NOT NULL,
  `item_name` varchar(180) NOT NULL,
  `priority` enum('Routine','Urgent','Stat') DEFAULT 'Routine',
  `clinical_note` text DEFAULT NULL,
  `dosage` varchar(100) DEFAULT NULL,
  `route` varchar(80) DEFAULT NULL,
  `schedule_at` datetime DEFAULT NULL,
  `status` enum('Menunggu Validasi','Dikirim','Diproses','Dijadwalkan','Selesai','Dibatalkan','Perlu Revisi') NOT NULL DEFAULT 'Menunggu Validasi',
  `destination_unit` varchar(100) NOT NULL,
  `result_text` text DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `changed_reason` text DEFAULT NULL,
  `validated_at` datetime DEFAULT NULL,
  `executed_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `patients`
--

CREATE TABLE `patients` (
  `id` int(10) UNSIGNED NOT NULL,
  `medical_record_no` varchar(40) NOT NULL,
  `name` varchar(120) NOT NULL,
  `birth_date` date NOT NULL,
  `gender` enum('L','P') NOT NULL,
  `room` varchar(80) DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `diagnosis` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `patients`
--

INSERT INTO `patients` (`id`, `medical_record_no`, `name`, `birth_date`, `gender`, `room`, `allergies`, `diagnosis`, `created_at`) VALUES
(1, 'RM-2026-0001', 'Budi Santoso', '1978-04-12', 'L', 'Anggrek 301', 'Penisilin', 'Pneumonia komunitas', '2026-09-15 13:23:57'),
(2, 'RM-2026-0002', 'Siti Rahma', '1989-09-03', 'P', 'Mawar 205', 'Tidak ada', 'Demam tifoid', '2026-09-15 13:23:57'),
(3, 'RM-2026-0003', 'Andi Wijaya', '2002-02-19', 'L', 'IGD', 'NSAID', 'Nyeri abdomen akut', '2026-09-15 13:23:57');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL,
  `username` varchar(80) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Dokter','Perawat/Bidan','Petugas Laboratorium','Petugas Radiologi','Petugas Farmasi','Petugas Gizi','Administrator SIMRS') NOT NULL,
  `unit_name` varchar(100) DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password`, `role`, `unit_name`, `active`, `created_at`) VALUES
(1, 'dr. Andika Pratama', 'dokter', 'b3959dee9b178b030c2b8373da55a04ab3adb318edeb178953ff8b77301a360a', 'Dokter', 'Rawat Inap', 1, '2026-09-15 13:23:57'),
(2, 'Ns. Rani Putri', 'perawat', 'f44dcd1a0fb5c13451ebf183c7833c9d881865469c4ebb33c4e247e777a63db3', 'Perawat/Bidan', 'Rawat Inap', 1, '2026-09-15 13:23:57'),
(3, 'Ayu Lestari, A.Md.Kes', 'lab', '3705b578e8fcb1b82a94ad917881ec248bbd4111645e91aed3c19af12d82116f', 'Petugas Laboratorium', 'Laboratorium', 1, '2026-09-15 13:23:57'),
(4, 'Bima Saputra, S.Kep', 'radiologi', '266006d9fd3fecdbf7c1ecfd087fd96700688f3b45c2e8b485b33445c48c9183', 'Petugas Radiologi', 'Radiologi', 1, '2026-09-15 13:23:57'),
(5, 'Dina Maharani, S.Farm', 'farmasi', '65c0654c9f0e0ca6968b123aa13843b19ad75fa687ee99151be8ebe18185e5ed', 'Petugas Farmasi', 'Farmasi', 1, '2026-09-15 13:23:57'),
(6, 'Fitri Wulandari, S.Gz', 'gizi', '07fd201b3126da449a9281419242658637bf3ad77d33c81322b7fa52050f6f05', 'Petugas Gizi', 'Gizi', 1, '2026-09-15 13:23:57'),
(7, 'Admin SIMRS', 'admin', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 'Administrator SIMRS', 'SIMRS', 1, '2026-09-15 13:23:57');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_audit_user` (`user_id`);

--
-- Indizes für die Tabelle `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orders_patient` (`patient_id`),
  ADD KEY `fk_orders_doctor` (`doctor_id`);

--
-- Indizes für die Tabelle `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `medical_record_no` (`medical_record_no`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT für Tabelle `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints der Tabelle `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_orders_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
