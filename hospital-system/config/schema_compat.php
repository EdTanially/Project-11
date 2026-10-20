<?php
declare(strict_types=1);

/**
 * CareFlow academic SIMRS schema compatibility layer.
 *
 * Purpose: make an existing local XAMPP/MariaDB database from an older
 * version compatible with the current application without requiring the
 * student to manually DROP the database every time the schema changes.
 */
function ensure_careflow_schema(PDO $pdo): void
{
    $tables = [
        'users' => "CREATE TABLE IF NOT EXISTS users(
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(120) NOT NULL,
            email VARCHAR(160) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            role VARCHAR(40) NOT NULL,
            employee_no VARCHAR(40),
            phone VARCHAR(30),
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        'patients' => "CREATE TABLE IF NOT EXISTS patients(
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NULL,
            mrn VARCHAR(30) UNIQUE NOT NULL,
            name VARCHAR(120) NOT NULL,
            nik VARCHAR(30),
            gender VARCHAR(20) NOT NULL,
            birth_date DATE NOT NULL,
            blood_type VARCHAR(5),
            phone VARCHAR(30),
            address TEXT,
            emergency_contact VARCHAR(120),
            emergency_phone VARCHAR(30),
            insurance VARCHAR(80),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        'doctors' => "CREATE TABLE IF NOT EXISTS doctors(
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            department VARCHAR(100),
            specialty VARCHAR(100),
            license_no VARCHAR(80)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        'appointments' => "CREATE TABLE IF NOT EXISTS appointments(
            id INT AUTO_INCREMENT PRIMARY KEY,
            patient_id INT NOT NULL,
            doctor_id INT NOT NULL,
            appointment_date DATETIME NOT NULL,
            status VARCHAR(30) DEFAULT 'booked',
            queue_no VARCHAR(20),
            visit_type VARCHAR(30) DEFAULT 'new',
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        'registrations' => "CREATE TABLE IF NOT EXISTS registrations(
            id INT AUTO_INCREMENT PRIMARY KEY,
            patient_id INT NOT NULL,
            registered_by INT NOT NULL,
            registration_date DATETIME DEFAULT CURRENT_TIMESTAMP,
            visit_type VARCHAR(30) DEFAULT 'outpatient',
            guarantor VARCHAR(80),
            complaint TEXT,
            status VARCHAR(30) DEFAULT 'registered'
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        'encounters' => "CREATE TABLE IF NOT EXISTS encounters(
            id INT AUTO_INCREMENT PRIMARY KEY,
            appointment_id INT NULL,
            patient_id INT NOT NULL,
            doctor_id INT NOT NULL,
            encounter_date DATETIME DEFAULT CURRENT_TIMESTAMP,
            chief_complaint TEXT,
            subjective TEXT,
            objective TEXT,
            assessment TEXT,
            plan TEXT,
            diagnosis_code VARCHAR(30),
            diagnosis_name VARCHAR(180),
            allergies TEXT,
            clinical_status VARCHAR(30) DEFAULT 'draft',
            signed_at DATETIME NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        'encounter_addenda' => "CREATE TABLE IF NOT EXISTS encounter_addenda(
            id INT AUTO_INCREMENT PRIMARY KEY,
            encounter_id INT NOT NULL,
            author_user_id INT NOT NULL,
            note TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        'nursing_notes' => "CREATE TABLE IF NOT EXISTS nursing_notes(
            id INT AUTO_INCREMENT PRIMARY KEY,
            patient_id INT NOT NULL,
            encounter_id INT NULL,
            nurse_id INT NOT NULL,
            recorded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            assessment TEXT,
            intervention TEXT,
            evaluation TEXT,
            fall_risk VARCHAR(20) DEFAULT 'low',
            pain_score INT DEFAULT 0,
            notes TEXT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        'vital_signs' => "CREATE TABLE IF NOT EXISTS vital_signs(
            id INT AUTO_INCREMENT PRIMARY KEY,
            patient_id INT NOT NULL,
            recorded_by INT NOT NULL,
            recorded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            temperature DECIMAL(4,1),
            systolic INT,
            diastolic INT,
            pulse INT,
            respiratory_rate INT,
            spo2 DECIMAL(5,2),
            weight DECIMAL(6,2),
            height DECIMAL(6,2),
            pain_score INT DEFAULT 0,
            status VARCHAR(20) NOT NULL DEFAULT 'normal',
            notes TEXT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        'medicines' => "CREATE TABLE IF NOT EXISTS medicines(
            id INT AUTO_INCREMENT PRIMARY KEY,
            code VARCHAR(40) UNIQUE NOT NULL,
            name VARCHAR(150) NOT NULL,
            category VARCHAR(80),
            unit VARCHAR(30) NOT NULL,
            stock INT NOT NULL DEFAULT 0,
            min_stock INT NOT NULL DEFAULT 10,
            expiry_date DATE,
            price DECIMAL(12,2) NOT NULL DEFAULT 0,
            is_active TINYINT(1) DEFAULT 1
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        'prescriptions' => "CREATE TABLE IF NOT EXISTS prescriptions(
            id INT AUTO_INCREMENT PRIMARY KEY,
            patient_id INT NOT NULL,
            doctor_id INT NOT NULL,
            encounter_id INT NULL,
            status VARCHAR(30) DEFAULT 'pending',
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            dispensed_at DATETIME NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        'prescription_items' => "CREATE TABLE IF NOT EXISTS prescription_items(
            id INT AUTO_INCREMENT PRIMARY KEY,
            prescription_id INT NOT NULL,
            medicine_id INT NOT NULL,
            quantity INT NOT NULL,
            dosage VARCHAR(160) NOT NULL,
            route VARCHAR(50) DEFAULT 'oral',
            duration VARCHAR(50)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        'cpoe_orders' => "CREATE TABLE IF NOT EXISTS cpoe_orders(
            id INT AUTO_INCREMENT PRIMARY KEY,
            encounter_id INT NOT NULL,
            patient_id INT NOT NULL,
            ordered_by INT NOT NULL,
            order_type VARCHAR(30) NOT NULL,
            priority VARCHAR(20) DEFAULT 'routine',
            order_name VARCHAR(180) NOT NULL,
            clinical_indication TEXT,
            instructions TEXT,
            status VARCHAR(30) DEFAULT 'ordered',
            ordered_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            completed_at DATETIME NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        'lab_results' => "CREATE TABLE IF NOT EXISTS lab_results(
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT NOT NULL,
            test_name VARCHAR(180) NOT NULL,
            result_value VARCHAR(120),
            unit VARCHAR(40),
            reference_range VARCHAR(80),
            flag VARCHAR(20) DEFAULT 'normal',
            verified_by INT NULL,
            result_notes TEXT,
            reported_at DATETIME NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        'radiology_results' => "CREATE TABLE IF NOT EXISTS radiology_results(
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT NOT NULL,
            modality VARCHAR(80),
            technique TEXT,
            findings TEXT,
            impression TEXT,
            reported_by INT NULL,
            reported_at DATETIME NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        'invoices' => "CREATE TABLE IF NOT EXISTS invoices(
            id INT AUTO_INCREMENT PRIMARY KEY,
            patient_id INT NOT NULL,
            invoice_no VARCHAR(40) UNIQUE NOT NULL,
            total DECIMAL(12,2) NOT NULL DEFAULT 0,
            status VARCHAR(30) DEFAULT 'unpaid',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        'invoice_items' => "CREATE TABLE IF NOT EXISTS invoice_items(
            id INT AUTO_INCREMENT PRIMARY KEY,
            invoice_id INT NOT NULL,
            description VARCHAR(200) NOT NULL,
            qty INT NOT NULL DEFAULT 1,
            price DECIMAL(12,2) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        'payments' => "CREATE TABLE IF NOT EXISTS payments(
            id INT AUTO_INCREMENT PRIMARY KEY,
            invoice_id INT NOT NULL,
            amount DECIMAL(12,2) NOT NULL,
            method VARCHAR(30) NOT NULL,
            paid_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            received_by INT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        'audit_logs' => "CREATE TABLE IF NOT EXISTS audit_logs(
            id BIGINT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NULL,
            action VARCHAR(120) NOT NULL,
            module VARCHAR(80),
            record_id INT NULL,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    ];

    foreach ($tables as $sql) {
        $pdo->exec($sql);
    }

    // Add columns that older versions of the project may not have.
    $columns = [
        'users' => [
            'name' => 'VARCHAR(120) NOT NULL', 'email' => 'VARCHAR(160) NULL', 'password_hash' => 'VARCHAR(255) NULL',
            'role' => "VARCHAR(40) NOT NULL DEFAULT 'patient'", 'employee_no' => 'VARCHAR(40) NULL', 'phone' => 'VARCHAR(30) NULL',
            'is_active' => 'TINYINT(1) DEFAULT 1', 'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
        ],
        'patients' => [
            'user_id' => 'INT NULL', 'mrn' => 'VARCHAR(30) NULL', 'name' => 'VARCHAR(120) NOT NULL', 'nik' => 'VARCHAR(30) NULL',
            'gender' => "VARCHAR(20) NOT NULL DEFAULT 'male'", 'birth_date' => 'DATE NULL', 'blood_type' => 'VARCHAR(5) NULL',
            'phone' => 'VARCHAR(30) NULL', 'address' => 'TEXT NULL', 'emergency_contact' => 'VARCHAR(120) NULL',
            'emergency_phone' => 'VARCHAR(30) NULL', 'insurance' => 'VARCHAR(80) NULL', 'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
        ],
        'doctors' => [
            'user_id' => 'INT NULL', 'department' => 'VARCHAR(100) NULL', 'specialty' => 'VARCHAR(100) NULL', 'license_no' => 'VARCHAR(80) NULL'
        ],
        'appointments' => [
            'patient_id' => 'INT NULL', 'doctor_id' => 'INT NULL', 'appointment_date' => 'DATETIME NULL', 'status' => "VARCHAR(30) DEFAULT 'booked'",
            'queue_no' => 'VARCHAR(20) NULL', 'visit_type' => "VARCHAR(30) DEFAULT 'new'", 'notes' => 'TEXT NULL', 'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
        ],
        'registrations' => [
            'patient_id' => 'INT NULL', 'registered_by' => 'INT NULL', 'registration_date' => 'DATETIME DEFAULT CURRENT_TIMESTAMP',
            'visit_type' => "VARCHAR(30) DEFAULT 'outpatient'", 'guarantor' => 'VARCHAR(80) NULL', 'complaint' => 'TEXT NULL',
            'status' => "VARCHAR(30) DEFAULT 'registered'"
        ],
        'encounters' => [
            'appointment_id' => 'INT NULL', 'patient_id' => 'INT NULL', 'doctor_id' => 'INT NULL', 'encounter_date' => 'DATETIME DEFAULT CURRENT_TIMESTAMP',
            'chief_complaint' => 'TEXT NULL', 'subjective' => 'TEXT NULL', 'objective' => 'TEXT NULL', 'assessment' => 'TEXT NULL', 'plan' => 'TEXT NULL',
            'diagnosis_code' => 'VARCHAR(30) NULL', 'diagnosis_name' => 'VARCHAR(180) NULL', 'allergies' => 'TEXT NULL',
            'clinical_status' => "VARCHAR(30) DEFAULT 'draft'", 'signed_at' => 'DATETIME NULL'
        ],
        'nursing_notes' => [
            'patient_id' => 'INT NULL', 'encounter_id' => 'INT NULL', 'nurse_id' => 'INT NULL', 'recorded_at' => 'DATETIME DEFAULT CURRENT_TIMESTAMP',
            'assessment' => 'TEXT NULL', 'intervention' => 'TEXT NULL', 'evaluation' => 'TEXT NULL', 'fall_risk' => "VARCHAR(20) DEFAULT 'low'",
            'pain_score' => 'INT DEFAULT 0', 'notes' => 'TEXT NULL'
        ],
        'vital_signs' => [
            'patient_id' => 'INT NULL', 'recorded_by' => 'INT NULL', 'recorded_at' => 'DATETIME DEFAULT CURRENT_TIMESTAMP', 'temperature' => 'DECIMAL(4,1) NULL',
            'systolic' => 'INT NULL', 'diastolic' => 'INT NULL', 'pulse' => 'INT NULL', 'respiratory_rate' => 'INT NULL', 'spo2' => 'DECIMAL(5,2) NULL',
            'weight' => 'DECIMAL(6,2) NULL', 'height' => 'DECIMAL(6,2) NULL', 'pain_score' => 'INT DEFAULT 0',
            'status' => "VARCHAR(20) NOT NULL DEFAULT 'normal'", 'notes' => 'TEXT NULL'
        ],
        'medicines' => [
            'code' => 'VARCHAR(40) NULL', 'name' => 'VARCHAR(150) NOT NULL', 'category' => 'VARCHAR(80) NULL', 'unit' => "VARCHAR(30) NOT NULL DEFAULT 'unit'",
            'stock' => 'INT NOT NULL DEFAULT 0', 'min_stock' => 'INT NOT NULL DEFAULT 10', 'expiry_date' => 'DATE NULL',
            'price' => 'DECIMAL(12,2) NOT NULL DEFAULT 0', 'is_active' => 'TINYINT(1) DEFAULT 1'
        ],
        'prescriptions' => [
            'patient_id' => 'INT NULL', 'doctor_id' => 'INT NULL', 'encounter_id' => 'INT NULL', 'status' => "VARCHAR(30) DEFAULT 'pending'",
            'notes' => 'TEXT NULL', 'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP', 'dispensed_at' => 'DATETIME NULL'
        ],
        'prescription_items' => [
            'prescription_id' => 'INT NULL', 'medicine_id' => 'INT NULL', 'quantity' => 'INT NOT NULL DEFAULT 1', 'dosage' => "VARCHAR(160) NOT NULL DEFAULT ''",
            'route' => "VARCHAR(50) DEFAULT 'oral'", 'duration' => 'VARCHAR(50) NULL'
        ],
        'cpoe_orders' => [
            'encounter_id' => 'INT NULL', 'patient_id' => 'INT NULL', 'ordered_by' => 'INT NULL', 'order_type' => "VARCHAR(30) NOT NULL DEFAULT 'procedure'",
            'priority' => "VARCHAR(20) DEFAULT 'routine'", 'order_name' => "VARCHAR(180) NOT NULL DEFAULT 'Clinical order'",
            'clinical_indication' => 'TEXT NULL', 'instructions' => 'TEXT NULL', 'status' => "VARCHAR(30) DEFAULT 'ordered'",
            'ordered_at' => 'DATETIME DEFAULT CURRENT_TIMESTAMP', 'completed_at' => 'DATETIME NULL'
        ],
        'lab_results' => [
            'order_id' => 'INT NULL', 'test_name' => "VARCHAR(180) NOT NULL DEFAULT 'Laboratory test'", 'result_value' => 'VARCHAR(120) NULL',
            'unit' => 'VARCHAR(40) NULL', 'reference_range' => 'VARCHAR(80) NULL', 'flag' => "VARCHAR(20) DEFAULT 'normal'",
            'verified_by' => 'INT NULL', 'result_notes' => 'TEXT NULL', 'reported_at' => 'DATETIME NULL'
        ],
        'radiology_results' => [
            'order_id' => 'INT NULL', 'modality' => 'VARCHAR(80) NULL', 'technique' => 'TEXT NULL', 'findings' => 'TEXT NULL', 'impression' => 'TEXT NULL',
            'reported_by' => 'INT NULL', 'reported_at' => 'DATETIME NULL'
        ],
        'invoices' => [
            'patient_id' => 'INT NULL', 'invoice_no' => 'VARCHAR(40) NULL', 'total' => 'DECIMAL(12,2) NOT NULL DEFAULT 0',
            'status' => "VARCHAR(30) DEFAULT 'unpaid'", 'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
        ],
        'invoice_items' => [
            'invoice_id' => 'INT NULL', 'description' => "VARCHAR(200) NOT NULL DEFAULT 'Layanan'", 'qty' => 'INT NOT NULL DEFAULT 1', 'price' => 'DECIMAL(12,2) NOT NULL DEFAULT 0'
        ],
        'payments' => [
            'invoice_id' => 'INT NULL', 'amount' => 'DECIMAL(12,2) NOT NULL DEFAULT 0', 'method' => "VARCHAR(30) NOT NULL DEFAULT 'cash'",
            'paid_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP', 'received_by' => 'INT NULL'
        ],
        'audit_logs' => [
            'user_id' => 'INT NULL', 'action' => "VARCHAR(120) NOT NULL DEFAULT 'SYSTEM'", 'module' => 'VARCHAR(80) NULL', 'record_id' => 'INT NULL',
            'description' => 'TEXT NULL', 'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
        ],
    ];

    foreach ($columns as $table => $defs) {
        $existing = [];
        $stmt = $pdo->query("SHOW COLUMNS FROM `{$table}`");
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $existing[strtolower($row['Field'])] = true;
        }
        foreach ($defs as $name => $definition) {
            if (!isset($existing[strtolower($name)])) {
                $pdo->exec("ALTER TABLE `{$table}` ADD COLUMN `{$name}` {$definition}");
            }
        }
    }

    // encounter_addenda is append-only clinical correction history.
    // Convert legacy ENUM columns to VARCHAR so new workflow states never fail
    // just because the user imported an older academic version of the project.
    $varcharMigrations = [
        ['users', 'role', 'VARCHAR(40) NOT NULL'],
        ['appointments', 'status', 'VARCHAR(30) DEFAULT \'booked\''],
        ['appointments', 'visit_type', 'VARCHAR(30) DEFAULT \'new\''],
        ['registrations', 'status', 'VARCHAR(30) DEFAULT \'registered\''],
        ['registrations', 'visit_type', 'VARCHAR(30) DEFAULT \'outpatient\''],
        ['encounters', 'clinical_status', 'VARCHAR(30) DEFAULT \'draft\''],
        ['nursing_notes', 'fall_risk', 'VARCHAR(20) DEFAULT \'low\''],
        ['vital_signs', 'status', 'VARCHAR(20) NOT NULL DEFAULT \'normal\''],
        ['prescriptions', 'status', 'VARCHAR(30) DEFAULT \'pending\''],
        ['cpoe_orders', 'order_type', 'VARCHAR(30) NOT NULL'],
        ['cpoe_orders', 'priority', 'VARCHAR(20) DEFAULT \'routine\''],
        ['cpoe_orders', 'status', 'VARCHAR(30) DEFAULT \'ordered\''],
        ['lab_results', 'flag', 'VARCHAR(20) DEFAULT \'normal\''],
        ['invoices', 'status', 'VARCHAR(30) DEFAULT \'unpaid\''],
        ['payments', 'method', 'VARCHAR(30) NOT NULL'],
    ];
    foreach ($varcharMigrations as [$table, $column, $definition]) {
        try { $pdo->exec("ALTER TABLE `{$table}` MODIFY COLUMN `{$column}` {$definition}"); } catch (Throwable $e) { /* compatible already */ }
    }

    // When an older database already contains appointments but not registration
    // episodes, reconstruct lightweight registration records so the front-office
    // dashboard is populated instead of appearing broken or empty.
    try {
        $adminId = (int)$pdo->query("SELECT id FROM users WHERE role='admin' ORDER BY id LIMIT 1")->fetchColumn();
        if ($adminId > 0) {
            $pdo->exec("INSERT INTO registrations(patient_id,registered_by,visit_type,guarantor,complaint,status)
                SELECT a.patient_id, {$adminId},
                       CASE WHEN a.visit_type='emergency' THEN 'emergency' ELSE 'outpatient' END,
                       COALESCE(p.insurance,'Umum'), COALESCE(a.notes,'Episode kunjungan'),
                       CASE WHEN a.status IN ('completed') THEN 'completed' ELSE 'queued' END
                FROM appointments a JOIN patients p ON p.id=a.patient_id
                WHERE NOT EXISTS (SELECT 1 FROM registrations r WHERE r.patient_id=a.patient_id AND DATE(r.registration_date)=DATE(a.appointment_date))");
        }
    } catch (Throwable $e) { /* old data can remain untouched if backfill is not possible */ }
}


