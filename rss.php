<?php
require_once 'includes/config.php';

header('Content-Type: application/rss+xml; charset=utf-8');

// RSS ayarlarını yükle
$seo_file = 'includes/seo_settings.json';
$rss_settings = [
    'rss_items' => 10,
    'rss_enabled' => true
];

if(file_exists($seo_file)) {
    $seo_settings = json_decode(file_get_contents($seo_file), true);
    $rss_settings = array_merge($rss_settings, $seo_settings);
}

if(!$rss_settings['rss_enabled']) {
    header('HTTP/1.0 404 Not Found');
    exit;
}

// Yazıları getir
$limit = $rss_settings['rss_items'];
$posts = $pdo->query("
    SELECT p.*, u.display_name, c.name as category_name 
    FROM posts p 
    LEFT JOIN users u ON p.author_id = u.id 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.status = 'published' 
    ORDER BY p.published_at DESC 
    LIMIT $limit
")->fetchAll();

// RSS içeriğini oluştur
$rss_content = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$rss_content .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n";
$rss_content .= '<channel>' . "\n";
$rss_content .= '<title>' . htmlspecialchars(SITE_NAME) . '</title>' . "\n";
$rss_content .= '<link>' . SITE_URL . '</link>' . "\n";
$rss_content .= '<description>' . htmlspecialchars(SITE_DESCRIPTION) . '</description>' . "\n";
$rss_content .= '<language>tr</language>' . "\n";
$rss_content .= '<lastBuildDate>' . date('r') . '</lastBuildDate>' . "\n";
$rss_content .= '<atom:link href="' . SITE_URL . '/rss.php" rel="self" type="application/rss+xml" />' . "\n";
$rss_content .= '<generator>Memur Blog RSS Generator</generator>' . "\n";

foreach($posts as $post) {
    $post_url = SITE_URL . '/' . $post['slug'] . '.html';
    $post_content = strip_tags($post['content']);
    $post_content = htmlspecialchars($post_content);
    
    $rss_content .= '<item>' . "\n";
    $rss_content .= '<title>' . htmlspecialchars($post['title']) . '</title>' . "\n";
    $rss_content .= '<link>' . $post_url . '</link>' . "\n";
    $rss_content .= '<guid isPermaLink="true">' . $post_url . '</guid>' . "\n";
    $rss_content .= '<description>' . $post_content . '</description>' . "\n";
    $rss_content .= '<pubDate>' . date('r', strtotime($post['published_at'])) . '</pubDate>' . "\n";
    $rss_content .= '<author>' . htmlspecialchars($post['display_name']) . '</author>' . "\n";
    
    if($post['category_name']) {
        $rss_content .= '<category>' . htmlspecialchars($post['category_name']) . '</category>' . "\n";
    }
    
    $rss_content .= '</item>' . "\n";
}

$rss_content .= '</channel>' . "\n";
$rss_content .= '</rss>';

echo $rss_content;
?>