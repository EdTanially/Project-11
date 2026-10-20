<?php
date_default_timezone_set('Asia/Jakarta');
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$scriptDir = str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
$basePath = preg_replace('#/config$#','',$scriptDir);
if (!$basePath || $basePath === '/') $basePath = '';
if (!defined('BASE_URL')) define('BASE_URL', rtrim($basePath,'/'));
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function current_user(): ?array { return $_SESSION['user'] ?? null; }
function require_login(): void { if (!current_user()) { header('Location: '.BASE_URL.'/login.php'); exit; } }
function require_role(array $roles): void { require_login(); if (!in_array($_SESSION['user']['role'], $roles, true)) { http_response_code(403); include __DIR__.'/../partials/403.php'; exit; } }
function audit(PDO $pdo,string $action,string $table='',?int $recordId=null,string $details=''): void {
    $u=current_user(); $uid=$u['id']??null;
    $pdo->prepare('INSERT INTO audit_logs(user_id,action,table_name,record_id,details) VALUES(?,?,?,?,?)')->execute([$uid,$action,$table,$recordId,$details]);
}
function next_queue(PDO $pdo, int $clinicId): string {
    $stmt=$pdo->prepare("SELECT queue_no FROM visits WHERE clinic_id=? AND DATE(created_at)=CURDATE() ORDER BY id DESC");
    $stmt->execute([$clinicId]);
    $max=0;
    foreach ($stmt->fetchAll() as $row) {
        if (preg_match('/(\d+)$/',(string)$row['queue_no'],$m)) $max=max($max,(int)$m[1]);
    }
    return 'A'.str_pad((string)($max+1),3,'0',STR_PAD_LEFT);
}
function date_id(string $format='d-m-Y'): string { return date($format); }
