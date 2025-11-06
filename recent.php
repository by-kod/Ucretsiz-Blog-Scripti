<?php
require_once 'includes/config.php';

$pageTitle = "Son Yazılar - " . SITE_NAME;
$pageDescription = "En yeni ve güncel yazılarımızı keşfedin";
$pageKeywords = "son yazılar, yeni içerikler, güncel makaleler, en son eklenen";

// Filtreleme parametreleri
$category_slug = isset($_GET['category']) ? sanitize($_GET['category']) : '';
$time_period = isset($_GET['period']) ? sanitize($_GET['period']) : 'all';

// Zaman periyodu için SQL koşulu
$time_conditions = [];
switch($time_period) {
    case 'today':
        $time_conditions[] = "p.created_at >= CURDATE()";
        break;
    case 'week':
        $time_conditions[] = "p.created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        break;
    case 'month':
        $time_conditions[] = "p.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        break;
    default:
        // Tüm zamanlar - herhangi bir koşul ekleme
        break;
}

// SQL koşulları
$where_conditions = ["p.status = 'published'"];
$params = [];

// Zaman filtreleme
if (!empty($time_conditions)) {
    $where_conditions = array_merge($where_conditions, $time_conditions);
}

// Kategori filtreleme
if ($category_slug) {
    $where_conditions[] = "c.slug = ?";
    $params[] = $category_slug;
}

$where_sql = $where_conditions ? "WHERE " . implode(" AND ", $where_conditions) : '';

// Sayfalama
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = 12;
$offset = ($page - 1) * $per_page;

// Toplam yazı sayısı
$count_sql = "SELECT COUNT(*) as total FROM posts p 
              LEFT JOIN categories c ON p.category_id = c.id 
              $where_sql";
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total_posts = $count_stmt->fetch()['total'];
$total_pages = ceil($total_posts / $per_page);

// Son yazıları getir
$recent_sql = "
    SELECT p.*, c.name as category_name, c.slug as category_slug, 
           u.username as author_name,
           (SELECT COUNT(*) FROM comments WHERE post_id = p.id AND status = 'approved') as comment_count
    FROM posts p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN users u ON p.author_id = u.id
    $where_sql
    ORDER BY p.created_at DESC
    LIMIT $offset, $per_page
";

$recent_stmt = $pdo->prepare($recent_sql);
$recent_stmt->execute($params);
$recent_posts = $recent_stmt->fetchAll();

// Kategorileri getir
$categories = $pdo->query("SELECT id, name, slug FROM categories ORDER BY name")->fetchAll();

// İstatistikler
$stats = $pdo->query("
    SELECT 
        (SELECT COUNT(*) FROM posts WHERE status = 'published') as total_posts,
        (SELECT COUNT(*) FROM comments WHERE status = 'approved') as total_comments,
        (SELECT COUNT(*) FROM posts WHERE status = 'published' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)) as last_week_posts,
        (SELECT COUNT(*) FROM posts WHERE status = 'published' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)) as last_month_posts
")->fetch();

// Zaman periyodlarına göre istatistikler
$period_stats = [
    'today' => $pdo->query("SELECT COUNT(*) as count FROM posts WHERE status = 'published' AND created_at >= CURDATE()")->fetch(),
    'week' => $pdo->query("SELECT COUNT(*) as count FROM posts WHERE status = 'published' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)")->fetch(),
    'month' => $pdo->query("SELECT COUNT(*) as count FROM posts WHERE status = 'published' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)")->fetch()
];

// Mevcut kategori bilgisini al
$current_category_name = '';
if ($category_slug) {
    $cat_stmt = $pdo->prepare("SELECT name FROM categories WHERE slug = ?");
    $cat_stmt->execute([$category_slug]);
    $current_category = $cat_stmt->fetch();
    $current_category_name = $current_category ? $current_category['name'] : '';
}

// Yeni yazıları kontrol et (24 saat içinde)
$new_posts_count = $pdo->query("SELECT COUNT(*) as count FROM posts WHERE status = 'published' AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)")->fetch()['count'];

