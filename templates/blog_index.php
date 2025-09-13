<?php $cfg=require __DIR__.'/../includes/config.php'; ?><!doctype html>
<html lang="ru"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?=e($cfg['site_name'])?> — Блог</title>
<link rel="stylesheet" href="<?=e(base_url('/assets/css/style.css'))?>">
</head><body>
<div class="container section">
  <h1>Блог</h1>
  <div class="grid-3" style="margin-top:18px">
    <?php foreach($posts as $p): ?>
      <article class="card">
        <h3><a href="<?=e(base_url('/blog/'.$p['slug']))?>" style="text-decoration:none;color:inherit"><?=e($p['title'])?></a></h3>
        <p class="small"><?=date('d.m.Y', strtotime($p['created_at']))?></p>
      </article>
    <?php endforeach; ?>
  </div>
</div>
</body></html>
