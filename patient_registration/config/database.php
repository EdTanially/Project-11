<?php
// RS Siloam Lippo Village — SIMRS Patient Registration
// Database connection is intentionally fixed to MariaDB/MySQL port 3307.
date_default_timezone_set('Asia/Jakarta');
$host='127.0.0.1'; $port=3307; $db='simrs_patient_registration'; $user='root'; $pass=''; $charset='utf8mb4';
$options=[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false];
try {
  $server=new PDO("mysql:host={$host};port={$port};charset={$charset}",$user,$pass,$options);
  $server->exec("CREATE DATABASE IF NOT EXISTS `{$db}` CHARACTER SET {$charset} COLLATE utf8mb4_unicode_ci");
  $pdo=new PDO("mysql:host={$host};port={$port};dbname={$db};charset={$charset}",$user,$pass,$options);
} catch(PDOException $e){
  $detail=htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
  exit("<div style='font-family:Segoe UI,Arial;padding:32px;max-width:820px;margin:60px auto;background:#fff;border:1px solid #d7e2e9;border-radius:16px'><h2 style='color:#123b57;margin:0 0 10px'>Database belum tersambung</h2><p style='color:#526a7d'>SIMRS membutuhkan MariaDB/MySQL pada <b>127.0.0.1:3307</b>. Pastikan service database aktif dan port-nya 3307.</p><details><summary>Detail teknis</summary><pre style='white-space:pre-wrap'>{$detail}</pre></details></div>");
}

$pdo->exec("CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY, username VARCHAR(80) UNIQUE NOT NULL, email VARCHAR(150) UNIQUE NOT NULL, password_hash VARCHAR(255) NOT NULL, full_name VARCHAR(150) NOT NULL, role VARCHAR(60) NOT NULL, is_active TINYINT(1) NOT NULL DEFAULT 1, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB");
$pdo->exec("CREATE TABLE IF NOT EXISTS patients (id INT AUTO_INCREMENT PRIMARY KEY, medical_record_no VARCHAR(30) UNIQUE NOT NULL, nik VARCHAR(30) NOT NULL, name VARCHAR(150) NOT NULL, dob DATE NOT NULL, gender ENUM('L','P') NOT NULL, phone VARCHAR(30), address TEXT, blood_type VARCHAR(5) DEFAULT NULL, marital_status VARCHAR(30) DEFAULT NULL, emergency_contact VARCHAR(150) DEFAULT NULL, duplicate_flag TINYINT(1) NOT NULL DEFAULT 0, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX idx_nik(nik), INDEX idx_name_dob(name,dob)) ENGINE=InnoDB");
$pdo->exec("CREATE TABLE IF NOT EXISTS master_clinics (id INT AUTO_INCREMENT PRIMARY KEY, code VARCHAR(20) UNIQUE NOT NULL, name VARCHAR(120) UNIQUE NOT NULL, schedule VARCHAR(120) DEFAULT 'Senin–Jumat 08:00–16:00', active TINYINT(1) DEFAULT 1) ENGINE=InnoDB");
$pdo->exec("CREATE TABLE IF NOT EXISTS master_doctors (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(150) NOT NULL, specialty VARCHAR(100) NOT NULL, clinic_id INT NULL, schedule VARCHAR(120) DEFAULT 'Senin–Jumat', active TINYINT(1) DEFAULT 1, FOREIGN KEY(clinic_id) REFERENCES master_clinics(id) ON DELETE SET NULL) ENGINE=InnoDB");
$pdo->exec("CREATE TABLE IF NOT EXISTS visits (id INT AUTO_INCREMENT PRIMARY KEY, patient_id INT NOT NULL, clinic_id INT NULL, clinic VARCHAR(120) NOT NULL, doctor VARCHAR(150) DEFAULT NULL, queue_no VARCHAR(20) NOT NULL, service_type VARCHAR(40) DEFAULT 'Rawat Jalan', status VARCHAR(30) NOT NULL, notes TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY(patient_id) REFERENCES patients(id), FOREIGN KEY(clinic_id) REFERENCES master_clinics(id) ON DELETE SET NULL) ENGINE=InnoDB");
$pdo->exec("CREATE TABLE IF NOT EXISTS insurance_verifications (id INT AUTO_INCREMENT PRIMARY KEY, patient_id INT NOT NULL, payer VARCHAR(60) NOT NULL, member_no VARCHAR(80), status VARCHAR(50) NOT NULL, eligibility VARCHAR(100) DEFAULT 'Terverifikasi', note TEXT, verified_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY(patient_id) REFERENCES patients(id)) ENGINE=InnoDB");
$pdo->exec("CREATE TABLE IF NOT EXISTS appointments (id INT AUTO_INCREMENT PRIMARY KEY, patient_id INT NOT NULL, clinic_id INT NULL, clinic VARCHAR(120) NOT NULL, doctor VARCHAR(150) NOT NULL, appointment_date DATE NOT NULL, appointment_time TIME NOT NULL, appointment_no VARCHAR(40) UNIQUE NOT NULL, status VARCHAR(30) NOT NULL, reminder_status VARCHAR(30) DEFAULT 'Terkirim', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY(patient_id) REFERENCES patients(id), FOREIGN KEY(clinic_id) REFERENCES master_clinics(id) ON DELETE SET NULL) ENGINE=InnoDB");
$pdo->exec("CREATE TABLE IF NOT EXISTS audit_logs (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NULL, action VARCHAR(60) NOT NULL, table_name VARCHAR(100), record_id INT, details TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL) ENGINE=InnoDB");

