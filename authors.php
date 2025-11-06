<?php
require_once 'includes/config.php';

// Sayfalama
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12; // Sayfa başına 12 yazar
$offset = ($page - 1) * $limit;

// Yazarları getir (yazı sayısına göre)
$authors_sql = "SELECT u.id, u.username, u.display_name, u.email, u.bio, u.avatar, 
                       COUNT(p.id) as post_count,
                       SUM(p.view_count) as total_views,
                       MAX(p.published_at) as last_post_date
                FROM users u 
                LEFT JOIN posts p ON u.id = p.author_id AND p.status = 'published'
                WHERE u.id IN (SELECT DISTINCT author_id FROM posts WHERE status = 'published')
                GROUP BY u.id 
                ORDER BY post_count DESC, total_views DESC 
                LIMIT $limit OFFSET $offset";

$authors = $pdo->query($authors_sql)->fetchAll();

// Toplam yazar sayısı
$total_stmt = $pdo->query("SELECT COUNT(DISTINCT author_id) as total FROM posts WHERE status = 'published'");
$totalAuthors = $total_stmt->fetch()['total'];
$totalPages = ceil($totalAuthors / $limit);

// SEO Meta
$pageTitle = 'Yazarlar - ' . SITE_NAME;
$pageDescription = SITE_NAME . ' yazarları ve makaleleri. Toplam ' . $totalAuthors . ' yazar.';
$pageKeywords = 'yazarlar, yazar listesi, makale yazarları, ' . SITE_KEYWORDS;
$canonicalUrl = SITE_URL . '/authors.php';

require_once 'themes/google-modern/header.php';
?>

<style>
/* Özgün Renk Paleti */
:root {
    --primary-color: #667eea;
    --secondary-color: #764ba2;
    --accent-color: #f093fb;
    --success-color: #4fd1c5;
    --warning-color: #fed7d7;
    --danger-color: #fc8181;
    --info-color: #63b3ed;
    --light-color: #f8f9fa;
    --dark-color: #2d3748;
    --gray-color: #718096;
    --gray-light: #e2e8f0;
}

/* Yazarlar Sayfası Özel Stiller */
.authors-header {
    padding: 3rem 0;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.05));
    border-radius: 20px;
    margin-bottom: 3rem;
    border: 1px solid rgba(102, 126, 234, 0.1);
    text-align: center;
}

.authors-stats {
    font-size: 1.1rem;
    margin-top: 1rem;
    color: var(--gray-color);
}

/* Grid Düzeni */
.authors-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

/* Yazar Kartları */
.author-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border: 1px solid var(--gray-light);
    display: flex;
    flex-direction: column;
    height: 100%;
}

.author-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
}

.author-header {
    padding: 2rem 2rem 1rem;
    text-align: center;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.08), rgba(79, 209, 197, 0.08));
    position: relative;
}

.author-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    margin: 0 auto 1rem;
    border: 4px solid white;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    overflow: hidden;
    background: var(--light-color);
}

.author-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.author-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    color: white;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.author-content {
    padding: 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.author-name {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--dark-color);
    margin-bottom: 0.5rem;
    text-align: center;
}

.author-username {
    color: var(--gray-color);
    text-align: center;
    margin-bottom: 1rem;
    font-size: 0.9rem;
}

.author-bio {
    color: var(--gray-color);
    line-height: 1.6;
    margin-bottom: 1.5rem;
    flex: 1;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-align: center;
}

.author-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding: 1rem;
    background: var(--light-color);
    border-radius: 12px;
    border: 1px solid var(--gray-light);
}

.stat-item {
    text-align: center;
}

.stat-number {
    display: block;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-color);
    line-height: 1;
}

.stat-label {
    font-size: 0.8rem;
    color: var(--gray-color);
    margin-top: 0.3rem;
}

.author-footer {
    padding-top: 1rem;
    border-top: 1px solid var(--gray-light);
    text-align: center;
}

.btn-author {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    border: none;
    box-shadow: 0 2px 10px rgba(102, 126, 234, 0.3);
}

