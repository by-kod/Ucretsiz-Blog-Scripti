<?php
require_once '../includes/config.php';

// Giriş kontrolü
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    redirect('login.php');
}

$pageTitle = "SEO Yönetimi - " . SITE_NAME;

// SEO ayarlarını yükle
$seo_file = '../includes/seo_settings.json';
$default_seo = [
    'permalink_structure' => '/%postname%.html',
    'generate_sitemap' => true,
    'sitemap_frequency' => 'weekly',
    'sitemap_priority' => '0.8',
    'rss_enabled' => true,
    'rss_items' => 10,
    'meta_robots' => 'index, follow',
    'open_graph' => true,
    'twitter_cards' => true,
    'structured_data' => true,
    'auto_meta' => true,
    'canonical_urls' => true
];

if(file_exists($seo_file)) {
    $seo_settings = json_decode(file_get_contents($seo_file), true);
    $seo_settings = array_merge($default_seo, $seo_settings);
} else {
    $seo_settings = $default_seo;
    file_put_contents($seo_file, json_encode($seo_settings, JSON_PRETTY_PRINT));
}

// Form gönderimi
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Kalıcı bağlantı ayarları
    if(isset($_POST['save_permalinks'])) {
        $old_structure = $seo_settings['permalink_structure'];
        $seo_settings['permalink_structure'] = sanitize($_POST['permalink_structure']);
        
        // .htaccess'i güncelle
        if(updateHtaccess($seo_settings['permalink_structure'])) {
            $_SESSION['success_message'] = "Kalıcı bağlantı yapısı başarıyla güncellendi.";
            
            // Mevcut yazıların URL'lerini güncelle (opsiyonel)
            if($old_structure != $seo_settings['permalink_structure']) {
                updatePostUrls($seo_settings['permalink_structure']);
            }
        } else {
            $_SESSION['error_message'] = ".htaccess dosyası güncellenirken hata oluştu.";
        }
    }
    
    // Site Haritası Ayarları
    if(isset($_POST['save_sitemap'])) {
        $seo_settings['generate_sitemap'] = isset($_POST['generate_sitemap']);
        $seo_settings['sitemap_frequency'] = sanitize($_POST['sitemap_frequency']);
        $seo_settings['sitemap_priority'] = sanitize($_POST['sitemap_priority']);
        
        // Sitemap oluştur
        if($seo_settings['generate_sitemap']) {
            generateSitemap();
        }
        
        $_SESSION['success_message'] = "Site haritası ayarları kaydedildi.";
    }
    
    // RSS Ayarları
    if(isset($_POST['save_rss'])) {
        $seo_settings['rss_enabled'] = isset($_POST['rss_enabled']);
        $seo_settings['rss_items'] = (int)$_POST['rss_items'];
        
        $_SESSION['success_message'] = "RSS ayarları kaydedildi.";
    }
    
    // Meta Ayarları
    if(isset($_POST['save_meta'])) {
        $seo_settings['meta_robots'] = sanitize($_POST['meta_robots']);
        $seo_settings['open_graph'] = isset($_POST['open_graph']);
        $seo_settings['twitter_cards'] = isset($_POST['twitter_cards']);
        $seo_settings['structured_data'] = isset($_POST['structured_data']);
        $seo_settings['auto_meta'] = isset($_POST['auto_meta']);
        $seo_settings['canonical_urls'] = isset($_POST['canonical_urls']);
        
        $_SESSION['success_message'] = "Meta ayarları kaydedildi.";
    }
    
    // Ayarları kaydet
    file_put_contents($seo_file, json_encode($seo_settings, JSON_PRETTY_PRINT));
    redirect('seo.php');
}

// .htaccess güncelleme fonksiyonu
function updateHtaccess($permalink_structure) {
    $htaccess_file = '../.htaccess';
    
    if(!file_exists($htaccess_file)) {
        // .htaccess yoksa oluştur
        $base_content = "RewriteEngine On\n\n";
    } else {
        $base_content = file_get_contents($htaccess_file);
    }
    
    // Mevcut rewrite kurallarını temizle
    $base_content = preg_replace('/# SEO URL Rewrite.*?# END SEO URL Rewrite/s', '', $base_content);
    
    // Yeni rewrite kurallarını oluştur
    $rewrite_rules = generateRewriteRules($permalink_structure);
    
    $new_content = $base_content . $rewrite_rules;
    
    return file_put_contents($htaccess_file, $new_content);
}

