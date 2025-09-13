<?php
require __DIR__.'/../includes/db.php';
require __DIR__.'/../includes/auth.php';
require __DIR__.'/../includes/helpers.php';
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $email=$_POST['email']??''; $pass=$_POST['password']??'';
  $st=DB::conn()->prepare('SELECT * FROM users WHERE email=? LIMIT 1');
  $st->execute([$email]); $u=$st->fetch();
  if($u && password_verify($pass,$u['password_hash'])){ $_SESSION['user_id']=$u['id']; redirect('/admin/'); }
  else $error='Неверный email или пароль';
}
?><!doctype html><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="/assets/css/style.css">
<div class="container section" style="max-width:420px">
  <h1>Вход в админ‑панель</h1>
  <?php if($error): ?><p class="small" style="color:#b00020"><?=e($error)?></p><?php endif; ?>
  <form class="card" method="post" style="margin-top:16px;display:grid;gap:12px;padding:16px">
    <input class="input" type="email" name="email" placeholder="Email" required>
    <input class="input" type="password" name="password" placeholder="Пароль" required>
    <button class="btn btn--primary" type="submit">Войти</button>
  </form>
</div>