.btn-author:hover {
    background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

/* Sıralama Filtreleri */
.sorting-filters {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.sort-btn {
    padding: 0.75rem 1.5rem;
    border: 2px solid var(--primary-color);
    background: white;
    color: var(--primary-color);
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.sort-btn:hover,
.sort-btn.active {
    background: var(--primary-color);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

/* Boş Durum */
.empty-state {
    padding: 4rem 2rem;
    text-align: center;
}

.empty-state i {
    font-size: 4rem;
    color: var(--gray-light);
    margin-bottom: 1rem;
}

/* Sayfalama */
.pagination .page-link {
    border: 1px solid var(--gray-light);
    color: var(--gray-color);
    padding: 0.75rem 1.25rem;
    margin: 0 0.25rem;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.pagination .page-link:hover {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.pagination .page-item.active .page-link {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

/* Responsive Tasarım */
@media (max-width: 768px) {
    .authors-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .authors-header {
        padding: 2rem 1rem;
        margin-bottom: 2rem;
    }
    
    .author-header {
        padding: 1.5rem 1.5rem 1rem;
    }
    
    .author-avatar {
        width: 80px;
        height: 80px;
    }
    
    .author-content {
        padding: 1.25rem;
    }
    
    .author-stats {
        grid-template-columns: repeat(3, 1fr);
        gap: 0.5rem;
        padding: 0.75rem;
    }
    
    .stat-number {
        font-size: 1.25rem;
    }
    
    .sorting-filters {
        flex-direction: column;
        align-items: center;
    }
    
    .sort-btn {
        width: 200px;
        text-align: center;
    }
}

@media (max-width: 480px) {
    .container {
        padding-left: 15px;
        padding-right: 15px;
    }
    
    .author-stats {
        grid-template-columns: 1fr;
        gap: 0.75rem;
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

/* Yazar Seviye Renkleri */
.badge-popular {
    background: linear-gradient(135deg, #ff6b6b, #ffa726);
}

.badge-active {
    background: linear-gradient(135deg, #4ecdc4, #44a08d);
}

.badge-new {
    background: linear-gradient(135deg, #667eea, #764ba2);
}

/* Ek Gradyan Efektleri */
.gradient-primary {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
}

.gradient-success {
    background: linear-gradient(135deg, var(--success-color), #38b2ac);
}

.gradient-warning {
    background: linear-gradient(135deg, #f6ad55, #ed8936);
}

.gradient-danger {
    background: linear-gradient(135deg, var(--danger-color), #e53e3e);
}
</style>

<div class="container">
    <!-- Yazarlar Başlığı -->
    <div class="row">
        <div class="col-12">
            <div class="authors-header">
                <h1 class="display-5 fw-bold mb-3">Yazarlar</h1>
                <p class="lead text-muted max-w-2xl mx-auto"><?php echo SITE_NAME; ?> platformundaki değerli yazarlarımız ve makaleleri</p>
                <div class="authors-stats">
                    <i class="fas fa-users me-2"></i>
                    Toplam <strong><?php echo $totalAuthors; ?> yazar</strong> 
                    <?php if($totalPages > 1): ?>
                        - <strong><?php echo $totalPages; ?> sayfa</strong>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Sıralama Filtreleri -->
    <div class="sorting-filters">
        <a href="authors.php?sort=posts" class="sort-btn active">
            <i class="fas fa-sort-amount-down me-2"></i>En Çok Yazı
        </a>
        <a href="authors.php?sort=views" class="sort-btn">
            <i class="fas fa-eye me-2"></i>En Çok Görüntülenme
        </a>
        <a href="authors.php?sort=recent" class="sort-btn">
            <i class="fas fa-clock me-2"></i>En Yeni
        </a>
    </div>

    <!-- Yazarlar Grid -->
    <?php if(empty($authors)): ?>
        <div class="row">
            <div class="col-12">
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <h3 style="color: var(--gray-color);">Henüz yazar bulunmuyor</h3>
                    <p class="text-muted mb-4">Yakında yeni yazarlar ve makaleler eklenecek.</p>
                    <a href="index.php" class="btn btn-primary gradient-primary border-0">
                        <i class="fas fa-arrow-left me-2"></i>Ana Sayfaya Dön
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="authors-grid">
            <?php foreach($authors as $author): 
                // Yazar seviyesini belirle
                $badge_class = '';
                $badge_text = '';
                
                if($author['post_count'] >= 20) {
                    $badge_class = 'badge-popular';
                    $badge_text = 'Popüler Yazar';
                } elseif($author['post_count'] >= 10) {
                    $badge_class = 'badge-active';
                    $badge_text = 'Aktif Yazar';
                } else {
                    $badge_class = 'badge-new';
                    $badge_text = 'Yeni Yazar';
                }
                
                // Avatar URL'si
                $avatar_url = $author['avatar'] ?: 'https://ui-avatars.com/api/?name=' . urlencode($author['display_name']) . '&background=667eea&color=fff&size=200';
                
                // Son yazı tarihi
                $last_post = $author['last_post_date'] ? date('d M Y', strtotime($author['last_post_date'])) : 'Henüz yok';
            ?>
                <div class="author-card fade-in-up">
                    <div class="author-header">
                        <div class="author-avatar">
                            <img src="<?php echo $avatar_url; ?>" 
                                 alt="<?php echo sanitize($author['display_name']); ?>"
                                 loading="lazy"
                                 onerror="this.src='https://ui-avatars.com/api/?name=Yazar&background=667eea&color=fff&size=200'">
                        </div>
                        <span class="author-badge <?php echo $badge_class; ?>">
                            <?php echo $badge_text; ?>
                        </span>
                    </div>
                    
                    <div class="author-content">
                        <h2 class="author-name"><?php echo sanitize($author['display_name']); ?></h2>
                        <div class="author-username">@<?php echo sanitize($author['username']); ?></div>
                        
                        <?php if($author['bio']): ?>
                            <p class="author-bio"><?php echo sanitize($author['bio']); ?></p>
                        <?php else: ?>
                            <p class="author-bio text-muted"><?php echo sanitize($author['display_name']); ?> hakkında henüz bir bilgi bulunmuyor.</p>
                        <?php endif; ?>
                        
                        <div class="author-stats">
                            <div class="stat-item">
                                <span class="stat-number"><?php echo $author['post_count']; ?></span>
                                <span class="stat-label">Yazı</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number"><?php echo number_format($author['total_views']); ?></span>
                                <span class="stat-label">Görüntülenme</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number"><?php echo round($author['total_views'] / max($author['post_count'], 1)); ?></span>
                                <span class="stat-label">Ort. Görüntülenme</span>
                            </div>
                        </div>
                        
                        <div class="author-footer">
                            <a href="author.php?id=<?php echo $author['id']; ?>" class="btn-author">
                                <i class="fas fa-user-circle me-1"></i>Profile Git
                            </a>
                        </div>
                    </div>
                </div>
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
                        <a class="page-link" href="authors.php?page=<?php echo $page - 1; ?>">
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
                        <a class="page-link" href="authors.php?page=1">1</a>
                    </li>
                    <?php if($startPage > 2): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    <?php endif;
                endif;
                
                for($i = $startPage; $i <= $endPage; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="authors.php?page=<?php echo $i; ?>">
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
                        <a class="page-link" href="authors.php?page=<?php echo $totalPages; ?>">
                            <?php echo $totalPages; ?>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Sonraki Sayfa -->
                <?php if($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="authors.php?page=<?php echo $page + 1; ?>">
                            Sonraki <i class="fas fa-chevron-right ms-1"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
            
            <!-- Sayfa Bilgisi -->
            <div class="text-center mt-3 text-muted">
                <small>
                    Sayfa <?php echo $page; ?> / <?php echo $totalPages; ?> 
                    - Toplam <?php echo $totalAuthors; ?> yazar
                </small>
            </div>
        </nav>
    <?php endif; ?>
</div>

<?php require_once 'themes/google-modern/footer.php'; ?>