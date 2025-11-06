<?php
require_once 'includes/config.php';

if(!isset($_GET['q']) && !isset($_GET['tag'])) {
    redirect('index.php');
}

$search_query = isset($_GET['q']) ? sanitize($_GET['q']) : '';
$tag_query = isset($_GET['tag']) ? sanitize($_GET['tag']) : '';

// Sayfalama
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 9;
$offset = ($page - 1) * $limit;

// Arama sorgusu oluştur
$where_conditions = ["p.status = 'published'"];
$params = [];

if(!empty($search_query)) {
    $where_conditions[] = "(p.title LIKE ? OR p.content LIKE ? OR p.excerpt LIKE ?)";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
    $pageTitle = "'$search_query' arama sonuçları - " . SITE_NAME;
} elseif(!empty($tag_query)) {
    $where_conditions[] = "t.slug = ?";
    $params[] = $tag_query;
    $pageTitle = "'$tag_query' etiketli yazılar - " . SITE_NAME;
}

$where_sql = implode(" AND ", $where_conditions);

// Yazıları getir
$posts_sql = "SELECT DISTINCT p.*, u.display_name, c.name as category_name, c.slug as category_slug 
              FROM posts p 
              LEFT JOIN users u ON p.author_id = u.id 
              LEFT JOIN categories c ON p.category_id = c.id 
              LEFT JOIN post_tags pt ON p.id = pt.post_id 
              LEFT JOIN tags t ON pt.tag_id = t.id 
              WHERE $where_sql 
              ORDER BY p.published_at DESC 
              LIMIT $limit OFFSET $offset";

$posts_stmt = $pdo->prepare($posts_sql);
$posts_stmt->execute($params);
$posts = $posts_stmt->fetchAll();

// Toplam yazı sayısı
$total_sql = "SELECT COUNT(DISTINCT p.id) as total 
              FROM posts p 
              LEFT JOIN post_tags pt ON p.id = pt.post_id 
              LEFT JOIN tags t ON pt.tag_id = t.id 
              WHERE $where_sql";

$total_stmt = $pdo->prepare($total_sql);
$total_stmt->execute($params);
$totalPosts = $total_stmt->fetch()['total'];
$totalPages = ceil($totalPosts / $limit);

// SEO Meta
$pageDescription = "Arama sonuçları: " . ($search_query ?: $tag_query);
$pageKeywords = $search_query ?: $tag_query;
$canonicalUrl = SITE_URL . '/search.php?' . ($search_query ? "q=$search_query" : "tag=$tag_query");

require_once 'themes/google-modern/header.php';
?>

