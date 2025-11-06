<?php
require_once 'includes/config.php';

$pageTitle = SITE_NAME . " - " . SITE_DESCRIPTION;
$pageDescription = SITE_DESCRIPTION;
$pageKeywords = SITE_KEYWORDS;

// Sayfalama
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 9;
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
$other_posts_sql = "SELECT p.*, u.display_name, c.name as category_name, c.slug as category_slug, c.color
                    FROM posts p 
                    LEFT JOIN users u ON p.author_id = u.id 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    WHERE p.status = 'published' AND p.id != ?
                    ORDER BY p.published_at DESC 
                    LIMIT $limit OFFSET $offset";

$other_posts = $pdo->prepare($other_posts_sql);
$other_posts->execute([$featured_post ? $featured_post['id'] : 0]);
$other_posts = $other_posts->fetchAll();

// Toplam yazı sayısı
$totalStmt = $pdo->query("SELECT COUNT(*) as total FROM posts WHERE status = 'published'");
$totalPosts = $totalStmt->fetch()['total'];
$totalPages = ceil($totalPosts / $limit);

require_once 'themes/google-modern/header.php';
?>

<div class="blog-grid">
    <?php if($featured_post): ?>
        <!-- Öne Çıkan Yazı -->
        <div class="featured-post fade-in-up">
            <div class="featured-content">
                <span class="featured-badge">ÖNE ÇIKAN</span>
                <div class="blog-card-meta mb-3">
                    <a href="category.php?slug=<?php echo $featured_post['category_slug']; ?>" 
                       class="category-badge" style="background: <?php echo $featured_post['color'] ?? '#FF6B35'; ?>">
                        <?php echo sanitize($featured_post['category_name']); ?>
                    </a>
                    <span class="post-date">
                        <i class="far fa-clock me-1"></i>
                        <?php echo date('d M Y', strtotime($featured_post['published_at'])); ?>
                    </span>
                </div>
                <h2 class="blog-card-title">
                    <a href="<?php echo $featured_post['slug']; ?>.html">
                        <?php echo decodeHtml($featured_post['title']); ?>
                    </a>
                </h2>
                <p class="blog-card-excerpt">
                    <?php echo strip_tags(substr($featured_post['excerpt'] ?: $featured_post['content'], 0, 200)); ?>...
                </p>
                <div class="blog-card-footer" style="border: none; padding-top: 0;">
                    <div class="author-info">
                        <i class="fas fa-user-circle"></i>
                        <?php echo sanitize($featured_post['display_name']); ?>
                    </div>
                    <div class="post-stats">
                        <div class="stat-item">
                            <i class="far fa-eye"></i>
                            <?php echo $featured_post['view_count']; ?>
                        </div>
                        <div class="stat-item">
                            <i class="far fa-comment"></i>
                            <?php echo $featured_post['comment_count']; ?>
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
                         alt="<?php echo sanitize($featured_post['title']); ?>">
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Diğer Yazılar -->
    <?php if(empty($other_posts) && !$featured_post): ?>
        <div class="col-12 text-center py-5">
            <div class="empty-state">
                <i class="fas fa-inbox fa-4x mb-4" style="color: #dee2e6;"></i>
                <h3 style="color: #4A5568;">Henüz yazı bulunmuyor</h3>
                <p class="text-muted">Yakında yeni yazılar eklenecek.</p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach($other_posts as $post): ?>
            <article class="blog-card fade-in-up">
                <?php if($post['featured_image']): ?>
                    <div class="blog-card-image">
                        <img src="<?php echo $post['featured_image']; ?>" 
                             alt="<?php echo sanitize($post['title']); ?>">
                    </div>
                <?php endif; ?>
                <div class="blog-card-content">
                    <div class="blog-card-meta">
                        <a href="category.php?slug=<?php echo $post['category_slug']; ?>" 
                           class="category-badge" style="background: <?php echo $post['color'] ?? '#FF6B35'; ?>">
                            <?php echo sanitize($post['category_name']); ?>
                        </a>
                        <span class="post-date">
                            <i class="far fa-clock me-1"></i>
                            <?php echo date('d M Y', strtotime($post['published_at'])); ?>
                        </span>
                    </div>
                    <h3 class="blog-card-title">
                        <a href="<?php echo $post['slug']; ?>.html">
                            <?php echo decodeHtml($post['title']); ?>
                        </a>
                    </h3>
                    <p class="blog-card-excerpt">
                        <?php echo strip_tags(substr($post['excerpt'] ?: $post['content'], 0, 120)); ?>...
                    </p>
                    <div class="blog-card-footer">
                        <div class="author-info">
                            <i class="fas fa-user-circle"></i>
                            <?php echo sanitize($post['display_name']); ?>
                        </div>
                        <div class="post-stats">
                            <div class="stat-item">
                                <i class="far fa-eye"></i>
                                <?php echo $post['view_count']; ?>
                            </div>
                            <div class="stat-item">
                                <i class="far fa-comment"></i>
                                <?php echo $post['comment_count']; ?>
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
    <nav aria-label="Sayfalama">
        <ul class="pagination">
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