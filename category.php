<?php
require_once 'includes/config.php';

if(!isset($_GET['slug'])) {
    redirect('index.php');
}

$category_slug = sanitize($_GET['slug']);

// Kategori bilgilerini getir
$category_stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ?");
$category_stmt->execute([$category_slug]);
$category = $category_stmt->fetch();

if(!$category) {
    http_response_code(404);
    die('Kategori bulunamadı.');
}

// Sayfalama
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 9; // Sayfa başına 9 yazı
$offset = ($page - 1) * $limit;

// Kategoriye ait yazıları getir (published_at NULL kontrolü ile)
$posts_sql = "SELECT p.*, u.display_name, c.name as category_name, c.slug as category_slug, c.color 
              FROM posts p 
              LEFT JOIN users u ON p.author_id = u.id 
              LEFT JOIN categories c ON p.category_id = c.id 
              WHERE p.status = 'published' AND c.slug = ? 
              ORDER BY COALESCE(p.published_at, p.created_at) DESC 
              LIMIT $limit OFFSET $offset";

$posts_stmt = $pdo->prepare($posts_sql);
$posts_stmt->execute([$category_slug]);
$posts = $posts_stmt->fetchAll();

// Toplam yazı sayısı
$total_stmt = $pdo->prepare("SELECT COUNT(*) as total FROM posts p 
                            LEFT JOIN categories c ON p.category_id = c.id 
                            WHERE p.status = 'published' AND c.slug = ?");
$total_stmt->execute([$category_slug]);
$totalPosts = $total_stmt->fetch()['total'];
$totalPages = ceil($totalPosts / $limit);

// SEO Meta
$pageTitle = $category['name'] . ' - ' . SITE_NAME;
$pageDescription = isset($category['description']) && !empty($category['description']) ? $category['description'] : $category['name'] . ' kategorisindeki yazılar';
$pageKeywords = $category['name'] . ', ' . SITE_KEYWORDS;
$canonicalUrl = SITE_URL . '/category.php?slug=' . $category_slug;

require_once 'themes/google-modern/header.php';
?>

<style>
/* Renk Değişkenleri - Canlı ve Modern Renkler */
:root {
    --primary-color: #667eea;
    --primary-light: rgba(102, 126, 234, 0.1);
    --secondary-color: #764ba2;
    --accent-color: #f093fb;
    --success-color: #4cd964;
    --warning-color: #ff9500;
    --danger-color: #ff3b30;
    --info-color: #5ac8fa;
    --dark-color: #1c1c1e;
    --gray-dark: #3a3a3c;
    --gray-medium: #8e8e93;
    --gray-light: #f2f2f7;
    --white: #ffffff;
    --shadow: 0 4px 20px rgba(0,0,0,0.08);
    --shadow-hover: 0 8px 30px rgba(0,0,0,0.12);
}

/* Kategori Sayfası Özel Stiller */
.category-header {
    padding: 3rem 0;
    background: linear-gradient(135deg, <?php echo $category['color'] ?? '#667eea'; ?>20, var(--white));
    border-radius: 20px;
    margin-bottom: 3rem;
    border: 1px solid <?php echo $category['color'] ?? '#667eea'; ?>10;
    text-align: center;
}

.category-title {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 1rem;
    background: linear-gradient(135deg, <?php echo $category['color'] ?? '#667eea'; ?>, var(--secondary-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.category-description {
    font-size: 1.2rem;
    color: var(--gray-medium);
    max-width: 600px;
    margin: 0 auto 1.5rem;
    line-height: 1.6;
}

.category-stats {
    font-size: 1.1rem;
    margin-top: 1rem;
    color: var(--gray-medium);
}

.category-stats .badge {
    background: linear-gradient(135deg, <?php echo $category['color'] ?? '#667eea'; ?>, var(--secondary-color));
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
}

/* Grid Düzeni */
.category-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
    margin-bottom: 3rem;
}

/* Blog Kartları */
.blog-card {
    background: var(--white);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--shadow);
    transition: all 0.3s ease;
    border: 1px solid var(--gray-light);
    display: flex;
    flex-direction: column;
    height: 100%;
}

.blog-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-hover);
}

