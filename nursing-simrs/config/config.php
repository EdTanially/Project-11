<?php
date_default_timezone_set('Asia/Jakarta');
if (session_status() === PHP_SESSION_NONE) session_start();

const DB_HOST = '127.0.0.1';
const DB_PORT = '3307';
const DB_NAME = 'nursing_simrs';
const DB_USER = 'root';
const DB_PASS = '';

function db(): PDO {
    static $pdo = null;
    if (!$pdo) {
        $dsn = 'mysql:host='.DB_HOST.';port='.DB_PORT.';dbname='.DB_NAME.';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
    return $pdo;
}

function current_user(): ?array { return $_SESSION['user'] ?? null; }
function require_login(): void { if (!current_user()) { header('Location: index.php'); exit; } }
function redirect(string $url): never { header('Location: '.$url); exit; }
function flash(string $type,string $message): void { $_SESSION['flash']=[$type,$message]; }
function take_flash(): ?array { $f=$_SESSION['flash']??null; unset($_SESSION['flash']); return $f; }
function esc($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function log_action(string $action,string $module,?int $ref=null,string $detail=''): void {
    if (!current_user()) return;
    $s=db()->prepare('INSERT INTO audit_logs(user_id,action,module,reference_id,detail) VALUES (?,?,?,?,?)');
    $s->execute([current_user()['id'],$action,$module,$ref,$detail]);
}
function role_label(string $role): string {
    return match($role){'nurse'=>'Perawat','midwife'=>'Bidan','doctor'=>'Dokter','pharmacy'=>'Petugas Farmasi','admin'=>'Administrator SIMRS',default=>$role};
}
function role_can(string $cap): bool {
    $r=current_user()['role']??'';
    return match($cap){
        'assessment'=>in_array($r,['nurse','midwife']),
        'vitals'=>in_array($r,['nurse','midwife']),
        'notes'=>in_array($r,['nurse','midwife']),
        'med_admin'=>in_array($r,['nurse','midwife']),
        'monitoring'=>in_array($r,['nurse','midwife']),
        'doctor_view'=>$r==='doctor',
        'pharmacy_view'=>$r==='pharmacy',
        'admin'=>$r==='admin',
        default=>false
    };
}
