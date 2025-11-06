<?php
require_once 'includes/config.php';

$pageTitle = "Gizlilik Politikası - " . SITE_NAME;
$pageDescription = SITE_NAME . " gizlilik politikası. Kişisel verilerinizin nasıl korunduğu ve kullanıldığı hakkında bilgiler.";
$pageKeywords = "gizlilik politikası, kişisel veriler, çerezler, GDPR, " . SITE_KEYWORDS;

require_once 'themes/google-modern/header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Ana Sayfa</a></li>
                    <li class="breadcrumb-item active">Gizlilik Politikası</li>
                </ol>
            </nav>

            <!-- Sayfa Başlığı -->
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold mb-3">Gizlilik Politikası</h1>
                <p class="lead text-muted">Kişisel verilerinizin güvenliği ve gizliliği bizim için önemlidir</p>
            </div>

            <!-- Gizlilik Politikası İçeriği -->
            <div class="privacy-content">
                
                <!-- Giriş -->
                <section class="mb-5">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <p class="fs-5 lh-base">
                                <?php echo SITE_NAME; ?> olarak, ziyaretçilerimizin ve kullanıcılarımızın gizliliğine büyük önem veriyoruz. 
                                Bu gizlilik politikası, kişisel verilerinizin nasıl toplandığını, kullanıldığını, 
                                korunduğunu ve paylaşıldığını açıklamaktadır.
                            </p>
                        </div>
                    </div>
                </section>

                <!-- Veri Toplama -->
                <section class="mb-5">
                    <h2 class="h3 mb-4">1. Toplanan Veriler</h2>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-user-circle text-vibrant-blue fa-2x"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h5 class="card-title mb-1">Kişisel Bilgiler</h5>
                                        </div>
                                    </div>
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-check text-success me-2"></i>Ad Soyad</li>
                                        <li><i class="fas fa-check text-success me-2"></i>E-posta adresi</li>
                                        <li><i class="fas fa-check text-success me-2"></i>IP adresi</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Tarayıcı bilgileri</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-chart-bar text-vibrant-teal fa-2x"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h5 class="card-title mb-1">Teknik Veriler</h5>
                                        </div>
                                    </div>
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-check text-success me-2"></i>Çerezler (Cookies)</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Sayfa görüntüleme istatistikleri</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Ziyaret süreleri</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Referans URL'ler</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Veri Kullanımı -->
                <section class="mb-5">
                    <h2 class="h3 mb-4">2. Verilerin Kullanım Amacı</h2>
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-bullhorn text-vibrant-orange fa-lg me-3 mt-1"></i>
                                        </div>
                                        <div>
                                            <h5 class="h6 mb-2">İçerik Sunumu</h5>
                                            <p class="text-muted mb-0">Size en uygun içerikleri sunmak ve kişiselleştirilmiş deneyim sağlamak.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-envelope text-vibrant-purple fa-lg me-3 mt-1"></i>
                                        </div>
                                        <div>
                                            <h5 class="h6 mb-2">İletişim</h5>
                                            <p class="text-muted mb-0">Soru ve görüşlerinize yanıt vermek, bilgilendirme yapmak.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-chart-line text-vibrant-green fa-lg me-3 mt-1"></i>
                                        </div>
                                        <div>
                                            <h5 class="h6 mb-2">Analiz</h5>
                                            <p class="text-muted mb-0">Site performansını analiz etmek ve iyileştirmeler yapmak.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-shield-alt text-vibrant-red fa-lg me-3 mt-1"></i>
                                        </div>
                                        <div>
                                            <h5 class="h6 mb-2">Güvenlik</h5>
                                            <p class="text-muted mb-0">Güvenlik ihlallerini tespit etmek ve önlemek.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Çerezler -->
                <section class="mb-5">
                    <h2 class="h3 mb-4">3. Çerez Politikası</h2>
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <p class="mb-4">
                                Çerezler, web sitemizi ziyaret ettiğinizde tarayıcınız aracılığıyla cihazınıza kaydedilen küçük metin dosyalarıdır.
                            </p>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="text-center p-3 border rounded h-100 cookie-card">
                                        <i class="fas fa-cog text-vibrant-blue fa-2x mb-3"></i>
                                        <h6 class="fw-bold">Zorunlu Çerezler</h6>
                                        <p class="small text-muted mb-0">Sitenin temel işlevleri için gereklidir</p>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="text-center p-3 border rounded h-100 cookie-card">
                                        <i class="fas fa-chart-pie text-vibrant-teal fa-2x mb-3"></i>
                                        <h6 class="fw-bold">Analitik Çerezler</h6>
                                        <p class="small text-muted mb-0">Site kullanım istatistikleri için</p>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="text-center p-3 border rounded h-100 cookie-card">
                                        <i class="fas fa-bullseye text-vibrant-orange fa-2x mb-3"></i>
                                        <h6 class="fw-bold">Reklam Çerezleri</h6>
                                        <p class="small text-muted mb-0">Hedefli reklamlar için kullanılır</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4 p-3 bg-light rounded">
                                <p class="mb-2">
                                    <strong>Çerez Tercihleri:</strong> Tarayıcınızın ayarlarından çerezleri kabul etmeyebilir 
                                    veya silmek istediğiniz çerezleri seçebilirsiniz. Ancak bu, web sitemizin bazı özelliklerinin 
                                    düzgün çalışmamasına neden olabilir.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Veri Paylaşımı -->
                <section class="mb-5">
                    <h2 class="h3 mb-4">4. Veri Paylaşımı</h2>
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <p class="mb-4">
                                Kişisel verileriniz, aşağıdaki durumlar dışında üçüncü şahıslarla paylaşılmaz:
                            </p>
                            
                            <div class="alert custom-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Yasal Zorunluluk:</strong> Mahkeme kararı veya yasal gereklilik durumunda
                            </div>
                            
                            <div class="alert custom-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Güvenlik:</strong> Dolandırıcılık önleme veya güvenlik ihlali durumunda
                            </div>
                            
                            <div class="alert custom-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>Hizmet Sağlayıcılar:</strong> Sadece veri işleme amacıyla anlaşmalı firmalarla
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Veri Güvenliği -->
                <section class="mb-5">
                    <h2 class="h3 mb-4">5. Veri Güvenliği</h2>
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-3 mb-3">
                                    <div class="p-3 h-100">
                                        <i class="fas fa-lock text-vibrant-green fa-2x mb-3"></i>
                                        <h6>SSL Şifreleme</h6>
                                        <p class="small text-muted mb-0">Tüm veri transferleri şifrelenir</p>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="p-3 h-100">
                                        <i class="fas fa-database text-vibrant-blue fa-2x mb-3"></i>
                                        <h6>Güvenli Sunucular</h6>
                                        <p class="small text-muted mb-0">Veriler güvenli sunucularda saklanır</p>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="p-3 h-100">
                                        <i class="fas fa-user-shield text-vibrant-orange fa-2x mb-3"></i>
                                        <h6>Erişim Kontrolü</h6>
                                        <p class="small text-muted mb-0">Sınırlı personel erişimi</p>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="p-3 h-100">
                                        <i class="fas fa-sync text-vibrant-teal fa-2x mb-3"></i>
                                        <h6>Düzenli Yedekleme</h6>
                                        <p class="small text-muted mb-0">Veriler düzenli olarak yedeklenir</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Haklarınız -->
                <section class="mb-5">
                    <h2 class="h3 mb-4">6. Haklarınız</h2>
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <p class="mb-4">
                                KVKK (Kişisel Verileri Koruma Kanunu) kapsamında aşağıdaki haklara sahipsiniz:
                            </p>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex align-items-center">
                                            <i class="fas fa-eye text-vibrant-blue me-3"></i>
                                            <span>Verilerinize erişim hakkı</span>
                                        </li>
                                        <li class="list-group-item d-flex align-items-center">
                                            <i class="fas fa-edit text-vibrant-orange me-3"></i>
                                            <span>Düzeltme talep etme hakkı</span>
                                        </li>
                                        <li class="list-group-item d-flex align-items-center">
                                            <i class="fas fa-trash text-vibrant-red me-3"></i>
                                            <span>Silinmesini talep etme hakkı</span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex align-items-center">
                                            <i class="fas fa-ban text-vibrant-purple me-3"></i>
                                            <span>İşleme itiraz etme hakkı</span>
                                        </li>
                                        <li class="list-group-item d-flex align-items-center">
                                            <i class="fas fa-file-export text-vibrant-teal me-3"></i>
                                            <span>Veri taşınabilirliği hakkı</span>
                                        </li>
                                        <li class="list-group-item d-flex align-items-center">
                                            <i class="fas fa-gavel text-vibrant-green me-3"></i>
                                            <span>Şikayet hakkı</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="mt-4 p-3 bg-light rounded">
                                <p class="mb-0">
                                    <strong>Haklarınızı Kullanmak İçin:</strong> Yukarıdaki haklarınızı kullanmak için 
                                    <a href="contact.php">iletişim formu</a> aracılığıyla bize ulaşabilirsiniz. 
                                    Talebiniz en geç 30 gün içinde değerlendirilecektir.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Politika Değişiklikleri -->
                <section class="mb-5">
                    <h2 class="h3 mb-4">7. Politika Değişiklikleri</h2>
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-vibrant-orange fa-2x me-3"></i>
                                </div>
                                <div>
                                    <p class="mb-3">
                                        Bu gizlilik politikasını zaman zaman güncelleyebiliriz. Önemli değişiklikler 
                                        olduğunda, site üzerinden veya e-posta yoluyla sizi bilgilendireceğiz.
                                    </p>
                                    <p class="mb-0 text-muted">
                                        <strong>Son güncelleme:</strong> <?php echo date('d.m.Y'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- İletişim -->
                <section class="mb-5">
                    <h2 class="h3 mb-4">8. İletişim</h2>
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <p class="fs-5 lh-base mb-4">
                                Gizlilik politikamız hakkında sorularınız varsa veya haklarınızı kullanmak istiyorsanız, 
                                aşağıdaki iletişim bilgilerinden bize ulaşabilirsiniz.
                            </p>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="fas fa-envelope text-vibrant-blue me-3 fa-lg"></i>
                                        <div>
                                            <div class="fw-bold">E-posta</div>
                                            <div class="text-muted"><?php echo SITE_EMAIL; ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="fas fa-globe text-vibrant-blue me-3 fa-lg"></i>
                                        <div>
                                            <div class="fw-bold">Web Sitesi</div>
                                            <div class="text-muted"><?php echo SITE_URL; ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center mt-4">
                                <a href="contact.php" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>İletişim Formu
                                </a>
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
    --vibrant-blue: #2563eb;
    --vibrant-teal: #0d9488;
    --vibrant-green: #16a34a;
    --vibrant-orange: #ea580c;
    --vibrant-red: #dc2626;
    --vibrant-purple: #7c3aed;
    --vibrant-pink: #db2777;
}