.blog-card-image {
    position: relative;
    width: 100%;
    height: 220px;
    overflow: hidden;
    background: var(--gray-light);
}

.blog-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.blog-card:hover .blog-card-image img {
    transform: scale(1.05);
}

.blog-card-content {
    padding: 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.blog-card-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.category-badge {
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    color: white;
    font-size: 0.8rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    background: linear-gradient(135deg, <?php echo $category['color'] ?? '#667eea'; ?>, var(--secondary-color));
}

.category-badge:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    color: white;
    text-decoration: none;
}

.post-date {
    font-size: 0.85rem;
    color: var(--gray-medium);
}

.blog-card-title {
    margin-bottom: 1rem;
    line-height: 1.4;
}

.blog-card-title a {
    color: var(--dark-color);
    text-decoration: none;
    font-size: 1.2rem;
    font-weight: 600;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    transition: color 0.3s ease;
}

.blog-card-title a:hover {
    color: var(--primary-color);
}

.blog-card-excerpt {
    color: var(--gray-medium);
    line-height: 1.6;
    margin-bottom: 1.5rem;
    flex: 1;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.blog-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid var(--gray-light);
    margin-top: auto;
}

.author-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    color: var(--gray-medium);
}

.author-info i {
    color: var(--primary-color);
}

.post-stats {
    display: flex;
    gap: 1rem;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.85rem;
    color: var(--gray-medium);
}

.stat-item i {
    color: var(--primary-color);
}

/* Sayfalama */
.pagination {
    margin-top: 3rem;
}

.page-link {
    border: none;
    color: var(--gray-medium);
    padding: 0.75rem 1rem;
    margin: 0 0.25rem;
    border-radius: 10px;
    transition: all 0.3s ease;
    font-weight: 500;
}

.page-link:hover {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    transform: translateY(-1px);
}

.page-item.active .page-link {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    border: none;
}

/* Boş Durum */
.empty-state {
    padding: 4rem 2rem;
    text-align: center;
}

.empty-state i {
    color: var(--gray-light);
    margin-bottom: 1.5rem;
}

.empty-state h3 {
    color: var(--dark-color);
    margin-bottom: 1rem;
}

.empty-state p {
    color: var(--gray-medium);
    margin-bottom: 2rem;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
}

/* Responsive Tasarım */
@media (max-width: 1200px) {
    .category-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }
}

@media (max-width: 768px) {
    .category-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .category-header {
        padding: 2rem 1rem;
        margin-bottom: 2rem;
    }
    
    .category-title {
        font-size: 2rem;
    }
    
    .blog-card-image {
        height: 200px;
    }
    
    .blog-card-content {
        padding: 1.25rem;
    }
    
    .blog-card-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .blog-card-footer {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .post-stats {
        width: 100%;
        justify-content: space-between;
    }
}

@media (max-width: 480px) {
    .container {
        padding-left: 15px;
        padding-right: 15px;
    }
    
    .blog-card-image {
        height: 180px;
    }
    
    .category-title {
        font-size: 1.75rem;
    }
}

/* Animasyon */
.fade-in-up {
    animation: fadeInUp 0.6s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Gradient Arkaplan Efekti */
.gradient-bg {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
}

/* Hover Efektleri */
.hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-hover);
}
</style>