// Header dosyasını include et
include 'themes/google-modern/header.php';
?>

<div class="container mt-4">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <h1 class="hero-title fade-in-up">Son Yazılar</h1>
            <p class="hero-subtitle fade-in-up">En yeni ve güncel içeriklerimizi keşfedin</p>
            <div class="hero-stats fade-in-up" style="display: flex; gap: 2rem; justify-content: center; font-size: 1.1rem;">
                <div><i class="fas fa-newspaper me-1"></i> <?php echo $stats['total_posts']; ?> Toplam Yazı</div>
                <div><i class="fas fa-clock me-1"></i> <?php echo $stats['last_week_posts']; ?> Son 7 Gün</div>
                <div><i class="fas fa-bolt me-1"></i> <?php echo $new_posts_count; ?> Yeni (24s)</div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Sol Sidebar - Filtreler -->
        <div class="col-lg-3">
            <div class="sticky-top" style="top: 100px;">
                <!-- Zaman Filtresi -->
                <div class="card mb-4">
                    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Zaman Aralığı</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <a href="recent.php<?php echo $category_slug ? '?category=' . $category_slug : ''; ?>" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo $time_period == 'all' ? 'active' : ''; ?>">
                                <span>Tüm Zamanlar</span>
                                <span class="badge rounded-pill" style="background: #667eea;"><?php echo $stats['total_posts']; ?></span>
                            </a>
                            <a href="recent.php?period=today<?php echo $category_slug ? '&category=' . $category_slug : ''; ?>" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo $time_period == 'today' ? 'active' : ''; ?>">
                                <span>Bugün</span>
                                <span class="badge rounded-pill" style="background: #28a745;"><?php echo $period_stats['today']['count'] ?? 0; ?></span>
                            </a>
                            <a href="recent.php?period=week<?php echo $category_slug ? '&category=' . $category_slug : ''; ?>" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo $time_period == 'week' ? 'active' : ''; ?>">
                                <span>Bu Hafta</span>
                                <span class="badge rounded-pill" style="background: #17a2b8;"><?php echo $period_stats['week']['count'] ?? 0; ?></span>
                            </a>
                            <a href="recent.php?period=month<?php echo $category_slug ? '&category=' . $category_slug : ''; ?>" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo $time_period == 'month' ? 'active' : ''; ?>">
                                <span>Bu Ay</span>
                                <span class="badge rounded-pill" style="background: #ffc107; color: #212529;"><?php echo $period_stats['month']['count'] ?? 0; ?></span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Kategori Filtresi -->
                <div class="card mb-4">
                    <div class="card-header" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white;">
                        <h6 class="mb-0"><i class="fas fa-folder me-2"></i>Kategoriler</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <a href="recent.php<?php echo $time_period != 'all' ? '?period=' . $time_period : ''; ?>" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo !$category_slug ? 'active' : ''; ?>">
                                Tüm Kategoriler
                                <span class="badge rounded-pill" style="background: #667eea;"><?php echo $stats['total_posts']; ?></span>
                            </a>
                            <?php foreach($categories as $category): ?>
                                <?php
                                // Kategoriye ait son yazı sayısını al (zaman filtresine göre)
                                $cat_count_sql = "SELECT COUNT(*) FROM posts p 
                                                LEFT JOIN categories c ON p.category_id = c.id 
                                                WHERE c.slug = ? AND p.status = 'published'";
                                
                                if ($time_period == 'today') {
                                    $cat_count_sql .= " AND p.created_at >= CURDATE()";
                                } elseif ($time_period == 'week') {
                                    $cat_count_sql .= " AND p.created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                                } elseif ($time_period == 'month') {
                                    $cat_count_sql .= " AND p.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
                                }
                                
                                $cat_count = $pdo->prepare($cat_count_sql);
                                $cat_count->execute([$category['slug']]);
                                $post_count = $cat_count->fetchColumn();
                                ?>
                                <a href="recent.php?category=<?php echo $category['slug']; ?><?php echo $time_period != 'all' ? '&period=' . $time_period : ''; ?>" 
                                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo $category_slug == $category['slug'] ? 'active' : ''; ?>">
                                    <?php echo sanitize($category['name']); ?>
                                    <span class="badge rounded-pill" style="background: #6c757d;"><?php echo $post_count; ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Son Eklenenler -->
                <div class="card">
                    <div class="card-header" style="background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%); color: white;">
                        <h6 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Son Eklenenler</h6>
                    </div>
                    <div class="card-body">
                        <?php
                        $latest_posts = $pdo->query("
                            SELECT p.title, p.slug, p.created_at 
                            FROM posts p 
                            WHERE p.status = 'published' 
                            ORDER BY p.created_at DESC 
                            LIMIT 5
                        ")->fetchAll();
                        ?>
                        <div class="list-group list-group-flush">
                            <?php foreach($latest_posts as $latest_post): ?>
                                <a href="<?php echo $latest_post['slug']; ?>.html" 
                                   class="list-group-item list-group-item-action border-0 px-0 py-2">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1 small"><?php echo truncate(sanitize($latest_post['title']), 50); ?></h6>
                                    </div>
                                    <small class="text-muted">
                                        <i class="far fa-clock me-1"></i>
                                        <?php echo time_ago($latest_post['created_at']); ?>
                                    </small>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- İstatistikler -->
                <div class="card mt-4">
                    <div class="card-header" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%); color: #212529;">
                        <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>İstatistikler</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted">Son 24 Saat</small>
                            <div class="fw-bold" style="color: #667eea;"><?php echo $new_posts_count; ?> yeni yazı</div>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Son 7 Gün</small>
                            <div class="fw-bold" style="color: #28a745;"><?php echo $stats['last_week_posts']; ?> yazı</div>
                        </div>
                        <div>
                            <small class="text-muted">Son 30 Gün</small>
                            <div class="fw-bold" style="color: #17a2b8;"><?php echo $stats['last_month_posts']; ?> yazı</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ana İçerik -->
        <div class="col-lg-9">
            <!-- Filtre Bilgisi -->
            <div class="archive-stats mb-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="h4 mb-2">
                            <i class="fas fa-clock me-2" style="color: #667eea;"></i>
                            <?php
                            $period_labels = [
                                'all' => 'Tüm Yazılar',
                                'today' => 'Bugünkü Yazılar',
                                'week' => 'Bu Haftaki Yazılar',
                                'month' => 'Bu Aydaki Yazılar'
                            ];
                            
                            echo $period_labels[$time_period];
                            
                            if ($current_category_name) {
                                echo " - " . $current_category_name;
                            }
                            ?>
                        </h2>
                        <p class="text-muted mb-0">
                            <?php echo $total_posts; ?> yazı bulundu
                            <?php if ($time_period != 'all'): ?>
                                - <strong><?php echo $period_labels[$time_period]; ?></strong> gösteriliyor
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="btn-group">
                            <span class="btn btn-outline-primary disabled">
                                <i class="fas fa-sort-amount-down-alt me-1"></i> Yeniden Eskiye
                            </span>
                            <a href="popular.php" class="btn btn-outline-secondary">
                                <i class="fas fa-fire me-1"></i> Popüler
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aktif Filtreler -->
            <?php if ($time_period != 'all' || $category_slug): ?>
            <div class="mb-4">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="text-muted">Aktif filtreler:</span>
                    <?php if ($time_period != 'all'): ?>
                        <span class="badge" style="background: #667eea;">
                            Zaman: <?php echo $period_labels[$time_period]; ?>
                            <a href="recent.php<?php echo $category_slug ? '?category=' . $category_slug : ''; ?>" class="text-white ms-2">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    <?php endif; ?>
                    <?php if ($category_slug): ?>
                        <span class="badge" style="background: #28a745;">
                            Kategori: <?php echo $current_category_name ?: ''; ?>
                            <a href="recent.php<?php echo $time_period != 'all' ? '?period=' . $time_period : ''; ?>" class="text-white ms-2">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    <?php endif; ?>
                    <?php if ($time_period != 'all' || $category_slug): ?>
                        <a href="recent.php" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-times"></i> Tümünü Temizle
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Son Yazılar Grid -->
            <?php if(empty($recent_posts)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-clock fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Henüz yazı bulunmuyor</h4>
                    <p class="text-muted">Seçtiğiniz kriterlere uygun yazı bulunamadı.</p>
                    <a href="recent.php" class="btn btn-primary" style="background: #667eea; border-color: #667eea;">Tüm Yazıları Görüntüle</a>
                </div>
            <?php else: ?>
                <div class="blog-grid">
                    <?php foreach($recent_posts as $index => $post): ?>
                        <div class="blog-card fade-in-up">
                            <!-- Yeni Yazı İndikatörü -->
                            <?php if (is_new_post($post['created_at'])): ?>
                                <div class="position-absolute top-0 end-0 m-3">
                                    <span class="badge rounded-pill" style="background: #28a745;">
                                        <i class="fas fa-star me-1"></i>YENİ
                                    </span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if($post['featured_image']): ?>
                                <div class="blog-card-image">
                                    <img src="<?php echo $post['featured_image']; ?>" alt="<?php echo sanitize($post['title']); ?>">
                                    <!-- Zaman İndikatörü -->
                                    <div class="position-absolute bottom-0 start-0 m-2">
                                        <span class="badge bg-dark bg-opacity-75 text-white">
                                            <i class="far fa-clock me-1"></i>
                                            <?php echo time_ago($post['created_at']); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="blog-card-image bg-light d-flex align-items-center justify-content-center position-relative">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                    <!-- Zaman İndikatörü -->
                                    <div class="position-absolute bottom-0 start-0 m-2">
                                        <span class="badge bg-dark bg-opacity-75 text-white">
                                            <i class="far fa-clock me-1"></i>
                                            <?php echo time_ago($post['created_at']); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="blog-card-content">
                                <div class="blog-card-meta">
                                    <a href="recent.php?category=<?php echo $post['category_slug']; ?><?php echo $time_period != 'all' ? '&period=' . $time_period : ''; ?>" 
                                       class="category-badge" style="background: #667eea;">
                                        <?php echo sanitize($post['category_name']); ?>
                                    </a>
                                    <span class="post-date">
                                        <?php echo date('d M Y', strtotime($post['created_at'])); ?>
                                    </span>
                                </div>
                                
                                <h3 class="blog-card-title">
                                    <a href="<?php echo $post['slug']; ?>.html">
                                        <?php echo sanitize($post['title']); ?>
                                    </a>
                                </h3>
                                
                                <p class="blog-card-excerpt">
                                    <?php 
                                    $excerpt = $post['excerpt'] ?: strip_tags($post['content']);
                                    echo truncate($excerpt, 120);
                                    ?>
                                </p>
                                
                                <!-- Yazı Yaşı İndikatörü -->
                                <div class="post-age-indicator mb-3">
                                    <div class="d-flex justify-content-between align-items-center small text-muted">
                                        <span>Yayınlandı:</span>
                                        <span class="fw-bold <?php echo get_post_age_class($post['created_at']); ?>">
                                            <?php echo time_ago($post['created_at']); ?> önce
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="blog-card-footer">
                                    <div class="author-info">
                                        <i class="fas fa-user"></i>
                                        <span><?php echo sanitize($post['author_name']); ?></span>
                                    </div>
                                    <div class="post-stats">
                                        <div class="stat-item" title="Yorum Sayısı">
                                            <i class="fas fa-comments"></i>
                                            <span><?php echo $post['comment_count']; ?></span>
                                        </div>
                                        <div class="stat-item" title="Yazı Yaşı">
                                            <i class="far fa-calendar"></i>
                                            <span><?php echo date('d/m', strtotime($post['created_at'])); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Sayfalama -->
                <?php if($total_pages > 1): ?>
                <nav aria-label="Sayfalama" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" style="color: #667eea;">
                                    <i class="fas fa-chevron-left"></i> Önceki
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" 
                                   style="<?php echo $i == $page ? 'background: #667eea; border-color: #667eea;' : 'color: #667eea;'; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" style="color: #667eea;">
                                    Sonraki <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Footer dosyasını include et
include 'themes/google-modern/footer.php';
?>

<style>
    /* Özel renk tanımlamaları */
    .btn-primary {
        background: #667eea;
        border-color: #667eea;
    }
    
    .btn-primary:hover {
        background: #5a6fd8;
        border-color: #5a6fd8;
    }
    
    .page-item.active .page-link {
        background: #667eea;
        border-color: #667eea;
    }
    
    .page-link {
        color: #667eea;
    }
    
    .category-badge {
        background: #667eea;
    }
    
    .category-badge:hover {
        background: #5a6fd8;
    }
</style>

<script>
    // Yeni yazıları vurgula
    document.addEventListener('DOMContentLoaded', function() {
        const recentCards = document.querySelectorAll('.blog-card');
        recentCards.forEach((card) => {
            const newBadge = card.querySelector('.badge');
            if (newBadge && newBadge.style.backgroundColor === 'rgb(40, 167, 69)') {
                card.style.border = '2px solid #28a745';
                card.style.boxShadow = '0 8px 30px rgba(40, 167, 69, 0.3)';
            }
            
            // Hover efekti
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) scale(1.02)';
                this.style.boxShadow = '0 15px 40px rgba(0,0,0,0.15)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
                const newBadge = this.querySelector('.badge');
                if (newBadge && newBadge.style.backgroundColor === 'rgb(40, 167, 69)') {
                    this.style.boxShadow = '0 8px 30px rgba(40, 167, 69, 0.3)';
                } else {
                    this.style.boxShadow = '0 2px 10px rgba(0,0,0,0.08)';
                }
            });
        });
        
        // Canlı zaman güncellemesi
        function updateTimeAgo() {
            document.querySelectorAll('.time-ago').forEach(element => {
                const timestamp = element.getAttribute('data-timestamp');
                if (timestamp) {
                    element.textContent = getTimeAgo(new Date(timestamp * 1000));
                }
            });
        }
        
        // Her dakika zamanı güncelle
        setInterval(updateTimeAgo, 60000);
    });