<div class="container">
    <!-- Arama Başlığı -->
    <div class="row mb-5">
        <div class="col-12 text-center">
            <div class="search-header">
                <span class="category-badge mb-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="fas fa-search me-2"></i>
                    <?php echo $search_query ? 'Arama Sonuçları' : 'Etiket'; ?>
                </span>
                <h1 class="display-5 fw-bold mb-3">
                    <?php if($search_query): ?>
                        "<?php echo sanitize($search_query); ?>"
                    <?php else: ?>
                        #<?php echo sanitize($tag_query); ?>
                    <?php endif; ?>
                </h1>
                <p class="lead text-muted">
                    <?php echo $totalPosts; ?> sonuç bulundu
                </p>
                
                <!-- Arama Formu -->
                <div class="row justify-content-center mt-4">
                    <div class="col-md-6">
                        <form action="search.php" method="GET" class="d-flex">
                            <input type="text" name="q" class="form-control me-2" 
                                   placeholder="Başka bir şey ara..." 
                                   value="<?php echo $search_query; ?>">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Yazılar Grid -->
    <div class="blog-grid">
        <?php if(empty($posts)): ?>
            <div class="col-12 text-center py-5">
                <div class="empty-state">
                    <i class="fas fa-search fa-4x mb-4" style="color: #dee2e6;"></i>
                    <h3 style="color: #6c757d;">Aramanızla eşleşen sonuç bulunamadı</h3>
                    <p class="text-muted mb-4">Farklı anahtar kelimelerle tekrar deneyin.</p>
                    <a href="index.php" class="btn btn-primary">
                        <i class="fas fa-home me-2"></i>Ana Sayfaya Dön
                    </a>
                </div>
            </div>
        <?php else: ?>
            <?php foreach($posts as $post): ?>
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
                               class="category-badge">
                                <?php echo sanitize($post['category_name']); ?>
                            </a>
                            <span class="post-date">
                                <i class="far fa-clock me-1"></i>
                                <?php echo date('d M Y', strtotime($post['published_at'])); ?>
                            </span>
                        </div>
                        <h3 class="blog-card-title">
                            <a href="<?php echo $post['slug']; ?>.html">
                                <?php 
                                $title = sanitize($post['title']);
                                if($search_query) {
                                    // Arama kelimesini başlıkta vurgula
                                    $title = preg_replace("/\b(" . preg_quote($search_query, '/') . ")\b/i", '<mark>$1</mark>', $title);
                                }
                                echo $title;
                                ?>
                            </a>
                        </h3>
                        <p class="blog-card-excerpt">
                            <?php 
                            $content = strip_tags($post['excerpt'] ?: $post['content']);
                            if($search_query) {
                                // Arama kelimesini içerikte vurgula
                                $content = preg_replace("/\b(" . preg_quote($search_query, '/') . ")\b/i", '<mark>$1</mark>', $content);
                            }
                            echo substr($content, 0, 120) . '...'; 
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
        <nav aria-label="Sayfalama" class="mt-5">
            <ul class="pagination justify-content-center">
                <?php if($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="search.php?<?php 
                            echo $search_query ? "q=" . urlencode($search_query) : "tag=" . urlencode($tag_query); 
                        ?>&page=<?php echo $page - 1; ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                <?php endif; ?>

                <?php 
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $page + 2);
                
                for($i = $startPage; $i <= $endPage; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="search.php?<?php 
                            echo $search_query ? "q=" . urlencode($search_query) : "tag=" . urlencode($tag_query); 
                        ?>&page=<?php echo $i; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <?php if($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="search.php?<?php 
                            echo $search_query ? "q=" . urlencode($search_query) : "tag=" . urlencode($tag_query); 
                        ?>&page=<?php echo $page + 1; ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<style>
    /* Özel Renk Paleti */
    :root {
        --primary-color: #667eea;
        --secondary-color: #764ba2;
        --accent-color: #f093fb;
        --success-color: #4cd964;
        --warning-color: #ff9500;
        --danger-color: #ff3b30;
        --info-color: #5ac8fa;
        --dark-color: #343a40;
        --light-color: #f8f9fa;
        --gray-color: #6c757d;
        --text-dark: #2c3e50;
        --text-light: #7f8c8d;
        --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --gradient-success: linear-gradient(135deg, #4cd964 0%, #5ac8fa 100%);
        --gradient-warning: linear-gradient(135deg, #ff9500 0%, #ffcc00 100%);
        --gradient-danger: linear-gradient(135deg, #ff3b30 0%, #ff2d55 100%);
    }
    
    .search-header {
        padding: 3rem 0;
    }
    
    .category-badge {
        background: var(--gradient-primary);
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 25px;
        font-size: 0.9rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
    }
    
    .category-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        color: white;
        text-decoration: none;
    }
    
    .blog-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.05);
    }
    
    .blog-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 35px rgba(0,0,0,0.15);
    }
    
    .blog-card-image {
        height: 220px;
        overflow: hidden;
        position: relative;
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
    
    .blog-card-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        font-size: 0.85rem;
    }
    
    .post-date {
        color: var(--text-light);
        font-size: 0.8rem;
    }
    
    .blog-card-title {
        font-size: 1.3rem;
        font-weight: 700;
        line-height: 1.4;
        margin-bottom: 1rem;
    }
    
    .blog-card-title a {
        color: var(--text-dark);
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .blog-card-title a:hover {
        color: var(--primary-color);
    }
    
    .blog-card-excerpt {
        color: var(--text-light);
        line-height: 1.6;
        margin-bottom: 1.5rem;
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
        border-top: 1px solid rgba(0,0,0,0.1);
    }
    
    .author-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        color: var(--text-light);
    }
    
    .post-stats {
        display: flex;
        gap: 1rem;
        font-size: 0.85rem;
        color: var(--text-light);
    }
    
    .stat-item {
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }
    
    .btn-primary {
        background: var(--gradient-primary);
        border: none;
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
        border: 1px solid #dee2e6;
        margin: 0 2px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .page-link:hover {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }
    
    mark {
        background: linear-gradient(120deg, #f093fb 0%, #f5576c 100%);
        background-repeat: no-repeat;
        background-size: 100% 0.2em;
        background-position: 0 88%;
        padding: 0.1rem 0.2rem;
        border-radius: 3px;
        color: inherit;
    }
    
    .empty-state {
        padding: 3rem 0;
    }
    
    @media (max-width: 768px) {
        .blog-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        
        .blog-card-meta {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
        
        .search-header h1 {
            font-size: 2rem;
        }
    }
</style>

<?php require_once 'themes/google-modern/footer.php'; ?>