// Rewrite kurallarını oluştur
function generateRewriteRules($structure) {
    $rules = "\n# SEO URL Rewrite - Generated: " . date('Y-m-d H:i:s') . "\n";
    
    if($structure == '/%postname%.html') {
        $rules .= "RewriteCond %{REQUEST_FILENAME} !-f\n";
        $rules .= "RewriteCond %{REQUEST_FILENAME} !-d\n";
        $rules .= "RewriteRule ^([a-z0-9-]+)\\.html$ single.php?slug=$1 [L,QSA]\n";
    }
    elseif($structure == '/%year%/%month%/%postname%') {
        $rules .= "RewriteCond %{REQUEST_FILENAME} !-f\n";
        $rules .= "RewriteCond %{REQUEST_FILENAME} !-d\n";
        $rules .= "RewriteRule ^([0-9]{4})/([0-9]{2})/([a-z0-9-]+)$ single.php?slug=$3 [L,QSA]\n";
    }
    elseif($structure == '/%category%/%postname%') {
        $rules .= "RewriteCond %{REQUEST_FILENAME} !-f\n";
        $rules .= "RewriteCond %{REQUEST_FILENAME} !-d\n";
        $rules .= "RewriteRule ^([a-z0-9-]+)/([a-z0-9-]+)$ single.php?slug=$2 [L,QSA]\n";
    }
    elseif($structure == '/archives/%post_id%') {
        $rules .= "RewriteCond %{REQUEST_FILENAME} !-f\n";
        $rules .= "RewriteCond %{REQUEST_FILENAME} !-d\n";
        $rules .= "RewriteRule ^archives/([0-9]+)$ single.php?id=$1 [L,QSA]\n";
    }
    
    // Kategori kuralları (sabit)
    $rules .= "RewriteRule ^kategori/([a-z0-9-]+)$ category.php?slug=$1 [L,QSA]\n";
    $rules .= "RewriteRule ^kategori/([a-z0-9-]+)/sayfa/([0-9]+)$ category.php?slug=$1&page=$2 [L,QSA]\n";
    
    // RSS kuralları
    $rules .= "RewriteRule ^feed/?$ rss.php [L,QSA]\n";
    $rules .= "RewriteRule ^rss/?$ rss.php [L,QSA]\n";
    
    // Site haritası kuralı
    $rules .= "RewriteRule ^sitemap\\.xml$ sitemap.php [L,QSA]\n";
    
    $rules .= "# END SEO URL Rewrite\n";
    
    return $rules;
}

// Yazı URL'lerini güncelleme fonksiyonu
function updatePostUrls($new_structure) {
    global $pdo;
    
    $posts = $pdo->query("SELECT id, slug, published_at FROM posts WHERE status = 'published'")->fetchAll();
    
    foreach($posts as $post) {
        $new_url = generatePermalink($post, $new_structure);
        // URL değişikliği log'u (gerçek uygulamada yönlendirme eklenmeli)
        error_log("URL Changed - Post ID: {$post['id']}, New URL: {$new_url}");
    }
}

// Permalink oluşturma
function generatePermalink($post, $structure) {
    $replacements = [
        '%year%' => date('Y', strtotime($post['published_at'])),
        '%month%' => date('m', strtotime($post['published_at'])),
        '%day%' => date('d', strtotime($post['published_at'])),
        '%postname%' => $post['slug'],
        '%post_id%' => $post['id'],
        '%category%' => 'uncategorized' // Basit implementasyon
    ];
    
    $permalink = $structure;
    foreach($replacements as $tag => $value) {
        $permalink = str_replace($tag, $value, $permalink);
    }
    
    return ltrim($permalink, '/');
}

