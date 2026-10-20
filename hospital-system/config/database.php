<?php
declare(strict_types=1);

$host = getenv('CAREFLOW_DB_HOST') ?: '127.0.0.1';
$db = getenv('CAREFLOW_DB_NAME') ?: 'hospital_system';
$user = getenv('CAREFLOW_DB_USER') ?: 'root';
$pass = getenv('CAREFLOW_DB_PASS') ?: '';
$charset = 'utf8mb4';
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    // Support both common local XAMPP/MariaDB setups used by the project:
    // custom 3307 and default 3306. The first reachable port wins.
    $candidatePorts = [];
    $configuredPort = getenv('CAREFLOW_DB_PORT');
    if ($configuredPort !== false && ctype_digit((string)$configuredPort)) {
        $candidatePorts[] = (int)$configuredPort;
    }
    foreach ([3307, 3306] as $fallbackPort) {
        if (!in_array($fallbackPort, $candidatePorts, true)) $candidatePorts[] = $fallbackPort;
    }

    $lastException = null;
    $pdo = null;
    foreach ($candidatePorts as $port) {
        try {
            $dsn = "mysql:host={$host};port={$port};dbname={$db};charset={$charset}";
            $pdo = new PDO($dsn, $user, $pass, $options);
            break;
        } catch (PDOException $e) {
            $lastException = $e;
            $message = strtolower($e->getMessage());
            $isMissingDb = (string)$e->getCode() === '1049' || str_contains($message, 'unknown database');
            if ($isMissingDb) {
                try {
                    $server = new PDO("mysql:host={$host};port={$port};charset={$charset}", $user, $pass, $options);
                    $server->exec("CREATE DATABASE IF NOT EXISTS `{$db}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                    $server = null;
                    $pdo = new PDO($dsn, $user, $pass, $options);
                    break;
                } catch (PDOException $createError) {
                    $lastException = $createError;
                }
            }
        }
    }

    if (!$pdo) {
        throw $lastException ?: new PDOException('Tidak dapat menemukan server MariaDB/MySQL pada port 3307 atau 3306.');
    }

    require_once __DIR__ . '/schema_compat.php';
    ensure_careflow_schema($pdo);
    // Seed the complete demo dataset first when the database is truly empty.
    seed_careflow_demo_if_empty($pdo);
    // Then enforce the documented demo credentials on fresh and existing databases.
    ensure_careflow_demo_accounts($pdo);
} catch (PDOException $e) {
    http_response_code(500);
    $detail = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    exit('<div style="font-family:Arial,sans-serif;max-width:850px;margin:50px auto;padding:24px;border:1px solid #e5e7eb;border-radius:14px">'
        . '<h2 style="margin-top:0">CareFlow belum dapat terhubung ke database</h2>'
        . '<p>Pastikan MariaDB/XAMPP berjalan. CareFlow otomatis mencoba <b>127.0.0.1:3307</b> lalu <b>127.0.0.1:3306</b>, dan akan membuat database <b>hospital_system</b> bila belum ada.</p>'
        . '<p>Pesan teknis: <code>' . $detail . '</code></p>'
        . '<p>Setelah MariaDB aktif, refresh halaman ini. Jika kamu memakai port khusus, set <code>CAREFLOW_DB_PORT</code>.</p></div>');
} catch (Throwable $e) {
    http_response_code(500);
    $detail = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    exit('<div style="font-family:Arial,sans-serif;max-width:850px;margin:50px auto;padding:24px;border:1px solid #e5e7eb;border-radius:14px">'
        . '<h2 style="margin-top:0">CareFlow gagal menyiapkan struktur database</h2>'
        . '<p>Struktur schema sedang tidak kompatibel dengan database lokal. Data lama tidak dihapus otomatis.</p>'
        . '<p>Pesan teknis: <code>' . $detail . '</code></p></div>');
}
?>