function add_col(PDO $pdo,string $table,string $col,string $definition):void{
  $s=$pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=? AND COLUMN_NAME=?"); $s->execute([$table,$col]);
  if(!(int)$s->fetchColumn()) $pdo->exec("ALTER TABLE `{$table}` ADD `{$col}` {$definition}");
}
foreach([
 ['patients','blood_type','VARCHAR(5) NULL'],['patients','marital_status','VARCHAR(30) NULL'],['patients','emergency_contact','VARCHAR(150) NULL'],
 ['visits','clinic_id','INT NULL'],['visits','doctor','VARCHAR(150) NULL'],['visits','service_type',"VARCHAR(40) DEFAULT 'Rawat Jalan'"],['visits','notes','TEXT NULL'],
 ['insurance_verifications','eligibility',"VARCHAR(100) DEFAULT 'Terverifikasi'"],['insurance_verifications','note','TEXT NULL'],
 ['appointments','clinic_id','INT NULL'],['appointments','appointment_time',"TIME NOT NULL DEFAULT '09:00:00'"],['appointments','reminder_status',"VARCHAR(30) DEFAULT 'Terkirim'"],
 ['users','email','VARCHAR(150) NULL'],['users','password_hash','VARCHAR(255) NULL'],['users','full_name','VARCHAR(150) NULL'],['users','is_active','TINYINT(1) NOT NULL DEFAULT 1']
] as $c) add_col($pdo,$c[0],$c[1],$c[2]);

$users=[
 ['registrar','registration@hospital.test','Petugas Loket Registrasi','Petugas Registrasi'],
 ['rm','rm@hospital.test','Petugas Administrasi Registrasi','Petugas Rekam Medis'],
 ['doctor','doctor@hospital.test','dr. Andi Pratama, Sp.PD','Dokter'],
 ['nurse','nurse@hospital.test','Ns. Siti Rahma, S.Kep','Perawat/Bidan'],
 ['admin','admin@hospital.test','Administrator SIMRS','Administrator SIMRS'],
 ['management','management@hospital.test','Manajemen Rumah Sakit','Manajemen']
];
$u=$pdo->prepare("INSERT INTO users(username,email,password_hash,full_name,role,is_active) VALUES(?,?,?,?,?,1) ON DUPLICATE KEY UPDATE email=VALUES(email),password_hash=VALUES(password_hash),full_name=VALUES(full_name),role=VALUES(role),is_active=1");
foreach($users as $x)$u->execute([$x[0],$x[1],password_hash('password',PASSWORD_DEFAULT),$x[2],$x[3]]);

$clinics=[
 ['PL01','Poli Penyakit Dalam','Senin–Jumat 08:00–16:00'],['JG01','Poli Jantung','Senin–Jumat 08:00–15:00'],['AN01','Poli Anak','Senin–Jumat 08:00–15:00'],['OB01','Poli Obstetri & Ginekologi','Senin–Jumat 08:00–15:00'],['SP01','Poli Saraf','Senin–Jumat 08:00–15:00'],['MA01','Poli Mata','Senin–Jumat 08:00–15:00'],['IGD01','Instalasi Gawat Darurat','24 Jam'],['LAB01','Laboratorium','Senin–Sabtu 07:00–18:00'],['RAD01','Radiologi','Senin–Sabtu 08:00–17:00']
];
$st=$pdo->prepare("INSERT IGNORE INTO master_clinics(code,name,schedule) VALUES(?,?,?)"); foreach($clinics as $c)$st->execute($c);
$clinicIds=[]; foreach($pdo->query("SELECT id,code FROM master_clinics") as $r)$clinicIds[$r['code']]=(int)$r['id'];
$docs=[
 ['dr. Andi Pratama, Sp.PD','Spesialis Penyakit Dalam','PL01'],['dr. Bima Wijaya, Sp.JP','Spesialis Jantung & Pembuluh Darah','JG01'],['dr. Citra Lestari, Sp.A','Spesialis Anak','AN01'],['dr. Dina Maharani, Sp.OG','Spesialis Obstetri & Ginekologi','OB01'],['dr. Eka Permata, Sp.S','Spesialis Saraf','SP01'],['dr. Fajar Santoso, Sp.M','Spesialis Mata','MA01']
];
$st=$pdo->prepare("INSERT INTO master_doctors(name,specialty,clinic_id,schedule) SELECT ?,?,?,? FROM DUAL WHERE NOT EXISTS(SELECT 1 FROM master_doctors WHERE name=?)"); foreach($docs as $d)$st->execute([$d[0],$d[1],$clinicIds[$d[2]]??null,'Senin–Jumat',$d[0]]);

