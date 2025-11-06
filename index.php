<?php
require_once 'includes/config.php';

$pageTitle = SITE_NAME . " - " . SITE_DESCRIPTION;
$pageDescription = SITE_DESCRIPTION;
$pageKeywords = SITE_KEYWORDS;

// Sayfalama
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Öne çıkan yazı (ilk yazı)
$featured_sql = "SELECT p.*, u.display_name, c.name as category_name, c.slug as category_slug, c.color 
                 FROM posts p 
                 LEFT JOIN users u ON p.author_id = u.id 
                 LEFT JOIN categories c ON p.category_id = c.id 
                 WHERE p.status = 'published' 
                 ORDER BY p.published_at DESC 
                 LIMIT 1";

$featured_post = $pdo->query($featured_sql)->fetch();

// Diğer yazılar (öne çıkan hariç)
if($featured_post) {
    $other_posts_sql = "SELECT p.*, u.display_name, c.name as category_name, c.slug as category_slug, c.color 
                        FROM posts p 
                        LEFT JOIN users u ON p.author_id = u.id 
                        LEFT JOIN categories c ON p.category_id = c.id 
                        WHERE p.status = 'published' AND p.id != ?
                        ORDER BY p.published_at DESC 
                        LIMIT $limit OFFSET $offset";
    $other_posts = $pdo->prepare($other_posts_sql);
    $other_posts->execute([$featured_post['id']]);
    $other_posts = $other_posts->fetchAll();
} else {
    // Öne çıkan yazı yoksa tüm yazıları getir
    $other_posts_sql = "SELECT p.*, u.display_name, c.name as category_name, c.slug as category_slug, c.color 
                        FROM posts p 
                        LEFT JOIN users u ON p.author_id = u.id 
                        LEFT JOIN categories c ON p.category_id = c.id 
                        WHERE p.status = 'published'
                        ORDER BY p.published_at DESC 
                        LIMIT $limit OFFSET $offset";
    $other_posts = $pdo->query($other_posts_sql)->fetchAll();
}

// Toplam yazı sayısı
$totalStmt = $pdo->query("SELECT COUNT(*) as total FROM posts WHERE status = 'published'");
$totalPosts = $totalStmt->fetch()['total'];
$totalPages = ceil($totalPosts / $limit);

// İstatistikler
$total_categories = $pdo->query("SELECT COUNT(*) as count FROM categories")->fetch()['count'];
$total_comments = $pdo->query("SELECT COUNT(*) as count FROM comments WHERE status = 'approved'")->fetch()['count'];

require_once 'themes/google-modern/header.php';
?>

<style>
:root {
    --primary-color: #2E86C1;
    --secondary-color: #28B463;
    --accent-color: #E74C3C;
    --warning-color: #F39C12;
    --info-color: #17A2B8;
    --dark-color: #2C3E50;
    --light-color: #F8F9FA;
    --gray-color: #6C757D;
    --gradient-primary: linear-gradient(135deg, #2E86C1, #3498DB);
    --gradient-secondary: linear-gradient(135deg, #28B463, #58D68D);
    --gradient-accent: linear-gradient(135deg, #E74C3C, #EC7063);
    --gradient-warning: linear-gradient(135deg, #F39C12, #F7DC6F);
    --shadow: 0 4px 20px rgba(0,0,0,0.08);
    --shadow-hover: 0 8px 30px rgba(0,0,0,0.12);
}

/* Öne Çıkan Yazı için Ek Stiller */
.featured-post {
    display: flex;
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--shadow);
    margin-bottom: 2rem;
    border: 1px solid rgba(46, 134, 193, 0.1);
    flex-wrap: wrap;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.featured-post:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-hover);
}

.featured-content {
    flex: 1;
    min-width: 300px;
    padding: 2.5rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.featured-badge {
    background: var(--gradient-accent);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    display: inline-block;
    margin-bottom: 1rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 10px rgba(231, 76, 60, 0.3);
}

.blog-card-image {
    flex: 0 0 400px;
    position: relative;
    overflow: hidden;
    background: var(--light-color);
}

.blog-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.featured-post:hover .blog-card-image img {
    transform: scale(1.05);
}

/* Blog Kartları */
.blog-card {
    background: white;
    border-radius: 16px;
    box-shadow: var(--shadow);
    transition: all 0.3s ease;
    overflow: hidden;
    border: 1px solid rgba(46, 134, 193, 0.1);
    height: 100%;
    display: flex;
    flex-direction: column;
}

.blog-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-hover);
}

.blog-card-content {
    padding: 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.blog-card-title {
    font-size: 1.4rem;
    font-weight: 700;
    line-height: 1.4;
    margin-bottom: 1rem;
    flex: 1;
}

.blog-card-title a {
    color: var(--dark-color);
    text-decoration: none;
    transition: color 0.3s ease;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.blog-card-title a:hover {
    color: var(--primary-color);
}

.blog-card-excerpt {
    color: var(--gray-color);
    line-height: 1.6;
    margin-bottom: 1.5rem;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    flex: 1;
}

.blog-card-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    font-size: 0.85rem;
    flex-wrap: wrap;
    gap: 0.5rem;
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
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 150px;
    display: inline-block;
    box-shadow: 0 2px 8px rgba(46, 134, 193, 0.3);
}

.category-badge:hover {
    background: var(--gradient-secondary);
    color: white;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(40, 180, 99, 0.4);
}

.post-date {
    color: var(--gray-color);
    font-size: 0.8rem;
}

.blog-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid rgba(0,0,0,0.1);
    margin-top: auto;
}

.author-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    color: var(--gray-color);
}

