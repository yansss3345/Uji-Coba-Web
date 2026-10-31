<?php
require_once __DIR__ . '/functions.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Metode tidak diizinkan.');
}
verify_csrf();
unset($_SESSION['user']);
session_regenerate_id(true);
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
header('Location: login.php');
exit;
