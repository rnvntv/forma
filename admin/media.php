<?php
require __DIR__.'/../includes/db.php';
require __DIR__.'/../includes/auth.php';
require __DIR__.'/../includes/helpers.php';
require_login();
$uploadDir = __DIR__.'/../uploads/';
$base = '/uploads/';
$msg = '';
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_FILES['file'])){
  $f=$_FILES['file'];
  if($f['error']===UPLOAD_ERR_OK){
    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    $whitelist = ['jpg','jpeg','png','gif','webp','svg','pdf','txt','mp4','mov','webm'];
    if(!in_array($ext,$whitelist,true)) { $msg='Недопустимый тип файла'; }
    if($f['size']>20*1024*1024) { $msg='Файл слишком большой (макс. 20MB)'; }
    $name = bin2hex(random_bytes(6)).'.'.$ext;
    if(!is_dir($uploadDir)) mkdir($uploadDir,0775,true);
    if(empty($msg) && move_uploaded_file($f['tmp_name'],$uploadDir.$name)){
      $msg = 'Загружено: '.$base.$name;
    } else if(empty($msg)) { $msg='Не удалось сохранить файл'; }
  } else { $msg='Ошибка загрузки'; }
}
$files = array_values(array_filter(scandir($uploadDir), fn($x)=>$x!=='.'&&$x!=='..'));
?><!doctype html><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="/assets/css/style.css">
<div class="container section">
  <div class="btns" style="justify-content:space-between"><h1>Медиа‑менеджер</h1><a class="btn" href="/admin/">← Назад</a></div>
  <?php if($msg): ?><p class="small"><?=e($msg)?></p><?php endif; ?>
  <form class="card" method="post" enctype="multipart/form-data" style="display:grid;gap:12px;padding:16px;max-width:520px">
    <input type="file" name="file" required>
    <button class="btn btn--primary" type="submit">Загрузить</button>
  </form>
  <div class="grid-3" style="margin-top:18px">
    <?php foreach($files as $f): $url=$base.$f; ?>
      <div class="card"><img src="<?=e($url)?>" alt="" style="max-height:160px;object-fit:cover;border-radius:10px"><p class="small" style="margin-top:8px"><input class="input" value="<?=e($url)?>" onclick="this.select()"></p></div>
    <?php endforeach; ?>
  </div>
</div>
