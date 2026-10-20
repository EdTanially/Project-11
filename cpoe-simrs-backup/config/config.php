<?php
session_start();

const DB_HOST = '127.0.0.1';
const DB_PORT = '3307';
const DB_NAME = 'cpoe_simrs';
const DB_USER = 'root';
const DB_PASS = '';

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST
     . ';port=' . DB_PORT
     . ';dbname=' . DB_NAME
     . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
    return $pdo;
}

function e(string $v): string { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
function redirect(string $url): never { header('Location: ' . $url); exit; }
function flash(?string $type = null, ?string $message = null) {
    if ($type && $message) { $_SESSION['flash'] = [$type, $message]; return; }
    $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f;
}
function csrf(): string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(16)); }
function verify_csrf(): void {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(419); exit('CSRF token tidak valid.'); }
}
function user(): ?array { return $_SESSION['user'] ?? null; }
function require_login(): void { if (!user()) redirect('?page=login'); }
function require_role(array $roles): void { require_login(); if (!in_array(user()['role'], $roles, true)) { http_response_code(403); exit('Akses ditolak.'); } }
function audit(string $action, string $entity, ?int $entityId = null, string $detail = ''): void {
    $u = user();
    $stmt = db()->prepare('INSERT INTO audit_logs (user_id, action, entity, entity_id, detail, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
    $stmt->execute([$u['id'] ?? null, $action, $entity, $entityId, $detail]);
}
function status_badge(string $status): string {
    $map = [
        'Draft' => 'muted', 'Menunggu Validasi' => 'warning', 'Dikirim' => 'info', 'Diproses' => 'info',
        'Dijadwalkan' => 'info', 'Selesai' => 'success', 'Dibatalkan' => 'danger', 'Perlu Revisi' => 'warning'
    ];
    return '<span class="badge ' . ($map[$status] ?? 'muted') . '">' . e($status) . '</span>';
}
