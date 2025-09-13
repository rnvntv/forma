<?php
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function base_url(string $path=''): string { $cfg=require __DIR__.'/config.php'; $b=rtrim($cfg['base_url']??'', '/'); return $b.$path; }
function slugify(string $text): string { $t=iconv('UTF-8','ASCII//TRANSLIT',$text); $t=strtolower($t); $t=preg_replace('~[^a-z0-9]+~','-',$t); return trim($t,'-') ?: 'page'; }
function redirect(string $path): void { header('Location: '.$path); exit; }
function audit(PDO $pdo,int $userId,string $action,string $type,int $id): void {
  try { $st=$pdo->prepare('INSERT INTO audit_log(user_id,action,entity_type,entity_id) VALUES(?,?,?,?)'); $st->execute([$userId,$action,$type,$id]); } catch(Throwable $e){}
}
