<?php
require_once __DIR__ . '/functions.php';
if (current_user()) {
    header('Location: index.php');
    exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $s = db()->prepare('SELECT id,name,username,password_hash,role,is_active FROM users WHERE username=? LIMIT 1');
    $s->execute([$username]);
    $u = $s->fetch();
    if ($u && (int)$u['is_active'] === 1 && password_verify($password, $u['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => (int)$u['id'], 'name' => $u['name'], 'username' => $u['username'], 'role' => $u['role']];
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        header('Location: index.php');
        exit;
    }
    $error = 'Username atau password salah, atau akun tidak aktif.';
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="auth-bg">
    <main class="auth-card">
        <div class="brand">
            <h1><?= e(COMPANY_NAME) ?></h1>
            <p><?= e(APP_NAME) ?></p>
        </div>
        <?php if (isset($_GET['setup'])): ?><div class="alert success">Administrator berhasil dibuat. Silakan login.</div><?php endif; ?>
        <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
        <form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <label>Username</label><input name="username" required autocomplete="username" autofocus>
            <label>Password</label><input type="password" name="password" required autocomplete="current-password">
            <button class="btn full">Login</button>
        </form>
        <p class="muted center">Akses hanya untuk pengguna terdaftar.</p>
    </main>
</body>

</html>