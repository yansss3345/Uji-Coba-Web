<?php
require_once __DIR__ . '/functions.php';
if ((int)db()->query('SELECT COUNT(*) FROM users')->fetchColumn() > 0) {
    header('Location: login.php');
    exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    if ($name === '' || $username === '' || strlen($password) < 10) $error = 'Lengkapi semua kolom. Password minimal 10 karakter.';
    elseif ($password !== $confirm) $error = 'Konfirmasi password tidak sama.';
    else {
        try {
            db()->prepare("INSERT INTO users(name,username,password_hash,role) VALUES(?,?,?,'admin')")
                ->execute([$name, $username, password_hash($password, PASSWORD_DEFAULT)]);
            header('Location: login.php?setup=success');
            exit;
        } catch (PDOException $ex) {
            $error = 'Username sudah digunakan atau database bermasalah.';
        }
    }
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Setup Admin</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="auth-bg">
    <main class="auth-card">
        <div class="brand">
            <h1><?= e(COMPANY_NAME) ?></h1>
            <p>Pengaturan Administrator Pertama</p>
        </div>
        <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
        <p>Buat akun administrator pertama. Halaman ini hanya aktif jika belum ada akun pengguna.</p>
        <form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <label>Nama lengkap</label><input name="name" required maxlength="100">
            <label>Username</label><input name="username" required maxlength="50" autocomplete="username">
            <label>Password (minimal 10 karakter)</label><input type="password" name="password" required minlength="10" autocomplete="new-password">
            <label>Ulangi password</label><input type="password" name="confirm_password" required minlength="10">
            <button class="btn full">Buat Administrator</button>
        </form>
    </main>
</body>

</html>