</script>

<?php
// URL'den parametre kaldırma fonksiyonu
function removeParam($params) {
    $current_params = $_GET;
    
    if (is_array($params)) {
        foreach ($params as $param) {
            unset($current_params[$param]);
        }
    } else {
        unset($current_params[$params]);
    }
    
    return 'recent.php?' . http_build_query($current_params);
}

// Metin kısaltma fonksiyonu
function truncate($text, $length) {
    if (mb_strlen($text) > $length) {
        $text = mb_substr($text, 0, $length);
        $text = mb_substr($text, 0, mb_strrpos($text, ' '));
        $text .= '...';
    }
    return $text;
}

// Zaman farkı hesaplama fonksiyonu
function time_ago($datetime) {
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;
    
    if ($diff < 60) {
        return 'az önce';
    } elseif ($diff < 3600) {
        return floor($diff / 60) . ' dk önce';
    } elseif ($diff < 86400) {
        return floor($diff / 3600) . ' sa önce';
    } elseif ($diff < 2592000) {
        return floor($diff / 86400) . ' gün önce';
    } elseif ($diff < 31536000) {
        return floor($diff / 2592000) . ' ay önce';
    } else {
        return floor($diff / 31536000) . ' yıl önce';
    }
}

// Yeni yazı kontrolü (24 saat içinde)
function is_new_post($datetime) {
    $post_time = strtotime($datetime);
    $now = time();
    $diff = $now - $post_time;
    return $diff < 86400; // 24 saat
}

// Yazı yaşına göre renk sınıfı
function get_post_age_class($datetime) {
    $post_time = strtotime($datetime);
    $now = time();
    $diff = $now - $post_time;
    
    if ($diff < 3600) { // 1 saat
        return 'text-success';
    } elseif ($diff < 86400) { // 24 saat
        return 'text-info';
    } elseif ($diff < 604800) { // 7 gün
        return 'text-primary';
    } else {
        return 'text-muted';
    }
}