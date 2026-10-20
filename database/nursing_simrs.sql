-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Erstellungszeit: 20. Okt 2026 um 09:51
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
-- Datenbank: `nursing_simrs`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `assessments`
--

CREATE TABLE `assessments` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `officer_id` int(11) NOT NULL,
  `chief_complaint` text DEFAULT NULL,
  `consciousness` varchar(100) DEFAULT NULL,
  `airway` varchar(100) DEFAULT NULL,
  `breathing` varchar(100) DEFAULT NULL,
  `circulation` varchar(100) DEFAULT NULL,
  `pain_score` tinyint(4) DEFAULT NULL,
  `fall_risk` varchar(50) DEFAULT NULL,
  `nutrition` varchar(100) DEFAULT NULL,
  `psychosocial` text DEFAULT NULL,
  `assessment_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `assessments`
--

INSERT INTO `assessments` (`id`, `patient_id`, `officer_id`, `chief_complaint`, `consciousness`, `airway`, `breathing`, `circulation`, `pain_score`, `fall_risk`, `nutrition`, `psychosocial`, `assessment_date`) VALUES
(1, 1, 1, 'Demam dan lemas', 'Compos mentis', 'Patent', 'RR meningkat ringan', 'Stabil', 4, 'Sedang', 'Asupan menurun', 'Cemas ringan terkait kondisi klinis', '2026-09-15 16:11:32');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(120) NOT NULL,
  `module` varchar(80) NOT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `detail` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `reference_id`, `detail`, `created_at`) VALUES
(1, 1, 'Login', 'Authentication', NULL, 'Login perawat berhasil', '2026-09-15 16:11:33'),
(2, 1, 'Input Vital Sign', 'Vital Sign', 1, 'TD 110/70, Nadi 96, RR 22, Suhu 38.2, SpO2 97%', '2026-09-15 16:11:33'),
(3, 1, 'Simpan Nursing Note', 'Nursing Note', 1, 'Progress note pasien RM-2026-0001', '2026-09-15 16:11:33'),
(4, 3, 'Review Dokumentasi', 'Doctor View', 1, 'Dokter melihat dokumentasi keperawatan pasien', '2026-09-15 16:11:33'),
(5, 1, 'Login', 'Authentication', NULL, 'Login berhasil', '2026-09-15 16:12:33'),
(6, 1, 'Login', 'Authentication', NULL, 'Login berhasil', '2026-09-15 16:13:54'),
(7, 2, 'Login', 'Authentication', NULL, 'Login berhasil', '2026-09-15 16:23:48'),
(8, 3, 'Login', 'Authentication', NULL, 'Login berhasil', '2026-09-15 16:26:46'),
(9, 4, 'Login', 'Authentication', NULL, 'Login berhasil', '2026-09-15 16:28:10'),
(10, 5, 'Login', 'Authentication', NULL, 'Login berhasil', '2026-09-15 16:29:30'),
(11, 1, 'Login', 'Authentication', NULL, 'Login berhasil', '2026-09-23 15:03:11'),
(12, 2, 'Login', 'Authentication', NULL, 'Login berhasil', '2026-09-23 15:06:16'),
(13, 1, 'Login', 'Authentication', NULL, 'Login berhasil', '2026-09-23 15:13:19'),
(14, 1, 'Logout', 'Authentication', NULL, 'Sesi pengguna diakhiri', '2026-09-23 15:13:42'),
(15, 2, 'Login', 'Authentication', NULL, 'Login berhasil', '2026-09-23 15:13:50'),
(16, 2, 'Logout', 'Authentication', NULL, 'Sesi pengguna diakhiri', '2026-09-23 15:14:08'),
(17, 3, 'Login', 'Authentication', NULL, 'Login berhasil', '2026-09-23 15:14:16'),
(18, 3, 'Logout', 'Authentication', NULL, 'Sesi pengguna diakhiri', '2026-09-23 15:14:27'),
(19, 4, 'Login', 'Authentication', NULL, 'Login berhasil', '2026-09-23 15:14:40'),
(20, 4, 'Logout', 'Authentication', NULL, 'Sesi pengguna diakhiri', '2026-09-23 15:14:50'),
(21, 5, 'Login', 'Authentication', NULL, 'Login berhasil', '2026-09-23 15:15:00'),
(22, 5, 'Logout', 'Authentication', NULL, 'Sesi pengguna diakhiri', '2026-09-23 15:15:09');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `medications`
--

CREATE TABLE `medications` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `ordered_by` int(11) NOT NULL,
  `drug_name` varchar(120) NOT NULL,
  `dose` varchar(80) NOT NULL,
  `route` varchar(50) NOT NULL,
  `frequency` varchar(50) NOT NULL,
  `scheduled_at` datetime NOT NULL,
  `administered_by` int(11) DEFAULT NULL,
  `status` enum('Ordered','Due','Given','Held') DEFAULT 'Ordered',
  `given_at` datetime DEFAULT NULL,
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `medications`
--

INSERT INTO `medications` (`id`, `patient_id`, `ordered_by`, `drug_name`, `dose`, `route`, `frequency`, `scheduled_at`, `administered_by`, `status`, `given_at`, `note`) VALUES
(1, 1, 3, 'Paracetamol', '500 mg', 'PO', '3x1', '2026-09-15 15:00:00', NULL, 'Due', NULL, NULL),
(2, 1, 3, 'Ondansetron', '4 mg', 'IV', 'PRN', '2026-09-15 14:00:00', NULL, 'Given', NULL, NULL),
(3, 2, 3, 'Ceftriaxone', '1 g', 'IV', '2x1', '2026-09-15 16:00:00', NULL, 'Due', NULL, NULL),
(4, 3, 3, 'Ferrous Fumarate', '1 tablet', 'PO', '1x1', '2026-09-15 19:00:00', NULL, 'Ordered', NULL, NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `monitoring`
--

CREATE TABLE `monitoring` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `officer_id` int(11) NOT NULL,
  `parameter_name` varchar(100) NOT NULL,
  `parameter_value` varchar(120) NOT NULL,
  `note` text DEFAULT NULL,
  `monitored_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `monitoring`
