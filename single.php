<?php
require_once 'includes/config.php';

// Debug modu
error_log("=== SINGLE.PHP ÇALIŞIYOR ===");

// URL'den slug'ı al
$slug = '';
if(isset($_SERVER['PATH_INFO'])) {
    $slug = str_replace('.html', '', trim($_SERVER['PATH_INFO'], '/'));
    error_log("PATH_INFO slug: " . $slug);
} elseif(isset($_GET['slug'])) {
    $slug = sanitize($_GET['slug']);
    error_log("GET slug: " . $slug);
} else {
    error_log("Slug bulunamadı, ana sayfaya yönlendiriliyor");
    redirect('index.php');
}

// Slug'ı temizle
$slug = sanitize($slug);
error_log("Temizlenmiş slug: " . $slug);

// Yazıyı getir
$stmt = $pdo->prepare("
    SELECT p.*, u.display_name, u.bio as author_bio, c.name as category_name, c.slug as category_slug 
    FROM posts p 
    LEFT JOIN users u ON p.author_id = u.id 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.slug = ? AND p.status = 'published'
");
$stmt->execute([$slug]);
$post = $stmt->fetch();

error_log("Yazı bulundu: " . ($post ? 'EVET - ' . $post['title'] : 'HAYIR'));

if(!$post) {
    error_log("Yazı bulunamadı, 404 sayfasına yönlendiriliyor");
    
    // Tüm yazıları listele (debug için)
    $all_posts = $pdo->query("SELECT id, title, slug, status FROM posts ORDER BY id DESC LIMIT 10")->fetchAll();
    error_log("Son 10 yazı:");
    foreach($all_posts as $p) {
        error_log(" - " . $p['id'] . ": " . $p['title'] . " (slug: " . $p['slug'] . ", status: " . $p['status'] . ")");
    }
    
    http_response_code(404);
    $pageTitle = "Yazı Bulunamadı - " . SITE_NAME;
    require_once 'themes/google-modern/header.php';
    ?>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center py-5">
                <div class="error-page">
                    <i class="fas fa-exclamation-triangle fa-5x text-warning mb-4"></i>
                    <h1 class="display-4 fw-bold text-muted mb-3">404</h1>
                    <h2 class="h3 mb-4">Yazı Bulunamadı</h2>
                    <p class="lead text-muted mb-5">
                        Aradığınız yazı silinmiş, taşınmış veya adresi yanlış yazmış olabilirsiniz.
                    </p>
                    <div class="debug-info bg-light p-4 rounded mb-4 text-start">
                        <h5>Debug Bilgisi:</h5>
                        <p><strong>Aranan Slug:</strong> <?php echo $slug; ?></p>
                        <p><strong>Mevcut Yazılar:</strong></p>
                        <ul>
                            <?php foreach($all_posts as $p): ?>
                                <li>
                                    <a href="<?php echo $p['slug']; ?>.html"><?php echo $p['title']; ?></a> 
                                    (Status: <?php echo $p['status']; ?>)
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="index.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-home me-2"></i>Ana Sayfaya Dön
                        </a>
                        <a href="javascript:history.back()" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-arrow-left me-2"></i>Geri Dön
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    require_once 'themes/google-modern/footer.php';
    exit;
}

// Görüntüleme sayısını artır
$pdo->prepare("UPDATE posts SET view_count = view_count + 1 WHERE id = ?")->execute([$post['id']]);

// Etiketleri getir
$tags_stmt = $pdo->prepare("
    SELECT t.name, t.slug 
    FROM tags t 
    INNER JOIN post_tags pt ON t.id = pt.tag_id 
    WHERE pt.post_id = ?
");
$tags_stmt->execute([$post['id']]);
$tags = $tags_stmt->fetchAll();

// Yorumları getir
$comments_stmt = $pdo->prepare("
    SELECT * FROM comments 
    WHERE post_id = ? AND status = 'approved' 
    ORDER BY created_at DESC
");
$comments_stmt->execute([$post['id']]);
$comments = $comments_stmt->fetchAll();

// Benzer yazılar
$similar_posts_stmt = $pdo->prepare("
    SELECT p.title, p.slug, p.featured_image, p.excerpt, u.display_name
    FROM posts p
    LEFT JOIN users u ON p.author_id = u.id
    WHERE p.category_id = ? AND p.id != ? AND p.status = 'published'
    ORDER BY p.published_at DESC
    LIMIT 3
");
$similar_posts_stmt->execute([$post['category_id'], $post['id']]);
$similar_posts = $similar_posts_stmt->fetchAll();

// Yorum gönderme
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_comment'])) {
    $author_name = sanitize($_POST['author_name']);
    $author_email = sanitize($_POST['author_email']);
    $author_website = sanitize($_POST['author_website']);
    $comment_content = sanitize($_POST['comment_content']);
    $spam_answer = strtolower(trim($_POST['spam_answer']));
    
    // Spam kontrolü
    if($spam_answer !== 'ankara') {
        $_SESSION['error_message'] = "Spam sorusunu yanlış cevapladınız!";
    } elseif(empty($author_name) || empty($comment_content)) {
        $_SESSION['error_message'] = "Lütfen gerekli alanları doldurun!";
    } else {
        // Yorumu kaydet
        $comment_stmt = $pdo->prepare("
            INSERT INTO comments (post_id, author_name, author_email, author_website, content, author_ip, user_agent, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
        ");
        
        $comment_stmt->execute([
            $post['id'],
            $author_name,
            $author_email,
            $author_website,
            $comment_content,
            $_SERVER['REMOTE_ADDR'],
            $_SERVER['HTTP_USER_AGENT']
        ]);
        
        $_SESSION['success_message'] = "Yorumunuz başarıyla gönderildi! Onaylandıktan sonra yayınlanacak.";
        
        // Yorum sayısını güncelle
        $pdo->prepare("UPDATE posts SET comment_count = comment_count + 1 WHERE id = ?")->execute([$post['id']]);
        
        redirect($post['slug'] . '.html');
    }
}

// SEO Meta
$pageTitle = $post['meta_title'] ?: $post['title'] . ' - ' . SITE_NAME;
$pageDescription = $post['meta_description'] ?: strip_tags(substr($post['content'], 0, 160));
$pageKeywords = $post['meta_keywords'] ?: SITE_KEYWORDS;
$canonicalUrl = SITE_URL . '/' . $post['slug'] . '.html';

// Paylaşım için gerekli değişkenler
$currentUrl = SITE_URL . '/' . $post['slug'] . '.html';
$excerpt = strip_tags($post['excerpt'] ?: substr($post['content'], 0, 200));

require_once 'themes/google-modern/header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Debug Info (Sadece admin görsün) -->
            <?php if(isset($_SESSION['admin_logged_in'])): ?>
            <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
                <h6><i class="fas fa-bug me-2"></i>Debug Bilgisi</h6>
                <p class="mb-1"><strong>Slug:</strong> <?php echo $post['slug']; ?></p>
                <p class="mb-1"><strong>ID:</strong> <?php echo $post['id']; ?></p>
                <p class="mb-1"><strong>Status:</strong> <?php echo $post['status']; ?></p>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <article class="single-post">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Ana Sayfa</a></li>
                        <li class="breadcrumb-item"><a href="category.php?slug=<?php echo $post['category_slug']; ?>">
                            <?php echo sanitize($post['category_name']); ?>
                        </a></li>
                        <li class="breadcrumb-item active"><?php echo sanitize($post['title']); ?></li>
                    </ol>
                </nav>

                <!-- Öne Çıkan Görsel -->
                <?php if($post['featured_image']): ?>
                    <div class="featured-image mb-4">
                        <img src="<?php echo $post['featured_image']; ?>" 
                             alt="<?php echo sanitize($post['title']); ?>" 
                             class="img-fluid rounded-3 shadow">
                    </div>
                <?php endif; ?>
                
                <!-- Yazı Başlığı ve Meta -->
                <header class="post-header text-center mb-5">
                    <div class="post-meta mb-3">
                        <a href="category.php?slug=<?php echo $post['category_slug']; ?>" 
                           class="category-badge me-3">
                            <?php echo sanitize($post['category_name']); ?>
                        </a>
                        <span class="post-date me-3">
                            <i class="far fa-calendar me-1"></i>
                            <?php echo date('d M Y', strtotime($post['published_at'])); ?>
                        </span>
                        <span class="reading-time">
                            <i class="far fa-clock me-1"></i>
                            <?php echo $post['reading_time'] ?: '3'; ?> dk okuma
                        </span>
                    </div>
                    <h1 class="post-title display-5 fw-bold mb-3"><?php echo sanitize($post['title']); ?></h1>
                    
                    <!-- Yazar Bilgisi -->
                    <div class="author-card d-flex align-items-center justify-content-center mt-4">
                        <div class="author-avatar me-3">
                            <i class="fas fa-user-circle fa-2x text-muted"></i>
                        </div>
                        <div class="author-info text-start">
                            <div class="author-name fw-bold"><?php echo sanitize($post['display_name']); ?></div>
                            <div class="author-bio text-muted small">
                                <?php echo $post['author_bio'] ?: 'Blog Yazarı'; ?>
                            </div>
                        </div>
                    </div>
                </header>
                
                <!-- Yazı İçeriği -->
                <div class="post-content fs-5 lh-base">
                    <?php echo $post['content']; ?>
                </div>
                
                <!-- Etiketler -->
                <?php if(!empty($tags)): ?>
                    <div class="post-tags mt-5 pt-4 border-top">
                        <h6 class="mb-3">
                            <i class="fas fa-tags me-2 text-muted"></i>Etiketler:
                        </h6>
                        <div class="tags-container">
                            <?php foreach($tags as $tag): ?>
                                <a href="search.php?tag=<?php echo $tag['slug']; ?>" 
                                   class="tag-badge">
                                    #<?php echo sanitize($tag['name']); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Yazı İstatistikleri -->
                <footer class="post-footer mt-5 pt-4 border-top">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="post-stats">
                                <span class="stat-item me-4">
                                    <i class="far fa-eye me-1 text-primary"></i>
                                    <?php echo $post['view_count']; ?> görüntüleme
                                </span>
                                <span class="stat-item">
                                    <i class="far fa-comment me-1 text-success"></i>
                                    <?php echo $post['comment_count']; ?> yorum
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            
                        </div>
                    </div>
                </footer>
            </article>

            <!-- Genişletilmiş Paylaşım Butonları -->
            <div class="share-buttons-extended mt-5">
                <h5 class="share-title text-center mb-4">
                    <i class="fas fa-share-alt me-2"></i>Bu İçeriği Paylaş
                </h5>
                <div class="share-buttons-grid">
                    <!-- Mevcut Butonlar -->
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($currentUrl); ?>" 
                       target="_blank" class="share-btn facebook" title="Facebook'ta Paylaş">
                        <i class="fab fa-facebook-f"></i>
                        <span>Facebook</span>
                    </a>
                    
                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($currentUrl); ?>&text=<?php echo urlencode($post['title']); ?>" 
                       target="_blank" class="share-btn twitter" title="Twitter'da Paylaş">
                        <i class="fab fa-twitter"></i>
                        <span>Twitter</span>
                    </a>
                    
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode($currentUrl); ?>" 
                       target="_blank" class="share-btn linkedin" title="LinkedIn'de Paylaş">
                        <i class="fab fa-linkedin-in"></i>
                        <span>LinkedIn</span>
                    </a>

                    <!-- Yeni Eklenen Butonlar -->
                    <a href="https://bsky.app/intent/compose?text=<?php echo urlencode($post['title'] . ' ' . $currentUrl); ?>" 
                       target="_blank" class="share-btn bluesky" title="Bluesky'de Paylaş">
                        <i class="fas fa-cloud"></i>
                        <span>Bluesky</span>
                    </a>
                    
                    <a href="https://mastodon.social/share?text=<?php echo urlencode($post['title'] . ' ' . $currentUrl); ?>" 
                       target="_blank" class="share-btn mastodon" title="Mastodon'da Paylaş">
                        <i class="fab fa-mastodon"></i>
                        <span>Mastodon</span>
                    </a>
                    
                    <a href="https://connect.ok.ru/offer?url=<?php echo urlencode($currentUrl); ?>&title=<?php echo urlencode($post['title']); ?>&description=<?php echo urlencode($excerpt); ?>" 
                       target="_blank" class="share-btn odnoklassniki" title="Odnoklassniki'de Paylaş">
                        <i class="fab fa-odnoklassniki"></i>
                        <span>OK.ru</span>
                    </a>
                    
                    <a href="https://vk.com/share.php?url=<?php echo urlencode($currentUrl); ?>&title=<?php echo urlencode($post['title']); ?>&description=<?php echo urlencode($excerpt); ?>&image=<?php echo urlencode($post['featured_image'] ?? ''); ?>" 
                       target="_blank" class="share-btn vk" title="VK'da Paylaş">
                        <i class="fab fa-vk"></i>
                        <span>VK</span>
                    </a>
                    
                    <a href="https://www.blogger.com/blog-this.g?u=<?php echo urlencode($currentUrl); ?>&n=<?php echo urlencode($post['title']); ?>&t=<?php echo urlencode($excerpt); ?>" 
                       target="_blank" class="share-btn blogger" title="Blogger'da Paylaş">
                        <i class="fab fa-blogger-b"></i>
                        <span>Blogger</span>
                    </a>
                    
                    <a href="https://mewe.com/share?url=<?php echo urlencode($currentUrl); ?>&title=<?php echo urlencode($post['title']); ?>" 
                       target="_blank" class="share-btn mewe" title="MeWe'de Paylaş">
                        <i class="fas fa-comments"></i>
                        <span>MeWe</span>
                    </a>
                    
                    <a href="https://www.diigo.com/post?url=<?php echo urlencode($currentUrl); ?>&title=<?php echo urlencode($post['title']); ?>&desc=<?php echo urlencode($excerpt); ?>" 
                       target="_blank" class="share-btn diigo" title="Diigo'da Paylaş">
                        <i class="fas fa-bookmark"></i>
                        <span>Diigo</span>
                    </a>
                    
                    <a href="https://reddit.com/submit?url=<?php echo urlencode($currentUrl); ?>&title=<?php echo urlencode($post['title']); ?>" 
                       target="_blank" class="share-btn reddit" title="Reddit'te Paylaş">
                        <i class="fab fa-reddit-alien"></i>
                        <span>Reddit</span>
                    </a>
                    
                    <a href="https://api.whatsapp.com/send?text=<?php echo urlencode($post['title'] . ' ' . $currentUrl); ?>" 
                       target="_blank" class="share-btn whatsapp" title="WhatsApp'ta Paylaş">
                        <i class="fab fa-whatsapp"></i>
                        <span>WhatsApp</span>
                    </a>
                    
                    <a href="https://t.me/share/url?url=<?php echo urlencode($currentUrl); ?>&text=<?php echo urlencode($post['title']); ?>" 
                       target="_blank" class="share-btn telegram" title="Telegram'da Paylaş">
                        <i class="fab fa-telegram-plane"></i>
                        <span>Telegram</span>
                    </a>
                </div>
            </div>

            <!-- Benzer Yazılar -->
            <?php if(!empty($similar_posts)): ?>
                <section class="similar-posts mt-5 pt-5 border-top">
                    <h4 class="mb-4">
                        <i class="fas fa-stream me-2 text-muted"></i>Benzer Yazılar
                    </h4>
                    <div class="row">
                        <?php foreach($similar_posts as $similar): ?>
                            <div class="col-md-4 mb-4">
                                <div class="similar-post-card">
                                    <?php if($similar['featured_image']): ?>
                                        <div class="similar-post-image mb-3">
                                            <img src="<?php echo $similar['featured_image']; ?>" 
                                                 alt="<?php echo sanitize($similar['title']); ?>" 
                                                 class="img-fluid rounded">
                                        </div>
                                    <?php endif; ?>
                                    <h6 class="similar-post-title">
                                        <a href="<?php echo $similar['slug']; ?>.html" class="text-decoration-none">
                                            <?php echo sanitize($similar['title']); ?>
                                        </a>
                                    </h6>
                                    <div class="similar-post-author small text-muted">
                                        <i class="fas fa-user me-1"></i><?php echo sanitize($similar['display_name']); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Yorumlar Bölümü -->
            <section class="comments-section mt-5 pt-5 border-top">
                <h4 class="mb-4">
                    <i class="far fa-comments me-2 text-muted"></i>
                    Yorumlar (<?php echo count($comments); ?>)
                </h4>

                <!-- Yorum Listesi -->
                <?php if(empty($comments)): ?>
                    <div class="no-comments text-center py-4">
                        <i class="far fa-comment-slash fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Henüz yorum yapılmamış. İlk yorumu siz yapın!</p>
                    </div>
                <?php else: ?>
                    <div class="comments-list">
                        <?php foreach($comments as $comment): ?>
                            <div class="comment-card mb-4 p-3 border rounded">
                                <div class="comment-header d-flex justify-content-between align-items-center mb-2">
                                    <div class="comment-author fw-bold">
                                        <i class="fas fa-user me-2 text-muted"></i>
                                        <?php echo sanitize($comment['author_name']); ?>
                                    </div>
                                    <div class="comment-date text-muted small">
                                        <?php echo date('d M Y H:i', strtotime($comment['created_at'])); ?>
                                    </div>
                                </div>
                                <div class="comment-content">
                                    <?php echo nl2br(sanitize($comment['content'])); ?>
                                </div>
                                <?php if($comment['author_website']): ?>
                                    <div class="comment-website mt-2">
                                        <a href="<?php echo sanitize($comment['author_website']); ?>" 
                                           target="_blank" class="text-decoration-none small">
                                            <i class="fas fa-globe me-1"></i>Websitesi
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Yorum Formu -->
                <div class="comment-form mt-5">
                    <h5 class="mb-4">Yorum Yap</h5>
                    
                    <?php if(isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="author_name" class="form-label">
                                    <i class="fas fa-user me-1 text-muted"></i>Adınız *
                                </label>
                                <input type="text" class="form-control" id="author_name" name="author_name" 
                                       value="<?php echo isset($_POST['author_name']) ? $_POST['author_name'] : ''; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="author_email" class="form-label">
                                    <i class="fas fa-envelope me-1 text-muted"></i>E-posta *
                                </label>
                                <input type="email" class="form-control" id="author_email" name="author_email" 
                                       value="<?php echo isset($_POST['author_email']) ? $_POST['author_email'] : ''; ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="author_website" class="form-label">
                                <i class="fas fa-globe me-1 text-muted"></i>Websiteniz
                            </label>
                            <input type="url" class="form-control" id="author_website" name="author_website" 
                                   value="<?php echo isset($_POST['author_website']) ? $_POST['author_website'] : ''; ?>"
                                   placeholder="https://">
                        </div>
                        <div class="mb-3">
                            <label for="comment_content" class="form-label">
                                <i class="fas fa-comment me-1 text-muted"></i>Yorumunuz *
                            </label>
                            <textarea class="form-control" id="comment_content" name="comment_content" 
                                      rows="5" required placeholder="Yorumunuzu buraya yazın..."><?php echo isset($_POST['comment_content']) ? $_POST['comment_content'] : ''; ?></textarea>
                        </div>
                        
                        <!-- Spam Koruması -->
                        <div class="mb-4 p-3 border rounded bg-light">
                            <label for="spam_answer" class="form-label fw-bold">
                                <i class="fas fa-shield-alt me-1 text-warning"></i>
                                Spam Koruması: Türkiye'nin başkenti neresidir? *
                            </label>
                            <input type="text" class="form-control" id="spam_answer" name="spam_answer" 
                                   placeholder="Cevabı yazın..." required>
                            <div class="form-text">Lütfen soruyu cevaplayın (büyük/küçük harf fark etmez)</div>
                        </div>
                        
                        <button type="submit" name="submit_comment" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Yorumu Gönder
                        </button>
                    </form>
                </div>
            </section>
        </div>
    </div>
</div>

<style>
    :root {
        --primary-color: #667eea;
        --secondary-color: #764ba2;
        --accent-color: #f093fb;
        --success-color: #4cd964;
        --warning-color: #ff9500;
        --danger-color: #ff3b30;
        --info-color: #5ac8fa;
        --light-color: #f8f9fa;
        --dark-color: #343a40;
        --text-color: #2c3e50;
        --text-muted: #6c757d;
        --border-color: #e9ecef;
        --card-shadow: 0 2px 20px rgba(0,0,0,0.08);
        --hover-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .single-post {
        background: white;
        padding: 2rem;
        border-radius: 16px;
        box-shadow: var(--card-shadow);
    }
    
    .post-title {
        color: var(--text-color);
        line-height: 1.3;
    }
    
    .post-content {
        color: var(--text-color);
    }
    
    .post-content img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 1rem 0;
    }
    
    .post-content h2, .post-content h3, .post-content h4 {
        margin-top: 2rem;
        margin-bottom: 1rem;
        color: var(--text-color);
    }
    
    .post-content blockquote {
        border-left: 4px solid var(--primary-color);
        padding-left: 1rem;
        margin: 1.5rem 0;
        font-style: italic;
        color: var(--text-muted);
    }
    
    .category-badge {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        text-decoration: none;
        font-size: 0.8rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .category-badge:hover {
        background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
        color: white;
        text-decoration: none;
        transform: translateY(-2px);
    }
    
    .tags-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .tag-badge {
        background: var(--light-color);
        color: var(--text-muted);
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        text-decoration: none;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }
    
    .tag-badge:hover {
        background: var(--primary-color);
        color: white;
        text-decoration: none;
        transform: translateY(-2px);
    }
    
    .author-card {
        background: var(--light-color);
        padding: 1rem;
        border-radius: 12px;
        max-width: 300px;
        margin: 0 auto;
        border: 1px solid var(--border-color);
    }
    
    .similar-post-card {
        transition: transform 0.3s ease;
        padding: 1rem;
        border-radius: 12px;
        border: 1px solid var(--border-color);
    }
    
    .similar-post-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--hover-shadow);
    }
    
    .similar-post-image {
        height: 120px;
        overflow: hidden;
        border-radius: 8px;
    }
    
    .similar-post-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .similar-post-card:hover .similar-post-image img {
        transform: scale(1.05);
    }
    
    .similar-post-title {
        font-weight: 600;
        line-height: 1.4;
        margin-bottom: 0.5rem;
    }
    
    .similar-post-title a {
        color: var(--text-color);
        text-decoration: none;
    }
    
    .similar-post-title a:hover {
        color: var(--primary-color);
    }
    
    .comment-card {
        background: var(--light-color);
        border: 1px solid var(--border-color) !important;
        border-radius: 12px;
    }
    
    .comment-form {
        background: var(--light-color);
        padding: 2rem;
        border-radius: 12px;
        border: 1px solid var(--border-color);
    }
    
    .debug-info {
        font-size: 0.9rem;
    }

    /* Genişletilmiş Paylaşım Butonları Stilleri */
    .share-buttons-extended {
        margin: 3rem 0;
        padding: 2rem;
        background: var(--light-color);
        border-radius: 16px;
        border: 1px solid var(--border-color);
    }

    .share-title {
        margin-bottom: 1.5rem;
        color: var(--text-color);
        font-weight: 600;
        text-align: center;
    }

    .share-buttons-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 1rem;
    }

    .share-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1rem 0.5rem;
        border-radius: 12px;
        text-decoration: none;
        color: white;
        font-weight: 500;
        font-size: 0.85rem;
        transition: all 0.3s ease;
        border: none;
        min-height: 80px;
    }

    .share-btn:hover {
        transform: translateY(-3px);
        box-shadow: var(--hover-shadow);
        color: white;
        text-decoration: none;
    }

    .share-btn i {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .share-btn span {
        font-size: 0.8rem;
    }

    /* Platform Renkleri - Özgün ve Canlı Renkler */
    .share-btn.facebook { background: #1877F2; }
    .share-btn.twitter { background: #1DA1F2; }
    .share-btn.linkedin { background: #0A66C2; }
    .share-btn.bluesky { background: #0285FF; }
    .share-btn.mastodon { background: #6364FF; }
    .share-btn.odnoklassniki { background: #EE8208; }
    .share-btn.vk { background: #0077FF; }
    .share-btn.blogger { background: #FF5722; }
    .share-btn.mewe { background: #007DA5; }
    .share-btn.diigo { background: #2D9EE0; }
    .share-btn.reddit { background: #FF4500; }
    .share-btn.whatsapp { background: #25D366; }
    .share-btn.telegram { background: #0088CC; }

    .share-btn.facebook:hover { background: #1664D9; }
    .share-btn.twitter:hover { background: #1A91DA; }
    .share-btn.linkedin:hover { background: #0956B3; }
    .share-btn.bluesky:hover { background: #0274E6; }
    .share-btn.mastodon:hover { background: #5859E6; }
    .share-btn.odnoklassniki:hover { background: #D67407; }
    .share-btn.vk:hover { background: #0066E6; }
    .share-btn.blogger:hover { background: #E64A19; }
    .share-btn.mewe:hover { background: #006D92; }
    .share-btn.diigo:hover { background: #288FCF; }
    .share-btn.reddit:hover { background: #E63E00; }
    .share-btn.whatsapp:hover { background: #20BD5C; }
    .share-btn.telegram:hover { background: #0077B8; }

    /* Buton Stilleri */
    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border: none;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
        transform: translateY(-2px);
        box-shadow: var(--hover-shadow);
    }
    
    .btn-outline-primary {
        border-color: var(--primary-color);
        color: var(--primary-color);
    }
    
    .btn-outline-primary:hover {
        background: var(--primary-color);
        border-color: var(--primary-color);
    }

    /* Responsive Tasarım */
    @media (max-width: 768px) {
        .share-buttons-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 0.8rem;
        }
        
        .share-btn {
            padding: 0.8rem 0.3rem;
            min-height: 70px;
            font-size: 0.8rem;
        }
        
        .share-btn i {
            font-size: 1.3rem;
        }
        
        .share-btn span {
            font-size: 0.75rem;
        }
        
        .share-buttons-extended {
            padding: 1.5rem 1rem;
            margin: 2rem 0;
        }
        
        .single-post {
            padding: 1.5rem;
        }
    }

    @media (max-width: 480px) {
        .share-buttons-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .single-post {
            padding: 1rem;
        }
    }
</style>

<?php require_once 'themes/google-modern/footer.php'; ?>