/**
 * Make the documented demo accounts deterministic on every application start.
 * This covers fresh databases, partial databases, and older local databases.
 * Only the fixed academic demo email addresses are touched.
 */
function ensure_careflow_demo_accounts(PDO $pdo): void
{
    $accounts = [
        ['Super Administrator','admin@hospital.test','admin','ADM-001'],
        ['Dr. Andi Wijaya','doctor@hospital.test','doctor','DOK-001'],
        ['Siti Perawat','nurse@hospital.test','nurse','NRS-001'],
        ['Rina Apoteker','pharmacy@hospital.test','pharmacist','APT-001'],
        ['Budi Kasir','cashier@hospital.test','cashier','KSR-001'],
        ['Budi Santoso','patient@hospital.test','patient',null],
        ['Maya Pendaftaran','registration@hospital.test','registration','REG-001'],
        ['Dewi Rekam Medis','rekammedis@hospital.test','medical_record','RM-001'],
        ['Lina Laboratorium','lab@hospital.test','laboratory','LAB-001'],
        ['Rudi Radiologi','radiology@hospital.test','radiology','RAD-001'],
        ['Nadia Manajemen','management@hospital.test','management','MGT-001'],
        ['Arif Auditor','auditor@hospital.test','auditor','AUD-001'],
    ];

    $hash = password_hash('password', PASSWORD_DEFAULT);
    $sql = "INSERT INTO users(name,email,password_hash,role,employee_no,is_active)
            VALUES(?,?,?,?,?,1)
            ON DUPLICATE KEY UPDATE
                name=VALUES(name),
                password_hash=VALUES(password_hash),
                role=VALUES(role),
                employee_no=VALUES(employee_no),
                is_active=1";
    $stmt = $pdo->prepare($sql);
    foreach ($accounts as $a) {
        $stmt->execute([$a[0],$a[1],$hash,$a[2],$a[3]]);
    }
}