.post-stats {
    display: flex;
    gap: 1rem;
    font-size: 0.85rem;
    color: var(--gray-color);
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

/* Blog Grid */
.blog-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
    padding: 3rem 0;
}

/* Hero Section */
.hero-section {
    background: var(--gradient-primary);
    color: white;
    padding: 4rem 0;
    text-align: center;
    position: relative;
    overflow: hidden;
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

.hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    margin-bottom: 1rem;
    background: linear-gradient(45deg, #fff, #e3f2fd);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-subtitle {
    font-size: 1.3rem;
    opacity: 0.9;
    max-width: 600px;
    margin: 0 auto 2rem;
}

/* Sayfalama */
.pagination {
    justify-content: center;
    margin: 3rem 0;
}

.page-link {
    border: none;
    color: var(--gray-color);
    padding: 0.75rem 1.25rem;
    margin: 0 0.25rem;
    border-radius: 10px;
    transition: all 0.3s ease;
    background: white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.page-link:hover {
    background: var(--primary-color);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(46, 134, 193, 0.3);
}

.page-item.active .page-link {
    background: var(--gradient-primary);
    color: white;
    box-shadow: 0 4px 12px rgba(46, 134, 193, 0.4);
}

/* Butonlar */
.btn-primary {
    background: var(--gradient-primary);
    border: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(46, 134, 193, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(46, 134, 193, 0.4);
    background: var(--gradient-primary);
}

/* Mobil için düzenlemeler */
@media (max-width: 768px) {
    .featured-post {
        flex-direction: column-reverse;
        margin-left: -15px;
        margin-right: -15px;
        border-radius: 0;
        border-left: none;
        border-right: none;
    }
    
    .featured-content {
        padding: 1.5rem;
        min-width: auto;
    }
    
    .blog-card-image {
        flex: 0 0 250px;
        min-height: 200px;
    }
    
    .blog-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
        padding: 1.5rem 0;
    }
    
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-subtitle {
        font-size: 1.1rem;
        padding: 0 1rem;
    }
    
    .container {
        padding-left: 15px;
        padding-right: 15px;
    }
}

@media (max-width: 480px) {
    .blog-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .blog-card-content {
        padding: 1rem;
    }
    
    .blog-card-title {
        font-size: 1.2rem;
    }
    
    .hero-title {
        font-size: 2rem;
    }
}

/* Genel taşma önleme */
* {
    max-width: 100%;
}

img {
    max-width: 100%;
    height: auto;
}

/* Yazı içeriği taşma önleme */
.blog-card-content,
.featured-content {
    word-wrap: break-word;
    overflow-wrap: break-word;
}

/* Animasyonlar */
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

.fade-in-up {
    animation: fadeInUp 0.6s ease-out;
}

/* Boş durum stili */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
}

.empty-state i {
    color: #dee2e6;
    margin-bottom: 1rem;
}
</style>

<div class="blog-grid">
    <?php if($featured_post): ?>
        <!-- Öne Çıkan Yazı -->
        <div class="featured-post fade-in-up">
            <div class="featured-content">
                <span class="featured-badge">ÖNE ÇIKAN</span>
                <div class="blog-card-meta mb-3">
                    <a href="category.php?slug=<?php echo $featured_post['category_slug']; ?>" 
                       class="category-badge" style="background: <?php echo $featured_post['color'] ?? 'var(--gradient-primary)'; ?>">
                        <?php echo sanitize($featured_post['category_name']); ?>
                    </a>
                    <span class="post-date">
                        <i class="far fa-clock me-1"></i>
                        <?php echo date('d M Y', strtotime($featured_post['published_at'])); ?>
                    </span>
                </div>
                <h2 class="blog-card-title">
                    <a href="<?php echo $featured_post['slug']; ?>.html">
                        <?php echo sanitize($featured_post['title']); ?>
                    </a>
                </h2>
                <p class="blog-card-excerpt">
                    <?php 
                    $excerpt = $featured_post['excerpt'] ?: $featured_post['content'];
                    echo strip_tags(substr($excerpt, 0, 200)); 
                    ?>...
                </p>
                <div class="blog-card-footer" style="border: none; padding-top: 0;">
                    <div class="author-info">
                        <i class="fas fa-user-circle"></i>
                        <?php echo sanitize($featured_post['display_name']); ?>
                    </div>
                    <div class="post-stats">
                        <div class="stat-item">
                            <i class="far fa-eye"></i>
                            <?php echo $featured_post['view_count'] ?: 0; ?>
                        </div>
                        <div class="stat-item">
                            <i class="far fa-comment"></i>
                            <?php echo $featured_post['comment_count'] ?: 0; ?>
                        </div>
                        <div class="stat-item">
                            <i class="far fa-clock"></i>
                            <?php echo $featured_post['reading_time'] ?: '3'; ?> dk
                        </div>
                    </div>
                </div>
            </div>
            <?php if($featured_post['featured_image']): ?>
                <div class="blog-card-image">
                    <img src="<?php echo $featured_post['featured_image']; ?>" 
                         alt="<?php echo sanitize($featured_post['title']); ?>"
                         onerror="this.src='https://via.placeholder.com/600x400/2E86C1/ffffff?text=Resim+Yüklenemedi'">
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Diğer Yazılar -->
    <?php if(empty($other_posts) && !$featured_post): ?>
        <div class="col-12 text-center py-5">
            <div class="empty-state">
                <i class="fas fa-inbox fa-4x mb-4"></i>
                <h3 style="color: var(--dark-color);">Henüz yazı bulunmuyor</h3>
                <p class="text-muted">Admin panelinden yazı ekleyerek başlayabilirsiniz.</p>
                <a href="admin/login.php" class="btn btn-primary mt-3">
                    <i class="fas fa-cog me-2"></i>Admin Paneli
                </a>
            </div>
        </div>
    <?php else: ?>
        <?php foreach($other_posts as $post): ?>
            <article class="blog-card fade-in-up">
                <?php if($post['featured_image']): ?>
                    <div class="blog-card-image">
                        <img src="<?php echo $post['featured_image']; ?>" 
                             alt="<?php echo sanitize($post['title']); ?>"
                             onerror="this.src='https://via.placeholder.com/400x250/2E86C1/ffffff?text=Resim+Yüklenemedi'">
                    </div>
                <?php endif; ?>
                <div class="blog-card-content">
                    <div class="blog-card-meta">
                        <a href="category.php?slug=<?php echo $post['category_slug']; ?>" 
                           class="category-badge" style="background: <?php echo $post['color'] ?? 'var(--gradient-primary)'; ?>">
                            <?php echo sanitize($post['category_name']); ?>
                        </a>
                        <span class="post-date">
                            <i class="far fa-clock me-1"></i>
                            <?php echo date('d M Y', strtotime($post['published_at'])); ?>
                        </span>
                    </div>
                    <h3 class="blog-card-title">
                        <a href="<?php echo $post['slug']; ?>.html">
                            <?php echo sanitize($post['title']); ?>
                        </a>
                    </h3>
                    <p class="blog-card-excerpt">
                        <?php 
                        $excerpt = $post['excerpt'] ?: $post['content'];
                        echo strip_tags(substr($excerpt, 0, 120)); 
                        ?>...
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
                        </div>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Sayfalama -->
<?php if($totalPages > 1): ?>
    <nav aria-label="Sayfalama" class="mt-5">
        <ul class="pagination justify-content-center">
            <?php if($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
            <?php endif; ?>

            <?php 
            $startPage = max(1, $page - 2);
            $endPage = min($totalPages, $page + 2);
            
            for($i = $startPage; $i <= $endPage; $i++): ?>
                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <?php if($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>

</div> <!-- container -->

<?php require_once 'themes/google-modern/footer.php'; ?>