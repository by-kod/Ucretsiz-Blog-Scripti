<?php
require_once 'includes/config.php';

$pageTitle = "Hakkımızda - " . SITE_NAME;
$pageDescription = SITE_NAME . " hakkında bilgiler ve misyonumuz";
$pageKeywords = "hakkımızda, misyon, vizyon, " . SITE_KEYWORDS;

require_once 'themes/google-modern/header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Ana Sayfa</a></li>
                    <li class="breadcrumb-item active">Hakkımızda</li>
                </ol>
            </nav>

            <!-- Sayfa Başlığı -->
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold mb-3">Hakkımızda</h1>
                <p class="lead text-muted"><?php echo SITE_NAME; ?> olarak misyonumuz ve vizyonumuz</p>
            </div>

            <!-- Hakkımızda İçeriği -->
            <div class="about-content">
                
                <!-- Giriş -->
                <section class="mb-5">
                    <h2 class="h3 mb-3">Biz Kimiz?</h2>
                    <p class="fs-5 lh-base">
                        <?php echo SITE_NAME; ?> olarak, okuyucularımıza kaliteli, güncel ve güvenilir içerikler sunmayı hedefliyoruz. 
                        Deneyimli yazar kadromuz ve uzman editörlerimiz ile siz değerli okurlarımıza en iyi içerik deneyimini 
                        yaşatmak için çalışıyoruz.
                    </p>
                </section>

                <!-- Misyon -->
                <section class="mb-5">
                    <h2 class="h3 mb-3">Misyonumuz</h2>
                    <div class="card border-0 shadow-sm mission-card">
                        <div class="card-body">
                            <p class="fs-5 lh-base mb-0">
                                Okuyucularımıza doğru, tarafsız ve kaliteli içerikler sunarak bilgiye kolay erişim sağlamak. 
                                Teknoloji, yaşam, kültür ve daha birçok alanda güncel bilgileri paylaşarak toplumun bilgi 
                                seviyesini yükseltmek.
                            </p>
                        </div>
                    </div>
                </section>

                <!-- Vizyon -->
                <section class="mb-5">
                    <h2 class="h3 mb-3">Vizyonumuz</h2>
                    <div class="card border-0 shadow-sm vision-card">
                        <div class="card-body">
                            <p class="fs-5 lh-base mb-0">
                                Türkiye'nin en güvenilir ve en çok tercih edilen içerik platformlarından biri olmak. 
                                Yenilikçi yaklaşımlarımız ve kaliteli içeriklerimiz ile dijital dünyada fark yaratmak.
                            </p>
                        </div>
                    </div>
                </section>

                <!-- Değerlerimiz -->
                <section class="mb-5">
                    <h2 class="h3 mb-4">Değerlerimiz</h2>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-emerald fa-lg me-3 mt-1"></i>
                                </div>
                                <div>
                                    <h5 class="h6 mb-2">Doğruluk</h5>
                                    <p class="text-muted mb-0">Tüm içeriklerimiz titizlikle kontrol edilir ve doğruluğu garanti altına alınır.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-balance-scale text-sapphire fa-lg me-3 mt-1"></i>
                                </div>
                                <div>
                                    <h5 class="h6 mb-2">Tarafsızlık</h5>
                                    <p class="text-muted mb-0">Her konuda objektif ve tarafsız bir bakış açısı sunuyoruz.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-bolt text-amber fa-lg me-3 mt-1"></i>
                                </div>
                                <div>
                                    <h5 class="h6 mb-2">Güncellik</h5>
                                    <p class="text-muted mb-0">İçeriklerimiz sürekli güncellenir ve en son bilgileri içerir.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-users text-coral fa-lg me-3 mt-1"></i>
                                </div>
                                <div>
                                    <h5 class="h6 mb-2">Topluluk</h5>
                                    <p class="text-muted mb-0">Okuyucularımızla sürekli etkileşim halindeyiz ve feedback'leri önemsiyoruz.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- İstatistikler -->
                <section class="mb-5">
                    <h2 class="h3 mb-4">Rakamlarla Biz</h2>
                    <div class="row text-center">
                        <?php
                        // İstatistikleri veritabanından al
                        $total_posts = $pdo->query("SELECT COUNT(*) as count FROM posts WHERE status = 'published'")->fetch()['count'];
                        $total_categories = $pdo->query("SELECT COUNT(*) as count FROM categories")->fetch()['count'];
                        $total_comments = $pdo->query("SELECT COUNT(*) as count FROM comments WHERE status = 'approved'")->fetch()['count'];
                        $total_views = $pdo->query("SELECT SUM(view_count) as total FROM posts")->fetch()['total'];
                        ?>
                        
                        <div class="col-6 col-md-3 mb-4">
                            <div class="stat-card">
                                <div class="stat-number text-sapphire fw-bold display-6"><?php echo $total_posts; ?></div>
                                <div class="stat-label text-muted">Toplam Yazı</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-4">
                            <div class="stat-card">
                                <div class="stat-number text-emerald fw-bold display-6"><?php echo $total_categories; ?></div>
                                <div class="stat-label text-muted">Kategori</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-4">
                            <div class="stat-card">
                                <div class="stat-number text-amber fw-bold display-6"><?php echo $total_comments; ?></div>
                                <div class="stat-label text-muted">Yorum</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-4">
                            <div class="stat-card">
                                <div class="stat-number text-coral fw-bold display-6"><?php echo $total_views ?: '0'; ?></div>
                                <div class="stat-label text-muted">Görüntüleme</div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- İletişim -->
                <section class="mb-5">
                    <h2 class="h3 mb-3">İletişim</h2>
                    <div class="card border-0 contact-card">
                        <div class="card-body">
                            <p class="fs-5 lh-base mb-3">
                                Bizimle iletişime geçmek için aşağıdaki bilgileri kullanabilirsiniz. 
                                Görüş, öneri ve şikayetleriniz bizim için değerlidir.
                            </p>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-envelope text-sapphire me-3"></i>
                                        <span>E-posta: <?php echo SITE_EMAIL; ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-globe text-sapphire me-3"></i>
                                        <span>Web: <?php echo SITE_URL; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </div>