// Site haritası oluşturma
function generateSitemap() {
    global $pdo, $seo_settings;
    
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
    $posts = $pdo->query("
        SELECT p.*, c.slug as category_slug 
        FROM posts p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.status = 'published' 
        ORDER BY p.published_at DESC
    ")->fetchAll();
    
    foreach($posts as $post) {
        $post_url = SITE_URL . '/' . generatePermalink($post, $seo_settings['permalink_structure']);
        $lastmod = date('Y-m-d', strtotime($post['updated_at'] ?: $post['published_at']));
        
        $sitemap_content .= "\t<url>\n";
        $sitemap_content .= "\t\t<loc>" . $post_url . "</loc>\n";
        $sitemap_content .= "\t\t<lastmod>" . $lastmod . "</lastmod>\n";
        $sitemap_content .= "\t\t<changefreq>" . $seo_settings['sitemap_frequency'] . "</changefreq>\n";
        $sitemap_content .= "\t\t<priority>" . $seo_settings['sitemap_priority'] . "</priority>\n";
        $sitemap_content .= "\t</url>\n";
    }
    
    // Kategoriler
    $categories = $pdo->query("SELECT * FROM categories")->fetchAll();
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
    
    return file_put_contents('../sitemap.xml', $sitemap_content);
}

// RSS oluşturma fonksiyonu
function generateRSS() {
    global $pdo;
    
    // RSS ayarlarını yükle
    $seo_file = '../includes/seo_settings.json';
    $rss_settings = [
        'rss_items' => 10,
        'rss_enabled' => true
    ];

    if(file_exists($seo_file)) {
        $seo_settings = json_decode(file_get_contents($seo_file), true);
        $rss_settings = array_merge($rss_settings, $seo_settings);
    }

    if(!$rss_settings['rss_enabled']) {
        return false;
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
        
        // İçeriği kısalt
        if(strlen($post_content) > 500) {
            $post_content = substr($post_content, 0, 500) . '...';
        }
        
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

    return file_put_contents('../rss.xml', $rss_content);
}

// Manuel site haritası oluşturma
if(isset($_GET['generate_sitemap'])) {
    if(generateSitemap()) {
        $_SESSION['success_message'] = "Site haritası başarıyla oluşturuldu.";
    } else {
        $_SESSION['error_message'] = "Site haritası oluşturulurken hata oluştu.";
    }
    redirect('seo.php');
}

// Manuel RSS oluşturma
if(isset($_GET['generate_rss'])) {
    if(generateRSS()) {
        $_SESSION['success_message'] = "RSS feed başarıyla oluşturuldu.";
    } else {
        $_SESSION['error_message'] = "RSS feed oluşturulurken hata oluştu.";
    }
    redirect('seo.php');
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #2c5aa0;
            --primary-purple: #6a4c93;
            --accent-teal: #00b4a0;
            --accent-orange: #ff6b35;
            --success-green: #28a745;
            --warning-orange: #fd7e14;
            --danger-red: #dc3545;
            --info-cyan: #17a2b8;
            --light-gray: #f8f9fa;
            --dark-gray: #343a40;
            --border-color: #dee2e6;
        }
        
        body {
            background-color: var(--light-gray);
            font-family: 'Inter', sans-serif;
        }
        
        .sidebar {
            background: white;
            min-height: 100vh;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            position: fixed;
            width: 250px;
            z-index: 1000;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        
        .navbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .sidebar-brand {
            padding: 1.5rem 1rem;
            border-bottom: 1px solid var(--border-color);
            text-align: center;
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
            color: white;
        }
        
        .sidebar-nav {
            padding: 1rem 0;
        }
        
        .nav-item {
            margin-bottom: 0.5rem;
        }
        
        .nav-link {
            color: var(--dark-gray);
            padding: 0.75rem 1.5rem;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
            text-decoration: none;
            display: block;
        }
        
        .nav-link:hover, .nav-link.active {
            color: var(--primary-blue);
            background: rgba(44, 90, 160, 0.1);
            border-left-color: var(--primary-blue);
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid var(--border-color);
            padding: 1.25rem;
            font-weight: 600;
            border-radius: 12px 12px 0 0 !important;
        }
        
        .permalink-option {
            border: 2px solid var(--border-color);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .permalink-option:hover {
            border-color: var(--primary-blue);
            background: rgba(44, 90, 160, 0.05);
        }
        
        .permalink-option.selected {
            border-color: var(--primary-blue);
            background: rgba(44, 90, 160, 0.1);
        }
        
        .permalink-preview {
            background: var(--light-gray);
            border-radius: 6px;
            padding: 1rem;
            margin-top: 1rem;
            font-family: monospace;
            border-left: 4px solid var(--accent-teal);
        }
        
        .seo-status-card {
            text-align: center;
            padding: 1.5rem;
            border-radius: 12px;
        }
        
        .status-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .status-good {
            color: var(--success-green);
        }
        
        .status-warning {
            color: var(--warning-orange);
        }
        
        .status-error {
            color: var(--danger-red);
        }
        
        .quick-action-card {
            text-align: center;
            padding: 1.5rem;
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
        }
        
        .quick-action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
            border: none;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(44, 90, 160, 0.4);
        }
        
        .btn-success {
            background: linear-gradient(135deg, var(--success-green), var(--accent-teal));
            border: none;
        }
        
        .btn-info {
            background: linear-gradient(135deg, var(--info-cyan), var(--primary-blue));
            border: none;
        }
        
        .badge-primary {
            background: var(--primary-blue);
        }
        
        .badge-success {
            background: var(--success-green);
        }
        
        .form-check-input:checked {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
        }
        
        .form-switch .form-check-input:checked {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <h4><i class="fas fa-cog me-2"></i>Admin Panel</h4>
            <small><?php echo SITE_NAME; ?></small>
        </div>
        
        <div class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">
                        <i class="fas fa-tachometer-alt"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="posts.php">
                        <i class="fas fa-newspaper"></i>Yazılar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="categories.php">
                        <i class="fas fa-folder"></i>Kategoriler
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="tags.php">
                        <i class="fas fa-tags"></i>Etiketler
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="comments.php">
                        <i class="fas fa-comments"></i>Yorumlar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="media.php">
                        <i class="fas fa-images"></i>Medya
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="analytics.php">
                        <i class="fas fa-chart-bar"></i>Analitik
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="seo.php">
                        <i class="fas fa-search"></i>SEO
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <a class="nav-link" href="../index.php" target="_blank">
                        <i class="fas fa-external-link-alt"></i>Siteyi Görüntüle
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-danger" href="logout.php">
                        <i class="fas fa-sign-out-alt"></i>Çıkış Yap
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light mb-4">
            <div class="container-fluid">
                <div class="navbar-nav">
                    <span class="navbar-text">
                        Hoş geldiniz, <strong><?php echo $_SESSION['admin_username']; ?></strong>
                        <span class="badge bg-primary ms-2"><?php echo $_SESSION['admin_role']; ?></span>
                    </span>
                </div>
                <div class="navbar-nav ms-auto">
                    <span class="navbar-text">
                        <i class="far fa-clock me-1"></i>
                        <?php echo date('d M Y H:i'); ?>
                    </span>
                </div>
            </div>
        </nav>

        <!-- Başlık -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">SEO Yönetimi</h1>
            <div class="btn-group">
                <a href="?generate_sitemap" class="btn btn-success">
                    <i class="fas fa-sitemap me-1"></i>Site Haritası Oluştur
                </a>
                <a href="?generate_rss" class="btn btn-info">
                    <i class="fas fa-rss me-1"></i>RSS Feed Oluştur
                </a>
            </div>
        </div>

        <!-- Mesajlar -->
        <?php if(isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Hızlı Erişim Kartları -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card quick-action-card">
                    <div class="status-icon status-good">
                        <i class="fas fa-sitemap"></i>
                    </div>
                    <h5>Site Haritası</h5>
                    <p class="text-muted small">Arama motorları için optimize edilmiş</p>
                    <a href="../sitemap.xml" target="_blank" class="btn btn-outline-primary btn-sm">
                        Görüntüle
                    </a>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card quick-action-card">
                    <div class="status-icon status-good">
                        <i class="fas fa-rss"></i>
                    </div>
                    <h5>RSS Feed</h5>
                    <p class="text-muted small">İçerik beslemesi</p>
                    <a href="../rss.xml" target="_blank" class="btn btn-outline-primary btn-sm">
                        Görüntüle
                    </a>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card quick-action-card">
                    <div class="status-icon status-good">
                        <i class="fas fa-link"></i>
                    </div>
                    <h5>Kalıcı Bağlantılar</h5>
                    <p class="text-muted small">SEO dostu URL'ler</p>
                    <span class="badge bg-success">Aktif</span>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card quick-action-card">
                    <div class="status-icon status-good">
                        <i class="fas fa-robot"></i>
                    </div>
                    <h5>Meta Etiketler</h5>
                    <p class="text-muted small">Otomatik oluşturuluyor</p>
                    <span class="badge bg-success">Aktif</span>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Kalıcı Bağlantı Ayarları -->
            <div class="col-lg-8">
                <form method="POST">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-link me-2"></i>Kalıcı Bağlantı Ayarları
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <h6>Yapı Seçenekleri:</h6>
                                
                                <div class="permalink-option <?php echo $seo_settings['permalink_structure'] == '/%postname%.html' ? 'selected' : ''; ?>" 
                                     onclick="selectPermalink('/%postname%.html')">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="permalink_structure" 
                                               value="/%postname%.html" id="option1" 
                                               <?php echo $seo_settings['permalink_structure'] == '/%postname%.html' ? 'checked' : ''; ?>>
                                        <label class="form-check-label fw-bold" for="option1">
                                            Yazı İsmi
                                        </label>
                                    </div>
                                    <div class="text-muted small ms-4">
                                        <code><?php echo SITE_URL; ?>/<strong>yazi-basligi</strong>.html</code>
                                    </div>
                                </div>
                                
                                <div class="permalink-option <?php echo $seo_settings['permalink_structure'] == '/%year%/%month%/%postname%' ? 'selected' : ''; ?>" 
                                     onclick="selectPermalink('/%year%/%month%/%postname%')">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="permalink_structure" 
                                               value="/%year%/%month%/%postname%" id="option2"
                                               <?php echo $seo_settings['permalink_structure'] == '/%year%/%month%/%postname%' ? 'checked' : ''; ?>>
                                        <label class="form-check-label fw-bold" for="option2">
                                            Gün ve isim
                                        </label>
                                    </div>
                                    <div class="text-muted small ms-4">
                                        <code><?php echo SITE_URL; ?>/<strong>2024</strong>/<strong>01</strong>/yazi-basligi</code>
                                    </div>
                                </div>
                                
                                <div class="permalink-option <?php echo $seo_settings['permalink_structure'] == '/%category%/%postname%' ? 'selected' : ''; ?>" 
                                     onclick="selectPermalink('/%category%/%postname%')">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="permalink_structure" 
                                               value="/%category%/%postname%" id="option3"
                                               <?php echo $seo_settings['permalink_structure'] == '/%category%/%postname%' ? 'checked' : ''; ?>>
                                        <label class="form-check-label fw-bold" for="option3">
                                            Kategori ve isim
                                        </label>
                                    </div>
                                    <div class="text-muted small ms-4">
                                        <code><?php echo SITE_URL; ?>/<strong>teknoloji</strong>/yazi-basligi</code>
                                    </div>
                                </div>
                                
                                <div class="permalink-option <?php echo $seo_settings['permalink_structure'] == '/archives/%post_id%' ? 'selected' : ''; ?>" 
                                     onclick="selectPermalink('/archives/%post_id%')">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="permalink_structure" 
                                               value="/archives/%post_id%" id="option4"
                                               <?php echo $seo_settings['permalink_structure'] == '/archives/%post_id%' ? 'checked' : ''; ?>>
                                        <label class="form-check-label fw-bold" for="option4">
                                            Yazı numarası
                                        </label>
                                    </div>
                                    <div class="text-muted small ms-4">
                                        <code><?php echo SITE_URL; ?>/archives/<strong>123</strong></code>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="permalink-preview">
                                <strong>Önizleme:</strong><br>
                                <span id="previewUrl"><?php echo SITE_URL; ?>/ornek-yazi-basligi</span>
                            </div>
                            
                            <div class="alert alert-warning mt-3">
                                <h6><i class="fas fa-exclamation-triangle me-2"></i>Önemli</h6>
                                <p class="mb-0">
                                    Kalıcı bağlantı yapısını değiştirdiğinizde, mevcut URL'ler değişecektir. 
                                    Eski URL'lere gelen ziyaretçiler için 301 yönlendirmeleri eklemeniz önerilir.
                                </p>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <button type="submit" name="save_permalinks" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Kalıcı Bağlantıları Güncelle
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Site Haritası Ayarları -->
                <form method="POST" class="mt-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-sitemap me-2"></i>Site Haritası Ayarları
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="generate_sitemap" 
                                           name="generate_sitemap" <?php echo $seo_settings['generate_sitemap'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label fw-bold" for="generate_sitemap">
                                        Otomatik Site Haritası Oluştur
                                    </label>
                                    <div class="form-text">Yeni yazı eklendiğinde site haritasını otomatik günceller</div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="sitemap_frequency" class="form-label">Güncelleme Sıklığı</label>
                                    <select class="form-select" id="sitemap_frequency" name="sitemap_frequency">
                                        <option value="always" <?php echo $seo_settings['sitemap_frequency'] == 'always' ? 'selected' : ''; ?>>Her zaman</option>
                                        <option value="hourly" <?php echo $seo_settings['sitemap_frequency'] == 'hourly' ? 'selected' : ''; ?>>Saatlik</option>
                                        <option value="daily" <?php echo $seo_settings['sitemap_frequency'] == 'daily' ? 'selected' : ''; ?>>Günlük</option>
                                        <option value="weekly" <?php echo $seo_settings['sitemap_frequency'] == 'weekly' ? 'selected' : ''; ?>>Haftalık</option>
                                        <option value="monthly" <?php echo $seo_settings['sitemap_frequency'] == 'monthly' ? 'selected' : ''; ?>>Aylık</option>
                                        <option value="yearly" <?php echo $seo_settings['sitemap_frequency'] == 'yearly' ? 'selected' : ''; ?>>Yıllık</option>
                                        <option value="never" <?php echo $seo_settings['sitemap_frequency'] == 'never' ? 'selected' : ''; ?>>Asla</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="sitemap_priority" class="form-label">Öncelik</label>
                                    <select class="form-select" id="sitemap_priority" name="sitemap_priority">
                                        <option value="1.0" <?php echo $seo_settings['sitemap_priority'] == '1.0' ? 'selected' : ''; ?>>1.0 - En yüksek</option>
                                        <option value="0.9" <?php echo $seo_settings['sitemap_priority'] == '0.9' ? 'selected' : ''; ?>>0.9</option>
                                        <option value="0.8" <?php echo $seo_settings['sitemap_priority'] == '0.8' ? 'selected' : ''; ?>>0.8</option>
                                        <option value="0.7" <?php echo $seo_settings['sitemap_priority'] == '0.7' ? 'selected' : ''; ?>>0.7</option>
                                        <option value="0.6" <?php echo $seo_settings['sitemap_priority'] == '0.6' ? 'selected' : ''; ?>>0.6</option>
                                        <option value="0.5" <?php echo $seo_settings['sitemap_priority'] == '0.5' ? 'selected' : ''; ?>>0.5</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <a href="../sitemap.xml" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-external-link-alt me-1"></i>Site Haritasını Görüntüle
                                </a>
                                <a href="?generate_sitemap" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-sync-alt me-1"></i>Şimdi Oluştur
                                </a>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <button type="submit" name="save_sitemap" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Site Haritası Ayarlarını Kaydet
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Yan Menü -->
            <div class="col-lg-4">
                <!-- RSS Ayarları -->
                <form method="POST">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-rss me-2"></i>RSS Feed Ayarları
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="rss_enabled" 
                                           name="rss_enabled" <?php echo $seo_settings['rss_enabled'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label fw-bold" for="rss_enabled">
                                        RSS Feed Aktif
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="rss_items" class="form-label">Gösterilecek Öğe Sayısı</label>
                                <input type="number" class="form-control" id="rss_items" name="rss_items" 
                                       value="<?php echo $seo_settings['rss_items']; ?>" min="1" max="50">
                            </div>
                            
                            <div class="mt-3">
                                <a href="../rss.xml" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-external-link-alt me-1"></i>RSS Feed'i Görüntüle
                                </a>
                                <a href="?generate_rss" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-sync-alt me-1"></i>Şimdi Oluştur
                                </a>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <button type="submit" name="save_rss" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>RSS Ayarlarını Kaydet
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Meta Ayarları -->
                <form method="POST" class="mt-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-tags me-2"></i>Meta Ayarları
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="meta_robots" class="form-label">Robots Meta</label>
                                <select class="form-select" id="meta_robots" name="meta_robots">
                                    <option value="index, follow" <?php echo $seo_settings['meta_robots'] == 'index, follow' ? 'selected' : ''; ?>>index, follow</option>
                                    <option value="index, nofollow" <?php echo $seo_settings['meta_robots'] == 'index, nofollow' ? 'selected' : ''; ?>>index, nofollow</option>
                                    <option value="noindex, follow" <?php echo $seo_settings['meta_robots'] == 'noindex, follow' ? 'selected' : ''; ?>>noindex, follow</option>
                                    <option value="noindex, nofollow" <?php echo $seo_settings['meta_robots'] == 'noindex, nofollow' ? 'selected' : ''; ?>>noindex, nofollow</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="open_graph" 
                                           name="open_graph" <?php echo $seo_settings['open_graph'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="open_graph">
                                        Open Graph Meta
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="twitter_cards" 
                                           name="twitter_cards" <?php echo $seo_settings['twitter_cards'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="twitter_cards">
                                        Twitter Cards
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="structured_data" 
                                           name="structured_data" <?php echo $seo_settings['structured_data'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="structured_data">
                                        Yapılandırılmış Veri
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="auto_meta" 
                                           name="auto_meta" <?php echo $seo_settings['auto_meta'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="auto_meta">
                                        Otomatik Meta Oluştur
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="canonical_urls" 
                                           name="canonical_urls" <?php echo $seo_settings['canonical_urls'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="canonical_urls">
                                        Canonical URL'ler
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <button type="submit" name="save_meta" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Meta Ayarlarını Kaydet
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Kalıcı bağlantı seçimi
        function selectPermalink(structure) {
            document.querySelectorAll('.permalink-option').forEach(option => {
                option.classList.remove('selected');
            });
            
            event.currentTarget.classList.add('selected');
            event.currentTarget.querySelector('input[type="radio"]').checked = true;
            
            // Önizlemeyi güncelle
            updatePreview(structure);
        }
        
        // Önizleme güncelleme
        function updatePreview(structure) {
            const preview = document.getElementById('previewUrl');
            let exampleUrl = '<?php echo SITE_URL; ?>';
            
            if(structure == '/%postname%.html') {
                exampleUrl += '/ornek-yazi-basligi.html';
            } else if(structure == '/%year%/%month%/%postname%') {
                exampleUrl += '/2024/01/ornek-yazi-basligi';
            } else if(structure == '/%category%/%postname%') {
                exampleUrl += '/teknoloji/ornek-yazi-basligi';
            } else if(structure == '/archives/%post_id%') {
                exampleUrl += '/archives/123';
            }
            
            preview.textContent = exampleUrl;
        }
        
        // Sayfa yüklendiğinde önizlemeyi ayarla
        document.addEventListener('DOMContentLoaded', function() {
            const selectedStructure = document.querySelector('input[name="permalink_structure"]:checked').value;
            updatePreview(selectedStructure);
        });
        
        // Form submit animasyonu
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const submitBtn = this.querySelector('button[type="submit"]');
                if(submitBtn) {
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Kaydediliyor...';
                    submitBtn.disabled = true;
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>