<?php
require_once __DIR__ . '/functions.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'add') {
            $name = trim($_POST['name'] ?? '');
            $status = $_POST['status'] ?? '';
            if ($name === '' || !in_array($status, ['Tetap', 'Training'], true)) throw new RuntimeException('Nama atau status tidak valid.');
            db()->prepare('INSERT INTO employees(name,status) VALUES(?,?)')->execute([$name, $status]);
            flash('Karyawan berhasil ditambahkan.');
        } elseif ($action === 'update') {
            $id = (int)($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $status = $_POST['status'] ?? '';
            if ($id < 1 || $name === '' || !in_array($status, ['Tetap', 'Training'], true)) throw new RuntimeException('Data karyawan tidak valid.');
            db()->prepare('UPDATE employees SET name=?,status=? WHERE id=?')->execute([$name, $status, $id]);
            flash('Data karyawan diperbarui.');
        } elseif ($action === 'delete') {
            db()->prepare('DELETE FROM employees WHERE id=?')->execute([(int)($_POST['id'] ?? 0)]);
            flash('Karyawan dihapus.');
        }
    } catch (Throwable $ex) {
        flash($ex instanceof RuntimeException ? $ex->getMessage() : 'Terjadi kesalahan database.', 'error');
    }
    header('Location: index.php');
    exit;
}
$employees = db()->query('SELECT * FROM employees ORDER BY id')->fetchAll();
$total = count($employees);
$permanent = 0;
$training = 0;
$salary = 0;
foreach ($employees as $emp) {
    if ($emp['status'] === 'Tetap') {
        $permanent++;
        $salary += 3500000;
    } else {
        $training++;
        $salary += 2000000;
    }
}
$notice = get_flash();
$user = current_user();
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Dashboard Penggajian</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header class="topbar">
        <div>
            <h1><?= e(COMPANY_NAME) ?></h1>
            <p><?= e(APP_NAME) ?></p>
        </div>
        <div class="top-actions"><span><?= e($user['name']) ?> (<?= $user['role'] === 'admin' ? 'Administrator' : 'HRD' ?>)</span>
            <?php if ($user['role'] === 'admin'): ?><a class="btn light" href="users.php">Kelola Pengguna</a><?php endif; ?>
            <form method="post" action="logout.php"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><button class="btn light">Logout</button></form>
        </div>
    </header>
    <main class="container"><?php if ($notice): ?><div class="alert <?= e($notice['type']) ?>"><?= e($notice['message']) ?></div><?php endif; ?>
        <section class="stats">
            <div class="stat"><span>Total Karyawan</span><strong><?= $total ?></strong></div>
            <div class="stat"><span>Karyawan Tetap</span><strong><?= $permanent ?></strong></div>
            <div class="stat"><span>Karyawan Training</span><strong><?= $training ?></strong></div>
            <div class="stat"><span>Total Gaji / Bulan</span><strong>Rp <?= number_format($salary, 0, ',', '.') ?></strong></div>
        </section>
        <section class="panel">
            <h2>Tambah Karyawan</h2>
            <form method="post" class="inline-form"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="add"><input name="name" placeholder="Nama karyawan" maxlength="100" required><select name="status">
                    <option>Tetap</option>
                    <option>Training</option>
                </select><button class="btn">Tambah Karyawan</button></form>
            <p class="muted small">Gaji contoh: Tetap Rp3.500.000 dan Training Rp2.000.000 per bulan.</p>
        </section>
        <section class="panel">
            <h2>Data Karyawan</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama</th>
                            <th>Status</th>
                            <th>Gaji / Bulan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$employees): ?><tr>
                                <td colspan="5" class="center">Belum ada data karyawan.</td>
                            </tr><?php endif; ?>
                        <?php foreach ($employees as $i => $emp): ?><tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= e($emp['name']) ?></td>
                                <td><span class="pill <?= $emp['status'] === 'Tetap' ? 'green' : 'orange' ?>"><?= e($emp['status']) ?></span></td>
                                <td>Rp <?= number_format($emp['status'] === 'Tetap' ? 3500000 : 2000000, 0, ',', '.') ?></td>
                                <td>
                                    <div class="actions">
                                        <details>
                                            <summary class="btn secondary small-btn">Edit</summary>
                                            <form method="post" class="edit-form"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="update"><input type="hidden" name="id" value="<?= (int)$emp['id'] ?>"><input name="name" value="<?= e($emp['name']) ?>" required><select name="status">
                                                    <option value="Tetap" <?= $emp['status'] === 'Tetap' ? 'selected' : '' ?>>Tetap</option>
                                                    <option value="Training" <?= $emp['status'] === 'Training' ? 'selected' : '' ?>>Training</option>
                                                </select><button class="btn small-btn">Simpan</button></form>
                                        </details>
                                        <form method="post" onsubmit="return confirm('Yakin menghapus karyawan ini?')"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$emp['id'] ?>"><button class="btn danger small-btn">Hapus</button></form>
                                    </div>
                                </td>
                            </tr><?php endforeach; ?></tbody>
                </table>
            </div>
        </section>
        <footer>MANAJEMEN SUMBER DAYA MANUSIA</footer>
    </main>
</body>

</html>