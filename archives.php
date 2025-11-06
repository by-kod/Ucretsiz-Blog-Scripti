<?php
require_once 'includes/config.php';

$pageTitle = "Arşiv - " . SITE_NAME;
$pageDescription = "Tüm yazıları kategori, tarih ve arama filtresi ile keşfedin";
$pageKeywords = "arşiv, yazılar, kategori, tarih, arama";

// Sayfa numarası
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Arama ve filtreleme parametreleri
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$year = isset($_GET['year']) ? (int)$_GET['year'] : '';
$month = isset($_GET['month']) ? (int)$_GET['month'] : '';
$category_slug = isset($_GET['category']) ? sanitize($_GET['category']) : '';

// SQL koşulları
$where_conditions = ["p.status = 'published'"];
$params = [];

if ($search) {
    $where_conditions[] = "(p.title LIKE ? OR p.content LIKE ? OR p.excerpt LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

if ($year && $month) {
    $where_conditions[] = "YEAR(p.created_at) = ? AND MONTH(p.created_at) = ?";
    $params[] = $year;
    $params[] = $month;
} elseif ($year) {
    $where_conditions[] = "YEAR(p.created_at) = ?";
    $params[] = $year;
}

if ($category_slug) {
    $where_conditions[] = "c.slug = ?";
    $params[] = $category_slug;
}

$where_sql = $where_conditions ? "WHERE " . implode(" AND ", $where_conditions) : '';

// Toplam yazı sayısı
$count_sql = "SELECT COUNT(*) as total FROM posts p LEFT JOIN categories c ON p.category_id = c.id $where_sql";
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total_posts = $count_stmt->fetch()['total'];
$total_pages = ceil($total_posts / $per_page);

// Yazıları getir
$posts_sql = "
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

$posts_stmt = $pdo->prepare($posts_sql);
$posts_stmt->execute($params);
$posts = $posts_stmt->fetchAll();

// Kategorileri getir
$categories = $pdo->query("SELECT id, name, slug FROM categories ORDER BY name")->fetchAll();

// Yılları ve ayları getir
$archive_dates = $pdo->query("
    SELECT YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count
    FROM posts 
    WHERE status = 'published' 
    GROUP BY YEAR(created_at), MONTH(created_at) 
    ORDER BY year DESC, month DESC
")->fetchAll();

// Ayların Türkçe karşılıkları
$turkish_months = [
    1 => 'Ocak', 2 => 'Şubat', 3 => 'Mart', 4 => 'Nisan',
    5 => 'Mayıs', 6 => 'Haziran', 7 => 'Temmuz', 8 => 'Ağustos',
    9 => 'Eylül', 10 => 'Ekim', 11 => 'Kasım', 12 => 'Aralık'
];

// Arşiv verilerini düzenle
$archives = [];
foreach ($archive_dates as $date) {
    $year = $date['year'];
    $month = $date['month'];
    if (!isset($archives[$year])) {
        $archives[$year] = [
            'year' => $year,
            'total' => 0,
            'months' => []
        ];
    }
    $archives[$year]['months'][] = [
        'month' => $month,
        'month_name' => $turkish_months[$month],
        'count' => $date['count']
    ];
    $archives[$year]['total'] += $date['count'];
}

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

<div class="container mt-4">
    <div class="row">
        <!-- Sol Sidebar - Arşiv Navigasyonu -->
        <div class="col-lg-3">
            <div class="archive-sidebar">
                <!-- Arama Kutusu -->
                <div class="search-box">
                    <h5 class="mb-3"><i class="fas fa-search me-2" style="color: #6f42c1;"></i>Yazı Ara</h5>
                    <form method="GET" action="archives.php">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Kelime ara..." value="<?php echo $search; ?>">
                            <button class="btn btn-light" type="submit" style="background: #6f42c1; color: white; border-color: #6f42c1;">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Kategoriler -->
                <div class="card mb-4">
                    <div class="card-header text-white" style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);">
                        <h6 class="mb-0"><i class="fas fa-folder me-2"></i>Kategoriler</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <a href="archives.php" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo !$category_slug ? 'active' : ''; ?>" 
                               style="<?php echo !$category_slug ? 'background: #6f42c1; color: white; border-color: #6f42c1;' : ''; ?>">
                                Tüm Kategoriler
                                <span class="badge rounded-pill" style="background: <?php echo !$category_slug ? 'rgba(255,255,255,0.3)' : '#6f42c1'; ?>"><?php echo $total_posts; ?></span>
                            </a>
                            <?php foreach($categories as $category): ?>
                                <?php
                                // Kategoriye ait yazı sayısını al
                                $cat_count = $pdo->prepare("SELECT COUNT(*) FROM posts p 
                                                          LEFT JOIN categories c ON p.category_id = c.id 
                                                          WHERE c.slug = ? AND p.status = 'published'");
                                $cat_count->execute([$category['slug']]);
                                $post_count = $cat_count->fetchColumn();
                                ?>
                                <a href="archives.php?category=<?php echo $category['slug']; ?>" 
                                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo $category_slug == $category['slug'] ? 'active' : ''; ?>"
                                   style="<?php echo $category_slug == $category['slug'] ? 'background: #e83e8c; color: white; border-color: #e83e8c;' : ''; ?>">
                                    <?php echo sanitize($category['name']); ?>
                                    <span class="badge rounded-pill" style="background: <?php echo $category_slug == $category['slug'] ? 'rgba(255,255,255,0.3)' : '#20c997'; ?>"><?php echo $post_count; ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Arşiv -->
                <div class="card">
                    <div class="card-header text-white" style="background: linear-gradient(135deg, #20c997 0%, #17a2b8 100%);">
                        <h6 class="mb-0"><i class="fas fa-archive me-2"></i>Arşiv</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <a href="archives.php" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo !$year ? 'active' : ''; ?>"
                               style="<?php echo !$year ? 'background: #20c997; color: white; border-color: #20c997;' : ''; ?>">
                                Tüm Zamanlar
                                <span class="badge rounded-pill" style="background: <?php echo !$year ? 'rgba(255,255,255,0.3)' : '#20c997'; ?>"><?php echo $total_posts; ?></span>
                            </a>
                            <?php foreach($archives as $archive): ?>
                                <div class="archive-year list-group-item list-group-item-action" 
                                     data-bs-toggle="collapse" 
                                     data-bs-target="#months-<?php echo $archive['year']; ?>"
                                     aria-expanded="<?php echo $year == $archive['year'] ? 'true' : 'false'; ?>"
                                     style="cursor: pointer; transition: all 0.3s ease;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fas fa-calendar me-2" style="color: #fd7e14;"></i><?php echo $archive['year']; ?>
                                        </span>
                                        <span class="badge rounded-pill" style="background: #fd7e14; color: white;"><?php echo $archive['total']; ?></span>
                                    </div>
                                </div>
                                <div class="collapse <?php echo $year == $archive['year'] ? 'show' : ''; ?>" id="months-<?php echo $archive['year']; ?>">
                                    <?php foreach($archive['months'] as $month_data): ?>
                                        <a href="archives.php?year=<?php echo $archive['year']; ?>&month=<?php echo $month_data['month']; ?>" 
                                           class="archive-month list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo ($year == $archive['year'] && $month == $month_data['month']) ? 'active' : ''; ?>"
                                           style="<?php echo ($year == $archive['year'] && $month == $month_data['month']) ? 'background: #fd7e14; color: white; border-color: #fd7e14;' : ''; ?>">
                                            <?php echo $month_data['month_name']; ?>
                                            <span class="badge rounded-pill" style="background: <?php echo ($year == $archive['year'] && $month == $month_data['month']) ? 'rgba(255,255,255,0.3)' : '#6c757d'; ?>; color: <?php echo ($year == $archive['year'] && $month == $month_data['month']) ? 'white' : 'inherit'; ?>">
                                                <?php echo $month_data['count']; ?>
                                            </span>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ana İçerik -->
        <div class="col-lg-9">
            <!-- Başlık ve İstatistikler -->
            <div class="archive-stats">
                <div class="row">
                    <div class="col-md-8">
                        <h1 class="h3 mb-2">
                            <i class="fas fa-archive me-2" style="color: #6f42c1;"></i>
                            <?php
                            if ($search) {
                                echo "Arama: \"$search\"";
                            } elseif ($year && $month) {
                                echo $turkish_months[$month] . " " . $year;
                            } elseif ($year) {
                                echo "Yıl: $year";
                            } elseif ($category_slug) {
                                echo "Kategori: " . ($current_category_name ?: '');
                            } else {
                                echo "Tüm Yazılar";
                            }
                            ?>
                        </h1>
                        <p class="text-muted mb-0">
                            <?php echo $total_posts; ?> yazı bulundu
                            <?php if ($search): ?>
                                - "<strong><?php echo $search; ?></strong>" için arama sonuçları
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="btn-group">
                            <button class="btn active" style="background: #6f42c1; color: white; border-color: #6f42c1;">
                                <i class="fas fa-list"></i> Liste
                            </button>
                            <a href="index.php" class="btn btn-outline-secondary">
                                <i class="fas fa-th"></i> Grid
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aktif Filtreler -->
            <?php if ($search || $year || $category_slug): ?>
            <div class="mb-4">
                <div class="d-flex flex-wrap gap-2">
                    <span class="text-muted">Aktif filtreler:</span>
                    <?php if ($search): ?>
                        <span class="badge" style="background: #6f42c1; color: white;">
                            Arama: <?php echo $search; ?>
                            <a href="<?php echo removeParam('search'); ?>" class="text-white ms-2" style="text-decoration: none;">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    <?php endif; ?>
                    <?php if ($year && $month): ?>
                        <span class="badge" style="background: #20c997; color: white;">
                            <?php echo $turkish_months[$month] . ' ' . $year; ?>
                            <a href="<?php echo removeParam(['year', 'month']); ?>" class="text-white ms-2" style="text-decoration: none;">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    <?php elseif ($year): ?>
                        <span class="badge" style="background: #20c997; color: white;">
                            Yıl: <?php echo $year; ?>
                            <a href="<?php echo removeParam('year'); ?>" class="text-white ms-2" style="text-decoration: none;">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    <?php endif; ?>
                    <?php if ($category_slug): ?>
                        <span class="badge" style="background: #e83e8c; color: white;">
                            Kategori: <?php echo $current_category_name ?: ''; ?>
                            <a href="<?php echo removeParam('category'); ?>" class="text-white ms-2" style="text-decoration: none;">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    <?php endif; ?>
                    <?php if ($search || $year || $category_slug): ?>
                        <a href="archives.php" class="btn btn-sm" style="background: #dc3545; color: white; border-color: #dc3545;">
                            <i class="fas fa-times"></i> Tümünü Temizle
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Yazı Listesi -->
            <?php if(empty($posts)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-4x mb-3" style="color: #6f42c1;"></i>
                    <h4 class="text-muted">Henüz yazı bulunmuyor</h4>
                    <p class="text-muted">Arama kriterlerinize uygun yazı bulunamadı.</p>
                    <a href="archives.php" class="btn" style="background: #6f42c1; color: white; border-color: #6f42c1;">
                        Tüm Yazıları Görüntüle
                    </a>
                </div>
            <?php else: ?>
                <div class="blog-grid">
                    <?php foreach($posts as $post): ?>
                        <div class="blog-card fade-in-up">
                            <?php if($post['featured_image']): ?>
                                <div class="blog-card-image">
                                    <img src="<?php echo $post['featured_image']; ?>" alt="<?php echo sanitize($post['title']); ?>">
                                </div>
                            <?php else: ?>
                                <div class="blog-card-image bg-light d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                    <i class="fas fa-image fa-3x" style="color: #6f42c1;"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="blog-card-content">
                                <div class="blog-card-meta">
                                    <a href="archives.php?category=<?php echo $post['category_slug']; ?>" class="category-badge" style="background: #e83e8c; color: white;">
                                        <?php echo sanitize($post['category_name']); ?>
                                    </a>
                                    <span class="post-date">
                                        <?php echo date('d M Y', strtotime($post['created_at'])); ?>
                                    </span>
                                </div>
                                
                                <h3 class="blog-card-title">
                                    <a href="<?php echo $post['slug']; ?>.html" style="color: #2d3748; text-decoration: none;">
                                        <?php echo sanitize($post['title']); ?>
                                    </a>
                                </h3>
                                
                                <p class="blog-card-excerpt" style="color: #718096;">
                                    <?php 
                                    $excerpt = $post['excerpt'] ?: strip_tags($post['content']);
                                    echo truncate($excerpt, 150);
                                    ?>
                                </p>
                                
                                <div class="blog-card-footer">
                                    <div class="author-info" style="color: #6f42c1;">
                                        <i class="fas fa-user"></i>
                                        <span><?php echo sanitize($post['author_name']); ?></span>
                                    </div>
                                    <div class="post-stats">
                                        <div class="stat-item" style="color: #20c997;">
                                            <i class="fas fa-eye"></i>
                                            <span><?php echo $post['views'] ?? 0; ?></span>
                                        </div>
                                        <div class="stat-item" style="color: #fd7e14;">
                                            <i class="fas fa-comments"></i>
                                            <span><?php echo $post['comment_count']; ?></span>
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
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" style="color: #6f42c1; border-color: #6f42c1;">
                                    <i class="fas fa-chevron-left"></i> Önceki
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" 
                                   style="<?php echo $i == $page ? 'background: #6f42c1; border-color: #6f42c1; color: white;' : 'color: #6f42c1; border-color: #6f42c1;'; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" style="color: #6f42c1; border-color: #6f42c1;">
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
    .archive-sidebar .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    
    .archive-sidebar .card:hover {
        transform: translateY(-5px);
    }
    
    .archive-sidebar .list-group-item {
        border: none;
        padding: 12px 15px;
        transition: all 0.3s ease;
    }
    
    .archive-sidebar .list-group-item:hover {
        background: rgba(111, 66, 193, 0.1);
        color: #6f42c1;
    }
    
    .archive-year:hover {
        background: rgba(253, 126, 20, 0.1) !important;
        color: #fd7e14 !important;
    }
    
    .blog-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        border: none;
        margin-bottom: 2rem;
    }
    
    .blog-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    }
    
    .blog-card-image {
        height: 200px;
        overflow: hidden;
        border-radius: 12px 12px 0 0;
    }
    
    .blog-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .blog-card:hover .blog-card-image img {
        transform: scale(1.1);
    }
    
    .blog-card-content {
        padding: 1.5rem;
    }
    
    .category-badge {
        background: #e83e8c;
        color: white;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .category-badge:hover {
        background: #d91a72;
        color: white;
        text-decoration: none;
        transform: scale(1.05);
    }
    
    .blog-card-title a:hover {
        color: #6f42c1 !important;
    }
    
    .page-link:hover {
        background: #6f42c1;
        color: white;
        border-color: #6f42c1;
    }
</style>

<script>
    // Arşiv yılı genişletme/daraltma
    document.addEventListener('DOMContentLoaded', function() {
        const archiveYears = document.querySelectorAll('.archive-year');
        archiveYears.forEach(year => {
            year.addEventListener('click', function() {
                const target = this.getAttribute('data-bs-target');
                const collapse = document.querySelector(target);
                const isExpanded = collapse.classList.contains('show');
                
                // Diğer tüm arşivleri kapat
                document.querySelectorAll('.collapse').forEach(c => {
                    if (c !== collapse) {
                        c.classList.remove('show');
                    }
                });
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
    
    return 'archives.php?' . http_build_query($current_params);
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