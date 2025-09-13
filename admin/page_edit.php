<?php
require __DIR__.'/../includes/db.php';
require __DIR__.'/../includes/auth.php';
require __DIR__.'/../includes/helpers.php';
require_login();
check_csrf();
$pdo=DB::conn();
$id=intval($_GET['id']??0);
$page=[ 'id'=>0,'slug'=>'','title'=>'','content'=>'','template'=>'raw','seo_title'=>'','seo_desc'=>'','published'=>1 ];
if($id){ $st=$pdo->prepare('SELECT * FROM pages WHERE id=?'); $st->execute([$id]); $page=$st->fetch() ?: $page; }
if($_SERVER['REQUEST_METHOD']==='POST'){
  $page['title']=$_POST['title']??'';
  $page['slug']=trim($_POST['slug']??'');
  $page['content']=$_POST['content']??'';
  $page['seo_title']=$_POST['seo_title']??'';
  $page['seo_desc']=$_POST['seo_desc']??'';
  $page['published']=isset($_POST['published'])?1:0;
  if(!$id){
    $st=$pdo->prepare('INSERT INTO pages(slug,title,content,template,seo_title,seo_desc,published) VALUES(?,?,?,?,?,?,?)');
    $st->execute([$page['slug'],$page['title'],$page['content'],$page['template'],$page['seo_title'],$page['seo_desc'],$page['published']]);
    $newId = (int)$pdo->lastInsertId();
    audit($pdo, (int)$_SESSION['user_id'], 'create', 'page', $newId);
    redirect('/admin/');
  } else {
    $st=$pdo->prepare('UPDATE pages SET slug=?, title=?, content=?, template=?, seo_title=?, seo_desc=?, published=? WHERE id=?');
    $st->execute([$page['slug'],$page['title'],$page['content'],$page['template'],$page['seo_title'],$page['seo_desc'],$page['published'],$id]);
    audit($pdo, (int)$_SESSION['user_id'], 'update', 'page', $id);
    redirect('/admin/');
  }
}
?><!doctype html><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="/assets/css/style.css">
<div class="container section" style="max-width:980px">
  <div class="btns" style="justify-content:space-between"><h1><?=$id?'Редактировать страницу':'Новая страница'?></h1><a class="btn" href="/admin/">← Назад</a></div>
  <form class="card" method="post" style="display:grid; gap:12px; padding:16px">
    <input type="hidden" name="csrf" value="<?=e(csrf_token())?>">
    <div class="grid-2">
      <label class="small">Заголовок<input class="input" name="title" value="<?=e($page['title'])?>" required></label>
      <label class="small">Слаг<input class="input" name="slug" value="<?=e($page['slug'])?>" placeholder="about / pricing / ..."></label>
    </div>
    <label class="small">Контент (HTML)
      <textarea id="editor" class="textarea" name="content" rows="18"><?=(e($page['content']))?></textarea>
    </label>
    <div class="grid-2">
      <label class="small">SEO title<input class="input" name="seo_title" value="<?=e($page['seo_title'])?>"></label>
      <label class="small">SEO description<input class="input" name="seo_desc" value="<?=e($page['seo_desc'])?>"></label>
    </div>
    <label class="small"><input type="checkbox" name="published" <?=$page['published']?'checked':''?>> Опубликована</label>
    <div class="btns">
      <button class="btn btn--primary" type="submit">Сохранить</button>
      <?php if($id): ?>
      <a class="btn" href="/admin/page_remove.php?id=<?=$id?>" onclick="return confirm('Удалить страницу?')">Удалить</a>
      <?php endif; ?>
    </div>
  </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/tinymce@7.2.1/tinymce.min.js" referrerpolicy="origin"></script>
<script>tinymce.init({ selector:'#editor', height: 520, menubar:false, plugins:'link lists image code table', toolbar:'undo redo | styles | bold italic | forecolor | alignleft aligncenter alignright | bullist numlist | link image table | code', content_css:'/assets/css/style.css' });</script>
