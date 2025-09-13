<?php $cfg=require __DIR__.'/../includes/config.php'; ?><!doctype html>
<html lang="ru"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?=e($post['title'])?> — <?=e($cfg['site_name'])?></title>
<link rel="stylesheet" href="<?=e(base_url('/assets/css/style.css'))?>">
</head><body>
<div class="container section">
  <a href="<?=e(base_url('/blog'))?>" class="btn" style="margin-bottom:12px">← Блог</a>
  <h1><?=e($post['title'])?></h1>
  <div class="lead small" style="margin:8px 0 18px">Опубликовано: <?=date('d.m.Y', strtotime($post['created_at']))?></div>
  <article class="card" style="padding:24px">
    <?=$post['content']?>
  </article>
</div>
</body></html>