$pSeeds=[
 ['RM20260001','3174015501010001','Ahmad Fauzan','1985-02-14','L','081234567801','Jl. Melati No. 12, Tangerang','O','Menikah','Siti Fauziah'],
 ['RM20260002','3174016207020002','Siti Nurhaliza','1992-07-02','P','081234567802','Jl. Mawar No. 8, Tangerang','A','Menikah','Budi Santoso'],
 ['RM20260003','3174011003050003','Rizky Maulana','2005-03-10','L','081234567803','Perumahan Citra, Tangerang','B','Belum Menikah','Hendra Maulana'],
 ['RM20260004','3174014511120004','Maria Cecilia','1988-12-05','P','081234567804','Jl. Pahlawan No. 22, Tangerang','AB','Menikah','Anton Wijaya'],
 ['RM20260005','3174016008060005','Dewi Lestari','2000-08-20','P','081234567805','Jl. Kenanga No. 4, Tangerang','O','Belum Menikah','Rina Lestari'],
 ['RM20260006','3174017209090006','Fajar Hidayat','1978-09-12','L','081234567806','Jl. Anggrek No. 17, Tangerang','A','Menikah','Ani Hidayat'],
 ['RM20260007','3174013004110007','Nadia Putri','2011-04-30','P','081234567807','Jl. Flamboyan No. 6, Tangerang','B','Belum Menikah','Rudi Putra'],
 ['RM20260008','3174012210010008','Yosef Kurniawan','1990-10-22','L','081234567808','Jl. Wijaya No. 2, Tangerang','O','Menikah','Maya Kurniawan'],
 ['RM20260009','3174014310030009','Clara Regina','1995-03-10','P','081234567809','Jl. Cendana No. 11, Tangerang','A','Belum Menikah','Rina Regina'],
 ['RM20260010','3174015405060010','Bagus Setiawan','1982-06-15','L','081234567810','Jl. Pinus No. 7, Tangerang','O','Menikah','Lia Setiawan'],
 ['RM20260011','3174016712080011','Kevin Jonathan','1998-12-28','L','081234567811','Jl. Bougenville No. 5, Tangerang','B','Belum Menikah','Maria Jonathan'],
 ['RM20260012','3174012209010012','Rani Wulandari','1975-09-22','P','081234567812','Jl. Dahlia No. 14, Tangerang','AB','Menikah','Tono Wulandari']
];
$st=$pdo->prepare("INSERT IGNORE INTO patients(medical_record_no,nik,name,dob,gender,phone,address,blood_type,marital_status,emergency_contact) VALUES(?,?,?,?,?,?,?,?,?,?)"); foreach($pSeeds as $p)$st->execute($p);
$patientMap=[]; foreach($pdo->query("SELECT id,medical_record_no FROM patients") as $r)$patientMap[$r['medical_record_no']]=(int)$r['id'];

