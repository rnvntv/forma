<?php
require __DIR__.'/../includes/db.php';
require __DIR__.'/../includes/auth.php';
require __DIR__.'/../includes/helpers.php';
require_login();
$pdo=DB::conn();
$rows=$pdo->query('SELECT a.*, u.email FROM audit_log a JOIN users u ON u.id=a.user_id ORDER BY a.id DESC LIMIT 200')->fetchAll();
?><!doctype html><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="/assets/css/style.css">
<div class="container section">
  <div class="btns" style="justify-content:space-between"><h1>Журнал действий</h1><a class="btn" href="/admin/">← Назад</a></div>
  <table class="table" style="margin-top:10px">
    <thead><tr><th>ID</th><th>Пользователь</th><th>Действие</th><th>Сущность</th><th>Время</th></tr></thead>
    <tbody>
      <?php foreach($rows as $r): ?>
      <tr>
        <td><?= (int)$r['id'] ?></td>
        <td><?= e($r['email']) ?></td>
        <td><?= e($r['action']) ?></td>
        <td><?= e($r['entity_type'].'#'.$r['entity_id']) ?></td>
        <td><?= e($r['created_at']) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