<div class="container">
    

    <!-- Yazılar Grid -->
    <?php if(empty($posts)): ?>
        <div class="row">
            <div class="col-12">
                <div class="empty-state">
                    <i class="fas fa-inbox fa-4x mb-4"></i>
                    <h3>Bu kategoride henüz yazı bulunmuyor</h3>
                    <p class="mb-4">Yakında yeni yazılar eklenecek.</p>
                    <a href="index.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i>Ana Sayfaya Dön
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="category-grid">
            <?php foreach($posts as $post): ?>
                <article class="blog-card fade-in-up hover-lift">
                    <?php if($post['featured_image']): ?>
                        <div class="blog-card-image">
                            <img src="<?php echo $post['featured_image']; ?>" 
                                 alt="<?php echo sanitize($post['title']); ?>"
                                 loading="lazy"
                                 onerror="this.src='https://via.placeholder.com/400x220/667eea/ffffff?text=Resim+Yüklenemedi'">
                        </div>
                    <?php else: ?>
                        <div class="blog-card-image">
                            <img src="https://via.placeholder.com/400x220/667eea/ffffff?text=Resim+Yok" 
                                 alt="<?php echo sanitize($post['title']); ?>">
                        </div>
                    <?php endif; ?>
                    
                    <div class="blog-card-content">
                        <div class="blog-card-meta">
                            <a href="category.php?slug=<?php echo $post['category_slug']; ?>" class="category-badge">
                                <?php echo sanitize($post['category_name']); ?>
                            </a>
                            <span class="post-date">
                                <i class="far fa-clock me-1"></i>
                                <?php 
                                // Tarih hatası çözümü
                                if(!empty($post['published_at']) && $post['published_at'] != '0000-00-00 00:00:00') {
                                    echo date('d M Y', strtotime($post['published_at']));
                                } else {
                                    echo date('d M Y', strtotime($post['created_at']));
                                }
                                ?>
                            </span>
                        </div>
                        
                        <h3 class="blog-card-title">
                            <a href="<?php echo $post['slug']; ?>.html">
                                <?php echo decodeHtml($post['title']); ?>
                            </a>
                        </h3>
                        
                        <p class="blog-card-excerpt">
                            <?php 
                            $excerpt = $post['excerpt'] ?: strip_tags($post['content']);
                            if(strlen($excerpt) > 120) {
                                echo substr($excerpt, 0, 120) . '...';
                            } else {
                                echo $excerpt;
                            }
                            ?>
                        </p>
                        
                        <div class="blog-card-footer">
                            <div class="author-info">
                                <i class="fas fa-user-circle"></i>
                                <?php echo sanitize($post['display_name']); ?>
                            </div>
                            <div class="post-stats">
                                <div class="stat-item">
                                    <i class="far fa-eye"></i>
                                    <?php echo $post['view_count'] ?: 0; ?>
                                </div>
                                <div class="stat-item">
                                    <i class="far fa-comment"></i>
                                    <?php echo $post['comment_count'] ?: 0; ?>
                                </div>
                                <?php if($post['reading_time']): ?>
                                    <div class="stat-item">
                                        <i class="far fa-clock"></i>
                                        <?php echo $post['reading_time']; ?> dk
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Sayfalama -->
    <?php if($totalPages > 1): ?>
        <nav aria-label="Sayfalama" class="mt-5">
            <ul class="pagination justify-content-center">
                <!-- Önceki Sayfa -->
                <?php if($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="category.php?slug=<?php echo $category_slug; ?>&page=<?php echo $page - 1; ?>">
                            <i class="fas fa-chevron-left me-1"></i> Önceki
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Sayfa Numaraları -->
                <?php 
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $page + 2);
                
                // İlk sayfa her zaman gösterilsin
                if($startPage > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="category.php?slug=<?php echo $category_slug; ?>&page=1">1</a>
                    </li>
                    <?php if($startPage > 2): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    <?php endif;
                endif;
                
                for($i = $startPage; $i <= $endPage; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="category.php?slug=<?php echo $category_slug; ?>&page=<?php echo $i; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <!-- Son sayfa her zaman gösterilsin -->
                <?php if($endPage < $totalPages): ?>
                    <?php if($endPage < $totalPages - 1): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    <?php endif; ?>
                    <li class="page-item">
                        <a class="page-link" href="category.php?slug=<?php echo $category_slug; ?>&page=<?php echo $totalPages; ?>">
                            <?php echo $totalPages; ?>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Sonraki Sayfa -->
                <?php if($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="category.php?slug=<?php echo $category_slug; ?>&page=<?php echo $page + 1; ?>">
                            Sonraki <i class="fas fa-chevron-right ms-1"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
            
            <!-- Sayfa Bilgisi -->
            <div class="text-center mt-3">
                <small class="text-muted">
                    <i class="fas fa-file-alt me-1"></i>
                    Sayfa <?php echo $page; ?> / <?php echo $totalPages; ?> 
                    - Toplam <?php echo $totalPosts; ?> yazı
                </small>
            </div>
        </nav>
    <?php endif; ?>
</div>

<?php require_once 'themes/google-modern/footer.php'; ?>