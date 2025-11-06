<?php
require_once 'includes/config.php';

http_response_code(404);
$pageTitle = "Sayfa Bulunamadı - " . SITE_NAME;
$pageDescription = "Aradığınız sayfa bulunamadı";

require_once 'themes/google-modern/header.php';
?>

<style>
    .error-page {
        padding: 4rem 0;
    }
    
    .error-icon {
        font-size: 6rem;
        margin-bottom: 2rem;
        background: linear-gradient(135deg, #FF6B6B 0%, #FF8E53 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .error-code {
        font-size: 8rem;
        font-weight: 800;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 1rem;
    }
    
    .btn-primary-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        color: white;
    }
    
    .btn-outline-custom {
        border: 2px solid #667eea;
        color: #667eea;
        background: transparent;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-outline-custom:hover {
        background: #667eea;
        color: white;
        transform: translateY(-2px);
    }
    
    .popular-section {
        background: linear-gradient(135deg, #f8f9ff 0%, #f0f2ff 100%);
        border-radius: 20px;
        padding: 3rem 2rem;
        margin-top: 3rem;
    }
    
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    
    .card-img-top {
        height: 180px;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .card:hover .card-img-top {
        transform: scale(1.05);
    }
    
    .card-title {
        font-size: 0.95rem;
        line-height: 1.4;
    }
    
    .card-title a {
        color: #2d3748;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .card-title a:hover {
        color: #667eea;
    }
    
    .section-title {
        color: #2d3748;
        font-weight: 700;
        margin-bottom: 2rem;
        position: relative;
    }
    
    .section-title::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 3px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 2px;
    }
</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10 text-center">
            <div class="error-page">
                <!-- Ana Hata İkonu -->
                <div class="error-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                
                <!-- Hata Kodu -->
                <h1 class="error-code">404</h1>
                
                <!-- Hata Başlığı -->
                <h2 class="h1 mb-4" style="color: #2d3748; font-weight: 700;">
                    Sayfa Bulunamadı
                </h2>
                
                <!-- Hata Açıklaması -->
                <p class="lead mb-5" style="color: #718096; font-size: 1.25rem; line-height: 1.6;">
                    Aradığınız sayfa silinmiş, taşınmış veya adresi yanlış yazmış olabilirsiniz.<br>
                    Aşağıdaki bağlantıları kullanarak istediğiniz içeriğe ulaşabilirsiniz.
                </p>
                
                <!-- Aksiyon Butonları -->
                <div class="d-flex justify-content-center gap-3 flex-wrap mb-5">
                    <a href="index.php" class="btn btn-primary-custom btn-lg">
                        <i class="fas fa-home me-2"></i>Ana Sayfaya Dön
                    </a>
                    <a href="javascript:history.back()" class="btn btn-outline-custom btn-lg">
                        <i class="fas fa-arrow-left me-2"></i>Geri Dön
                    </a>
                    <a href="contact.php" class="btn btn-outline-custom btn-lg">
                        <i class="fas fa-envelope me-2"></i>İletişime Geç
                    </a>
                </div>
                
                <!-- Hızlı Erişim Linkleri -->
                <div class="row justify-content-center mb-5">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body py-4">
                                <h5 class="card-title mb-3" style="color: #2d3748;">
                                    <i class="fas fa-compass me-2" style="color: #667eea;"></i>
                                    Hızlı Erişim
                                </h5>
                                <div class="d-flex flex-wrap justify-content-center gap-3">
                                    <a href="blog.php" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-newspaper me-1"></i>Tüm Yazılar
                                    </a>
                                    <a href="categories.php" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-folder me-1"></i>Kategoriler
                                    </a>
                                    <a href="search.php" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-search me-1"></i>Arama Yap
                                    </a>
                                    <a href="sitemap.php" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-sitemap me-1"></i>Site Haritası
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Popüler Yazılar Bölümü -->
                <div class="popular-section">
                    <h3 class="section-title">Popüler Yazılar</h3>
                    <div class="row justify-content-center">
                        <?php
                        $popular_posts = $pdo->query("
                            SELECT title, slug, featured_image, excerpt 
                            FROM posts 
                            WHERE status = 'published' 
                            ORDER BY view_count DESC 
                            LIMIT 3
                        ")->fetchAll();
                        
                        if(!empty($popular_posts)):
                            foreach($popular_posts as $post): ?>
                                <div class="col-lg-4 col-md-6 mb-4">
                                    <div class="card h-100">
                                        <?php if($post['featured_image']): ?>
                                            <img src="<?php echo $post['featured_image']; ?>" 
                                                 class="card-img-top" alt="<?php echo sanitize($post['title']); ?>"
                                                 style="height: 200px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="card-img-top d-flex align-items-center justify-content-center bg-light"
                                                 style="height: 200px;">
                                                <i class="fas fa-image fa-3x text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="card-body d-flex flex-column">
                                            <h6 class="card-title flex-grow-1">
                                                <a href="<?php echo $post['slug']; ?>.html" class="text-decoration-none">
                                                    <?php echo sanitize($post['title']); ?>
                                                </a>
                                            </h6>
                                            <?php if($post['excerpt']): ?>
                                                <p class="card-text small text-muted mt-2">
                                                    <?php echo strip_tags(substr($post['excerpt'], 0, 80)); ?>...
                                                </p>
                                            <?php endif; ?>
                                            <div class="mt-auto pt-3">
                                                <a href="<?php echo $post['slug']; ?>.html" 
                                                   class="btn btn-sm btn-outline-custom w-100">
                                                    Devamını Oku
                                                    <i class="fas fa-arrow-right ms-1"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach;
                        else: ?>
                            <div class="col-12 text-center">
                                <p class="text-muted">Henüz popüler yazı bulunmuyor.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Ek Bilgi -->
                <div class="mt-5">
                    <div class="alert alert-light border-0" style="background: linear-gradient(135deg, #f8f9ff 0%, #f0f2ff 100%);">
                        <div class="row align-items-center">
                            <div class="col-md-2 text-center">
                                <i class="fas fa-life-ring fa-2x" style="color: #667eea;"></i>
                            </div>
                            <div class="col-md-10">
                                <h5 class="alert-heading mb-2">Yardıma mı ihtiyacınız var?</h5>
                                <p class="mb-0">
                                    Aradığınızı bulamadıysanız, <a href="contact.php" class="fw-bold" style="color: #667eea;">iletişim sayfamızdan</a> 
                                    bize ulaşabilir veya <a href="search.php" class="fw-bold" style="color: #667eea;">arama yaparak</a> 
                                    istediğiniz içeriği bulmaya çalışabilirsiniz.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'themes/google-modern/footer.php'; ?>