.text-vibrant-blue { color: var(--vibrant-blue) !important; }
.text-vibrant-teal { color: var(--vibrant-teal) !important; }
.text-vibrant-green { color: var(--vibrant-green) !important; }
.text-vibrant-orange { color: var(--vibrant-orange) !important; }
.text-vibrant-red { color: var(--vibrant-red) !important; }
.text-vibrant-purple { color: var(--vibrant-purple) !important; }
.text-vibrant-pink { color: var(--vibrant-pink) !important; }

.privacy-content {
    line-height: 1.8;
}

.privacy-content section {
    padding: 2rem 0;
    border-bottom: 1px solid #e9ecef;
}

.privacy-content section:last-child {
    border-bottom: none;
}

.card {
    border-radius: 16px;
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.05);
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.15) !important;
}

.list-group-item {
    border: none;
    padding: 1rem 0.5rem;
    background: transparent;
}

.display-4 {
    background: linear-gradient(135deg, var(--vibrant-blue), var(--vibrant-purple));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.cookie-card {
    transition: all 0.3s ease;
    border: 2px solid transparent !important;
}

.cookie-card:hover {
    border-color: var(--vibrant-blue) !important;
    transform: scale(1.05);
}

.alert.custom-info {
    background: rgba(37, 99, 235, 0.1);
    border: 1px solid var(--vibrant-blue);
    color: var(--vibrant-blue);
}

.alert.custom-warning {
    background: rgba(234, 88, 12, 0.1);
    border: 1px solid var(--vibrant-orange);
    color: var(--vibrant-orange);
}

.alert.custom-success {
    background: rgba(22, 163, 74, 0.1);
    border: 1px solid var(--vibrant-green);
    color: var(--vibrant-green);
}

.btn-primary {
    background: linear-gradient(135deg, var(--vibrant-blue), var(--vibrant-purple));
    border: none;
    padding: 0.75rem 2rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
}

.breadcrumb {
    background: rgba(37, 99, 235, 0.05);
    border-radius: 10px;
    padding: 0.75rem 1rem;
}

.breadcrumb-item.active {
    color: var(--vibrant-blue);
    font-weight: 600;
}

.bg-light {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9) !important;
}

@media (max-width: 768px) {
    .display-4 {
        font-size: 2.5rem;
    }
    
    .privacy-content section {
        padding: 1.5rem 0;
    }
}
</style>

<?php require_once 'themes/google-modern/footer.php'; ?>