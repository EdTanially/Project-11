CREATE DATABASE IF NOT EXISTS nursing_simrs CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nursing_simrs;

DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS monitoring;
DROP TABLE IF EXISTS medications;
DROP TABLE IF EXISTS nursing_notes;
DROP TABLE IF EXISTS vital_signs;
DROP TABLE IF EXISTS assessments;
DROP TABLE IF EXISTS patients;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
 id INT AUTO_INCREMENT PRIMARY KEY,
 username VARCHAR(50) UNIQUE NOT NULL,
 password VARCHAR(255) NOT NULL,
 full_name VARCHAR(120) NOT NULL,
 role ENUM('nurse','midwife','doctor','pharmacy','admin') NOT NULL,
 unit VARCHAR(100) NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE patients (
 id INT AUTO_INCREMENT PRIMARY KEY,
 medical_record_no VARCHAR(30) UNIQUE NOT NULL,
 full_name VARCHAR(120) NOT NULL,
 birth_date DATE NOT NULL,
 gender ENUM('L','P') NOT NULL,
 room VARCHAR(50) NOT NULL,
 diagnosis VARCHAR(255) DEFAULT NULL,
 allergies VARCHAR(255) DEFAULT NULL,
 status ENUM('Active','Observation','Discharged') DEFAULT 'Active',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE assessments (
 id INT AUTO_INCREMENT PRIMARY KEY,
 patient_id INT NOT NULL,
 officer_id INT NOT NULL,
 chief_complaint TEXT,
 consciousness VARCHAR(100),
 airway VARCHAR(100),
 breathing VARCHAR(100),
 circulation VARCHAR(100),
 pain_score TINYINT,
 fall_risk VARCHAR(50),
 nutrition VARCHAR(100),
 psychosocial TEXT,
 assessment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(patient_id) REFERENCES patients(id),
 FOREIGN KEY(officer_id) REFERENCES users(id)
);

CREATE TABLE vital_signs (
 id INT AUTO_INCREMENT PRIMARY KEY,
 patient_id INT NOT NULL,
 officer_id INT NOT NULL,
 systolic INT,
 diastolic INT,
 pulse INT,
 respiration INT,
 temperature DECIMAL(4,1),
 spo2 INT,
 height_cm DECIMAL(5,1),
 weight_kg DECIMAL(5,1),
 measured_at DATETIME DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(patient_id) REFERENCES patients(id),
 FOREIGN KEY(officer_id) REFERENCES users(id)
);

CREATE TABLE nursing_notes (
 id INT AUTO_INCREMENT PRIMARY KEY,
 patient_id INT NOT NULL,
 officer_id INT NOT NULL,
 note_type VARCHAR(50) DEFAULT 'Progress Note',
 condition_text TEXT,
 intervention_text TEXT,
 response_text TEXT,
 education_text TEXT,
 note_date DATETIME DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(patient_id) REFERENCES patients(id),
 FOREIGN KEY(officer_id) REFERENCES users(id)
);

CREATE TABLE medications (
 id INT AUTO_INCREMENT PRIMARY KEY,
 patient_id INT NOT NULL,
 ordered_by INT NOT NULL,
 drug_name VARCHAR(120) NOT NULL,
 dose VARCHAR(80) NOT NULL,
 route VARCHAR(50) NOT NULL,
 frequency VARCHAR(50) NOT NULL,
 scheduled_at DATETIME NOT NULL,
 administered_by INT NULL,
 status ENUM('Ordered','Due','Given','Held') DEFAULT 'Ordered',
 given_at DATETIME NULL,
 note TEXT,
 FOREIGN KEY(patient_id) REFERENCES patients(id),
 FOREIGN KEY(ordered_by) REFERENCES users(id),
 FOREIGN KEY(administered_by) REFERENCES users(id)
);

CREATE TABLE monitoring (
 id INT AUTO_INCREMENT PRIMARY KEY,
 patient_id INT NOT NULL,
 officer_id INT NOT NULL,
 parameter_name VARCHAR(100) NOT NULL,
 parameter_value VARCHAR(120) NOT NULL,
 note TEXT,
 monitored_at DATETIME DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(patient_id) REFERENCES patients(id),
 FOREIGN KEY(officer_id) REFERENCES users(id)
);

CREATE TABLE audit_logs (
 id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NOT NULL,
 action VARCHAR(120) NOT NULL,
 module VARCHAR(80) NOT NULL,
 reference_id INT NULL,
 detail TEXT,
 created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(user_id) REFERENCES users(id)
);

INSERT INTO users (username,password,full_name,role,unit) VALUES
('perawat','perawat123','Ners. Alya Rahmawati','nurse','Rawat Inap - Melati'),
('bidan','bidan123','Bidan. Sinta Maharani','midwife','Kamar Bersalin'),
('dokter','dokter123','dr. Raka Wijaya','doctor','Tim Medis'),
('farmasi','farmasi123','Apt. Nadia Putri','pharmacy','Farmasi'),
('admin','admin123','Administrator SIMRS','admin','SIMRS');

INSERT INTO patients (medical_record_no,full_name,birth_date,gender,room,diagnosis,allergies,status) VALUES
('RM-2026-0001','Alya Nurfadilah','1998-04-12','P','Melati 201','Demam berdarah dengue','Paracetamol','Observation'),
('RM-2026-0002','Budi Santoso','1975-11-23','L','Melati 203','Pneumonia','-','Active'),
('RM-2026-0003','Citra Lestari','1992-08-05','P','Kebidanan 02','Kehamilan 38 minggu','-','Active'),
('RM-2026-0004','Dimas Pratama','1987-01-14','L','Anggrek 105','Diabetes mellitus tipe 2','Penicillin','Active');

INSERT INTO assessments (patient_id,officer_id,chief_complaint,consciousness,airway,breathing,circulation,pain_score,fall_risk,nutrition,psychosocial)
VALUES (1,1,'Demam dan lemas','Compos mentis','Patent','RR meningkat ringan','Stabil',4,'Sedang','Asupan menurun','Cemas ringan terkait kondisi klinis');

INSERT INTO vital_signs (patient_id,officer_id,systolic,diastolic,pulse,respiration,temperature,spo2,height_cm,weight_kg) VALUES
(1,1,110,70,96,22,38.2,97,158,54.5),
(2,1,128,82,88,20,37.2,96,170,68.2),
(3,2,118,76,84,18,36.8,98,160,63.4);

INSERT INTO nursing_notes (patient_id,officer_id,condition_text,intervention_text,response_text,education_text) VALUES
(1,1,'Pasien sadar, kooperatif, keluhan demam masih ada.','Monitoring vital sign, kompres hangat, observasi intake cairan.','Pasien tampak lebih nyaman setelah intervensi.','Edukasi hidrasi dan melaporkan bila kondisi memburuk.'),
(2,1,'Batuk produktif, sesak ringan saat aktivitas.','Posisi semi-Fowler dan monitoring SpO2 berkala.','SpO2 stabil, sesak berkurang saat istirahat.','Ajarkan teknik batuk efektif.');

INSERT INTO medications (patient_id,ordered_by,drug_name,dose,route,frequency,scheduled_at,status) VALUES
(1,3,'Paracetamol','500 mg','PO','3x1','2026-09-15 15:00:00','Due'),
(1,3,'Ondansetron','4 mg','IV','PRN','2026-09-15 14:00:00','Given'),
(2,3,'Ceftriaxone','1 g','IV','2x1','2026-09-15 16:00:00','Due'),
(3,3,'Ferrous Fumarate','1 tablet','PO','1x1','2026-09-15 19:00:00','Ordered');

INSERT INTO monitoring (patient_id,officer_id,parameter_name,parameter_value,note) VALUES
(1,1,'Kesadaran','Compos mentis','Tidak ada perubahan bermakna'),
(1,1,'SpO2','97%','Stabil pada udara ruangan'),
(2,1,'Respirasi','20 x/menit','Observasi sesak ringan saat aktivitas');

INSERT INTO audit_logs (user_id,action,module,reference_id,detail) VALUES
(1,'Login','Authentication',NULL,'Login perawat berhasil'),
(1,'Input Vital Sign','Vital Sign',1,'TD 110/70, Nadi 96, RR 22, Suhu 38.2, SpO2 97%'),
(1,'Simpan Nursing Note','Nursing Note',1,'Progress note pasien RM-2026-0001'),
(3,'Review Dokumentasi','Doctor View',1,'Dokter melihat dokumentasi keperawatan pasien');
