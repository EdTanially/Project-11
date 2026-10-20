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
-- Datenbank: `hospital_system`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_date` datetime NOT NULL,
  `status` varchar(30) DEFAULT 'booked',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `queue_no` varchar(20) DEFAULT NULL,
  `visit_type` varchar(30) DEFAULT 'new'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `doctor_id`, `appointment_date`, `status`, `notes`, `created_at`, `queue_no`, `visit_type`) VALUES
(1, 3, 1, '2026-09-21 09:51:00', 'booked', 'Sakit dada', '2026-09-21 05:51:53', 'Q-61', 'new');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(120) NOT NULL,
  `module` varchar(80) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `description`, `created_at`) VALUES
(1, 13, 'CREATE', 'REGISTRATION', 3, 'Episode kunjungan dan MRN dibuat oleh petugas pendaftaran.', '2026-09-21 05:50:32'),
(2, 13, 'UPDATE', 'REGISTRATION', 1, 'Status episode menjadi queued', '2026-09-21 05:50:45'),
(3, 13, 'CREATE', 'APPOINTMENT', 1, 'Appointment dibuat.', '2026-09-21 05:51:53'),
(4, 2, 'CREATE', 'ENCOUNTER', 1, 'Clinical encounter dibuat oleh dokter.', '2026-09-21 05:55:08'),
(5, 2, 'SIGN', 'ENCOUNTER', 1, 'SOAP difinalisasi dan ditandatangani oleh dokter.', '2026-09-21 05:57:52'),
(6, 2, 'CREATE', 'CPOE', 1, 'Clinical order dibuat dokter.', '2026-09-21 05:59:53'),
(7, 13, 'CREATE', 'REGISTRATION', 4, 'Episode kunjungan dan MRN dibuat oleh petugas pendaftaran.', '2026-09-21 06:12:04'),
(8, 13, 'CREATE', 'REGISTRATION', 5, 'Episode kunjungan dan MRN dibuat oleh petugas pendaftaran.', '2026-09-21 06:12:10'),
(9, 13, 'CREATE', 'REGISTRATION', 6, 'Episode kunjungan dan MRN dibuat oleh petugas pendaftaran.', '2026-09-21 06:12:11'),
(10, 13, 'CREATE', 'REGISTRATION', 7, 'Episode kunjungan dan MRN dibuat oleh petugas pendaftaran.', '2026-09-21 06:12:12'),
(11, 13, 'CREATE', 'REGISTRATION', 8, 'Episode kunjungan dan MRN dibuat oleh petugas pendaftaran.', '2026-09-21 06:12:14'),
(12, 13, 'CREATE', 'REGISTRATION', 9, 'Episode kunjungan dan MRN dibuat oleh petugas pendaftaran.', '2026-09-21 06:12:17'),
(13, 13, 'UPDATE', 'REGISTRATION', 7, 'Status episode menjadi cancelled', '2026-09-21 06:12:33'),
(14, 13, 'UPDATE', 'REGISTRATION', 6, 'Status episode menjadi cancelled', '2026-09-21 06:12:35');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `cpoe_orders`
--

CREATE TABLE `cpoe_orders` (
  `id` int(11) NOT NULL,
  `encounter_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `ordered_by` int(11) NOT NULL,
  `order_type` varchar(30) NOT NULL,
  `priority` varchar(20) DEFAULT 'routine',
  `order_name` varchar(180) NOT NULL,
  `clinical_indication` text DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `status` varchar(30) DEFAULT 'ordered',
  `ordered_at` datetime DEFAULT current_timestamp(),
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `cpoe_orders`
--

