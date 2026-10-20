<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../config/auth.php';
if (!empty($_SESSION['user'])) { header('Location: '.url('')); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare("SELECT * FROM users WHERE LOWER(TRIM(email))=? AND is_active=1 LIMIT 1");
    $stmt->execute([$email]); $u = $stmt->fetch();
    if ($u && password_verify($password, (string)$u['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id'=>$u['id'],'name'=>$u['name'],'email'=>$u['email'],'role'=>$u['role'],'employee_no'=>$u['employee_no']];
        header('Location: '.url('')); exit;
    }
    $error = 'Email atau password salah.';
}
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Hospital System — Login</title><link rel="stylesheet" href="../assets/css/app.css"></head>
<body class="login-bg"><div class="login-card">
<div class="brand">✚ <span>CareFlow</span></div><h1>Selamat datang</h1><p class="muted">Masuk ke Hospital Management System</p>
<?php if($error): ?><div class="alert danger"><?=h($error)?></div><?php endif; ?>
<form method="post"><label>Email</label><input name="email" type="email" required placeholder="admin@hospital.test">
<label>Password</label><input name="password" type="password" required placeholder="••••••••">
<button class="btn primary full">Masuk ke Sistem</button></form>
<div class="demo">Demo: registration@hospital.test / password<br>Admin: admin@hospital.test / password</div></div></body></html>