function seed_careflow_demo_if_empty(PDO $pdo): void
{
    $count = (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    if ($count > 0) return;

    $pwd = password_hash('password', PASSWORD_DEFAULT);
    $pdo->beginTransaction();
    try {
        $users = [
            ['Super Administrator','admin@hospital.test','admin','ADM-001'],
            ['Dr. Andi Wijaya','doctor@hospital.test','doctor','DOK-001'],
            ['Siti Perawat','nurse@hospital.test','nurse','NRS-001'],
            ['Rina Apoteker','pharmacy@hospital.test','pharmacist','APT-001'],
            ['Budi Kasir','cashier@hospital.test','cashier','KSR-001'],
            ['Budi Santoso','patient@hospital.test','patient',null],
            ['Maya Pendaftaran','registration@hospital.test','registration','REG-001'],
            ['Dewi Rekam Medis','rekammedis@hospital.test','medical_record','RM-001'],
            ['Lina Laboratorium','lab@hospital.test','laboratory','LAB-001'],
            ['Rudi Radiologi','radiology@hospital.test','radiology','RAD-001'],
            ['Nadia Manajemen','management@hospital.test','management','MGT-001'],
            ['Arif Auditor','auditor@hospital.test','auditor','AUD-001'],
        ];
        $u = $pdo->prepare('INSERT INTO users(name,email,password_hash,role,employee_no,is_active) VALUES(?,?,?,?,?,1)');
        foreach ($users as $row) $u->execute([$row[0],$row[1],$pwd,$row[2],$row[3]]);
        $findUser = $pdo->prepare('SELECT id FROM users WHERE email=? LIMIT 1');
        $ids=[];
        foreach ($users as $row) {
            $findUser->execute([$row[1]]);
            $uid=(int)$findUser->fetchColumn();
            $ids[$row[2]]=$uid;
            if($row[1]==='patient@hospital.test') $ids['patient_user']=$uid;
        }
        $doctorUser = (int)$pdo->query("SELECT id FROM users WHERE role='doctor' ORDER BY id LIMIT 1")->fetchColumn();
        $nurseUser = (int)$pdo->query("SELECT id FROM users WHERE role='nurse' ORDER BY id LIMIT 1")->fetchColumn();
        $regUser = (int)$pdo->query("SELECT id FROM users WHERE role='registration' ORDER BY id LIMIT 1")->fetchColumn();
        $labUser = (int)$pdo->query("SELECT id FROM users WHERE role='laboratory' ORDER BY id LIMIT 1")->fetchColumn();
        $cashierUser = (int)$pdo->query("SELECT id FROM users WHERE role='cashier' ORDER BY id LIMIT 1")->fetchColumn();

        $q=$pdo->prepare('INSERT INTO patients(user_id,mrn,name,nik,gender,birth_date,blood_type,phone,address,emergency_contact,emergency_phone,insurance) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)');
        $q->execute([$ids['patient_user'],'MRN-000001','Budi Santoso','3671000000000001','male','1992-04-12','O+','081234567890','Tangerang','Ani Santoso','081298765432','BPJS Kesehatan']);
        $q->execute([null,'MRN-000002','Siti Rahma','3671000000000002','female','1987-09-21','A+','081233344455','Karawaci','Andi Rahma','081233344466','Prudential']);
        $q->execute([null,'MRN-000003','Andi Pratama','3671000000000003','male','1975-02-11','B+','081277788899','Cipondoh','Dina Pratama','081277788800','BPJS Kesehatan']);
        $doctor=$pdo->prepare('INSERT INTO doctors(user_id,department,specialty,license_no) VALUES(?,?,?,?)');
        $doctor->execute([$doctorUser,'Poli Umum','Dokter Umum','SIP-DR-001']);
        $doctorId=(int)$pdo->lastInsertId();
        $med=$pdo->prepare('INSERT INTO medicines(code,name,category,unit,stock,min_stock,expiry_date,price,is_active) VALUES(?,?,?,?,?,?,?,?,1)');
        foreach([
            ['MED-001','Paracetamol 500 mg','Analgesik','tablet',250,30,'2028-01-31',500],
            ['MED-002','Amoxicillin 500 mg','Antibiotik','kapsul',120,20,'2027-08-30',1200],
            ['MED-003','Omeprazole 20 mg','Gastro','kapsul',15,25,'2027-03-31',1500],
            ['MED-004','NaCl 0.9% 500 mL','Infus','botol',60,10,'2028-05-31',8500],
            ['MED-005','Metformin 500 mg','Antidiabetik','tablet',8,20,'2027-11-30',900],
        ] as $m) $med->execute($m);
        $app=$pdo->prepare('INSERT INTO appointments(patient_id,doctor_id,appointment_date,status,queue_no,visit_type,notes) VALUES(?,?,DATE_ADD(NOW(),INTERVAL ? DAY),?,?,?,?)');
        $app->execute([1,$doctorId,0,'waiting','A-001','follow_up','Kontrol rutin']); $a1=(int)$pdo->lastInsertId();
        $app->execute([1,$doctorId,3,'booked','A-014','follow_up','Kontrol lanjutan']);
        $app->execute([2,$doctorId,1,'booked','A-015','new','Keluhan batuk']);
        $reg=$pdo->prepare('INSERT INTO registrations(patient_id,registered_by,visit_type,guarantor,complaint,status) VALUES(?,?,?,?,?,?)');
        $reg->execute([1,$regUser,'outpatient','BPJS Kesehatan','Kontrol hipertensi','queued']);
        $reg->execute([2,$regUser,'outpatient','Prudential','Batuk 3 hari','registered']);
        $reg->execute([3,$regUser,'outpatient','BPJS Kesehatan','Nyeri ulu hati','completed']);
        $v=$pdo->prepare('INSERT INTO vital_signs(patient_id,recorded_by,temperature,systolic,diastolic,pulse,respiratory_rate,spo2,weight,height,pain_score,status,notes) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)');
        $v->execute([1,$nurseUser,36.7,122,78,78,18,98,82,175,1,'normal','Pemeriksaan rutin']);
        $v->execute([2,$nurseUser,38.2,165,101,112,25,93,68,160,4,'warning','Demam dan tekanan darah meningkat']);
        $v->execute([3,$nurseUser,36.8,138,88,84,19,97,74,170,3,'normal','Nyeri ulu hati']);
        $enc=$pdo->prepare('INSERT INTO encounters(appointment_id,patient_id,doctor_id,chief_complaint,subjective,objective,assessment,plan,diagnosis_code,diagnosis_name,allergies,clinical_status,signed_at) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)');
        $enc->execute([$a1,1,$doctorId,'Kontrol tekanan darah','Pasien mengatakan rutin minum obat. Tidak ada nyeri dada atau sesak.','TD 122/78 mmHg, N 78 x/menit, RR 18 x/menit, SpO2 98%.','Hipertensi terkontrol.','Lanjutkan terapi, edukasi diet rendah garam, kontrol 2 minggu.','I10','Essential (primary) hypertension','Tidak ada alergi obat','signed',date('Y-m-d H:i:s')]);
        $enc1=(int)$pdo->lastInsertId();
        $enc->execute([null,2,$doctorId,'Batuk dan demam','Batuk berdahak sejak 3 hari disertai demam.','T 38.2 C, TD 165/101, N 112, SpO2 93%.','Infeksi saluran napas akut, perlu evaluasi lebih lanjut.','Order darah lengkap dan foto toraks; observasi tanda bahaya.','J06.9','Acute upper respiratory infection, unspecified','Tidak diketahui','draft',null]);
        $enc2=(int)$pdo->lastInsertId();
        $n=$pdo->prepare('INSERT INTO nursing_notes(patient_id,encounter_id,nurse_id,assessment,intervention,evaluation,fall_risk,pain_score,notes) VALUES(?,?,?,?,?,?,?,?,?)');
        $n->execute([1,$enc1,$nurseUser,'Pasien sadar compos mentis, kooperatif.','Monitoring tanda vital dan edukasi kepatuhan obat.','Pasien memahami edukasi.','low',1,'Tidak ada keluhan akut.']);
        $n->execute([2,$enc2,$nurseUser,'Pasien tampak lemah, suhu meningkat.','Kompres hangat, monitoring SpO2, lapor dokter.','Menunggu evaluasi dokter.','moderate',4,'Pantau sesak.']);
        $pr=$pdo->prepare('INSERT INTO prescriptions(patient_id,doctor_id,encounter_id,status,notes) VALUES(?,?,?,?,?)');
        $pr->execute([1,$doctorId,$enc1,'dispensed','Terapi kontrol']); $pr1=(int)$pdo->lastInsertId();
        $pr->execute([2,$doctorId,$enc2,'pending','Menunggu verifikasi apotek']); $pr2=(int)$pdo->lastInsertId();
        $item=$pdo->prepare('INSERT INTO prescription_items(prescription_id,medicine_id,quantity,dosage,route,duration) VALUES(?,?,?,?,?,?)');
        $item->execute([$pr1,1,10,'1 tablet 3x sehari setelah makan','oral','3 hari']);
        $item->execute([$pr1,3,10,'1 kapsul 1x sehari sebelum makan','oral','10 hari']);
        $item->execute([$pr2,1,15,'1 tablet 3x sehari bila demam','oral','5 hari']);
        $ord=$pdo->prepare('INSERT INTO cpoe_orders(encounter_id,patient_id,ordered_by,order_type,priority,order_name,clinical_indication,instructions,status) VALUES(?,?,?,?,?,?,?,?,?)');
        $ord->execute([$enc2,2,$doctorUser,'laboratory','routine','Darah lengkap','Demam dan dugaan infeksi','Ambil sampel darah EDTA','in_progress']);
        $ord->execute([$enc2,2,$doctorUser,'radiology','urgent','Foto Toraks PA','Batuk dan SpO2 93%','Evaluasi infiltrat paru','ordered']); $radOrder=(int)$pdo->lastInsertId();
        $ord->execute([$enc1,1,$doctorUser,'laboratory','routine','Profil lipid','Monitoring faktor risiko kardiovaskular','Puasa 8-10 jam','completed']); $labOrder=(int)$pdo->lastInsertId();
        $lab=$pdo->prepare('INSERT INTO lab_results(order_id,test_name,result_value,unit,reference_range,flag,verified_by,result_notes,reported_at) VALUES(?,?,?,?,?,?,?,?,?)');
        $lab->execute([$labOrder,'Profil lipid','182','mg/dL','<200','normal',$labUser,'Kolesterol total dalam batas target umum.',date('Y-m-d H:i:s')]);
        $rad=$pdo->prepare('INSERT INTO radiology_results(order_id,modality,technique,findings,impression,reported_by,reported_at) VALUES(?,?,?,?,?,?,?)');
        $rad->execute([$radOrder,'X-ray','Thorax PA','Belum dilakukan','Pemeriksaan belum dilakukan',null,null]);
        $inv=$pdo->prepare('INSERT INTO invoices(patient_id,invoice_no,total,status) VALUES(?,?,?,?)');
        $inv->execute([1,'INV-2026-0001',125000,'partial']); $i1=(int)$pdo->lastInsertId();
        $inv->execute([2,'INV-2026-0002',225000,'unpaid']); $i2=(int)$pdo->lastInsertId();
        $inv->execute([3,'INV-2026-0003',75000,'paid']); $i3=(int)$pdo->lastInsertId();
        $ii=$pdo->prepare('INSERT INTO invoice_items(invoice_id,description,qty,price) VALUES(?,?,?,?)');
        $ii->execute([$i1,'Konsultasi dokter',1,75000]); $ii->execute([$i1,'Obat',1,50000]);
        $ii->execute([$i2,'Konsultasi dokter',1,75000]); $ii->execute([$i2,'Laboratorium',1,150000]); $ii->execute([$i3,'Konsultasi dokter',1,75000]);
        $pay=$pdo->prepare('INSERT INTO payments(invoice_id,amount,method,received_by) VALUES(?,?,?,?)');
        $pay->execute([$i1,50000,'transfer',$cashierUser]); $pay->execute([$i3,75000,'cash',$cashierUser]);
        $al=$pdo->prepare('INSERT INTO audit_logs(user_id,action,module,record_id,description) VALUES(?,?,?,?,?)');
        $al->execute([$ids['admin'],'LOGIN','AUTH',null,'Administrator masuk ke sistem']);
        $al->execute([$doctorUser,'CREATE','ENCOUNTER',$enc1,'SOAP pasien MRN-000001 dibuat dan ditandatangani']);
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}
