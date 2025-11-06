<?php
require_once 'includes/config.php';

$pageTitle = "Çerez Politikası - " . SITE_NAME;
$pageDescription = "Çerez kullanımı, türleri ve yönetimi hakkında detaylı bilgi";
$pageKeywords = "çerez politikası, cookie policy, çerez yönetimi, gizlilik, veri koruma";

// Header dosyasını include et
include 'themes/google-modern/header.php';
?>

<div class="container mt-4">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <h1 class="hero-title fade-in-up">Çerez Politikası</h1>
            <p class="hero-subtitle fade-in-up">Çerez kullanımımız ve gizlilik haklarınız hakkında detaylı bilgi</p>
        </div>
    </div>

    <div class="row justify-content-center mt-5">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light py-4">
                    <h3 class="card-title mb-0 text-center">
                        <i class="fas fa-cookie-bite me-2" style="color: #e67e22;"></i>
                        Çerez (Cookie) Politikası
                    </h3>
                </div>
                <div class="card-body p-5">
                    <!-- Hızlı Özet -->
                    <div class="alert alert-info border-0 mb-5" style="background: linear-gradient(135deg, #d6eaf8 0%, #aed6f1 100%); border-left: 4px solid #3498db !important;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle fa-2x me-3" style="color: #2980b9;"></i>
                            <div>
                                <h5 class="alert-heading mb-2" style="color: #2c3e50;">Hızlı Özet</h5>
                                <p class="mb-0" style="color: #34495e;">
                                    Sitemiz, kullanıcı deneyiminizi iyileştirmek ve site performansını artırmak için çerezler kullanmaktadır. 
                                    Çerez tercihlerinizi istediğiniz zaman yönetebilirsiniz.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Çerez Onayı -->
                    <div class="cookie-consent-banner mb-5 p-4 border rounded-3" style="background: linear-gradient(135deg, #fff9e6 0%, #ffeccc 100%); border: 2px solid #f39c12 !important;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0" style="color: #d35400;">
                                <i class="fas fa-cookie me-2" style="color: #e67e22;"></i>
                                Çerez Tercihleriniz
                            </h5>
                            <span class="badge" style="background: <?php echo isset($_COOKIE['cookie_consent']) ? '#27ae60' : '#e74c3c'; ?>;">
                                <?php echo isset($_COOKIE['cookie_consent']) ? 'Onay Verildi' : 'Beklemede'; ?>
                            </span>
                        </div>
                        <p class="mb-3" style="color: #7f8c8d;">Çerez kullanım tercihlerinizi aşağıdan yönetebilirsiniz:</p>
                        <div class="row g-3" id="cookiePreferences">
                            <div class="col-md-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="essentialCookies" checked disabled style="background-color: #95a5a6;">
                                    <label class="form-check-label fw-bold" for="essentialCookies" style="color: #2c3e50;">
                                        Zorunlu Çerezler
                                    </label>
                                    <small class="form-text d-block" style="color: #7f8c8d;">Sitenin çalışması için gerekli</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="analyticsCookies" 
                                           <?php echo isset($_COOKIE['analytics_cookies']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="analyticsCookies" style="color: #2c3e50;">
                                        Analitik Çerezler
                                    </label>
                                    <small class="form-text d-block" style="color: #7f8c8d;">Site kullanım istatistikleri</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="marketingCookies"
                                           <?php echo isset($_COOKIE['marketing_cookies']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="marketingCookies" style="color: #2c3e50;">
                                        Pazarlama Çerezler
                                    </label>
                                    <small class="form-text d-block" style="color: #7f8c8d;">Kişiselleştirilmiş reklamlar</small>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="button" class="btn btn-success btn-sm me-2" onclick="saveCookiePreferences()" style="background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%); border: none;">
                                <i class="fas fa-save me-1"></i>Tercihleri Kaydet
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="resetCookiePreferences()" style="border-color: #e74c3c; color: #e74c3c;">
                                <i class="fas fa-trash me-1"></i>Tüm Çerezleri Temizle
                            </button>
                        </div>
                    </div>

                    <!-- İçerik -->
                    <div class="cookie-policy-content">
                        <!-- Çerez Nedir? -->
                        <section class="mb-5">
                            <h4 class="mb-4" style="color: #3498db;">
                                <i class="fas fa-question-circle me-2"></i>
                                1. Çerez (Cookie) Nedir?
                            </h4>
                            <div class="ps-4">
                                <p class="mb-3" style="color: #2c3e50;">
                                    Çerezler, web siteleri tarafından tarayıcınıza kaydedilen küçük metin dosyalarıdır. 
                                    Bu dosyalar:
                                </p>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="card border-0 h-100" style="background: linear-gradient(135deg, #e8f4fd 0%, #d4e6f1 100%);">
                                            <div class="card-body text-center">
                                                <i class="fas fa-user-circle fa-2x mb-3" style="color: #3498db;"></i>
                                                <h6 style="color: #2c3e50;">Kullanıcı Tercihleri</h6>
                                                <p class="small mb-0" style="color: #7f8c8d;">Dil seçimi, tema ayarları gibi tercihlerinizi hatırlar</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-0 h-100" style="background: linear-gradient(135deg, #e8f6f3 0%, #d1f2eb 100%);">
                                            <div class="card-body text-center">
                                                <i class="fas fa-shopping-cart fa-2x mb-3" style="color: #27ae60;"></i>
                                                <h6 style="color: #2c3e50;">Oturum Bilgisi</h6>
                                                <p class="small mb-0" style="color: #7f8c8d;">Alışveriş sepeti, giriş oturumu gibi bilgileri saklar</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <p class="mb-0" style="color: #2c3e50;">
                                    Çerezler kişisel bilgisayarınıza zarar veremez veya virüs bulaştıramaz. 
                                    Sadece sizin onayınız ve bilginiz dahilinde çalışırlar.
                                </p>
                            </div>
                        </section>

                        <!-- Kullandığımız Çerez Türleri -->
                        <section class="mb-5">
                            <h4 class="mb-4" style="color: #3498db;">
                                <i class="fas fa-cookie me-2"></i>
                                2. Kullandığımız Çerez Türleri
                            </h4>
                            <div class="ps-4">
                                <div class="table-responsive mb-4">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="background: #34495e; color: white;">Çerez Türü</th>
                                                <th style="background: #34495e; color: white;">Amaç</th>
                                                <th style="background: #34495e; color: white;">Saklama Süresi</th>
                                                <th style="background: #34495e; color: white;">Zorunlu</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><strong>Oturum Çerezleri</strong></td>
                                                <td>Site gezintiniz sırasında oturum bilgilerinizi saklamak</td>
                                                <td>Tarayıcı kapanana kadar</td>
                                                <td><span class="badge" style="background: #27ae60;">Evet</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Tercih Çerezleri</strong></td>
                                                <td>Dil, tema gibi kişisel tercihlerinizi hatırlamak</td>
                                                <td>1 yıl</td>
                                                <td><span class="badge" style="background: #e67e22;">Hayır</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Analitik Çerezler</strong></td>
                                                <td>Site kullanım istatistikleri ve performans analizi</td>
                                                <td>2 yıl</td>
                                                <td><span class="badge" style="background: #e67e22;">Hayır</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Pazarlama Çerezleri</strong></td>
                                                <td>İlgi alanlarınıza göre kişiselleştirilmiş içerik sunmak</td>
                                                <td>1 yıl</td>
                                                <td><span class="badge" style="background: #e67e22;">Hayır</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Üçüncü Taraf Çerezler</strong></td>
                                                <td>Sosyal medya, reklam ve analiz partnerlerimiz</td>
                                                <td>Değişken</td>
                                                <td><span class="badge" style="background: #e67e22;">Hayır</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </section>

                        <!-- Çerezlerin Kullanım Amaçları -->
                        <section class="mb-5">
                            <h4 class="mb-4" style="color: #3498db;">
                                <i class="fas fa-bullseye me-2"></i>
                                3. Çerezlerin Kullanım Amaçları
                            </h4>
                            <div class="ps-4">
                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-user-check me-3 mt-1" style="color: #27ae60;"></i>
                                            <div>
                                                <h6 style="color: #2c3e50;">Kullanıcı Deneyimi</h6>
                                                <p class="small mb-0" style="color: #7f8c8d;">Kişiselleştirilmiş içerik ve tercihler sunmak</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-chart-line me-3 mt-1" style="color: #3498db;"></i>
                                            <div>
                                                <h6 style="color: #2c3e50;">Site Analizi</h6>
                                                <p class="small mb-0" style="color: #7f8c8d;">Site performansını ve kullanımını izlemek</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-shopping-bag me-3 mt-1" style="color: #9b59b6;"></i>
                                            <div>
                                                <h6 style="color: #2c3e50;">İşlevsellik</h6>
                                                <p class="small mb-0" style="color: #7f8c8d;">Temel site özelliklerinin çalışmasını sağlamak</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-ad me-3 mt-1" style="color: #e67e22;"></i>
                                            <div>
                                                <h6 style="color: #2c3e50;">Reklam Optimizasyonu</h6>
                                                <p class="small mb-0" style="color: #7f8c8d;">İlgi alanlarınıza uygun reklamlar göstermek</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Üçüncü Taraf Çerezler -->
                        <section class="mb-5">
                            <h4 class="mb-4" style="color: #3498db;">
                                <i class="fas fa-external-link-alt me-2"></i>
                                4. Üçüncü Taraf Çerezler
                            </h4>
                            <div class="ps-4">
                                <p class="mb-3" style="color: #2c3e50;">
                                    Sitemizde aşağıdaki üçüncü taraf hizmet sağlayıcıların çerezleri kullanılabilir:
                                </p>
                                <div class="row mb-3">
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-0 h-100" style="background: linear-gradient(135deg, #fdedec 0%, #fadbd8 100%);">
                                            <div class="card-body text-center">
                                                <i class="fas fa-chart-bar fa-2x mb-3" style="color: #e74c3c;"></i>
                                                <h6 style="color: #2c3e50;">Analiz Araçları</h6>
                                                <p class="small mb-2" style="color: #7f8c8d;">Site trafiği analizi</p>
                                                <a href="#" class="btn btn-sm" style="background: #e74c3c; color: white; border: none;">Gizlilik Politikası</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-0 h-100" style="background: linear-gradient(135deg, #e8f4fd 0%, #d4e6f1 100%);">
                                            <div class="card-body text-center">
                                                <i class="fas fa-share-alt fa-2x mb-3" style="color: #3498db;"></i>
                                                <h6 style="color: #2c3e50;">Sosyal Medya</h6>
                                                <p class="small mb-2" style="color: #7f8c8d;">Sosyal paylaşım entegrasyonu</p>
                                                <a href="#" class="btn btn-sm" style="background: #3498db; color: white; border: none;">Çerez Politikası</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-0 h-100" style="background: linear-gradient(135deg, #e8f6f3 0%, #d1f2eb 100%);">
                                            <div class="card-body text-center">
                                                <i class="fas fa-ad fa-2x mb-3" style="color: #27ae60;"></i>
                                                <h6 style="color: #2c3e50;">Reklam Ağları</h6>
                                                <p class="small mb-2" style="color: #7f8c8d;">Reklam optimizasyonu</p>
                                                <a href="#" class="btn btn-sm" style="background: #27ae60; color: white; border: none;">Reklam Politikası</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <p class="mb-0" style="color: #7f8c8d;">
                                    Üçüncü taraf çerez politikaları için ilgili sitelerin gizlilik politikalarını inceleyebilirsiniz.
                                </p>
                            </div>
                        </section>

                        <!-- Çerez Yönetimi -->
                        <section class="mb-5">
                            <h4 class="mb-4" style="color: #3498db;">
                                <i class="fas fa-cogs me-2"></i>
                                5. Çerez Tercihlerinizi Yönetme
                            </h4>
                            <div class="ps-4">
                                <p class="mb-3" style="color: #2c3e50;">
                                    Çerez kullanımını aşağıdaki yöntemlerle kontrol edebilirsiniz:
                                </p>
                                
                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3">
                                        <div class="card border-0 h-100" style="background: linear-gradient(135deg, #fef9e7 0%, #fcf3cf 100%);">
                                            <div class="card-body">
                                                <h6 class="card-title" style="color: #2c3e50;">
                                                    <i class="fas fa-sliders-h me-2" style="color: #f39c12;"></i>
                                                    Tarayıcı Ayarları
                                                </h6>
                                                <p class="small mb-0" style="color: #7f8c8d;">
                                                    Tarayıcınızın ayarlar menüsünden çerezleri engelleyebilir veya silebilirsiniz.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="card border-0 h-100" style="background: linear-gradient(135deg, #fdedec 0%, #fadbd8 100%);">
                                            <div class="card-body">
                                                <h6 class="card-title" style="color: #2c3e50;">
                                                    <i class="fas fa-ban me-2" style="color: #e74c3c;"></i>
                                                    Çerez Engelleme
                                                </h6>
                                                <p class="small mb-0" style="color: #7f8c8d;">
                                                    Çerezleri engellerseniz, sitenin bazı özellikleri düzgün çalışmayabilir.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="mb-3" style="color: #2c3e50;">Popüler Tarayıcılarda Çerez Ayarları:</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="background: #34495e; color: white;">Tarayıcı</th>
                                                <th style="background: #34495e; color: white;">Çerez Ayarlarına Erişim</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><i class="fab fa-chrome me-2" style="color: #27ae60;"></i>Google Chrome</td>
                                                <td>Ayarlar → Gizlilik ve güvenlik → Çerezler ve diğer site verileri</td>
                                            </tr>
                                            <tr>
                                                <td><i class="fab fa-firefox me-2" style="color: #e67e22;"></i>Mozilla Firefox</td>
                                                <td>Seçenekler → Gizlilik ve Güvenlik → Çerezler ve Site Verileri</td>
                                            </tr>
                                            <tr>
                                                <td><i class="fab fa-safari me-2" style="color: #3498db;"></i>Safari</td>
                                                <td>Tercihler → Gizlilik → Çerezler ve web sitesi verileri</td>
                                            </tr>
                                            <tr>
                                                <td><i class="fab fa-edge me-2" style="color: #2980b9;"></i>Microsoft Edge</td>
                                                <td>Ayarlar → Gizlilik ve hizmetler → Tanımlama bilgileri</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </section>

                        <!-- Yasal Dayanak -->
                        <section class="mb-5">
                            <h4 class="mb-4" style="color: #3498db;">
                                <i class="fas fa-gavel me-2"></i>
                                6. Yasal Dayanak ve Mevzuat
                            </h4>
                            <div class="ps-4">
                                <p class="mb-3" style="color: #2c3e50;">
                                    Çerez kullanımımız aşağıdaki yasal düzenlemelere uygun şekilde gerçekleştirilmektedir:
                                </p>
                                <ul class="mb-3">
                                    <li class="mb-2" style="color: #2c3e50;">
                                        <strong>KVKK (Kişisel Verilerin Korunması Kanunu)</strong> - 6698 sayılı kanun
                                    </li>
                                    <li class="mb-2" style="color: #2c3e50;">
                                        <strong>GDPR (Genel Veri Koruma Tüzüğü)</strong> - Avrupa Birliği düzenlemesi
                                    </li>
                                    <li class="mb-2" style="color: #2c3e50;">
                                        <strong>ePrivacy Direktifi</strong> - Elektronik gizlilik direktifi
                                    </li>
                                </ul>
                                <div class="alert alert-warning border-0" style="background: linear-gradient(135deg, #fef9e7 0%, #fcf3cf 100%); border-left: 4px solid #f39c12 !important;">
                                    <h6 class="alert-heading" style="color: #d35400;">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Önemli Not:
                                    </h6>
                                    <p class="mb-0" style="color: #7f8c8d;">
                                        Zorunlu çerezler olmadan sitemizin temel işlevlerini yerine getiremeyiz. 
                                        Diğer çerez türleri için onayınızı istemekteyiz.
                                    </p>
                                </div>
                            </div>
                        </section>

                        <!-- Güncellemeler -->
                        <section class="mb-5">
                            <h4 class="mb-4" style="color: #3498db;">
                                <i class="fas fa-sync-alt me-2"></i>
                                7. Politika Güncellemeleri
                            </h4>
                            <div class="ps-4">
                                <p class="mb-3" style="color: #2c3e50;">
                                    Bu çerez politikasını aşağıdaki durumlarda güncelleyebiliriz:
                                </p>
                                <ul class="mb-3">
                                    <li class="mb-2" style="color: #2c3e50;">Yasal düzenlemelerde değişiklik olması durumunda</li>
                                    <li class="mb-2" style="color: #2c3e50;">Site işlevselliğinde önemli değişiklikler yapılması durumunda</li>
                                    <li class="mb-2" style="color: #2c3e50;">Yeni çerez türleri veya kullanım amaçları eklenmesi durumunda</li>
                                    <li class="mb-2" style="color: #2c3e50;">Teknolojik gelişmeler doğrultusunda</li>
                                </ul>
                                <p class="mb-0" style="color: #2c3e50;">
                                    Politikadaki değişiklikler bu sayfada yayınlandığı anda geçerli olacaktır. 
                                    Önemli değişikliklerde sizi bilgilendireceğiz.
                                </p>
                            </div>
                        </section>

                        <!-- İletişim -->
                        <section>
                            <h4 class="mb-4" style="color: #3498db;">
                                <i class="fas fa-envelope me-2"></i>
                                8. İletişim
                            </h4>
                            <div class="ps-4">
                                <p class="mb-3" style="color: #2c3e50;">
                                    Çerez politikamız veya gizlilik haklarınızla ilgili sorularınız için:
                                </p>
                                <div class="card border-0" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6 class="mb-3" style="color: #2c3e50;">İletişim Bilgileri:</h6>
                                                <ul class="list-unstyled">
                                                    <li class="mb-2">
                                                        <i class="fas fa-envelope me-2" style="color: #3498db;"></i>
                                                        <a href="mailto:mail@blog.blog" class="text-decoration-none" style="color: #2980b9;">mail@blog.blog</a>
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-globe me-2" style="color: #3498db;"></i>
                                                        <a href="contact.php" class="text-decoration-none" style="color: #2980b9;">İletişim Formu</a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="mb-3" style="color: #2c3e50;">İlgili Sayfalar:</h6>
                                                <ul class="list-unstyled">
                                                    <li class="mb-2">
                                                        <i class="fas fa-shield-alt me-2" style="color: #27ae60;"></i>
                                                        <a href="privacy.php" class="text-decoration-none" style="color: #229954;">Gizlilik Politikası</a>
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-file-contract me-2" style="color: #27ae60;"></i>
                                                        <a href="terms.php" class="text-decoration-none" style="color: #229954;">Kullanım Şartları</a>
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-balance-scale me-2" style="color: #27ae60;"></i>
                                                        <a href="disclaimer.php" class="text-decoration-none" style="color: #229954;">Sorumluluk Reddi</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>

                    <!-- Son Güncelleme -->
                    <div class="text-center mt-5">
                        <div class="alert alert-light border-0">
                            <p class="mb-0" style="color: #7f8c8d;">
                                <i class="fas fa-history me-2"></i>
                                <strong>Son Güncelleme:</strong> <?php echo date('d/m/Y'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Footer dosyasını include et
include 'themes/google-modern/footer.php';
?>

<script>
    // Çerez tercihlerini kaydetme
    function saveCookiePreferences() {
        const analytics = document.getElementById('analyticsCookies').checked;
        const marketing = document.getElementById('marketingCookies').checked;
        
        // Çerezleri ayarla
        setCookie('cookie_consent', 'true', 365);
        setCookie('analytics_cookies', analytics ? 'true' : 'false', 365);
        setCookie('marketing_cookies', marketing ? 'true' : 'false', 365);
        
        // UI güncelleme
        showAlert('Çerez tercihleriniz başarıyla kaydedildi.', 'success');
        
        // Sayfayı yenile (isteğe bağlı)
        setTimeout(() => {
            location.reload();
        }, 2000);
    }
    
    // Çerezleri temizle
    function resetCookiePreferences() {
        if (confirm('Tüm çerez tercihleriniz sıfırlanacak. Bu işlem geri alınamaz. Devam etmek istiyor musunuz?')) {
            // Tüm çerezleri temizle
            document.cookie.split(";").forEach(function(c) {
                document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/");
            });
            
            showAlert('Tüm çerezler temizlendi. Sayfa yeniden yüklenecek.', 'info');
            
            setTimeout(() => {
                location.reload();
            }, 1500);
        }
    }
    
    // Çerez ayarlama fonksiyonu
    function setCookie(name, value, days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        const expires = "expires=" + date.toUTCString();
        document.cookie = name + "=" + value + ";" + expires + ";path=/;SameSite=Lax";
    }
    
    // Bildirim gösterme
    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        const bgColor = type === 'success' ? '#27ae60' : type === 'info' ? '#3498db' : '#e74c3c';
        alertDiv.className = `alert alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = `
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            background: ${bgColor};
            color: white;
            border: none;
        `;
        alertDiv.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check' : type === 'info' ? 'info' : 'exclamation'}-circle me-2"></i>
            ${message}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(alertDiv);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
    
    // Sayfa yüklendiğinde çerez durumunu kontrol et
    document.addEventListener('DOMContentLoaded', function() {
        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
        
        // Bölüm animasyonları
        const sections = document.querySelectorAll('.cookie-policy-content section');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });
        
        sections.forEach(section => {
            section.style.opacity = '0';
            section.style.transform = 'translateY(20px)';
            section.style.transition = 'all 0.6s ease';
            observer.observe(section);
        });
    });
</script>

<style>
    .form-check-input:checked {
        background-color: #27ae60;
        border-color: #27ae60;
    }
    
    .form-check-input:focus {
        border-color: #3498db;
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
    }
    
    .table th {
        border-top: none;
        font-weight: 600;
    }
    
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
    }
    
    .alert {
        transition: all 0.3s ease;
    }
    
    .alert:hover {
        transform: scale(1.02);
    }
    
    @media (max-width: 768px) {
        .cookie-consent-banner .row {
            flex-direction: column;
        }
        
        .table-responsive {
            font-size: 0.875rem;
        }
    }
    
    .badge {
        font-size: 0.75em;
    }
    
    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(39, 174, 96, 0.4);
    }
    
    .btn-outline-danger:hover {
        background-color: #e74c3c;
        color: white;
    }
</style>