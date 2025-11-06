<?php
require_once 'includes/config.php';

$pageTitle = "Popüler Yazılar - " . SITE_NAME;
$pageDescription = "En çok okunan ve beğenilen popüler yazılarımızı keşfedin";
$pageKeywords = "popüler yazılar, en çok okunan, trend yazılar, beğenilen içerikler";

// Filtreleme parametreleri
$time_filter = isset($_GET['time']) ? sanitize($_GET['time']) : 'all';
$category_slug = isset($_GET['category']) ? sanitize($_GET['category']) : '';

// Zaman filtreleme için SQL koşulu
$time_conditions = [];
switch($time_filter) {
    case 'today':
        $time_conditions[] = "p.created_at >= CURDATE()";
        break;
    case 'week':
        $time_conditions[] = "p.created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        break;
    case 'month':
        $time_conditions[] = "p.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        break;
    case 'year':
        $time_conditions[] = "p.created_at >= DATE_SUB(CURDATE(), INTERVAL 365 DAY)";
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

// Popüler yazıları getir (yorum sayısı ve tarihe göre)
$popular_sql = "
    SELECT p.*, c.name as category_name, c.slug as category_slug, 
           u.username as author_name,
           (SELECT COUNT(*) FROM comments WHERE post_id = p.id AND status = 'approved') as comment_count,
           (SELECT COUNT(*) FROM comments WHERE post_id = p.id AND status = 'approved') * 10 as popularity_score
    FROM posts p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN users u ON p.author_id = u.id
    $where_sql
    ORDER BY popularity_score DESC, p.created_at DESC
    LIMIT $offset, $per_page
";

$popular_stmt = $pdo->prepare($popular_sql);
$popular_stmt->execute($params);
$popular_posts = $popular_stmt->fetchAll();

// Kategorileri getir
$categories = $pdo->query("SELECT id, name, slug FROM categories ORDER BY name")->fetchAll();

// İstatistikler - views olmadan
$stats = $pdo->query("
    SELECT 
        (SELECT COUNT(*) FROM posts WHERE status = 'published') as total_posts,
        (SELECT COUNT(*) FROM comments WHERE status = 'approved') as total_comments,
        (SELECT COUNT(*) FROM comments WHERE status = 'approved') / 
        (SELECT COUNT(*) FROM posts WHERE status = 'published') as avg_comments_per_post
")->fetch();

// Zaman filtrelerine göre istatistikler
$time_stats = [
    'today' => $pdo->query("SELECT COUNT(*) as count FROM posts WHERE status = 'published' AND created_at >= CURDATE()")->fetch(),
    'week' => $pdo->query("SELECT COUNT(*) as count FROM posts WHERE status = 'published' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)")->fetch(),
    'month' => $pdo->query("SELECT COUNT(*) as count FROM posts WHERE status = 'published' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)")->fetch(),
    'year' => $pdo->query("SELECT COUNT(*) as count FROM posts WHERE status = 'published' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 365 DAY)")->fetch()
];

// Mevcut kategori bilgisini al
$current_category_name = '';
if ($category_slug) {
    $cat_stmt = $pdo->prepare("SELECT name FROM categories WHERE slug = ?");
    $cat_stmt->execute([$category_slug]);
    $current_category = $cat_stmt->fetch();
    $current_category_name = $current_category ? $current_category['name'] : '';
}

// Header dosyasını include et
include 'themes/google-modern/header.php';
?>

<style>
:root {
    --primary-color: #667eea;
    --secondary-color: #764ba2;
    --accent-color: #f093fb;
    --success-color: #4cd964;
    --warning-color: #ff9500;
    --danger-color: #ff3b30;
    --info-color: #5ac8fa;
    --dark-color: #1c1c1e;
    --light-color: #f8f9fa;
    --text-dark: #2c3e50;
    --text-light: #6c757d;
    --gradient-primary: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    --gradient-warning: linear-gradient(135deg, #ff9500 0%, #ffcc00 100%);
    --gradient-success: linear-gradient(135deg, #4cd964 0%, #5ac8fa 100%);
    --gradient-danger: linear-gradient(135deg, #ff3b30 0%, #ff9500 100%);
    --shadow-light: 0 2px 15px rgba(0,0,0,0.08);
    --shadow-medium: 0 5px 25px rgba(0,0,0,0.15);
    --shadow-heavy: 0 10px 40px rgba(0,0,0,0.2);
}

.hero-section {
    background: var(--gradient-primary);
    color: white;
    padding: 4rem 0;
    text-align: center;
    position: relative;
    overflow: hidden;
    border-radius: 15px;
    margin-bottom: 2rem;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="rgba(255,255,255,0.1)"><polygon points="1000,100 1000,0 0,100"/></svg>');
    background-size: cover;
}

.card {
    border: none;
    border-radius: 12px;
    box-shadow: var(--shadow-light);
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-medium);
}

.card-header {
    border-radius: 12px 12px 0 0 !important;
    font-weight: 600;
}

.bg-warning { background: var(--gradient-warning) !important; }
.bg-primary { background: var(--gradient-primary) !important; }
.bg-success { background: var(--gradient-success) !important; }
.bg-danger { background: var(--gradient-danger) !important; }

.list-group-item.active {
    background: var(--gradient-primary);
    border-color: var(--primary-color);
}

.badge {
    font-weight: 600;
}

.progress {
    border-radius: 10px;
    background: #e9ecef;
}

.progress-bar {
    border-radius: 10px;
    background: var(--gradient-warning);
}

.blog-card {
    background: white;
    border-radius: 15px;
    box-shadow: var(--shadow-light);
    transition: all 0.4s ease;
    overflow: hidden;
    border: 1px solid rgba(0,0,0,0.05);
}

.blog-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-heavy);
}

.popularity-indicator {
    background: rgba(255, 149, 0, 0.05);
    padding: 10px;
    border-radius: 8px;
    border-left: 4px solid var(--warning-color);
}

.btn-primary {
    background: var(--gradient-primary);
    border: none;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.page-item.active .page-link {
    background: var(--gradient-primary);
    border-color: var(--primary-color);
}

.page-link {
    color: var(--primary-color);
    border-radius: 8px;
    margin: 0 3px;
    border: 1px solid #dee2e6;
}

.page-link:hover {
    color: var(--secondary-color);
    background-color: #e9ecef;
    border-color: #dee2e6;
}

.category-badge {
    background: var(--gradient-primary);
    color: white;
    padding: 0.4rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}

.category-badge:hover {
    background: var(--gradient-warning);
    color: white;
    text-decoration: none;
    transform: scale(1.05);
}

.hero-stats div {
    background: rgba(255,255,255,0.2);
    padding: 0.5rem 1rem;
    border-radius: 25px;
    backdrop-filter: blur(10px);
}

.archive-stats {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: var(--shadow-light);
    border-left: 4px solid var(--warning-color);
}

@media (max-width: 768px) {
    .hero-section {
        padding: 2rem 0;
        border-radius: 10px;
    }
    
    .hero-stats {
        flex-direction: column;
        gap: 1rem !important;
    }
    
    .blog-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="container mt-4">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <h1 class="hero-title fade-in-up">Popüler Yazılar</h1>
            <p class="hero-subtitle fade-in-up">En çok beğenilen ve yorum alan içeriklerimizi keşfedin</p>
            <div class="hero-stats fade-in-up" style="display: flex; gap: 2rem; justify-content: center; font-size: 1.1rem;">
                <div><i class="fas fa-newspaper me-1"></i> <?php echo $stats['total_posts']; ?> Yazı</div>
                <div><i class="fas fa-comments me-1"></i> <?php echo $stats['total_comments']; ?> Yorum</div>
                <div><i class="fas fa-chart-bar me-1"></i> Ortalama <?php echo number_format($stats['avg_comments_per_post'], 1); ?> yorum/yazı</div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Sol Sidebar - Filtreler -->
        <div class="col-lg-3">
            <div class="sticky-top" style="top: 100px;">
                <!-- Zaman Filtresi -->
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Zaman Aralığı</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <a href="popular.php<?php echo $category_slug ? '?category=' . $category_slug : ''; ?>" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo $time_filter == 'all' ? 'active' : ''; ?>">
                                <span>Tüm Zamanlar</span>
                                <span class="badge bg-primary rounded-pill"><?php echo $stats['total_posts']; ?></span>
                            </a>
                            <a href="popular.php?time=today<?php echo $category_slug ? '&category=' . $category_slug : ''; ?>" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo $time_filter == 'today' ? 'active' : ''; ?>">
                                <span>Bugün</span>
                                <span class="badge bg-success rounded-pill"><?php echo $time_stats['today']['count'] ?? 0; ?></span>
                            </a>
                            <a href="popular.php?time=week<?php echo $category_slug ? '&category=' . $category_slug : ''; ?>" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo $time_filter == 'week' ? 'active' : ''; ?>">
                                <span>Bu Hafta</span>
                                <span class="badge bg-info rounded-pill"><?php echo $time_stats['week']['count'] ?? 0; ?></span>
                            </a>
                            <a href="popular.php?time=month<?php echo $category_slug ? '&category=' . $category_slug : ''; ?>" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo $time_filter == 'month' ? 'active' : ''; ?>">
                                <span>Bu Ay</span>
                                <span class="badge bg-warning rounded-pill"><?php echo $time_stats['month']['count'] ?? 0; ?></span>
                            </a>
                            <a href="popular.php?time=year<?php echo $category_slug ? '&category=' . $category_slug : ''; ?>" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo $time_filter == 'year' ? 'active' : ''; ?>">
                                <span>Bu Yıl</span>
                                <span class="badge bg-danger rounded-pill"><?php echo $time_stats['year']['count'] ?? 0; ?></span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Kategori Filtresi -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-folder me-2"></i>Kategoriler</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <a href="popular.php<?php echo $time_filter != 'all' ? '?time=' . $time_filter : ''; ?>" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo !$category_slug ? 'active' : ''; ?>">
                                Tüm Kategoriler
                                <span class="badge bg-primary rounded-pill"><?php echo $stats['total_posts']; ?></span>
                            </a>
                            <?php foreach($categories as $category): ?>
                                <?php
                                // Kategoriye ait popüler yazı sayısını al (zaman filtresine göre)
                                $cat_count_sql = "SELECT COUNT(*) FROM posts p 
                                                LEFT JOIN categories c ON p.category_id = c.id 
                                                WHERE c.slug = ? AND p.status = 'published'";
                                
                                if ($time_filter == 'today') {
                                    $cat_count_sql .= " AND p.created_at >= CURDATE()";
                                } elseif ($time_filter == 'week') {
                                    $cat_count_sql .= " AND p.created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                                } elseif ($time_filter == 'month') {
                                    $cat_count_sql .= " AND p.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
                                } elseif ($time_filter == 'year') {
                                    $cat_count_sql .= " AND p.created_at >= DATE_SUB(CURDATE(), INTERVAL 365 DAY)";
                                }
                                
                                $cat_count = $pdo->prepare($cat_count_sql);
                                $cat_count->execute([$category['slug']]);
                                $post_count = $cat_count->fetchColumn();
                                ?>
                                <a href="popular.php?category=<?php echo $category['slug']; ?><?php echo $time_filter != 'all' ? '&time=' . $time_filter : ''; ?>" 
                                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo $category_slug == $category['slug'] ? 'active' : ''; ?>">
                                    <?php echo sanitize($category['name']); ?>
                                    <span class="badge bg-secondary rounded-pill"><?php echo $post_count; ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Popülerlik İstatistikleri -->
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>İstatistikler</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted">Toplam Yorum</small>
                            <div class="fw-bold text-primary"><?php echo number_format($stats['total_comments']); ?></div>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Ortalama Yorum</small>
                            <div class="fw-bold text-success"><?php echo number_format($stats['avg_comments_per_post'], 1); ?> / yazı</div>
                        </div>
                        <div>
                            <small class="text-muted">Popülerlik Skoru</small>
                            <div class="fw-bold text-warning">Yorum Sayısı × 10</div>
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
                            <i class="fas fa-fire me-2 text-danger"></i>
                            <?php
                            $time_labels = [
                                'all' => 'Tüm Zamanlar',
                                'today' => 'Bugün',
                                'week' => 'Bu Hafta',
                                'month' => 'Bu Ay',
                                'year' => 'Bu Yıl'
                            ];
                            
                            echo $time_labels[$time_filter];
                            
                            if ($current_category_name) {
                                echo " - " . $current_category_name;
                            }
                            ?>
                        </h2>
                        <p class="text-muted mb-0">
                            <?php echo $total_posts; ?> popüler yazı bulundu
                            <?php if ($time_filter != 'all'): ?>
                                - <strong><?php echo $time_labels[$time_filter]; ?></strong> için sıralanmıştır
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="btn-group">
                            <span class="btn btn-outline-primary disabled">
                                <i class="fas fa-sort-amount-down me-1"></i> Popülerlik
                            </span>
                            <a href="archives.php" class="btn btn-outline-secondary">
                                <i class="fas fa-clock me-1"></i> Tarihe Göre
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aktif Filtreler -->
            <?php if ($time_filter != 'all' || $category_slug): ?>
            <div class="mb-4">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="text-muted">Aktif filtreler:</span>
                    <?php if ($time_filter != 'all'): ?>
                        <span class="badge bg-warning text-dark">
                            Zaman: <?php echo $time_labels[$time_filter]; ?>
                            <a href="popular.php<?php echo $category_slug ? '?category=' . $category_slug : ''; ?>" class="text-dark ms-2">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    <?php endif; ?>
                    <?php if ($category_slug): ?>
                        <span class="badge bg-primary">
                            Kategori: <?php echo $current_category_name ?: ''; ?>
                            <a href="popular.php<?php echo $time_filter != 'all' ? '?time=' . $time_filter : ''; ?>" class="text-white ms-2">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    <?php endif; ?>
                    <?php if ($time_filter != 'all' || $category_slug): ?>
                        <a href="popular.php" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-times"></i> Tümünü Temizle
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Popüler Yazılar Grid -->
            <?php if(empty($popular_posts)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-fire fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Popüler yazı bulunmuyor</h4>
                    <p class="text-muted">Seçtiğiniz kriterlere uygun popüler yazı bulunamadı.</p>
                    <a href="popular.php" class="btn btn-primary">Tüm Popüler Yazılar</a>
                </div>
            <?php else: ?>
                <div class="blog-grid">
                    <?php foreach($popular_posts as $index => $post): ?>
                        <div class="blog-card fade-in-up">
                            <!-- Popülerlik Sırası -->
                            <div class="position-absolute top-0 start-0 m-3">
                                <span class="badge bg-danger rounded-circle p-2" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                    #<?php echo $index + 1 + $offset; ?>
                                </span>
                            </div>
                            
                            <?php if($post['featured_image']): ?>
                                <div class="blog-card-image">
                                    <img src="<?php echo $post['featured_image']; ?>" alt="<?php echo sanitize($post['title']); ?>">
                                </div>
                            <?php else: ?>
                                <div class="blog-card-image bg-light d-flex align-items-center justify-content-center">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="blog-card-content">
                                <div class="blog-card-meta">
                                    <a href="popular.php?category=<?php echo $post['category_slug']; ?><?php echo $time_filter != 'all' ? '&time=' . $time_filter : ''; ?>" class="category-badge">
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
                                
                                <!-- Popülerlik Göstergesi -->
                                <div class="popularity-indicator mb-3">
                                    <div class="d-flex justify-content-between align-items-center small text-muted mb-1">
                                        <span>Popülerlik Skoru:</span>
                                        <span class="fw-bold text-warning"><?php echo number_format($post['popularity_score']); ?></span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <?php
                                        // Maksimum popülerlik skorunu bul (ilk 10 yazı içinde)
                                        $max_score = $popular_posts[0]['popularity_score'];
                                        $percentage = $max_score > 0 ? ($post['popularity_score'] / $max_score) * 100 : 0;
                                        ?>
                                        <div class="progress-bar bg-warning" role="progressbar" 
                                             style="width: <?php echo $percentage; ?>%" 
                                             aria-valuenow="<?php echo $percentage; ?>" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100"></div>
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
                                        <div class="stat-item" title="Popülerlik Skoru">
                                            <i class="fas fa-fire"></i>
                                            <span><?php echo number_format($post['popularity_score']); ?></span>
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
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">
                                    <i class="fas fa-chevron-left"></i> Önceki
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">
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

<script>
    // Popülerlik skorlarına göre animasyon
    document.addEventListener('DOMContentLoaded', function() {
        const popularCards = document.querySelectorAll('.blog-card');
        popularCards.forEach((card, index) => {
            // İlk 3 kartı vurgula
            if (index < 3) {
                card.style.border = '2px solid #ff9500';
                card.style.boxShadow = '0 8px 30px rgba(255, 149, 0, 0.3)';
            }
            
            // Hover efekti
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
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
    
    return 'popular.php?' . http_build_query($current_params);
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