--

INSERT INTO `monitoring` (`id`, `patient_id`, `officer_id`, `parameter_name`, `parameter_value`, `note`, `monitored_at`) VALUES
(1, 1, 1, 'Kesadaran', 'Compos mentis', 'Tidak ada perubahan bermakna', '2026-09-15 16:11:33'),
(2, 1, 1, 'SpO2', '97%', 'Stabil pada udara ruangan', '2026-09-15 16:11:33'),
(3, 2, 1, 'Respirasi', '20 x/menit', 'Observasi sesak ringan saat aktivitas', '2026-09-15 16:11:33');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `nursing_notes`
--

CREATE TABLE `nursing_notes` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `officer_id` int(11) NOT NULL,
  `note_type` varchar(50) DEFAULT 'Progress Note',
  `condition_text` text DEFAULT NULL,
  `intervention_text` text DEFAULT NULL,
  `response_text` text DEFAULT NULL,
  `education_text` text DEFAULT NULL,
  `note_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `nursing_notes`
--

INSERT INTO `nursing_notes` (`id`, `patient_id`, `officer_id`, `note_type`, `condition_text`, `intervention_text`, `response_text`, `education_text`, `note_date`) VALUES
(1, 1, 1, 'Progress Note', 'Pasien sadar, kooperatif, keluhan demam masih ada.', 'Monitoring vital sign, kompres hangat, observasi intake cairan.', 'Pasien tampak lebih nyaman setelah intervensi.', 'Edukasi hidrasi dan melaporkan bila kondisi memburuk.', '2026-09-15 16:11:32'),
(2, 2, 1, 'Progress Note', 'Batuk produktif, sesak ringan saat aktivitas.', 'Posisi semi-Fowler dan monitoring SpO2 berkala.', 'SpO2 stabil, sesak berkurang saat istirahat.', 'Ajarkan teknik batuk efektif.', '2026-09-15 16:11:32');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `medical_record_no` varchar(30) NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `birth_date` date NOT NULL,
  `gender` enum('L','P') NOT NULL,
  `room` varchar(50) NOT NULL,
  `diagnosis` varchar(255) DEFAULT NULL,
  `allergies` varchar(255) DEFAULT NULL,
  `status` enum('Active','Observation','Discharged') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `patients`
--

INSERT INTO `patients` (`id`, `medical_record_no`, `full_name`, `birth_date`, `gender`, `room`, `diagnosis`, `allergies`, `status`, `created_at`) VALUES
(1, 'RM-2026-0001', 'Alya Nurfadilah', '1998-04-12', 'P', 'Melati 201', 'Demam berdarah dengue', 'Paracetamol', 'Observation', '2026-09-15 14:11:31'),
(2, 'RM-2026-0002', 'Budi Santoso', '1975-11-23', 'L', 'Melati 203', 'Pneumonia', '-', 'Active', '2026-09-15 14:11:31'),
(3, 'RM-2026-0003', 'Citra Lestari', '1992-08-05', 'P', 'Kebidanan 02', 'Kehamilan 38 minggu', '-', 'Active', '2026-09-15 14:11:31'),
(4, 'RM-2026-0004', 'Dimas Pratama', '1987-01-14', 'L', 'Anggrek 105', 'Diabetes mellitus tipe 2', 'Penicillin', 'Active', '2026-09-15 14:11:31');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `role` enum('nurse','midwife','doctor','pharmacy','admin') NOT NULL,
  `unit` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `role`, `unit`, `created_at`) VALUES
(1, 'perawat', 'perawat123', 'Ners. Alya Rahmawati', 'nurse', 'Rawat Inap - Melati', '2026-09-15 14:11:31'),
(2, 'bidan', 'bidan123', 'Bidan. Sinta Maharani', 'midwife', 'Kamar Bersalin', '2026-09-15 14:11:31'),
(3, 'dokter', 'dokter123', 'dr. Raka Wijaya', 'doctor', 'Tim Medis', '2026-09-15 14:11:31'),
(4, 'farmasi', 'farmasi123', 'Apt. Nadia Putri', 'pharmacy', 'Farmasi', '2026-09-15 14:11:31'),
(5, 'admin', 'admin123', 'Administrator SIMRS', 'admin', 'SIMRS', '2026-09-15 14:11:31');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `vital_signs`
--

CREATE TABLE `vital_signs` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `officer_id` int(11) NOT NULL,
  `systolic` int(11) DEFAULT NULL,
  `diastolic` int(11) DEFAULT NULL,
  `pulse` int(11) DEFAULT NULL,
  `respiration` int(11) DEFAULT NULL,
  `temperature` decimal(4,1) DEFAULT NULL,
  `spo2` int(11) DEFAULT NULL,
  `height_cm` decimal(5,1) DEFAULT NULL,
  `weight_kg` decimal(5,1) DEFAULT NULL,
  `measured_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `vital_signs`
