<?php
require_once 'includes/config.php';

$pageTitle = "Yardım Merkezi - " . SITE_NAME;
$pageDescription = "Sıkça sorulan sorular, kullanım kılavuzları ve teknik destek";
$pageKeywords = "yardım, destek, SSS, sıkça sorulan sorular, kullanım kılavuzu, teknik destek";

// Header dosyasını include et
include 'themes/google-modern/header.php';
?>

<div class="container mt-4">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <h1 class="hero-title fade-in-up">Yardım Merkezi</h1>
            <p class="hero-subtitle fade-in-up">Sıkça sorulan sorular, kullanım kılavuzları ve teknik destek</p>
        </div>
    </div>

    <div class="row mt-5">
        <!-- Sol Menü - Kategoriler -->
        <div class="col-lg-3">
            <div class="sticky-top" style="top: 100px;">
                <!-- Hızlı Arama -->
                <div class="card mb-4">
                    <div class="card-header" style="background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); color: white;">
                        <h6 class="mb-0"><i class="fas fa-search me-2"></i>Hızlı Arama</h6>
                    </div>
                    <div class="card-body">
                        <div class="input-group">
                            <input type="text" id="helpSearch" class="form-control" placeholder="Sorunuzu ara...">
                            <button class="btn btn-outline-primary" type="button" onclick="performSearch()" style="border-color: #6a11cb; color: #6a11cb;">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Yardım Kategorileri -->
                <div class="card mb-4">
                    <div class="card-header" style="background: linear-gradient(135deg, #00b09b 0%, #96c93d 100%); color: white;">
                        <h6 class="mb-0"><i class="fas fa-folder me-2"></i>Yardım Kategorileri</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <a href="#general" class="list-group-item list-group-item-action active-category">
                                <i class="fas fa-question-circle me-2"></i>Genel Sorular
                            </a>
                            <a href="#account" class="list-group-item list-group-item-action">
                                <i class="fas fa-user me-2"></i>Hesap İşlemleri
                            </a>
                            <a href="#content" class="list-group-item list-group-item-action">
                                <i class="fas fa-newspaper me-2"></i>İçerik Yönetimi
                            </a>
                            <a href="#technical" class="list-group-item list-group-item-action">
                                <i class="fas fa-cog me-2"></i>Teknik Sorunlar
                            </a>
                            <a href="#privacy" class="list-group-item list-group-item-action">
                                <i class="fas fa-shield-alt me-2"></i>Gizlilik ve Güvenlik
                            </a>
                            <a href="#mobile" class="list-group-item list-group-item-action">
                                <i class="fas fa-mobile-alt me-2"></i>Mobil Kullanım
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Acil Destek -->
                <div class="card">
                    <div class="card-header" style="background: linear-gradient(135deg, #ff9a00 0%, #ff6a00 100%); color: white;">
                        <h6 class="mb-0"><i class="fas fa-life-ring me-2"></i>Acil Destek</h6>
                    </div>
                    <div class="card-body">
                        <p class="small mb-3">Hemen yardıma mı ihtiyacınız var?</p>
                        <div class="d-grid gap-2">
                            <a href="contact.php" class="btn btn-danger btn-sm">
                                <i class="fas fa-envelope me-1"></i>İletişim Formu
                            </a>
                            <button class="btn btn-outline-warning btn-sm" onclick="startLiveChat()" style="border-color: #ff9a00; color: #ff9a00;">
                                <i class="fas fa-comments me-1"></i>Canlı Destek
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Faydalı Linkler -->
                <div class="card mt-4">
                    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <h6 class="mb-0"><i class="fas fa-link me-2"></i>Faydalı Linkler</h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <a href="faq.php" class="list-group-item list-group-item-action border-0 px-0 py-2">
                                <i class="fas fa-list-alt me-2" style="color: #6a11cb;"></i>SSS
                            </a>
                            <a href="tutorials.php" class="list-group-item list-group-item-action border-0 px-0 py-2">
                                <i class="fas fa-graduation-cap me-2" style="color: #00b09b;"></i>Eğitimler
                            </a>
                            <a href="blog.php" class="list-group-item list-group-item-action border-0 px-0 py-2">
                                <i class="fas fa-blog me-2" style="color: #ff9a00;"></i>Yardım Blogu
                            </a>
                            <a href="status.php" class="list-group-item list-group-item-action border-0 px-0 py-2">
                                <i class="fas fa-server me-2" style="color: #667eea;"></i>Sistem Durumu
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ana İçerik -->
        <div class="col-lg-9">
            <!-- Arama Sonuçları -->
            <div id="searchResults" class="card mb-4 d-none">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-search me-2"></i>Arama Sonuçları</h5>
                    <button class="btn btn-sm btn-outline-secondary" onclick="clearSearch()">Temizle</button>
                </div>
                <div class="card-body">
                    <div id="searchResultsContent"></div>
                </div>
            </div>

            <!-- Yardım İçeriği -->
            <div class="help-content">
                <!-- Genel Sorular -->
                <section id="general" class="help-section mb-5">
                    <div class="section-header mb-4">
                        <h3 style="color: #6a11cb;">
                            <i class="fas fa-question-circle me-2"></i>
                            Genel Sorular
                        </h3>
                        <p class="text-muted">Sitemiz hakkında en çok sorulan genel sorular</p>
                    </div>

                    <div class="accordion" id="generalAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#general1">
                                    <?php echo SITE_NAME; ?> nedir ve ne sunar?
                                </button>
                            </h2>
                            <div id="general1" class="accordion-collapse collapse show" data-bs-parent="#generalAccordion">
                                <div class="accordion-body">
                                    <p>
                                        <?php echo SITE_NAME; ?>, memur bilgi platformu olarak hizmet veren bir web sitesidir. 
                                        Size şunları sunar:
                                    </p>
                                    <ul>
                                        <li>Güncel memur haberleri ve duyuruları</li>
                                        <li>Kapsamlı rehberlik içerikleri</li>
                                        <li>Yasal mevzuat bilgileri</li>
                                        <li>Kariyer gelişim ipuçları</li>
                                        <li>Topluluk desteği ve deneyim paylaşımı</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#general2">
                                    Siteyi ücretsiz kullanabilir miyim?
                                </button>
                            </h2>
                            <div id="general2" class="accordion-collapse collapse" data-bs-parent="#generalAccordion">
                                <div class="accordion-body">
                                    <p>
                                        Evet, <?php echo SITE_NAME; ?> tamamen ücretsizdir. Temel özelliklerin tümünü 
                                        herhangi bir ücret ödemeden kullanabilirsiniz.
                                    </p>
                                    <div class="alert alert-info">
                                        <strong>Not:</strong> Premium özellikler eklenirse, bunlar için ayrıca 
                                        ücretlendirme yapılacak ve şeffaf şekilde duyurulacaktır.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#general3">
                                    İçerikler ne sıklıkla güncelleniyor?
                                </button>
                            </h2>
                            <div id="general3" class="accordion-collapse collapse" data-bs-parent="#generalAccordion">
                                <div class="accordion-body">
                                    <p>
                                        İçeriklerimiz düzenli olarak güncellenmektedir:
                                    </p>
                                    <ul>
                                        <li><strong>Haberler:</strong> Günlük</li>
                                        <li><strong>Rehberler:</strong> Haftalık</li>
                                        <li><strong>Mevzuat:</strong> Değişiklik oldukça</li>
                                        <li><strong>Genel içerik:</strong> Aylık kontrol</li>
                                    </ul>
                                    <p class="mb-0">
                                        En güncel içerikler için bültenimize abone olabilirsiniz.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Hesap İşlemleri -->
                <section id="account" class="help-section mb-5">
                    <div class="section-header mb-4">
                        <h3 style="color: #00b09b;">
                            <i class="fas fa-user me-2"></i>
                            Hesap İşlemleri
                        </h3>
                        <p class="text-muted">Hesap oluşturma, giriş ve yönetim ile ilgili sorular</p>
                    </div>

                    <div class="accordion" id="accountAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#account1">
                                    Nasıl hesap oluşturabilirim?
                                </button>
                            </h2>
                            <div id="account1" class="accordion-collapse collapse show" data-bs-parent="#accountAccordion">
                                <div class="accordion-body">
                                    <p>Hesap oluşturmak için aşağıdaki adımları izleyin:</p>
                                    <ol>
                                        <li>Sağ üst köşedeki "Kayıt Ol" butonuna tıklayın</li>
                                        <li>Email adresinizi ve güçlü bir şifre girin</li>
                                        <li>Email adresinize gelen doğrulama linkine tıklayın</li>
                                        <li>Profil bilgilerinizi tamamlayın</li>
                                    </ol>
                                    <div class="alert alert-warning">
                                        <strong>Önemli:</strong> Spam klasörünüzü kontrol etmeyi unutmayın.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#account2">
                                    Şifremi unuttum, ne yapmalıyım?
                                </button>
                            </h2>
                            <div id="account2" class="accordion-collapse collapse" data-bs-parent="#accountAccordion">
                                <div class="accordion-body">
                                    <p>Şifrenizi unuttuysanız:</p>
                                    <ol>
                                        <li>Giriş sayfasındaki "Şifremi Unuttum" linkine tıklayın</li>
                                        <li>Kayıtlı email adresinizi girin</li>
                                        <li>Emailinize gelen şifre sıfırlama linkine tıklayın</li>
                                        <li>Yeni şifrenizi belirleyin</li>
                                    </ol>
                                    <p class="mb-0">
                                        <strong>Sorun yaşıyorsanız:</strong> 
                                        <a href="contact.php" class="text-decoration-none">destek ekibimizle iletişime geçin</a>.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#account3">
                                    Hesabımı nasıl silebilirim?
                                </button>
                            </h2>
                            <div id="account3" class="accordion-collapse collapse" data-bs-parent="#accountAccordion">
                                <div class="accordion-body">
                                    <p>Hesabınızı silmek için:</p>
                                    <ol>
                                        <li>Hesap ayarlarına gidin</li>
                                        <li>"Hesabı Sil" bölümünü bulun</li>
                                        <li>Silme nedeninizi belirtin</li>
                                        <li>Onaylayın ve şifrenizi girin</li>
                                    </ol>
                                    <div class="alert alert-danger">
                                        <strong>Dikkat:</strong> Hesap silme işlemi geri alınamaz. 
                                        Tüm verileriniz kalıcı olarak silinecektir.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- İçerik Yönetimi -->
                <section id="content" class="help-section mb-5">
                    <div class="section-header mb-4">
                        <h3 style="color: #ff9a00;">
                            <i class="fas fa-newspaper me-2"></i>
                            İçerik Yönetimi
                        </h3>
                        <p class="text-muted">İçerik okuma, yorum yapma ve paylaşım ile ilgili sorular</p>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-book-reader fa-2x mb-3" style="color: #6a11cb;"></i>
                                    <h6>İçerik Okuma</h6>
                                    <p class="small mb-0">Yazıları okuma, favorilere ekleme ve kaydetme</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-comments fa-2x mb-3" style="color: #00b09b;"></i>
                                    <h6>Yorum Yapma</h6>
                                    <p class="small mb-0">Yorum yazma, düzenleme ve silme işlemleri</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="accordion" id="contentAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#content1">
                                    Yorumlarım neden onay bekliyor?
                                </button>
                            </h2>
                            <div id="content1" class="accordion-collapse collapse show" data-bs-parent="#contentAccordion">
                                <div class="accordion-body">
                                    <p>
                                        İlk yorumlarınız ve bazı durumlarda tüm yorumlar, spam ve uygunsuz 
                                        içerikleri önlemek için moderasyon sürecinden geçer.
                                    </p>
                                    <ul>
                                        <li><strong>İlk yorumlar:</strong> Otomatik moderasyon</li>
                                        <li><strong>Link içeren yorumlar:</strong> Manuel onay</li>
                                        <li><strong>Belirli anahtar kelimeler:</strong> İnceleme gerekli</li>
                                    </ul>
                                    <p class="mb-0">
                                        Yorumlar genellikle 24 saat içinde incelenir ve onaylanır.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#content2">
                                    İçerik önerme veya katkıda bulunma imkanı var mı?
                                </button>
                            </h2>
                            <div id="content2" class="accordion-collapse collapse" data-bs-parent="#contentAccordion">
                                <div class="accordion-body">
                                    <p>
                                        Evet, içerik önerme ve katkıda bulunma imkanımız bulunuyor:
                                    </p>
                                    <ul>
                                        <li><strong>Hata bildirimi:</strong> Yanlış bilgileri rapor edin</li>
                                        <li><strong>İçerik önerisi:</strong> Yazılmasını istediğiniz konular</li>
                                        <li><strong>Misafir yazarlık:</strong> Deneyimlerinizi paylaşın</li>
                                    </ul>
                                    <p class="mb-0">
                                        Katkıda bulunmak için 
                                        <a href="contact.php" class="text-decoration-none">iletişim formunu</a> kullanabilirsiniz.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Teknik Sorunlar -->
                <section id="technical" class="help-section mb-5">
                    <div class="section-header mb-4">
                        <h3 style="color: #667eea;">
                            <i class="fas fa-cog me-2"></i>
                            Teknik Sorunlar
                        </h3>
                        <p class="text-muted">Site performansı, hata mesajları ve teknik problemler</p>
                    </div>

                    <div class="alert alert-warning mb-4">
                        <h6 class="alert-heading">
                            <i class="fas fa-tools me-2"></i>
                            Hızlı Çözüm Önerileri
                        </h6>
                        <p class="mb-0">
                            Bir teknik sorun yaşıyorsanız, öncelikle tarayıcı önbelleğinizi temizlemeyi 
                            ve sayfayı yenilemeyi deneyin.
                        </p>
                    </div>

                    <div class="accordion" id="technicalAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#technical1">
                                    "404 Sayfa Bulunamadı" hatası alıyorum
                                </button>
                            </h2>
                            <div id="technical1" class="accordion-collapse collapse show" data-bs-parent="#technicalAccordion">
                                <div class="accordion-body">
                                    <p>404 hatası genellikle şu nedenlerle oluşur:</p>
                                    <ul>
                                        <li>Yanlış URL adresi</li>
                                        <li>Silinmiş veya taşınmış içerik</li>
                                        <li>Geçersiz bağlantılar</li>
                                    </ul>
                                    <p><strong>Çözüm yolları:</strong></p>
                                    <ol>
                                        <li>URL'yi kontrol edin ve düzeltin</li>
                                        <li>Ana sayfaya dönüp arama yapın</li>
                                        <li>Site haritasını kullanın</li>
                                        <li>Hata devam ederse bildirin</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#technical2">
                                    Site çok yavaş çalışıyor
                                </button>
                            </h2>
                            <div id="technical2" class="accordion-collapse collapse" data-bs-parent="#technicalAccordion">
                                <div class="accordion-body">
                                    <p>Yavaşlık sorunu için:</p>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Tarayıcı Tarafında:</h6>
                                            <ul>
                                                <li>Önbelleği temizleyin</li>
                                                <li>Gereksiz sekmeleri kapatın</li>
                                                <li>Tarayıcıyı güncelleyin</li>
                                                <li>Eklentileri devre dışı bırakın</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>İnternet Tarafında:</h6>
                                            <ul>
                                                <li>Bağlantı hızınızı test edin</li>
                                                <li>Modemi yeniden başlatın</li>
                                                <li>Farklı cihaz deneyin</li>
                                                <li>Farklı ağ deneyin</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Gizlilik ve Güvenlik -->
                <section id="privacy" class="help-section mb-5">
                    <div class="section-header mb-4">
                        <h3 style="color: #764ba2;">
                            <i class="fas fa-shield-alt me-2"></i>
                            Gizlilik ve Güvenlik
                        </h3>
                        <p class="text-muted">Veri güvenliği, gizlilik ayarları ve hesap güvenliği</p>
                    </div>

                    <div class="accordion" id="privacyAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#privacy1">
                                    Verilerim nasıl korunuyor?
                                </button>
                            </h2>
                            <div id="privacy1" class="accordion-collapse collapse show" data-bs-parent="#privacyAccordion">
                                <div class="accordion-body">
                                    <p>Veri güvenliğiniz için aşağıdaki önlemleri alıyoruz:</p>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Teknik Önlemler:</h6>
                                            <ul>
                                                <li>SSL şifreleme</li>
                                                <li>Güvenli sunucu altyapısı</li>
                                                <li>Düzenli yedekleme</li>
                                                <li>Güvenlik duvarı</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Yasal Önlemler:</h6>
                                            <ul>
                                                <li>KVKK uyumu</li>
                                                <li>GDPR uyumu</li>
                                                <li>Veri işleme sözleşmeleri</li>
                                                <li>Düzenli denetimler</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#privacy2">
                                    Hesabımı nasıl daha güvenli hale getirebilirim?
                                </button>
                            </h2>
                            <div id="privacy2" class="accordion-collapse collapse" data-bs-parent="#privacyAccordion">
                                <div class="accordion-body">
                                    <p>Hesap güvenliğinizi artırmak için:</p>
                                    <ul>
                                        <li><strong>Güçlü şifre kullanın:</strong> Büyük/küçük harf, sayı ve sembol içeren</li>
                                        <li><strong>Şifrenizi paylaşmayın:</strong> Hiç kimseyle şifrenizi paylaşmayın</li>
                                        <li><strong>Ortak cihazlarda çıkış yapın:</strong> İşlemlerinizi tamamladıktan sonra çıkış yapın</li>
                                        <li><strong>Şüpheli linklere tıklamayın:</strong> Güvenmediğiniz linklerden uzak durun</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Mobil Kullanım -->
                <section id="mobile" class="help-section mb-5">
                    <div class="section-header mb-4">
                        <h3 style="color: #2575fc;">
                            <i class="fas fa-mobile-alt me-2"></i>
                            Mobil Kullanım
                        </h3>
                        <p class="text-muted">Mobil cihazlarda site kullanımı ve uygulama bilgileri</p>
                    </div>

                    <div class="accordion" id="mobileAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#mobile1">
                                    Mobil uygulamanız var mı?
                                </button>
                            </h2>
                            <div id="mobile1" class="accordion-collapse collapse show" data-bs-parent="#mobileAccordion">
                                <div class="accordion-body">
                                    <p>
                                        Şu anda resmi bir mobil uygulamamız bulunmuyor. Ancak web sitemiz 
                                        tamamen mobil uyumlu olarak tasarlandı. Mobil tarayıcınızdan 
                                        sitemize erişebilir ve tüm özellikleri kullanabilirsiniz.
                                    </p>
                                    <div class="alert alert-info">
                                        <strong>Mobil Optimizasyon:</strong> Sitemiz tüm mobil cihazlarda 
                                        (iOS, Android) en iyi deneyimi sunacak şekilde optimize edilmiştir.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#mobile2">
                                    Ana ekrana nasıl eklerim?
                                </button>
                            </h2>
                            <div id="mobile2" class="accordion-collapse collapse" data-bs-parent="#mobileAccordion">
                                <div class="accordion-body">
                                    <p>Sitemizi ana ekranınıza şu şekilde ekleyebilirsiniz:</p>
                                    
                                    <h6>iOS (Safari):</h6>
                                    <ol>
                                        <li>Safari'de sitemizi açın</li>
                                        <li>Paylaş butonuna tıklayın (kare ve ok simgesi)</li>
                                        <li>"Ana Ekrana Ekle" seçeneğini seçin</li>
                                        <li>İsmi onaylayın ve "Ekle" butonuna tıklayın</li>
                                    </ol>

                                    <h6>Android (Chrome):</h6>
                                    <ol>
                                        <li>Chrome'da sitemizi açın</li>
                                        <li>Menü butonuna tıklayın (üç nokta)</li>
                                        <li>"Ana ekrana ekle" seçeneğini seçin</li>
                                        <li>İsmi onaylayın ve "Ekle" butonuna tıklayın</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Yardıma İhtiyacınız Var? -->
                <div class="card text-white mt-5" style="background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);">
                    <div class="card-body text-center p-5">
                        <h3 class="card-title mb-3">
                            <i class="fas fa-hands-helping me-2"></i>
                            Hala Yardıma İhtiyacınız Var?
                        </h3>
                        <p class="card-text mb-4">
                            Sorunuzu bulamadıysanız veya kişisel yardıma ihtiyacınız varsa, 
                            destek ekibimiz size yardımcı olmaktan mutluluk duyacaktır.
                        </p>
                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <a href="contact.php" class="btn btn-light btn-lg">
                                <i class="fas fa-envelope me-2"></i>İletişim Formu
                            </a>
                            <a href="mailto:mail@blog.blog" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-at me-2"></i>Email Gönder
                            </a>
                            <button class="btn btn-outline-light btn-lg" onclick="startLiveChat()">
                                <i class="fas fa-comments me-2"></i>Canlı Destek
                            </button>
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
    // Arama fonksiyonu
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('helpSearch');
        const searchResults = document.getElementById('searchResults');
        const searchResultsContent = document.getElementById('searchResultsContent');
        
        // Enter tuşu ile arama
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });

        // Kategori navigasyonu
        document.querySelectorAll('.list-group-item-action').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetSection = document.querySelector(targetId);
                
                if (targetSection) {
                    // Aktif kategoriyi güncelle
                    document.querySelectorAll('.list-group-item-action').forEach(i => {
                        i.classList.remove('active-category');
                    });
                    this.classList.add('active-category');
                    
                    // Bölüme kaydır
                    targetSection.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    });

    function performSearch() {
        const searchTerm = document.getElementById('helpSearch').value.trim().toLowerCase();
        const searchResults = document.getElementById('searchResults');
        const searchResultsContent = document.getElementById('searchResultsContent');
        
        if (searchTerm.length < 2) {
            showAlert('Lütfen en az 2 karakter giriniz.', 'warning');
            return;
        }

        // Tüm soruları ara
        const allQuestions = document.querySelectorAll('.accordion-button');
        let results = [];
        
        allQuestions.forEach(question => {
            const questionText = question.textContent.toLowerCase();
            if (questionText.includes(searchTerm)) {
                const accordionItem = question.closest('.accordion-item');
                const answer = accordionItem.querySelector('.accordion-body').innerHTML;
                results.push({
                    question: questionText,
                    answer: answer,
                    element: accordionItem
                });
            }
        });

        // Sonuçları göster
        if (results.length > 0) {
            searchResultsContent.innerHTML = '';
            results.forEach((result, index) => {
                const resultElement = document.createElement('div');
                resultElement.className = 'search-result-item mb-3';
                resultElement.innerHTML = `
                    <h6 style="color: #6a11cb;">${index + 1}. ${result.question}</h6>
                    <div class="search-answer">${result.answer}</div>
                    <hr>
                `;
                searchResultsContent.appendChild(resultElement);
            });
            
            searchResults.classList.remove('d-none');
            searchResults.scrollIntoView({ behavior: 'smooth' });
        } else {
            searchResultsContent.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aramanızla eşleşen sonuç bulunamadı</h5>
                    <p class="text-muted">Farklı anahtar kelimelerle deneyin veya destek ekibimizle iletişime geçin.</p>
                </div>
            `;
            searchResults.classList.remove('d-none');
        }
    }

    function clearSearch() {
        document.getElementById('helpSearch').value = '';
        document.getElementById('searchResults').classList.add('d-none');
        document.getElementById('helpSearch').focus();
    }

    function startLiveChat() {
        showAlert('Canlı destek şu anda mevcut değil. Lütfen iletişim formunu kullanın.', 'info');
        // Gerçek canlı destek entegrasyonu burada yapılabilir
    }

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

    // Sayfa yüklendiğinde ilk bölümü aktif yap
    document.addEventListener('DOMContentLoaded', function() {
        const firstCategory = document.querySelector('.list-group-item-action');
        if (firstCategory) {
            firstCategory.classList.add('active-category');
        }
    });
</script>

<style>
    .help-section {
        padding: 2rem 0;
        border-bottom: 1px solid #e9ecef;
    }

    .help-section:last-child {
        border-bottom: none;
    }

    .section-header {
        padding-bottom: 1rem;
        border-bottom: 2px solid #6a11cb;
    }

    .active-category {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%) !important;
        color: white !important;
        border-color: #6a11cb !important;
    }

    .search-result-item {
        padding: 1rem;
        border-radius: 8px;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }

    .search-result-item:hover {
        background-color: #e9ecef;
        transform: translateX(5px);
    }

    .search-answer {
        max-height: 200px;
        overflow-y: auto;
        padding: 1rem;
        background: white;
        border-radius: 5px;
        border-left: 4px solid #6a11cb;
    }

    .accordion-button:not(.collapsed) {
        background-color: rgba(106, 17, 203, 0.1);
        color: #6a11cb;
        border-color: #6a11cb;
    }

    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
    }

    @media (max-width: 768px) {
        .sticky-top {
            position: relative !important;
            top: 0 !important;
        }
        
        .help-section {
            padding: 1rem 0;
        }
    }

    .alert {
        transition: all 0.3s ease;
    }

    .alert:hover {
        transform: scale(1.02);
    }

    /* Özel renk paleti */
    .text-primary-custom { color: #6a11cb !important; }
    .text-success-custom { color: #00b09b !important; }
    .text-warning-custom { color: #ff9a00 !important; }
    .text-info-custom { color: #667eea !important; }
    .text-purple-custom { color: #764ba2 !important; }
    .text-blue-custom { color: #2575fc !important; }
</style>