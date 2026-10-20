CREATE DATABASE IF NOT EXISTS cpoe_simrs CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cpoe_simrs;

DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS patients;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  username VARCHAR(80) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('Dokter','Perawat/Bidan','Petugas Laboratorium','Petugas Radiologi','Petugas Farmasi','Petugas Gizi','Administrator SIMRS') NOT NULL,
  unit_name VARCHAR(100) NULL,
  active TINYINT(1) DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE patients (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  medical_record_no VARCHAR(40) UNIQUE NOT NULL,
  name VARCHAR(120) NOT NULL,
  birth_date DATE NOT NULL,
  gender ENUM('L','P') NOT NULL,
  room VARCHAR(80) NULL,
  allergies TEXT NULL,
  diagnosis TEXT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE orders (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  patient_id INT UNSIGNED NOT NULL,
  doctor_id INT UNSIGNED NOT NULL,
  type ENUM('Laboratorium','Radiologi','Farmasi','Tindakan Medis','Konsultasi','Diet') NOT NULL,
  item_name VARCHAR(180) NOT NULL,
  priority ENUM('Routine','Urgent','Stat') DEFAULT 'Routine',
  clinical_note TEXT NULL,
  dosage VARCHAR(100) NULL,
  route VARCHAR(80) NULL,
  schedule_at DATETIME NULL,
  status ENUM('Menunggu Validasi','Dikirim','Diproses','Dijadwalkan','Selesai','Dibatalkan','Perlu Revisi') NOT NULL DEFAULT 'Menunggu Validasi',
  destination_unit VARCHAR(100) NOT NULL,
  result_text TEXT NULL,
  cancellation_reason TEXT NULL,
  changed_reason TEXT NULL,
  validated_at DATETIME NULL,
  executed_at DATETIME NULL,
  completed_at DATETIME NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_orders_patient FOREIGN KEY (patient_id) REFERENCES patients(id),
  CONSTRAINT fk_orders_doctor FOREIGN KEY (doctor_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE audit_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NULL,
  action VARCHAR(80) NOT NULL,
  entity VARCHAR(80) NOT NULL,
  entity_id INT UNSIGNED NULL,
  detail TEXT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO users(name,username,password,role,unit_name) VALUES
('dr. Andika Pratama','dokter',SHA2('dokter123',256),'Dokter','Rawat Inap'),
('Ns. Rani Putri','perawat',SHA2('perawat123',256),'Perawat/Bidan','Rawat Inap'),
('Ayu Lestari, A.Md.Kes','lab',SHA2('lab123',256),'Petugas Laboratorium','Laboratorium'),
('Bima Saputra, S.Kep','radiologi',SHA2('radiologi123',256),'Petugas Radiologi','Radiologi'),
('Dina Maharani, S.Farm','farmasi',SHA2('farmasi123',256),'Petugas Farmasi','Farmasi'),
('Fitri Wulandari, S.Gz','gizi',SHA2('gizi123',256),'Petugas Gizi','Gizi'),
('Admin SIMRS','admin',SHA2('admin123',256),'Administrator SIMRS','SIMRS');

INSERT INTO patients(medical_record_no,name,birth_date,gender,room,allergies,diagnosis) VALUES
('RM-2026-0001','Budi Santoso','1978-04-12','L','Anggrek 301','Penisilin','Pneumonia komunitas'),
('RM-2026-0002','Siti Rahma','1989-09-03','P','Mawar 205','Tidak ada','Demam tifoid'),
('RM-2026-0003','Andi Wijaya','2002-02-19','L','IGD','NSAID','Nyeri abdomen akut');
