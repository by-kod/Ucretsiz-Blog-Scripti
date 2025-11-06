<?php
require_once 'includes/config.php';

$pageTitle = "Kullanım Şartları - " . SITE_NAME;
$pageDescription = SITE_NAME . " kullanım şartları ve hizmet koşulları.";
$pageKeywords = "kullanım şartları, hizmet koşulları, gizlilik, güvenlik, " . SITE_KEYWORDS;

require_once 'themes/google-modern/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Başlık -->
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold text-gradient mb-3">
                    <i class="fas fa-file-contract me-3"></i>Kullanım Şartları
                </h1>
                <p class="lead text-muted">
                    <?php echo SITE_NAME; ?> platformunu kullanmadan önce lütfen bu şartları dikkatlice okuyun.
                </p>
                <div class="last-updated">
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        Son güncellenme: <?php echo date('d.m.Y'); ?>
                    </small>
                </div>
            </div>

            <!-- İçerik -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">
                    <!-- Giriş -->
                    <section class="mb-5">
                        <h2 class="h4 section-title mb-3">
                            <i class="fas fa-info-circle me-2"></i>Giriş
                        </h2>
                        <p>
                            <?php echo SITE_NAME; ?>'yi ("Site", "Platform", "Biz") ziyaret ederek ve kullanarak, 
                            aşağıda belirtilen kullanım şartlarını kabul etmiş sayılırsınız. 
                            Bu şartları kabul etmiyorsanız, lütfen sitemizi kullanmayınız.
                        </p>
                    </section>

                    <!-- Kullanıcı Sorumlulukları -->
                    <section class="mb-5">
                        <h2 class="h4 section-title mb-3">
                            <i class="fas fa-user-check me-2"></i>Kullanıcı Sorumlulukları
                        </h2>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Siteyi yasalara uygun şekilde kullanacaksınız
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Başkalarının haklarına saygı göstereceksiniz
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Telif hakkıyla korunan içerikleri izinsiz paylaşmayacaksınız
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Spam, zararlı yazılım veya uygunsuz içerik paylaşmayacaksınız
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Diğer kullanıcıların deneyimini olumsuz etkilemeyeceksiniz
                            </li>
                        </ul>
                    </section>

                    <!-- İçerik Politikası -->
                    <section class="mb-5">
                        <h2 class="h4 section-title mb-3">
                            <i class="fas fa-newspaper me-2"></i>İçerik Politikası
                        </h2>
                        <div class="alert alert-warning-custom">
                            <h5 class="alert-heading">
                                <i class="fas fa-exclamation-triangle me-2"></i>Yasaklı İçerikler
                            </h5>
                            Aşağıdaki içerik türleri kesinlikle yasaktır:
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-danger">
                                    <li>Nefret söylemi</li>
                                    <li>Şiddet içerikli materyaller</li>
                                    <li>Yetişkinlere yönelik içerikler</li>
                                    <li>Yasa dışı faaliyetler</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-danger">
                                    <li>Sahte haber ve yanıltıcı bilgiler</li>
                                    <li>Kişisel saldırılar</li>
                                    <li>Spam ve reklam içerikleri</li>
                                    <li>Telif hakkı ihlalleri</li>
                                </ul>
                            </div>
                        </div>
                    </section>

                    <!-- Fikri Mülkiyet -->
                    <section class="mb-5">
                        <h2 class="h4 section-title mb-3">
                            <i class="fas fa-copyright me-2"></i>Fikri Mülkiyet
                        </h2>
                        <p>
                            Sitede yayınlanan tüm içerikler (yazılar, görseller, logo, tasarım vb.) 
                            <?php echo SITE_NAME; ?>'ye aittir ve telif hakkı yasalarıyla korunmaktadır.
                        </p>
                        <div class="alert alert-danger-custom">
                            <h6 class="alert-heading">
                                <i class="fas fa-ban me-2"></i>İzin Almadan Yapılamazlar
                            </h6>
                            <ul class="mb-0">
                                <li>İçeriklerin ticari amaçlarla kopyalanması</li>
                                <li>Kaynak gösterilmeden alıntı yapılması</li>
                                <li>İçeriklerin değiştirilerek kullanılması</li>
                                <li>Otomatik botlarla içerik çekilmesi</li>
                            </ul>
                        </div>
                    </section>

                    <!-- Yorum ve Etkileşim Kuralları -->
                    <section class="mb-5">
                        <h2 class="h4 section-title mb-3">
                            <i class="fas fa-comments me-2"></i>Yorum ve Etkileşim Kuralları
                        </h2>
                        <p>
                            Yorum yaparken aşağıdaki kurallara uymanız gerekmektedir:
                        </p>
                        <div class="table-responsive">
                            <table class="table table-bordered table-custom">
                                <thead class="table-header-custom">
                                    <tr>
                                        <th>Kural</th>
                                        <th>Açıklama</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Nezaket Kuralları</td>
                                        <td>Diğer kullanıcılara saygılı davranın</td>
                                    </tr>
                                    <tr>
                                        <td>Konuya Uygunluk</td>
                                        <td>Yazı konusuyla ilgili yorum yapın</td>
                                    </tr>
                                    <tr>
                                        <td>Reklam Yasağı</td>
                                        <td>Yorumlarda reklam ve spam yapmayın</td>
                                    </tr>
                                    <tr>
                                        <td>Kişisel Bilgi</td>
                                        <td>Kişisel bilgilerinizi paylaşmayın</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <!-- Gizlilik ve Veri Kullanımı -->
                    <section class="mb-5">
                        <h2 class="h4 section-title mb-3">
                            <i class="fas fa-shield-alt me-2"></i>Gizlilik ve Veri Kullanımı
                        </h2>
                        <p>
                            Gizlilik politikamızla ilgili detaylı bilgi için 
                            <a href="privacy.php" class="text-link">Gizlilik Politikası</a> 
                            sayfamızı inceleyebilirsiniz.
                        </p>
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-accent">Toplanan Veriler</h6>
                                <ul class="list-primary">
                                    <li>İsim (isteğe bağlı)</li>
                                    <li>E-posta adresi</li>
                                    <li>IP adresi</li>
                                    <li>Tarayıcı bilgileri</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-accent">Veri Kullanımı</h6>
                                <ul class="list-primary">
                                    <li>Hizmet iyileştirme</li>
                                    <li>İletişim</li>
                                    <li>Güvenlik</li>
                                    <li>Analiz</li>
                                </ul>
                            </div>
                        </div>
                    </section>

                    <!-- Hizmet Değişiklikleri -->
                    <section class="mb-5">
                        <h2 class="h4 section-title mb-3">
                            <i class="fas fa-sync-alt me-2"></i>Hizmet Değişiklikleri
                        </h2>
                        <p>
                            <?php echo SITE_NAME; ?>, herhangi bir zamanda önceden haber vermeksizin:
                        </p>
                        <ul class="list-warning">
                            <li>Hizmetleri değiştirme veya sonlandırma hakkını saklı tutar</li>
                            <li>Kullanım şartlarını güncelleme hakkına sahiptir</li>
                            <li>İçerikleri kaldırma veya düzenleme yetkisine sahiptir</li>
                        </ul>
                        <div class="alert alert-info-custom">
                            <i class="fas fa-info-circle me-2"></i>
                            Önemli değişiklikler kullanıcılara duyurulacaktır.
                        </div>
                    </section>

                    <!-- Sorumluluk Reddi -->
                    <section class="mb-5">
                        <h2 class="h4 section-title mb-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>Sorumluluk Reddi
                        </h2>
                        <p>
                            <?php echo SITE_NAME; ?> olarak:
                        </p>
                        <ul class="list-secondary">
                            <li>İçeriklerin doğruluğu garanti edilmez</li>
                            <li>Kesintisiz hizmet garanti edilmez</li>
                            <li>Üçüncü parti bağlantıların içeriğinden sorumlu değiliz</li>
                            <li>Kullanıcı hatalarından kaynaklanan sorunlardan sorumlu değiliz</li>
                        </ul>
                    </section>

                    <!-- İhlal Bildirimi -->
                    <section class="mb-5">
                        <h2 class="h4 section-title mb-3">
                            <i class="fas fa-flag me-2"></i>İhlal Bildirimi
                        </h2>
                        <p>
                            Herhangi bir kural ihlali veya uygunsuz içerik tespit ettiğinizde, 
                            lütfen aşağıdaki iletişim kanallarından bize ulaşın:
                        </p>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-0 bg-light-custom">
                                    <div class="card-body">
                                        <h6 class="card-title text-accent">
                                            <i class="fas fa-envelope me-2"></i>E-posta
                                        </h6>
                                        <p class="card-text mb-0">
                                            <a href="mailto:mail@blog.blog" class="text-link">
                                                mail@blog.blog
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-0 bg-light-custom">
                                    <div class="card-body">
                                        <h6 class="card-title text-accent">
                                            <i class="fas fa-phone me-2"></i>Telefon
                                        </h6>
                                        <p class="card-text mb-0">
                                            <a href="tel:+905555555555" class="text-link">
                                                +90 555 555 5555
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Yürürlük -->
                    <section class="mb-4">
                        <h2 class="h4 section-title mb-3">
                            <i class="fas fa-gavel me-2"></i>Yürürlük
                        </h2>
                        <p>
                            Bu kullanım şartları <?php echo date('d.m.Y'); ?> tarihinden itibaren 
                            yürürlüğe girmiştir. Sitemizi kullanmaya devam etmeniz, 
                            bu şartları kabul ettiğiniz anlamına gelir.
                        </p>
                        <div class="alert alert-success-custom">
                            <h6 class="alert-heading">
                                <i class="fas fa-check-circle me-2"></i>Kabul ve Onay
                            </h6>
                            <?php echo SITE_NAME; ?>'yi kullanarak bu kullanım şartlarını 
                            okuduğunuzu, anladığınızı ve kabul ettiğinizi beyan edersiniz.
                        </div>
                    </section>

                    <!-- İletişim -->
                    <section class="text-center mt-5 pt-4 border-top">
                        <h3 class="h5 text-muted mb-3">Sorularınız mı var?</h3>
                        <p class="text-muted mb-4">
                            Kullanım şartları hakkında sorularınız için bizimle iletişime geçebilirsiniz.
                        </p>
                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <a href="contact.php" class="btn btn-primary-custom">
                                <i class="fas fa-envelope me-2"></i>İletişime Geç
                            </a>
                            <a href="privacy.php" class="btn btn-outline-custom">
                                <i class="fas fa-shield-alt me-2"></i>Gizlilik Politikası
                            </a>
                        </div>
                    </section>
                </div>
            </div>

            <!-- Hızlı Navigasyon -->
            <div class="text-center mt-4">
                <nav aria-label="Sayfa navigasyonu">
                    <ul class="list-inline">
                        <li class="list-inline-item">
                            <a href="index.php" class="text-link">
                                <i class="fas fa-home me-1"></i>Ana Sayfa
                            </a>
                        </li>
                        <li class="list-inline-item">•</li>
                        <li class="list-inline-item">
                            <a href="privacy.php" class="text-link">
                                <i class="fas fa-shield-alt me-1"></i>Gizlilik Politikası
                            </a>
                        </li>
                        <li class="list-inline-item">•</li>
                        <li class="list-inline-item">
                            <a href="contact.php" class="text-link">
                                <i class="fas fa-envelope me-1"></i>İletişim
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<style>
    /* Özel Renk Paleti - Google renkleri yerine */
    :root {
        --primary-color: #7c3aed;      /* Mor - Canlı ve profesyonel */
        --secondary-color: #06b6d4;    /* Camgöbeği - Enerjik */
        --accent-color: #f59e0b;       /* Amber - Dikkat çekici */
        --success-color: #10b981;      /* Zümrüt yeşili - Doğal */
        --warning-color: #f97316;      /* Turuncu - Uyarıcı */
        --danger-color: #ef4444;       /* Kırmızı - Acil */
        --info-color: #3b82f6;         /* Mavi - Bilgilendirici */
        --light-color: #f8fafc;        /* Açık arkaplan */
        --dark-color: #1e293b;         /* Koyu metin */
    }

    .text-gradient {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .section-title {
        color: var(--primary-color);
        border-bottom: 2px solid var(--primary-color);
        padding-bottom: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .text-accent {
        color: var(--accent-color) !important;
    }

    .text-link {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    .text-link:hover {
        color: var(--secondary-color);
        text-decoration: underline;
    }

    /* Özel Buton Stilleri */
    .btn-primary-custom {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border: none;
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(124, 58, 237, 0.3);
        color: white;
    }

    .btn-outline-custom {
        border: 2px solid var(--primary-color);
        color: var(--primary-color);
        background: transparent;
        padding: 0.75rem 2rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-outline-custom:hover {
        background: var(--primary-color);
        color: white;
        transform: translateY(-2px);
    }

    /* Özel Alert Stilleri */
    .alert-warning-custom {
        background: linear-gradient(135deg, #fff7ed, #fed7aa);
        border-left: 4px solid var(--warning-color);
        color: #92400e;
        border-radius: 10px;
        border: none;
    }

    .alert-danger-custom {
        background: linear-gradient(135deg, #fef2f2, #fecaca);
        border-left: 4px solid var(--danger-color);
        color: #991b1b;
        border-radius: 10px;
        border: none;
    }

    .alert-info-custom {
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
        border-left: 4px solid var(--info-color);
        color: #1e40af;
        border-radius: 10px;
        border: none;
    }

    .alert-success-custom {
        background: linear-gradient(135deg, #ecfdf5, #a7f3d0);
        border-left: 4px solid var(--success-color);
        color: #065f46;
        border-radius: 10px;
        border: none;
    }

    /* Liste Stilleri */
    .list-primary li {
        color: var(--primary-color);
        margin-bottom: 0.5rem;
        padding-left: 1rem;
        position: relative;
    }

    .list-primary li:before {
        content: "•";
        color: var(--primary-color);
        font-weight: bold;
        position: absolute;
        left: 0;
    }

    .list-danger li {
        color: var(--danger-color);
        margin-bottom: 0.5rem;
        padding-left: 1rem;
        position: relative;
    }

    .list-danger li:before {
        content: "⚠";
        color: var(--danger-color);
        position: absolute;
        left: 0;
    }

    .list-warning li {
        color: var(--warning-color);
        margin-bottom: 0.5rem;
        padding-left: 1rem;
        position: relative;
    }

    .list-warning li:before {
        content: "⚡";
        color: var(--warning-color);
        position: absolute;
        left: 0;
    }

    .list-secondary li {
        color: var(--secondary-color);
        margin-bottom: 0.5rem;
        padding-left: 1rem;
        position: relative;
    }

    .list-secondary li:before {
        content: "ⓘ";
        color: var(--secondary-color);
        position: absolute;
        left: 0;
    }

    /* Tablo Stilleri */
    .table-custom {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .table-header-custom {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important;
        color: white;
        border: none;
    }

    .table-custom th,
    .table-custom td {
        padding: 1rem;
        border-color: #e2e8f0;
    }

    /* Kart Stilleri */
    .card {
        border-radius: 15px;
        border: none;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .bg-light-custom {
        background: linear-gradient(135deg, var(--light-color), #f1f5f9) !important;
        border-radius: 10px;
    }

    /* Responsive Tasarım */
    @media (max-width: 768px) {
        .card-body {
            padding: 2rem !important;
        }
        
        .display-4 {
            font-size: 2.5rem;
        }
        
        .btn-primary-custom,
        .btn-outline-custom {
            width: 100%;
            margin-bottom: 1rem;
        }
        
        .d-flex.justify-content-center {
            flex-direction: column;
        }
    }
</style>

<?php require_once 'themes/google-modern/footer.php'; ?>