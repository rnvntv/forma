<?php
require __DIR__.'/../includes/db.php';
require __DIR__.'/../includes/auth.php';
require __DIR__.'/../includes/helpers.php';
require_login();
$pdo=DB::conn();
$id=(int)($_GET['id']??0);
if($id){
  $pdo->prepare('DELETE FROM pages WHERE id=?')->execute([$id]);
  audit($pdo,(int)$_SESSION['user_id'],'delete','page',$id);
}
redirect('/admin/');
