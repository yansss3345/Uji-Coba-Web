<?php
require_once __DIR__.'/functions.php'; require_admin();
if($_SERVER['REQUEST_METHOD']==='POST') {
 verify_csrf(); $action=$_POST['action']??'';
 try {
  if($action==='create') {
   $name=trim($_POST['name']??''); $username=trim($_POST['username']??''); $password=$_POST['password']??''; $role=$_POST['role']??'hrd';
   if($name===''||$username===''||strlen($password)<10||!in_array($role,['admin','hrd'],true)) throw new RuntimeException('Lengkapi data. Password minimal 10 karakter.');
   db()->prepare('INSERT INTO users(name,username,password_hash,role) VALUES(?,?,?,?)')->execute([$name,$username,password_hash($password,PASSWORD_DEFAULT),$role]);
   flash('Akun pengguna berhasil dibuat.');
  } elseif($action==='delete') {
   $id=(int)($_POST['id']??0);
   if($id===(int)current_user()['id']) throw new RuntimeException('Anda tidak dapat menghapus akun sendiri.');
   $s=db()->prepare('SELECT role FROM users WHERE id=?');$s->execute([$id]);$target=$s->fetch();
   if(!$target) throw new RuntimeException('Pengguna tidak ditemukan.');
   if($target['role']==='admin' && (int)db()->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn()<=1) throw new RuntimeException('Administrator terakhir tidak boleh dihapus.');
   db()->prepare('DELETE FROM users WHERE id=?')->execute([$id]); flash('Akun pengguna berhasil dihapus.');
  }
 } catch(PDOException $ex) { flash('Username sudah digunakan atau terjadi kesalahan database.','error'); }
 catch(Throwable $ex) { flash($ex->getMessage(),'error'); }
 header('Location: users.php');exit;
}
$users=db()->query('SELECT id,name,username,role,is_active FROM users ORDER BY id')->fetchAll();$notice=get_flash();
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Kelola Pengguna</title><link rel="stylesheet" href="style.css"></head><body>
<header class="topbar"><div><h1><?=e(COMPANY_NAME)?></h1><p>Manajemen Akun Pengguna</p></div><div class="top-actions"><a class="btn light" href="index.php">Kembali ke Dashboard</a></div></header><main class="container">
<?php if($notice):?><div class="alert <?=e($notice['type'])?>"><?=e($notice['message'])?></div><?php endif;?>
<section class="panel"><h2>Buat Akun Baru</h2><form method="post" class="user-form"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="create">
<div><label>Nama lengkap</label><input name="name" required maxlength="100"></div><div><label>Username</label><input name="username" required maxlength="50"></div><div><label>Password (minimal 10 karakter)</label><input type="password" name="password" required minlength="10" autocomplete="new-password"></div><div><label>Hak akses</label><select name="role"><option value="hrd">HRD</option><option value="admin">Administrator</option></select></div><button class="btn">Buat Akun</button></form></section>
<section class="panel"><h2>Daftar Pengguna</h2><div class="table-wrap"><table><thead><tr><th>No.</th><th>Nama</th><th>Username</th><th>Hak Akses</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
<?php foreach($users as $i=>$u):?><tr><td><?=$i+1?></td><td><?=e($u['name'])?></td><td><?=e($u['username'])?></td><td><?=$u['role']==='admin'?'Administrator':'HRD'?></td><td><?=(int)$u['is_active']?'Aktif':'Nonaktif'?></td><td>
<?php if((int)$u['id']!==(int)current_user()['id']):?><form method="post" onsubmit="return confirm('Hapus akun <?=e($u['username'])?>?')"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=(int)$u['id']?>"><button class="btn danger small-btn">Hapus</button></form><?php else:?><span class="muted">Akun Anda</span><?php endif;?>
</td></tr><?php endforeach;?></tbody></table></div></section><footer>MANAJEMEN SUMBER DAYA MANUSIA</footer></main></body></html>
