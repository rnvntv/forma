<?php
session_start();
function is_logged_in(): bool { return isset($_SESSION['user_id']); }
function require_login(): void { if (!is_logged_in()) { header('Location: /admin/login.php'); exit; } }
function csrf_token(): string { if (empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(16)); return $_SESSION['csrf']; }
function check_csrf(): void { if ($_SERVER['REQUEST_METHOD']==='POST') { if (($_POST['csrf']??'')!==($_SESSION['csrf']??'')) { http_response_code(403); exit('Invalid CSRF'); } } }
