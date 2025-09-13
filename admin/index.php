<?php
require __DIR__.'/../includes/db.php';
require __DIR__.'/../includes/auth.php';
require __DIR__.'/../includes/helpers.php';
require_login();
$pdo=DB::conn();
$pages=$pdo->query('SELECT id,slug,title,published,updated_at FROM pages ORDER BY created_at DESC')->fetchAll();
$posts=$pdo->query('SELECT id,slug,title,published,updated_at FROM posts ORDER BY created_at DESC')->fetchAll();
$userId = $_SESSION['user_id'];
?><!doctype html><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="/assets/css/style.css">
<div class="container section">
  <div class="btns" style="justify-content:space-between">
    <h1>Админ‑панель</h1>
    <div class="btns">
      <a class="btn" href="/admin/media.php">Медиа</a>
      <?php $role = $pdo->query('SELECT role FROM users WHERE id='.(int)$userId)->fetchColumn(); if($role==='admin'): ?>
      <a class="btn" href="/admin/settings.php">Настройки</a>
      <a class="btn" href="/admin/audit.php">Журнал</a>
      <?php endif; ?>
      <a class="btn" href="/">Открыть сайт</a>
      <a class="btn" href="/admin/logout.php">Выйти</a>
    </div>
  </div>
  <div class="grid-2" style="margin-top:18px">
    <div class="card">
      <div class="btns" style="justify-content:space-between"><h2>Страницы</h2><div class="btns"><a class="btn" href="/">Превью главной</a><a class="btn btn--primary" href="/admin/page_edit.php">+ Новая</a></div></div>
      <table class="table" style="margin-top:10px">
        <thead><tr><th>Заголовок</th><th>Слаг</th><th>Статус</th><th></th></tr></thead>
        <tbody>
          <?php foreach($pages as $p): ?>
          <tr>
            <td><?=e($p['title'])?></td>
            <td><?=e($p['slug']?:'/')?></td>
            <td><?= $p['published']? 'Опубликована':'Черновик' ?></td>
            <td class="btns"><a class="btn" href="/admin/page_edit.php?id=<?=$p['id']?>">Редактировать</a></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="card">
      <div class="btns" style="justify-content:space-between"><h2>Блог</h2><a class="btn btn--primary" href="/admin/post_edit.php">+ Новая</a></div>
      <table class="table" style="margin-top:10px">
        <thead><tr><th>Заголовок</th><th>Слаг</th><th>Статус</th><th></th></tr></thead>
        <tbody>
          <?php foreach($posts as $p): ?>
          <tr>
            <td><?=e($p['title'])?></td>
            <td><?=e($p['slug'])?></td>
            <td><?= $p['published']? 'Опубликована':'Черновик' ?></td>
            <td class="btns"><a class="btn" href="/admin/post_edit.php?id=<?=$p['id']?>">Редактировать</a></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