</div>

<style>
:root {
    --sapphire-blue: #2E5BFF;
    --emerald-green: #00C851;
    --amber-orange: #FF9500;
    --coral-pink: #FF3B30;
    --violet-purple: #8E44AD;
    --teal-cyan: #00C9C9;
    --slate-gray: #5F6368;
    --charcoal-dark: #2C3E50;
    --cloud-light: #F8F9FA;
    --success-emerald: #00B894;
}

.about-content {
    line-height: 1.8;
}

.stat-card {
    padding: 1.5rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    transition: transform 0.3s ease;
    border: 1px solid #e9ecef;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.stat-number {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.9rem;
    font-weight: 500;
}

.card {
    border-radius: 12px;
}

.display-4 {
    color: var(--charcoal-dark);
}

.lead {
    color: var(--slate-gray);
}

/* Özgün Renk Sınıfları */
.text-sapphire { color: var(--sapphire-blue) !important; }
.text-emerald { color: var(--emerald-green) !important; }
.text-amber { color: var(--amber-orange) !important; }
.text-coral { color: var(--coral-pink) !important; }
.text-violet { color: var(--violet-purple) !important; }
.text-teal { color: var(--teal-cyan) !important; }

.bg-sapphire { background-color: var(--sapphire-blue) !important; }
.bg-emerald { background-color: var(--emerald-green) !important; }
.bg-amber { background-color: var(--amber-orange) !important; }
.bg-coral { background-color: var(--coral-pink) !important; }
.bg-violet { background-color: var(--violet-purple) !important; }
.bg-teal { background-color: var(--teal-cyan) !important; }

/* Özel Kart Stilleri */
.mission-card {
    border-left: 4px solid var(--sapphire-blue);
    background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
}

.vision-card {
    border-left: 4px solid var(--violet-purple);
    background: linear-gradient(135deg, #ffffff 0%, #faf8ff 100%);
}

.contact-card {
    background: linear-gradient(135deg, #ffffff 0%, #f0f9ff 100%);
    border-left: 4px solid var(--teal-cyan);
}

/* Breadcrumb Stili */
.breadcrumb {
    background-color: var(--cloud-light);
    border-radius: 8px;
    padding: 0.75rem 1rem;
}

.breadcrumb-item.active {
    color: var(--sapphire-blue);
    font-weight: 500;
}

/* İkon Animasyonları */
.fas {
    transition: transform 0.3s ease;
}

.d-flex:hover .fas {
    transform: scale(1.1);
}

/* Responsive Tasarım */
@media (max-width: 768px) {
    .display-4 {
        font-size: 2rem;
    }
    
    .stat-number {
        font-size: 2rem;
    }
    
    .stat-card {
        padding: 1rem;
    }
}

/* Gradient Efektleri */
.about-content section {
    position: relative;
}

.about-content section::before {
    content: '';
    position: absolute;
    top: 0;
    left: -20px;
    width: 4px;
    height: 100%;
    background: linear-gradient(to bottom, var(--sapphire-blue), var(--violet-purple));
    border-radius: 2px;
    opacity: 0.7;
}

/* Buton Stilleri (Gelecekte kullanılabilir) */
.btn-sapphire {
    background-color: var(--sapphire-blue);
    border-color: var(--sapphire-blue);
    color: white;
}

.btn-sapphire:hover {
    background-color: #2545d6;
    border-color: #2545d6;
    color: white;
}

.btn-emerald {
    background-color: var(--emerald-green);
    border-color: var(--emerald-green);
    color: white;
}

.btn-emerald:hover {
    background-color: #00a842;
    border-color: #00a842;
    color: white;
}
</style>

<?php require_once 'themes/google-modern/footer.php'; ?>