// Fill transactions when the corresponding table is still sparse. All values are local sample data and do not connect to external services.
$visitSeeds=[
 ['RM20260001','PL01','dr. Andi Pratama, Sp.PD','A001','Selesai','Kontrol rutin'],['RM20260002','OB01','dr. Dina Maharani, Sp.OG','B012','Menunggu','Kontrol kehamilan'],['RM20260003','AN01','dr. Citra Lestari, Sp.A','C006','Dipanggil','Demam'],['RM20260004','JG01','dr. Bima Wijaya, Sp.JP','A018','Terdaftar','Kontrol hipertensi'],['RM20260005','PL01','dr. Andi Pratama, Sp.PD','A019','Selesai','Pemeriksaan berkala'],['RM20260006','SP01','dr. Eka Permata, Sp.S','A021','Menunggu','Keluhan pusing'],['RM20260007','AN01','dr. Citra Lestari, Sp.A','C008','Terdaftar','Kontrol tumbuh kembang'],['RM20260008','MA01','dr. Fajar Santoso, Sp.M','D004','Selesai','Pemeriksaan mata'],['RM20260009','PL01','dr. Andi Pratama, Sp.PD','A024','Menunggu','Kontrol diabetes'],['RM20260010','JG01','dr. Bima Wijaya, Sp.JP','B021','Terdaftar','Kontrol tekanan darah']
];
if((int)$pdo->query("SELECT COUNT(*) FROM visits")->fetchColumn()<8){
 $st=$pdo->prepare("INSERT INTO visits(patient_id,clinic_id,clinic,doctor,queue_no,service_type,status,notes) VALUES(?,?,?,?,?,'Rawat Jalan',?,?)");
 foreach($visitSeeds as $v){ if(isset($patientMap[$v[0]],$clinicIds[$v[1]]))$st->execute([$patientMap[$v[0]],$clinicIds[$v[1]],$pdo->query("SELECT name FROM master_clinics WHERE id=".(int)$clinicIds[$v[1]])->fetchColumn(),$v[2],$v[3],$v[4],$v[5]]); }
}
$insSeeds=[
 ['RM20260001','BPJS','0001876543210','AKTIF','Layanan Rawat Jalan','Peserta aktif dan sesuai layanan'],['RM20260002','BPJS','0001876543211','AKTIF','Layanan Rawat Jalan','FKTP sesuai'],['RM20260003','Asuransi','INS-2026-7788','AKTIF','Rawat Jalan','Plafon tersedia'],['RM20260004','Pribadi','-','SELF PAY','Pembayaran Mandiri',''],['RM20260005','BPJS','0001876543214','AKTIF','Layanan Rawat Jalan','Tidak ada tunggakan'],['RM20260006','BPJS','0001876543216','AKTIF','Layanan Rawat Jalan','Peserta aktif'],['RM20260007','Pribadi','-','SELF PAY','Pembayaran Mandiri',''],['RM20260009','BPJS','0001876543220','AKTIF','Layanan Rawat Jalan','Peserta aktif']
];
if((int)$pdo->query("SELECT COUNT(*) FROM insurance_verifications")->fetchColumn()<6){$st=$pdo->prepare("INSERT INTO insurance_verifications(patient_id,payer,member_no,status,eligibility,note) VALUES(?,?,?,?,?,?)");foreach($insSeeds as $x)if(isset($patientMap[$x[0]]))$st->execute([$patientMap[$x[0]],$x[1],$x[2],$x[3],$x[4],$x[5]]);}
$tomorrow=date('Y-m-d',strtotime('+1 day'));$day2=date('Y-m-d',strtotime('+2 day'));$day3=date('Y-m-d',strtotime('+3 day'));$day4=date('Y-m-d',strtotime('+4 day'));
$appts=[
 ['RM20260001','PL01','dr. Andi Pratama, Sp.PD',$tomorrow,'09:00','APT-26001','confirmed'],['RM20260002','OB01','dr. Dina Maharani, Sp.OG',$tomorrow,'10:00','APT-26002','confirmed'],['RM20260004','JG01','dr. Bima Wijaya, Sp.JP',$day2,'13:00','APT-26003','confirmed'],['RM20260006','SP01','dr. Eka Permata, Sp.S',$day3,'14:00','APT-26004','pending'],['RM20260009','PL01','dr. Andi Pratama, Sp.PD',$day4,'11:00','APT-26005','confirmed'],['RM20260010','JG01','dr. Bima Wijaya, Sp.JP',$day4,'15:00','APT-26006','pending']
];
if((int)$pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn()<5){$st=$pdo->prepare("INSERT IGNORE INTO appointments(patient_id,clinic_id,clinic,doctor,appointment_date,appointment_time,appointment_no,status,reminder_status) VALUES(?,?,?,?,?,?,?,?,'Terkirim')");foreach($appts as $a)if(isset($patientMap[$a[0]],$clinicIds[$a[1]])){$clinicName=$pdo->query("SELECT name FROM master_clinics WHERE id=".(int)$clinicIds[$a[1]])->fetchColumn();$st->execute([$patientMap[$a[0]],$clinicIds[$a[1]],$clinicName,$a[2],$a[3],$a[4],$a[5],$a[6]]);}}

if((int)$pdo->query("SELECT COUNT(*) FROM audit_logs")->fetchColumn()<5){$uid=(int)$pdo->query("SELECT id FROM users WHERE username='registrar' LIMIT 1")->fetchColumn();$st=$pdo->prepare("INSERT INTO audit_logs(user_id,action,table_name,record_id,details) VALUES(?,?,?,?,?)");$st->execute([$uid,'SYSTEM_READY','users',$uid,'Konfigurasi Patient Registration aktif di port 3307']);$st->execute([$uid,'CREATE','patients',$patientMap['RM20260001']??null,'Registrasi pasien contoh']);}