--

INSERT INTO `vital_signs` (`id`, `patient_id`, `officer_id`, `systolic`, `diastolic`, `pulse`, `respiration`, `temperature`, `spo2`, `height_cm`, `weight_kg`, `measured_at`) VALUES
(1, 1, 1, 110, 70, 96, 22, 38.2, 97, 158.0, 54.5, '2026-09-15 16:11:32'),
(2, 2, 1, 128, 82, 88, 20, 37.2, 96, 170.0, 68.2, '2026-09-15 16:11:32'),
(3, 3, 2, 118, 76, 84, 18, 36.8, 98, 160.0, 63.4, '2026-09-15 16:11:32');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `assessments`
--
ALTER TABLE `assessments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `officer_id` (`officer_id`);

--
-- Indizes für die Tabelle `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indizes für die Tabelle `medications`
--
ALTER TABLE `medications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `ordered_by` (`ordered_by`),
  ADD KEY `administered_by` (`administered_by`);

--
-- Indizes für die Tabelle `monitoring`
--
ALTER TABLE `monitoring`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `officer_id` (`officer_id`);

--
-- Indizes für die Tabelle `nursing_notes`
--
ALTER TABLE `nursing_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `officer_id` (`officer_id`);

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
-- Indizes für die Tabelle `vital_signs`
--
ALTER TABLE `vital_signs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `officer_id` (`officer_id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `assessments`
--
ALTER TABLE `assessments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT für Tabelle `medications`
--
ALTER TABLE `medications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `monitoring`
--
ALTER TABLE `monitoring`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `nursing_notes`
--
ALTER TABLE `nursing_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT für Tabelle `vital_signs`
--
ALTER TABLE `vital_signs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `assessments`
--
ALTER TABLE `assessments`
  ADD CONSTRAINT `assessments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `assessments_ibfk_2` FOREIGN KEY (`officer_id`) REFERENCES `users` (`id`);

--
-- Constraints der Tabelle `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints der Tabelle `medications`
--
ALTER TABLE `medications`
  ADD CONSTRAINT `medications_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `medications_ibfk_2` FOREIGN KEY (`ordered_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `medications_ibfk_3` FOREIGN KEY (`administered_by`) REFERENCES `users` (`id`);

--
-- Constraints der Tabelle `monitoring`
--
ALTER TABLE `monitoring`
  ADD CONSTRAINT `monitoring_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `monitoring_ibfk_2` FOREIGN KEY (`officer_id`) REFERENCES `users` (`id`);

--
-- Constraints der Tabelle `nursing_notes`
--
ALTER TABLE `nursing_notes`
  ADD CONSTRAINT `nursing_notes_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `nursing_notes_ibfk_2` FOREIGN KEY (`officer_id`) REFERENCES `users` (`id`);

--
-- Constraints der Tabelle `vital_signs`
--
ALTER TABLE `vital_signs`
  ADD CONSTRAINT `vital_signs_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `vital_signs_ibfk_2` FOREIGN KEY (`officer_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
