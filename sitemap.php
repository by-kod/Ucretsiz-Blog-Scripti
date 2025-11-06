<?php
require_once 'includes/config.php';

header('Content-Type: application/xml; charset=utf-8');

// SEO ayarlarını yükle
$seo_file = 'includes/seo_settings.json';
$sitemap_settings = [
    'sitemap_frequency' => 'weekly',
    'sitemap_priority' => '0.8'
];

if(file_exists($seo_file)) {
    $seo_settings = json_decode(file_get_contents($seo_file), true);
    $sitemap_settings = array_merge($sitemap_settings, $seo_settings);
}

// Yazıları getir
$posts = $pdo->query("
    SELECT p.*, c.slug as category_slug 
    FROM posts p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.status = 'published' 
    ORDER BY p.published_at DESC
")->fetchAll();

// Kategorileri getir
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

// Site haritası oluştur
$sitemap_content = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$sitemap_content .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// Ana sayfa
$sitemap_content .= "\t<url>\n";
$sitemap_content .= "\t\t<loc>" . SITE_URL . "/</loc>\n";
$sitemap_content .= "\t\t<lastmod>" . date('Y-m-d') . "</lastmod>\n";
$sitemap_content .= "\t\t<changefreq>daily</changefreq>\n";
$sitemap_content .= "\t\t<priority>1.0</priority>\n";
$sitemap_content .= "\t</url>\n";

// Yazılar
foreach($posts as $post) {
    $post_url = SITE_URL . '/' . $post['slug'] . '.html';
    $lastmod = date('Y-m-d', strtotime($post['updated_at'] ?: $post['published_at']));
    
    $sitemap_content .= "\t<url>\n";
    $sitemap_content .= "\t\t<loc>" . $post_url . "</loc>\n";
    $sitemap_content .= "\t\t<lastmod>" . $lastmod . "</lastmod>\n";
    $sitemap_content .= "\t\t<changefreq>" . $sitemap_settings['sitemap_frequency'] . "</changefreq>\n";
    $sitemap_content .= "\t\t<priority>" . $sitemap_settings['sitemap_priority'] . "</priority>\n";
    $sitemap_content .= "\t</url>\n";
}

// Kategoriler
foreach($categories as $category) {
    $cat_url = SITE_URL . '/kategori/' . $category['slug'];
    
    $sitemap_content .= "\t<url>\n";
    $sitemap_content .= "\t\t<loc>" . $cat_url . "</loc>\n";
    $sitemap_content .= "\t\t<lastmod>" . date('Y-m-d') . "</lastmod>\n";
    $sitemap_content .= "\t\t<changefreq>weekly</changefreq>\n";
    $sitemap_content .= "\t\t<priority>0.7</priority>\n";
    $sitemap_content .= "\t</url>\n";
}

$sitemap_content .= '</urlset>';

echo $sitemap_content;
?>