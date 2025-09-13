<?php
// Simple installer: creates config.php, DB, admin user, imports content
ini_set('display_errors', 0);
if (php_sapi_name()==='cli-server') { $_SERVER['REQUEST_SCHEME']='http'; }
$step = $_POST['step'] ?? 'form';
function h($s){return htmlspecialchars($s,ENT_QUOTES,'UTF-8');}
function view_form($error=''){
  $sample = require __DIR__.'/includes/config.sample.php';
  echo '<!doctype html><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Установка FORMA</title><link rel="stylesheet" href="assets/css/style.css">';
  echo '<div class="container" style="max-width:760px;padding:40px 24px">';
  echo '<h1>Установка FORMA</h1><p class="lead">Заполните доступы к MySQL и данные администратора.</p>'; if($error) echo '<p class="small" style="color:#b00020">'.h($error).'</p>';
  echo '<form method="post" class="card" style="margin-top:16px">';
  echo '<input type="hidden" name="step" value="run">';
  echo '<h3>База данных</h3>';
  echo '<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">';
  echo '<div><label class="small">Host<input class="input" name="db_host" value="'.h($sample['db_host']).'"></label></div>';
  echo '<div><label class="small">DB name<input class="input" name="db_name" value="'.h($sample['db_name']).'"></label></div>';
  echo '<div><label class="small">User<input class="input" name="db_user" value="'.h($sample['db_user']).'"></label></div>';
  echo '<div><label class="small">Password<input class="input" name="db_pass" type="password"></label></div>';
  echo '</div><hr>';
  echo '<h3>Сайт</h3>';
  $base = (isset($_SERVER['REQUEST_SCHEME'])?$_SERVER['REQUEST_SCHEME']:'https').'://'.($_SERVER['HTTP_HOST']??'localhost');
  echo '<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">';
  echo '<div><label class="small">Название сайта<input class="input" name="site_name" value="FORMA"></label></div>';
  echo '<div><label class="small">Базовый URL<input class="input" name="base_url" value="'.h($base).'"></label></div>';
  echo '</div><hr>';
  echo '<h3>Администратор</h3>';
  echo '<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">';
  echo '<div><label class="small">Email<input class="input" name="admin_email" value="admin@example.com"></label></div>';
  echo '<div><label class="small">Пароль<input class="input" name="admin_pass" type="password" value="admin123"></label></div>';
  echo '</div><div class="btns" style="margin-top:16px"><button class="btn btn--primary" type="submit">Установить</button></div>';
  echo '</form></div>';
}
if ($step==='form') { view_form(); exit; }

// Run install
try {
  $cfg = [
    'db_host'=>$_POST['db_host']??'localhost',
    'db_name'=>$_POST['db_name']??'forma_site',
    'db_user'=>$_POST['db_user']??'root',
    'db_pass'=>$_POST['db_pass']??'',
    'site_name'=>$_POST['site_name']??'FORMA',
    'base_url'=>rtrim($_POST['base_url']??'', '/'),
    'env'=>'prod'
  ];
  // Write config.php
  $cfg_php = "<?php\nreturn ".var_export($cfg,true).";\n";
  file_put_contents(__DIR__.'/includes/config.php', $cfg_php);

  // Create schema
  require __DIR__.'/includes/db.php';
  $pdo = DB::conn();
  $sql = file_get_contents(__DIR__.'/includes/schema.sql');
  $pdo->exec($sql);

  // Create admin user
  $email = $_POST['admin_email']??'admin@example.com';
  $pass  = $_POST['admin_pass']??'admin123';
  $hash = password_hash($pass, PASSWORD_DEFAULT);
  $stmt = $pdo->prepare('INSERT INTO users(email,password_hash,name) VALUES(?,?,?)');
  $stmt->execute([$email,$hash,'Admin']);

  // Seed homepage and basic pages from existing static files (optional minimal)
  $seedPages = [
    ['slug'=>'', 'title'=>'Главная', 'file'=>'index.html'],
    ['slug'=>'services', 'title'=>'Услуги', 'file'=>'services.html'],
    ['slug'=>'portfolio', 'title'=>'Портфолио', 'file'=>'portfolio.html'],
    ['slug'=>'process', 'title'=>'Процесс', 'file'=>'process.html'],
    ['slug'=>'pricing', 'title'=>'Стоимость', 'file'=>'pricing.html'],
    ['slug'=>'about', 'title'=>'О нас', 'file'=>'about.html'],
    ['slug'=>'contact', 'title'=>'Связаться', 'file'=>'contact.html'],
  ];
  foreach ($seedPages as $p) {
    $path = __DIR__.'/'.$p['file'];
    $html = file_exists($path) ? file_get_contents($path) : '<h1>'.$p['title'].'</h1>';
    $stmt = $pdo->prepare('INSERT INTO pages(slug,title,content,template) VALUES(?,?,?,?)');
    $stmt->execute([$p['slug'], $p['title'], $html, 'raw']);
  }

  // Done
  echo '<!doctype html><meta charset="utf-8"><link rel="stylesheet" href="assets/css/style.css">';
  echo '<div class="container" style="max-width:760px;padding:40px 24px">';
  echo '<h1>Установка завершена</h1><p class="lead">Админ‑панель: <a href="/admin/login.php">/admin/login.php</a></p>';
  echo '<p>Войдите под указанными email и паролем.</p>';
  echo '</div>';
} catch (Throwable $e) {
  view_form('Ошибка: '.$e->getMessage());
}
