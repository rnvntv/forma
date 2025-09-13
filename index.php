<?php
// Simple front controller routing pages and posts from DB, fallback to static
require __DIR__.'/includes/db.php';
require __DIR__.'/includes/helpers.php';
// Generate sitemap.xml dynamically if requested
if (trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/')==='sitemap.xml') {
  header('Content-Type: application/xml; charset=UTF-8');
  $pdo=DB::conn();
  $urls=[];
  $rows=$pdo->query("SELECT slug, updated_at FROM pages WHERE published=1")->fetchAll();
  foreach($rows as $r){ $path = ($r['slug']===''?'/':'/'.trim($r['slug'],'/')); $urls[]=$path; }
  $posts=$pdo->query("SELECT slug, updated_at FROM posts WHERE published=1")->fetchAll();
  foreach($posts as $r){ $urls[]='/blog/'.trim($r['slug'],'/'); }
  echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
  foreach($urls as $u){ echo "  <url><loc>".e(base_url($u))."</loc></url>\n"; }
  echo "</urlset>"; exit;
}
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
if ($uri==='') { $slug=''; } else { $slug=$uri; }
$pdo = DB::conn();
// Admin and assets bypass via .htaccess

// Try page by slug (home slug empty)
$stmt=$pdo->prepare("SELECT * FROM pages WHERE (CASE WHEN ?='' THEN slug='' ELSE slug=? END) AND published=1 LIMIT 1");
$stmt->execute([$slug,$slug]);
$page=$stmt->fetch();
if ($page) {
  $title = $page['seo_title'] ?: $page['title'];
  $desc  = $page['seo_desc'] ?: '';
  $html  = $page['content'];
  // naive replace base_url if provided
  $html = str_replace(['href="/','src="/'], ['href="'.base_url('/'),'src="'.base_url('/')], $html);
  // inject analytics hook if needed
  echo $html; exit;
}

// Try blog index or post
if (strpos($slug,'blog')===0) {
  $parts = explode('/', $slug);
  if (count($parts)===1) {
    // blog index
    $posts = $pdo->query("SELECT * FROM posts WHERE published=1 ORDER BY created_at DESC")->fetchAll();
    include __DIR__.'/templates/blog_index.php';
    exit;
  } else {
    $pslug = $parts[1] ?? '';
    $st=$pdo->prepare('SELECT * FROM posts WHERE slug=? AND published=1 LIMIT 1');
    $st->execute([$pslug]);
    $post=$st->fetch();
    if ($post) { include __DIR__.'/templates/blog_post.php'; exit; }
  }
}

http_response_code(404);
echo '<!doctype html><meta charset="utf-8"><div style="font-family:Inter,system-ui;padding:40px"><h1>404</h1><p>Страница не найдена</p></div>';