INSERT INTO `cpoe_orders` (`id`, `encounter_id`, `patient_id`, `ordered_by`, `order_type`, `priority`, `order_name`, `clinical_indication`, `instructions`, `status`, `ordered_at`, `completed_at`) VALUES
(1, 1, 3, 2, 'laboratory', 'routine', 'CATH LAB Jantung', 'ACS', '', 'ordered', '2026-09-21 07:59:53', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `license_no` varchar(80) DEFAULT NULL,
  `specialty` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `doctors`
--

INSERT INTO `doctors` (`id`, `user_id`, `department`, `license_no`, `specialty`) VALUES
(1, 2, 'General Medicine', 'SIP-DR-001', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `encounters`
--

CREATE TABLE `encounters` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `encounter_date` datetime DEFAULT current_timestamp(),
  `chief_complaint` text DEFAULT NULL,
  `subjective` text DEFAULT NULL,
  `objective` text DEFAULT NULL,
  `assessment` text DEFAULT NULL,
  `plan` text DEFAULT NULL,
  `diagnosis_code` varchar(30) DEFAULT NULL,
  `diagnosis_name` varchar(180) DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `clinical_status` varchar(30) DEFAULT 'draft',
  `signed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `encounters`
--

INSERT INTO `encounters` (`id`, `appointment_id`, `patient_id`, `doctor_id`, `encounter_date`, `chief_complaint`, `subjective`, `objective`, `assessment`, `plan`, `diagnosis_code`, `diagnosis_name`, `allergies`, `clinical_status`, `signed_at`) VALUES
(1, 1, 3, 1, '2026-09-21 07:55:08', 'Sakit dada', 'Sakit dada sejak 3 hari lalu, sakit seperti tertindih benda berat, naik tangga terbatas, jalan kaki juga makin lama makin lemah, dan makin lemas', 'tensi 150/100\r\nEKG ada ST elevation anterolateral', 'ACS', 'CATH lab jantung', 'ICD-10', 'ACS', '-', 'signed', '2026-09-21 07:57:51');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `encounter_addenda`
--

CREATE TABLE `encounter_addenda` (
  `id` int(11) NOT NULL,
  `encounter_id` int(11) NOT NULL,
  `author_user_id` int(11) NOT NULL,
  `note` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `invoice_no` varchar(40) NOT NULL,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` varchar(30) DEFAULT 'unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `invoice_items`
--

CREATE TABLE `invoice_items` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `description` varchar(200) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `price` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `lab_results`
--

CREATE TABLE `lab_results` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `test_name` varchar(180) NOT NULL,
  `result_value` varchar(120) DEFAULT NULL,
  `unit` varchar(40) DEFAULT NULL,
  `reference_range` varchar(80) DEFAULT NULL,
  `flag` varchar(20) DEFAULT 'normal',
  `verified_by` int(11) DEFAULT NULL,
  `result_notes` text DEFAULT NULL,
  `reported_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `medicines`
--

CREATE TABLE `medicines` (
  `id` int(11) NOT NULL,
  `code` varchar(40) NOT NULL,
  `name` varchar(150) NOT NULL,
  `category` varchar(80) DEFAULT NULL,
  `unit` varchar(30) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `min_stock` int(11) NOT NULL DEFAULT 10,
  `expiry_date` date DEFAULT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `medicines`
--

INSERT INTO `medicines` (`id`, `code`, `name`, `category`, `unit`, `stock`, `min_stock`, `expiry_date`, `price`, `is_active`) VALUES
(1, 'MED-001', 'Paracetamol 500mg', 'Analgesic', 'tablet', 250, 30, '2028-01-31', 500.00, 1),
(2, 'MED-002', 'Amoxicillin 500mg', 'Antibiotic', 'capsule', 120, 20, '2027-08-30', 1200.00, 1),
(3, 'MED-003', 'Omeprazole 20mg', 'Gastric', 'capsule', 15, 25, '2027-03-31', 1500.00, 1),
(4, 'MED-004', 'Normal Saline 0.9%', 'Infusion', 'bottle', 60, 10, '2028-05-31', 8500.00, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `nursing_notes`
--

CREATE TABLE `nursing_notes` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `encounter_id` int(11) DEFAULT NULL,
  `nurse_id` int(11) NOT NULL,
  `recorded_at` datetime DEFAULT current_timestamp(),
  `assessment` text DEFAULT NULL,
  `intervention` text DEFAULT NULL,
  `evaluation` text DEFAULT NULL,
  `fall_risk` varchar(20) DEFAULT 'low',
  `pain_score` int(11) DEFAULT 0,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `mrn` varchar(30) NOT NULL,
  `name` varchar(120) NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `birth_date` date NOT NULL,
  `blood_type` varchar(5) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `nik` varchar(30) DEFAULT NULL,
  `emergency_contact` varchar(120) DEFAULT NULL,
  `emergency_phone` varchar(30) DEFAULT NULL,
  `insurance` varchar(80) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `patients`
--

INSERT INTO `patients` (`id`, `user_id`, `mrn`, `name`, `gender`, `birth_date`, `blood_type`, `phone`, `address`, `created_at`, `nik`, `emergency_contact`, `emergency_phone`, `insurance`) VALUES
(1, 6, 'MRN-000001', 'Budi Santoso', 'male', '1992-04-12', 'O+', '081234567890', 'Tangerang', '2026-09-21 04:20:56', NULL, NULL, NULL, NULL),
(2, NULL, 'MRN-00002', 'Edward', 'male', '2005-11-30', 'O+', '081224479798', '', '2026-09-21 04:32:13', NULL, NULL, NULL, NULL),
(3, NULL, 'MRN-000003', 'Faris', 'male', '2006-01-25', 'O-', '08246372576', '...', '2026-09-21 05:50:31', '164612874683265', '09149867315', '1294879217647', 'BPJS'),
(4, NULL, 'MRN-000004', 'ferry', 'male', '2026-09-21', 'AB+', '017661431217', 'y', '2026-09-21 06:12:04', '010101001010010010101001001010', '---', '', 'Asuransi'),
(5, NULL, 'MRN-000005', 'ferry', 'male', '2026-09-21', 'AB+', '017661431217', 'y', '2026-09-21 06:12:09', '010101001010010010101001001010', '---', '', 'Asuransi'),
(6, NULL, 'MRN-000006', 'ferry', 'male', '2026-09-21', 'AB+', '017661431217', 'y', '2026-09-21 06:12:10', '010101001010010010101001001010', '---', '', 'Asuransi'),
(7, NULL, 'MRN-000007', 'ferry', 'male', '2026-09-21', 'AB+', '017661431217', 'y', '2026-09-21 06:12:12', '010101001010010010101001001010', '---', '', 'Asuransi'),
(8, NULL, 'MRN-000008', 'ferry', 'male', '2026-09-21', 'AB+', '017661431217', 'y', '2026-09-21 06:12:13', '010101001010010010101001001010', '---', '', 'Asuransi'),
(9, NULL, 'MRN-000009', 'ferry', 'male', '2026-09-21', 'AB+', '017661431217', 'y', '2026-09-21 06:12:17', '010101001010010010101001001010', '---', '', 'Asuransi');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `method` varchar(30) NOT NULL,
  `paid_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `received_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prescriptions`
--

CREATE TABLE `prescriptions` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `status` varchar(30) DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `encounter_id` int(11) DEFAULT NULL,
  `dispensed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prescription_items`
--

CREATE TABLE `prescription_items` (
  `id` int(11) NOT NULL,
  `prescription_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `dosage` varchar(100) NOT NULL,
  `route` varchar(50) DEFAULT 'oral',
  `duration` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `radiology_results`
--

CREATE TABLE `radiology_results` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `modality` varchar(80) DEFAULT NULL,
  `technique` text DEFAULT NULL,
  `findings` text DEFAULT NULL,
  `impression` text DEFAULT NULL,
  `reported_by` int(11) DEFAULT NULL,
  `reported_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `registrations`
--

CREATE TABLE `registrations` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `registered_by` int(11) NOT NULL,
  `registration_date` datetime DEFAULT current_timestamp(),
  `visit_type` varchar(30) DEFAULT 'outpatient',
  `guarantor` varchar(80) DEFAULT NULL,
  `complaint` text DEFAULT NULL,
  `status` varchar(30) DEFAULT 'registered'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `registrations`
--

INSERT INTO `registrations` (`id`, `patient_id`, `registered_by`, `registration_date`, `visit_type`, `guarantor`, `complaint`, `status`) VALUES
(1, 3, 13, '2026-09-21 07:50:31', 'outpatient', 'BPJS', 'Sakit dada', 'queued'),
(2, 4, 13, '2026-09-21 08:12:04', 'outpatient', 'Asuransi', 'daaadad', 'registered'),
(3, 5, 13, '2026-09-21 08:12:10', 'outpatient', 'Asuransi', 'daaadad', 'registered'),
(4, 6, 13, '2026-09-21 08:12:11', 'outpatient', 'Asuransi', 'daaadad', 'registered'),
(5, 7, 13, '2026-09-21 08:12:12', 'outpatient', 'Asuransi', 'daaadad', 'registered'),
(6, 8, 13, '2026-09-21 08:12:14', 'outpatient', 'Asuransi', 'daaadad', 'cancelled'),
(7, 9, 13, '2026-09-21 08:12:17', 'outpatient', 'Asuransi', 'daaadad', 'cancelled');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(160) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` varchar(40) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `employee_no` varchar(40) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `is_active`, `created_at`, `employee_no`, `phone`) VALUES
(1, 'Super Administrator', 'admin@hospital.test', '$2y$10$d6h5a4OXKBEenKj8G9uQ/.C95DxKxhOJ/wBg/YZ2DptQ75H/uAO9G', 'admin', 1, '2026-09-21 04:20:56', 'ADM-001', NULL),
(2, 'Dr. Andi Wijaya', 'doctor@hospital.test', '$2y$10$d6h5a4OXKBEenKj8G9uQ/.C95DxKxhOJ/wBg/YZ2DptQ75H/uAO9G', 'doctor', 1, '2026-09-21 04:20:56', 'DOK-001', NULL),
(3, 'Siti Perawat', 'nurse@hospital.test', '$2y$10$d6h5a4OXKBEenKj8G9uQ/.C95DxKxhOJ/wBg/YZ2DptQ75H/uAO9G', 'nurse', 1, '2026-09-21 04:20:56', 'NRS-001', NULL),
(4, 'Rina Apoteker', 'pharmacy@hospital.test', '$2y$10$d6h5a4OXKBEenKj8G9uQ/.C95DxKxhOJ/wBg/YZ2DptQ75H/uAO9G', 'pharmacist', 1, '2026-09-21 04:20:56', 'APT-001', NULL),
(5, 'Budi Kasir', 'cashier@hospital.test', '$2y$10$d6h5a4OXKBEenKj8G9uQ/.C95DxKxhOJ/wBg/YZ2DptQ75H/uAO9G', 'cashier', 1, '2026-09-21 04:20:56', 'KSR-001', NULL),
(6, 'Budi Santoso', 'patient@hospital.test', '$2y$10$d6h5a4OXKBEenKj8G9uQ/.C95DxKxhOJ/wBg/YZ2DptQ75H/uAO9G', 'patient', 1, '2026-09-21 04:20:56', NULL, NULL),
(13, 'Maya Pendaftaran', 'registration@hospital.test', '$2y$10$d6h5a4OXKBEenKj8G9uQ/.C95DxKxhOJ/wBg/YZ2DptQ75H/uAO9G', 'registration', 1, '2026-09-21 05:48:11', 'REG-001', NULL),
(14, 'Dewi Rekam Medis', 'rekammedis@hospital.test', '$2y$10$d6h5a4OXKBEenKj8G9uQ/.C95DxKxhOJ/wBg/YZ2DptQ75H/uAO9G', 'medical_record', 1, '2026-09-21 05:48:12', 'RM-001', NULL),
(15, 'Lina Laboratorium', 'lab@hospital.test', '$2y$10$d6h5a4OXKBEenKj8G9uQ/.C95DxKxhOJ/wBg/YZ2DptQ75H/uAO9G', 'laboratory', 1, '2026-09-21 05:48:12', 'LAB-001', NULL),
(16, 'Rudi Radiologi', 'radiology@hospital.test', '$2y$10$d6h5a4OXKBEenKj8G9uQ/.C95DxKxhOJ/wBg/YZ2DptQ75H/uAO9G', 'radiology', 1, '2026-09-21 05:48:12', 'RAD-001', NULL),
(17, 'Nadia Manajemen', 'management@hospital.test', '$2y$10$d6h5a4OXKBEenKj8G9uQ/.C95DxKxhOJ/wBg/YZ2DptQ75H/uAO9G', 'management', 1, '2026-09-21 05:48:13', 'MGT-001', NULL),
(18, 'Arif Auditor', 'auditor@hospital.test', '$2y$10$d6h5a4OXKBEenKj8G9uQ/.C95DxKxhOJ/wBg/YZ2DptQ75H/uAO9G', 'auditor', 1, '2026-09-21 05:48:13', 'AUD-001', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `vital_signs`
--

CREATE TABLE `vital_signs` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `recorded_by` int(11) NOT NULL,
  `recorded_at` datetime DEFAULT current_timestamp(),
  `temperature` decimal(4,1) DEFAULT NULL,
  `systolic` int(11) DEFAULT NULL,
  `diastolic` int(11) DEFAULT NULL,
  `pulse` int(11) DEFAULT NULL,
  `respiratory_rate` int(11) DEFAULT NULL,
  `spo2` decimal(5,2) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'normal',
  `notes` text DEFAULT NULL,
  `weight` decimal(6,2) DEFAULT NULL,
  `height` decimal(6,2) DEFAULT NULL,
  `pain_score` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indizes für die Tabelle `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `cpoe_orders`
--
ALTER TABLE `cpoe_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indizes für die Tabelle `encounters`
--
ALTER TABLE `encounters`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `encounter_addenda`
--
ALTER TABLE `encounter_addenda`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_no` (`invoice_no`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indizes für die Tabelle `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indizes für die Tabelle `lab_results`
--
ALTER TABLE `lab_results`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `medicines`
--
ALTER TABLE `medicines`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indizes für die Tabelle `nursing_notes`
--
ALTER TABLE `nursing_notes`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mrn` (`mrn`),
  ADD KEY `user_id` (`user_id`);

--
-- Indizes für die Tabelle `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indizes für die Tabelle `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indizes für die Tabelle `prescription_items`
--
ALTER TABLE `prescription_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prescription_id` (`prescription_id`),
  ADD KEY `medicine_id` (`medicine_id`);

--
-- Indizes für die Tabelle `radiology_results`
--
ALTER TABLE `radiology_results`
  ADD PRIMARY KEY (`id`);

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
  ADD UNIQUE KEY `email` (`email`);

--
-- Indizes für die Tabelle `vital_signs`
--
ALTER TABLE `vital_signs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `recorded_by` (`recorded_by`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT für Tabelle `cpoe_orders`
--
ALTER TABLE `cpoe_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `encounters`
--
ALTER TABLE `encounters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `encounter_addenda`
--
ALTER TABLE `encounter_addenda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `lab_results`
--
ALTER TABLE `lab_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `medicines`
--
ALTER TABLE `medicines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `nursing_notes`
--
ALTER TABLE `nursing_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT für Tabelle `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `prescription_items`
--
ALTER TABLE `prescription_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `radiology_results`
--
ALTER TABLE `radiology_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=811;

--
-- AUTO_INCREMENT für Tabelle `vital_signs`
--
ALTER TABLE `vital_signs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`);

--
-- Constraints der Tabelle `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `doctors_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`);

--
-- Constraints der Tabelle `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD CONSTRAINT `invoice_items_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `patients_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints der Tabelle `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`);

--
-- Constraints der Tabelle `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD CONSTRAINT `prescriptions_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `prescriptions_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`);

--
-- Constraints der Tabelle `prescription_items`
--
ALTER TABLE `prescription_items`
  ADD CONSTRAINT `prescription_items_ibfk_1` FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `prescription_items_ibfk_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`);

--
-- Constraints der Tabelle `vital_signs`
--
ALTER TABLE `vital_signs`
  ADD CONSTRAINT `vital_signs_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `vital_signs_ibfk_2` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
