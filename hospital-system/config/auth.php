<?php
declare(strict_types=1);
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
function base_url(): string { $script=str_replace('\\','/',$_SERVER['SCRIPT_NAME']??'/hospital-system/index.php'); if(($pos=strpos($script,'/auth/'))!==false) return $pos>0?substr($script,0,$pos):''; $dir=rtrim(str_replace('\\','/',dirname($script)),'/'); return $dir==='/'?'':$dir; }
function url(string $path=''): string { return base_url().'/'.ltrim($path,'/'); }
function require_login(): void { if(empty($_SESSION['user'])) { header('Location: '.url('auth/login.php')); exit; } }
function deny_access(string $message='Role akun Anda tidak memiliki kewenangan untuk membuka modul ini.'): never { http_response_code(403); exit('<!doctype html><html><body style="font-family:Arial,sans-serif;background:#f7f8fa"><div style="max-width:720px;margin:70px auto;background:#fff;padding:30px;border:1px solid #e5e7eb;border-radius:18px;box-shadow:0 8px 30px rgba(0,0,0,.05)"><h2 style="margin:0 0 10px">Akses ditolak</h2><p style="color:#64748b">'.h($message).'</p><a href="'.h(url('')).'">Kembali ke Dashboard</a></div></body></html>'); }
function require_role(array $roles): void { require_login(); if(!in_array(current_role(),$roles,true)) deny_access(); }
function h(?string $v): string { return htmlspecialchars((string)($v ?? ''),ENT_QUOTES,'UTF-8'); }
function csrf_token(): string { if(empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function check_csrf(): void { if(!hash_equals($_SESSION['csrf'] ?? '',$_POST['csrf'] ?? '')){ http_response_code(419); exit('CSRF token tidak valid. Silakan refresh halaman dan ulangi.'); } }
function flash(?string $message=null): ?string { if($message!==null){$_SESSION['flash']=$message;return null;} $x=$_SESSION['flash']??null;unset($_SESSION['flash']);return $x; }
function role_label(string $role): string { return ['admin'=>'Administrator / IT','doctor'=>'Dokter','nurse'=>'Perawat','pharmacist'=>'Apoteker','cashier'=>'Kasir','patient'=>'Pasien','registration'=>'Pendaftaran','medical_record'=>'Rekam Medis','laboratory'=>'Laboratorium','radiology'=>'Radiologi','management'=>'Manajemen','auditor'=>'Auditor'][$role]??ucfirst($role); }
function current_role(): string{return $_SESSION['user']['role']??'';}
function is_role(string ...$roles): bool{return in_array(current_role(),$roles,true);}
function current_user_id(): int{return (int)($_SESSION['user']['id']??0);}
function current_patient_id(PDO $pdo): ?int { static $pid=null,$loaded=false; if($loaded)return $pid; $loaded=true; $s=$pdo->prepare('SELECT id FROM patients WHERE user_id=? LIMIT 1'); $s->execute([current_user_id()]); $v=$s->fetchColumn(); return $pid=$v===false?null:(int)$v; }
function doctor_id(PDO $pdo): int { static $id=null; if($id!==null)return $id; $s=$pdo->prepare('SELECT id FROM doctors WHERE user_id=? LIMIT 1'); $s->execute([current_user_id()]); return $id=(int)($s->fetchColumn()?:0); }
function redirect(string $path): never{header('Location: '.url($path));exit;}
function audit(PDO $pdo,string $action,string $module,?int $recordId, string $description):void{ $s=$pdo->prepare('INSERT INTO audit_logs(user_id,action,module,record_id,description) VALUES(?,?,?,?,?)'); $s->execute([current_user_id()?:null,$action,$module,$recordId,$description]); }
function page_roles(string $page): array {
    return [
        'appointments.php'=>['doctor','nurse','registration','patient'],
        'patients.php'=>['doctor','nurse','registration','medical_record','laboratory','radiology'],
        'registration.php'=>['registration','nurse','doctor'],
        'encounters.php'=>['doctor','nurse','medical_record'],
        'nursing.php'=>['nurse','doctor','medical_record'],
        'vitals.php'=>['nurse','doctor','patient','medical_record'],
        'cpoe.php'=>['doctor','nurse','laboratory','radiology'],
        'lab.php'=>['laboratory','doctor','nurse','medical_record','patient'],
        'radiology.php'=>['radiology','doctor','nurse','medical_record','patient'],
        'prescriptions.php'=>['doctor','pharmacist','patient'],
        'medicines.php'=>['pharmacist'],
        'billing.php'=>['cashier','patient'],
        'reports.php'=>['doctor','nurse','pharmacist','cashier','management','medical_record','laboratory','radiology','registration'],
        'audit.php'=>['auditor'],
        'system_health.php'=>['admin'],
        'user_management.php'=>['admin'],
    ][$page] ?? [];
}
function require_page(string $page): void { require_login(); $roles=page_roles($page); if($roles && !in_array(current_role(),$roles,true)) deny_access(); }
function can_manage_system(): bool { return is_role('admin'); }
?>