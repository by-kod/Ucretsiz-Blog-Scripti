<?php
require_once 'includes/config.php';

$pageTitle = "KVKK ve GDPR - " . SITE_NAME;
$pageDescription = "Kişisel Verilerin Korunması Kanunu (KVKK) ve Genel Veri Koruma Tüzüğü (GDPR) uyum bilgileri";
$pageKeywords = "KVKK, GDPR, kişisel veriler, veri koruma, gizlilik hakları, 6698 sayılı kanun";

// Header dosyasını include et
include 'themes/google-modern/header.php';
?>

<div class="container mt-4">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <h1 class="hero-title fade-in-up">KVKK & GDPR</h1>
            <p class="hero-subtitle fade-in-up">Kişisel Verilerin Korunması Kanunu ve Genel Veri Koruma Tüzüğü Uyum Bilgileri</p>
        </div>
    </div>

    <div class="row justify-content-center mt-5">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light py-4">
                    <h3 class="card-title mb-0 text-center">
                        <i class="fas fa-user-shield me-2" style="color: #8B5CF6;"></i>
                        KVKK (6698) ve GDPR Uyum Bildirimi
                    </h3>
                </div>
                <div class="card-body p-5">
                    <!-- Hızlı Özet -->
                    <div class="alert alert-vibrant border-0 mb-5">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle fa-2x me-3" style="color: #7C3AED;"></i>
                            <div>
                                <h5 class="alert-heading mb-2">Önemli Bilgilendirme</h5>
                                <p class="mb-0">
                                    <?php echo SITE_NAME; ?> olarak, kişisel verilerinizin güvenliğini önemsiyor ve 
                                    6698 sayılı Kişisel Verilerin Korunması Kanunu (KVKK) ile Genel Veri Koruma Tüzüğü (GDPR) 
                                    kapsamında gerekli tüm tedbirleri alıyoruz.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- KVKK & GDPR Karşılaştırması -->
                    <div class="compliance-overview mb-5">
                        <h4 class="mb-4 text-center" style="color: #7C3AED;">
                            <i class="fas fa-balance-scale me-2"></i>
                            KVKK ve GDPR Uyum Karşılaştırması
                        </h4>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="card border-vibrant h-100">
                                    <div class="card-header text-white text-center" style="background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);">
                                        <h5 class="mb-0">
                                            <i class="fas fa-flag me-2"></i>
                                            KVKK (6698)
                                        </h5>
                                        <small>Türkiye</small>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled mb-0">
                                            <li class="mb-2">
                                                <i class="fas fa-check me-2" style="color: #10B981;"></i>
                                                <strong>Yürürlük:</strong> 07.04.2016
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-check me-2" style="color: #10B981;"></i>
                                                <strong>Denetim:</strong> Kişisel Verileri Koruma Kurumu (KVKK)
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-check me-2" style="color: #10B981;"></i>
                                                <strong>Kapsam:</strong> Türkiye'deki veri işleme faaliyetleri
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-check me-2" style="color: #10B981;"></i>
                                                <strong>Rıza:</strong> Açık rıza gerekliliği
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="card border-vibrant h-100">
                                    <div class="card-header text-white text-center" style="background: linear-gradient(135deg, #06B6D4 0%, #0EA5E9 100%);">
                                        <h5 class="mb-0">
                                            <i class="fas fa-flag-eu me-2"></i>
                                            GDPR
                                        </h5>
                                        <small>Avrupa Birliği</small>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled mb-0">
                                            <li class="mb-2">
                                                <i class="fas fa-check me-2" style="color: #10B981;"></i>
                                                <strong>Yürürlük:</strong> 25.05.2018
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-check me-2" style="color: #10B981;"></i>
                                                <strong>Denetim:</strong> Ulusal Veri Koruma Otoriteleri
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-check me-2" style="color: #10B981;"></i>
                                                <strong>Kapsam:</strong> AB vatandaşlarının verileri
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-check me-2" style="color: #10B981;"></i>
                                                <strong>Rıza:</strong> Açık, özgür iradeyle verilmiş rıza
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- İçerik -->
                    <div class="gdpr-content">
                        <!-- Veri İşleme Prensipleri -->
                        <section class="mb-5">
                            <h4 class="mb-4" style="color: #7C3AED;">
                                <i class="fas fa-list-alt me-2"></i>
                                1. Veri İşleme Prensiplerimiz
                            </h4>
                            <div class="ps-4">
                                <p class="mb-4">
                                    Kişisel verilerinizi işlerken aşağıdaki temel prensiplere uygun hareket ediyoruz:
                                </p>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="card border-0 bg-light h-100">
                                            <div class="card-body">
                                                <h6 class="card-title" style="color: #10B981;">
                                                    <i class="fas fa-balance-scale me-2"></i>
                                                    Hukuka ve Dürüstlük Kurallarına Uygunluk
                                                </h6>
                                                <p class="small mb-0">Tüm veri işleme faaliyetlerimiz yasal düzenlemelere uygun şekilde gerçekleştirilir.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="card border-0 bg-light h-100">
                                            <div class="card-body">
                                                <h6 class="card-title" style="color: #10B981;">
                                                    <i class="fas fa-bullseye me-2"></i>
                                                    Amaçla Bağlantılı, Sınırlı ve Ölçülü Olma
                                                </h6>
                                                <p class="small mb-0">Veriler yalnızca belirli, açık ve meşru amaçlar için işlenir.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="card border-0 bg-light h-100">
                                            <div class="card-body">
                                                <h6 class="card-title" style="color: #10B981;">
                                                    <i class="fas fa-database me-2"></i>
                                                    İşlendikleri Amaç için Gereklilik
                                                </h6>
                                                <p class="small mb-0">Veriler, amaçların gerçekleştirilmesi için gerekli olduğu ölçüde işlenir.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="card border-0 bg-light h-100">
                                            <div class="card-body">
                                                <h6 class="card-title" style="color: #10B981;">
                                                    <i class="fas fa-clock me-2"></i>
                                                    İşlendikleri Amaçla Bağlantılı, Sınırlı Süreyle Saklama
                                                </h6>
                                                <p class="small mb-0">Veriler, ilgili mevzuatta öngörülen veya işlendikleri amaç için gerekli olan süre kadar saklanır.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- İşlenen Veri Türleri -->
                        <section class="mb-5">
                            <h4 class="mb-4" style="color: #7C3AED;">
                                <i class="fas fa-table me-2"></i>
                                2. İşlediğimiz Kişisel Veri Türleri
                            </h4>
                            <div class="ps-4">
                                <div class="table-responsive mb-4">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Veri Kategorisi</th>
                                                <th>Veri Türleri</th>
                                                <th>İşleme Amacı</th>
                                                <th>Hukuki Dayanak</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><strong>Kimlik Bilgileri</strong></td>
                                                <td>Ad, soyad, email</td>
                                                <td>Hesap oluşturma, iletişim</td>
                                                <td>Sözleşme, Açık Rıza</td>
                                            </tr>
                                            <tr>
                                                <td><strong>İletişim Bilgileri</strong></td>
                                                <td>Email, IP adresi</td>
                                                <td>Teknik güvenlik, analiz</td>
                                                <td>Meşru Menfaat</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Kullanıcı Tercihleri</strong></td>
                                                <td>Çerez tercihleri, dil seçimi</td>
                                                <td>Kişiselleştirilmiş hizmet</td>
                                                <td>Açık Rıza</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Teknik Veriler</strong></td>
                                                <td>IP, tarayıcı bilgisi, cihaz</td>
                                                <td>Site performansı, güvenlik</td>
                                                <td>Meşru Menfaat</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Kullanım Verileri</strong></td>
                                                <td>Sayfa görüntüleme, tıklama</td>
                                                <td>İçerik optimizasyonu</td>
                                                <td>Açık Rıza</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </section>

                        <!-- Veri Sahibi Hakları -->
                        <section class="mb-5">
                            <h4 class="mb-4" style="color: #7C3AED;">
                                <i class="fas fa-user-check me-2"></i>
                                3. Veri Sahibi Olarak Haklarınız
                            </h4>
                            <div class="ps-4">
                                <p class="mb-4">
                                    KVKK'nın 11. maddesi ve GDPR'ın 15-22. maddeleri uyarınca aşağıdaki haklara sahipsiniz:
                                </p>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-info-circle me-3 mt-1" style="color: #8B5CF6;"></i>
                                            <div>
                                                <h6>Bilgi Edinme Hakkı</h6>
                                                <p class="small mb-0">Verilerinizin işlenip işlenmediğini öğrenme</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-eye me-3 mt-1" style="color: #10B981;"></i>
                                            <div>
                                                <h6>Erişim Hakkı</h6>
                                                <p class="small mb-0">İşlenen verilerinize erişim sağlama</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-sync-alt me-3 mt-1" style="color: #F59E0B;"></i>
                                            <div>
                                                <h6>Düzeltme Hakkı</h6>
                                                <p class="small mb-0">Eksik veya yanlış verileri düzeltme</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-trash-alt me-3 mt-1" style="color: #EF4444;"></i>
                                            <div>
                                                <h6>Silme Hakkı</h6>
                                                <p class="small mb-0">Verilerinizin silinmesini talep etme</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-ban me-3 mt-1" style="color: #06B6D4;"></i>
                                            <div>
                                                <h6>İşleme İtiraz Hakkı</h6>
                                                <p class="small mb-0">Veri işleme faaliyetlerine itiraz etme</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-download me-3 mt-1" style="color: #8B5CF6;"></i>
                                            <div>
                                                <h6>Veri Taşınabilirliği</h6>
                                                <p class="small mb-0">Verilerinizi taşınabilir formatta alma</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Haklarınızı Kullanma Formu -->
                                <div class="card mt-4 border-warning">
                                    <div class="card-header text-dark" style="background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);">
                                        <h6 class="mb-0">
                                            <i class="fas fa-file-alt me-2"></i>
                                            Haklarınızı Kullanma Başvurusu
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="small mb-3">
                                            Yukarıda belirtilen haklarınızı kullanmak için aşağıdaki yöntemlerle başvuruda bulunabilirsiniz:
                                        </p>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6 class="mb-3">Başvuru Yöntemleri:</h6>
                                                <ul class="list-unstyled">
                                                    <li class="mb-2">
                                                        <i class="fas fa-envelope me-2" style="color: #8B5CF6;"></i>
                                                        <strong>Email:</strong> kvkk@blog.blog
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-print me-2" style="color: #8B5CF6;"></i>
                                                        <strong>Yazılı Başvuru:</strong> 
                                                        <a href="#" onclick="downloadApplicationForm()" class="text-decoration-none">Formu İndir</a>
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-mobile-alt me-2" style="color: #8B5CF6;"></i>
                                                        <strong>İletişim Formu:</strong> 
                                                        <a href="contact.php" class="text-decoration-none">İletişim Sayfası</a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="mb-3">Başvuru Süreci:</h6>
                                                <ul class="list-unstyled small">
                                                    <li class="mb-2">✓ Kimlik doğrulama gereklidir</li>
                                                    <li class="mb-2">✓ 30 gün içinde yanıt verilir</li>
                                                    <li class="mb-2">✓ Ücretsiz işlem (istisnalar hariç)</li>
                                                    <li class="mb-2">✓ KVKK'ya şikayet hakkı</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Veri Güvenliği -->
                        <section class="mb-5">
                            <h4 class="mb-4" style="color: #7C3AED;">
                                <i class="fas fa-shield-alt me-2"></i>
                                4. Veri Güvenliği Önlemlerimiz
                            </h4>
                            <div class="ps-4">
                                <p class="mb-4">
                                    Kişisel verilerinizin güvenliği için teknik ve idari tüm önlemleri alıyoruz:
                                </p>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-0 text-center h-100">
                                            <div class="card-body">
                                                <i class="fas fa-lock fa-2x mb-3" style="color: #10B981;"></i>
                                                <h6>Şifreleme</h6>
                                                <p class="small mb-0">Veriler SSL sertifikası ile şifrelenir</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-0 text-center h-100">
                                            <div class="card-body">
                                                <i class="fas fa-user-lock fa-2x mb-3" style="color: #8B5CF6;"></i>
                                                <h6>Erişim Kontrolü</h6>
                                                <p class="small mb-0">Yetkisiz erişim önlenir</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-0 text-center h-100">
                                            <div class="card-body">
                                                <i class="fas fa-history fa-2x mb-3" style="color: #F59E0B;"></i>
                                                <h6>Düzenli Denetim</h6>
                                                <p class="small mb-0">Sistemler periyodik olarak denetlenir</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="alert alert-success border-0 mt-4">
                                    <h6 class="alert-heading">
                                        <i class="fas fa-certificate me-2"></i>
                                        Güvenlik Sertifikalarımız
                                    </h6>
                                    <div class="row mt-3">
                                        <div class="col-md-4 text-center">
                                            <i class="fas fa-shield-check fa-2x mb-2" style="color: #8B5CF6;"></i>
                                            <div class="fw-bold">SSL Sertifikası</div>
                                            <small class="text-muted">256-bit şifreleme</small>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <i class="fas fa-server fa-2x mb-2" style="color: #10B981;"></i>
                                            <div class="fw-bold">Güvenli Sunucu</div>
                                            <small class="text-muted">DDoS korumalı</small>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <i class="fas fa-cloud fa-2x mb-2" style="color: #06B6D4;"></i>
                                            <div class="fw-bold">Yedekleme</div>
                                            <small class="text-muted">Günlük yedekleme</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Veri İhlal Bildirimi -->
                        <section class="mb-5">
                            <h4 class="mb-4" style="color: #7C3AED;">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                5. Veri İhlal Bildirimi
                            </h4>
                            <div class="ps-4">
                                <p class="mb-3">
                                    Olası bir veri ihlali durumunda aşağıdaki prosedürü izliyoruz:
                                </p>
                                <div class="row mb-4">
                                    <div class="col-md-3 text-center mb-3">
                                        <div class="text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 60px; height: 60px; background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);">
                                            <strong>1</strong>
                                        </div>
                                        <h6>Tespit</h6>
                                        <p class="small mb-0">İhlal anında tespit</p>
                                    </div>
                                    <div class="col-md-3 text-center mb-3">
                                        <div class="text-dark rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 60px; height: 60px; background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);">
                                            <strong>2</strong>
                                        </div>
                                        <h6>Değerlendirme</h6>
                                        <p class="small mb-0">Risk analizi yapılır</p>
                                    </div>
                                    <div class="col-md-3 text-center mb-3">
                                        <div class="text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 60px; height: 60px; background: linear-gradient(135deg, #06B6D4 0%, #0EA5E9 100%);">
                                            <strong>3</strong>
                                        </div>
                                        <h6>Bildirim</h6>
                                        <p class="small mb-0">72 saat içinde bildirim</p>
                                    </div>
                                    <div class="col-md-3 text-center mb-3">
                                        <div class="text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 60px; height: 60px; background: linear-gradient(135deg, #10B981 0%, #059669 100%);">
                                            <strong>4</strong>
                                        </div>
                                        <h6>Önlem</h6>
                                        <p class="small mb-0">Önleyici tedbirler alınır</p>
                                    </div>
                                </div>
                                <div class="alert alert-danger border-0">
                                    <h6 class="alert-heading">
                                        <i class="fas fa-phone me-2"></i>
                                        Acil İletişim
                                    </h6>
                                    <p class="mb-0">
                                        Veri ihlali şüpheniz varsa derhal 
                                        <a href="mailto:security@blog.blog" class="alert-link">security@blog.blog</a> 
                                        adresinden bize ulaşın.
                                    </p>
                                </div>
                            </div>
                        </section>

                        <!-- Veri Transferi -->
                        <section class="mb-5">
                            <h4 class="mb-4" style="color: #7C3AED;">
                                <i class="fas fa-exchange-alt me-2"></i>
                                6. Veri Transferi ve Paylaşımı
                            </h4>
                            <div class="ps-4">
                                <p class="mb-3">
                                    Kişisel verileriniz yalnızca aşağıdaki durumlarda ve gerekli güvenlik önlemleri alınarak paylaşılır:
                                </p>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <h6 class="mb-3">Yurtiçi Transfer:</h6>
                                        <ul>
                                            <li class="mb-2">Hosting sağlayıcıları</li>
                                            <li class="mb-2">Analiz araçları (Google Analytics)</li>
                                            <li class="mb-2">Email servis sağlayıcıları</li>
                                            <li class="mb-2">Yasal zorunluluk halleri</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="mb-3">Yurtdışı Transfer:</h6>
                                        <ul>
                                            <li class="mb-2">Yeterli koruma bulunan ülkeler</li>
                                            <li class="mb-2">Açık rıza alınması durumunda</li>
                                            <li class="mb-2">Standard sözleşmeli kurallar ile</li>
                                            <li class="mb-2">Onaylı kurumsal kurallar ile</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="alert alert-info border-0">
                                    <h6 class="alert-heading">
                                        <i class="fas fa-handshake me-2"></i>
                                        Veri İşleyenler
                                    </h6>
                                    <p class="mb-0">
                                        Veri işleyen tüm üçüncü taraflarla KVKK ve GDPR uyumlu sözleşmeler imzalanmaktadır.
                                    </p>
                                </div>
                            </div>
                        </section>

                        <!-- İletişim -->
                        <section>
                            <h4 class="mb-4" style="color: #7C3AED;">
                                <i class="fas fa-headset me-2"></i>
                                7. İletişim ve Şikayet
                            </h4>
                            <div class="ps-4">
                                <p class="mb-3">
                                    KVKK ve GDPR ile ilgili tüm soru, görüş ve şikayetleriniz için:
                                </p>
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6 class="mb-3">İletişim Bilgileri:</h6>
                                                <ul class="list-unstyled">
                                                    <li class="mb-2">
                                                        <i class="fas fa-user-tie me-2" style="color: #8B5CF6;"></i>
                                                        <strong>Veri Sorumlusu:</strong> <?php echo SITE_NAME; ?>
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-envelope me-2" style="color: #8B5CF6;"></i>
                                                        <strong>Email:</strong> kvkk@blog.blog
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-phone me-2" style="color: #8B5CF6;"></i>
                                                        <strong>Telefon:</strong> +90 555 555 5555
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-map-marker-alt me-2" style="color: #8B5CF6;"></i>
                                                        <strong>Adres:</strong> Ankara, Türkiye
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="mb-3">Resmi Makamlar:</h6>
                                                <ul class="list-unstyled">
                                                    <li class="mb-2">
                                                        <i class="fas fa-landmark me-2" style="color: #10B981;"></i>
                                                        <a href="https://www.kvkk.gov.tr" target="_blank" class="text-decoration-none">
                                                            Kişisel Verileri Koruma Kurumu
                                                        </a>
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-gavel me-2" style="color: #10B981;"></i>
                                                        <a href="https://ec.europa.eu/info/law/law-topic/data-protection_en" target="_blank" class="text-decoration-none">
                                                            Avrupa Veri Koruma Kurulu
                                                        </a>
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-file-contract me-2" style="color: #10B981;"></i>
                                                        <a href="privacy.php" class="text-decoration-none">
                                                            Gizlilik Politikası
                                                        </a>
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-cookie me-2" style="color: #10B981;"></i>
                                                        <a href="cookie-policy.php" class="text-decoration-none">
                                                            Çerez Politikası
                                                        </a>
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
                            <p class="mb-0">
                                <i class="fas fa-history me-2 text-muted"></i>
                                <strong>Son Güncelleme:</strong> <?php echo date('d/m/Y'); ?> | 
                                <strong>Belge Versiyonu:</strong> 2.1
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
    // Başvuru formu indirme
    function downloadApplicationForm() {
        // Basit bir metin dosyası oluştur
        const formContent = `
KVKK VERİ SAHİBİ BAŞVURU FORMU

Başvuru Sahibi Bilgileri:
Ad Soyad: ____________________
TC Kimlik No: ________________
İletişim Adresi: _____________
Telefon: _____________________
Email: _______________________

Talep Edilen Hak (Lütfen işaretleyiniz):
[ ] Bilgi Edinme Hakkı
[ ] Erişim Hakkı
[ ] Düzeltme Hakkı
[ ] Silme Hakkı (Unutulma Hakkı)
[ ] İşleme İtiraz Hakkı
[ ] Veri Taşınabilirliği Hakkı

Talep Açıklaması:
__________________________________________________
__________________________________________________
__________________________________________________

İmza: _________________________
Tarih: ________________________

Not: Bu formu doldurup kvkk@blog.blog adresine gönderiniz.
30 gün içinde yanıt verilecektir.
        `;
        
        const blob = new Blob([formContent], { type: 'text/plain' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'KVKK_Basvuru_Formu.txt';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        
        // Bildirim göster
        showAlert('Başvuru formu indiriliyor...', 'success');
    }
    
    // Bildirim gösterme
    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alertDiv.style.top = '20px';
        alertDiv.style.right = '20px';
        alertDiv.style.zIndex = '9999';
        alertDiv.style.minWidth = '300px';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(alertDiv);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
    
    // Sayfa yüklendiğinde
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
        const sections = document.querySelectorAll('.gdpr-content section');
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
    /* Özel Renk Paleti */
    :root {
        --vibrant-purple: #8B5CF6;
        --vibrant-purple-dark: #7C3AED;
        --vibrant-teal: #06B6D4;
        --vibrant-teal-dark: #0EA5E9;
        --vibrant-green: #10B981;
        --vibrant-green-dark: #059669;
        --vibrant-amber: #F59E0B;
        --vibrant-amber-dark: #D97706;
        --vibrant-red: #EF4444;
        --vibrant-red-dark: #DC2626;
    }

    .alert-vibrant {
        background: linear-gradient(135deg, #F0F9FF 0%, #E0F2FE 100%);
        border-left: 4px solid var(--vibrant-purple);
        color: #1E293B;
    }

    .border-vibrant {
        border-color: var(--vibrant-purple) !important;
    }

    .compliance-overview .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 2px solid transparent;
    }
    
    .compliance-overview .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(139, 92, 246, 0.15);
    }
    
    .table th {
        border-top: none;
        font-weight: 600;
        background: linear-gradient(135deg, #F8FAFC 0%, #F1F5F9 100%);
        color: var(--vibrant-purple-dark);
    }
    
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    
    .alert {
        transition: all 0.3s ease;
    }
    
    .alert:hover {
        transform: scale(1.02);
    }

    /* Özel buton stilleri */
    .btn-primary {
        background: linear-gradient(135deg, var(--vibrant-purple) 0%, var(--vibrant-purple-dark) 100%);
        border: none;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(139, 92, 246, 0.4);
    }

    /* Gradient arkaplanlar */
    .bg-gradient-primary {
        background: linear-gradient(135deg, var(--vibrant-purple) 0%, var(--vibrant-purple-dark) 100%);
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, var(--vibrant-green) 0%, var(--vibrant-green-dark) 100%);
    }

    .bg-gradient-info {
        background: linear-gradient(135deg, var(--vibrant-teal) 0%, var(--vibrant-teal-dark) 100%);
    }

    .bg-gradient-warning {
        background: linear-gradient(135deg, var(--vibrant-amber) 0%, var(--vibrant-amber-dark) 100%);
    }
    
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.875rem;
        }
        
        .compliance-overview .row {
            flex-direction: column;
        }

        .card-body {
            padding: 1.5rem !important;
        }
    }
</style>