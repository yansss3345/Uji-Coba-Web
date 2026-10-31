<?php
require_once __DIR__ . '/db.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
 $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
 session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>$https,'httponly'=>true,'samesite'=>'Lax']);
 session_start();
}
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function csrf_token(): string {
 if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
 return $_SESSION['csrf_token'];
}
function verify_csrf(): void {
 $t = $_POST['csrf_token'] ?? '';
 if (!is_string($t) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $t)) {
  http_response_code(403); exit('Permintaan ditolak: token keamanan tidak valid.');
 }
}
function current_user(): ?array { return $_SESSION['user'] ?? null; }
function require_login(): void { if (!current_user()) { header('Location: login.php'); exit; } }
function require_admin(): void {
 require_login();
 if ((current_user()['role'] ?? '') !== 'admin') { http_response_code(403); exit('Akses ditolak. Khusus administrator.'); }
}
function flash(string $m, string $type='success'): void { $_SESSION['flash']=['message'=>$m,'type'=>$type]; }
function get_flash(): ?array { $f=$_SESSION['flash']??null; unset($_SESSION['flash']); return $f; }
