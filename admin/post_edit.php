<?php
require __DIR__.'/../includes/db.php';
require __DIR__.'/../includes/auth.php';
require __DIR__.'/../includes/helpers.php';
require_login();
check_csrf();
$pdo=DB::conn();
$id=intval($_GET['id']??0);
$post=[ 'id'=>0,'slug'=>'','title'=>'','content'=>'','published'=>1 ];
if($id){ $st=$pdo->prepare('SELECT * FROM posts WHERE id=?'); $st->execute([$id]); $post=$st->fetch() ?: $post; }
if($_SERVER['REQUEST_METHOD']==='POST'){
  $post['title']=$_POST['title']??'';
  $post['slug']=trim($_POST['slug']??'');
  $post['content']=$_POST['content']??'';
  $post['published']=isset($_POST['published'])?1:0;
  if(!$id){
    $st=$pdo->prepare('INSERT INTO posts(slug,title,content,published) VALUES(?,?,?,?)');
    $st->execute([$post['slug'],$post['title'],$post['content'],$post['published']]);
    $newId=(int)$pdo->lastInsertId();
    audit($pdo,(int)$_SESSION['user_id'],'create','post',$newId);
    redirect('/admin/');
  } else {
    $st=$pdo->prepare('UPDATE posts SET slug=?, title=?, content=?, published=? WHERE id=?');
    $st->execute([$post['slug'],$post['title'],$post['content'],$post['published'],$id]);
    audit($pdo,(int)$_SESSION['user_id'],'update','post',$id);
    redirect('/admin/');
  }
}
?><!doctype html><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="/assets/css/style.css">
<div class="container section" style="max-width:980px">
  <div class="btns" style="justify-content:space-between"><h1><?=$id?'Редактировать пост':'Новый пост'?></h1><a class="btn" href="/admin/">← Назад</a></div>
  <form class="card" method="post" style="display:grid; gap:12px; padding:16px">
    <input type="hidden" name="csrf" value="<?=e(csrf_token())?>">
    <div class="grid-2">
      <label class="small">Заголовок<input class="input" name="title" value="<?=e($post['title'])?>" required></label>
      <label class="small">Слаг<input class="input" name="slug" value="<?=e($post['slug'])?>" placeholder="minimalism / brief / ..."></label>
    </div>
    <label class="small">Контент (HTML)
      <textarea id="editor" class="textarea" name="content" rows="18"><?=(e($post['content']))?></textarea>
    </label>
    <label class="small"><input type="checkbox" name="published" <?=$post['published']?'checked':''?>> Опубликован</label>
    <div class="btns"><button class="btn btn--primary" type="submit">Сохранить</button></div>
  </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/tinymce@7.2.1/tinymce.min.js" referrerpolicy="origin"></script>
<script>tinymce.init({ selector:'#editor', height: 520, menubar:false, plugins:'link lists image code table', toolbar:'undo redo | styles | bold italic | forecolor | alignleft aligncenter alignright | bullist numlist | link image table | code', content_css:'/assets/css/style.css' });</script>
