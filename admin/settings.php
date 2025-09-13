<?php
require __DIR__.'/../includes/db.php';
require __DIR__.'/../includes/auth.php';
require __DIR__.'/../includes/helpers.php';
require_login();
check_csrf();
$pdo=DB::conn();
$cfg=require __DIR__.'/../includes/config.php';
$settings=['site_name','base_url','preview_secret'];
if($_SERVER['REQUEST_METHOD']==='POST'){
  foreach($settings as $k){ $v=$_POST[$k]??''; $st=$pdo->prepare('REPLACE INTO settings(`key`,`value`) VALUES(?,?)'); $st->execute([$k,$v]); }
  redirect('/admin/settings.php');
}
$vals=[]; foreach($settings as $k){ $st=$pdo->prepare('SELECT value FROM settings WHERE `key`=?'); $st->execute([$k]); $vals[$k]=$st->fetchColumn() ?: ($cfg[$k]??''); }
?><!doctype html><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="/assets/css/style.css">
<div class="container section" style="max-width:720px">
  <div class="btns" style="justify-content:space-between"><h1>Настройки сайта</h1><a class="btn" href="/admin/">← Назад</a></div>
  <form class="card" method="post" style="display:grid;gap:12px;padding:16px">
    <input type="hidden" name="csrf" value="<?=e(csrf_token())?>">
    <label class="small">Название сайта<input class="input" name="site_name" value="<?=e($vals['site_name'])?>"></label>
    <label class="small">Базовый URL<input class="input" name="base_url" value="<?=e($vals['base_url'])?>"></label>
    <label class="small">Preview secret (для предпросмотра черновиков)
      <input class="input" name="preview_secret" value="<?=e($vals['preview_secret']??'')?>" placeholder="например, XyZ123">
    </label>
    <div class="btns"><button class="btn btn--primary">Сохранить</button></div>
  </form>
</div>
