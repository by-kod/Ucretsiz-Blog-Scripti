<?php
require_once 'includes/config.php';

$pageTitle = "Sıkça Sorulan Sorular - " . SITE_NAME;
$pageDescription = "En çok sorulan sorular ve cevapları - Hızlı çözüm ve yardım";
$pageKeywords = "SSS, sıkça sorulan sorular, FAQ, yardım, destek, soru-cevap";

// Header dosyasını include et
include 'themes/google-modern/header.php';
?>

<div class="container mt-4">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <h1 class="hero-title fade-in-up">Sıkça Sorulan Sorular</h1>
            <p class="hero-subtitle fade-in-up">En çok merak edilen sorular ve detaylı cevapları</p>
        </div>
    </div>

    <div class="row mt-5">
        <!-- Sol Menü - Kategori Filtresi -->
        <div class="col-lg-3">
            <div class="sticky-top" style="top: 100px;">
                <!-- Kategori Filtresi -->
                <div class="card mb-4">
                    <div class="card-header" style="background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%); color: white;">
                        <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Kategori Filtresi</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <button class="list-group-item list-group-item-action active" onclick="filterFAQs('all')">
                                <i class="fas fa-layer-group me-2"></i>Tüm Sorular
                                <span class="badge float-end" style="background: #8B5CF6;" id="count-all">0</span>
                            </button>
                            <button class="list-group-item list-group-item-action" onclick="filterFAQs('general')">
                                <i class="fas fa-question-circle me-2"></i>Genel
                                <span class="badge bg-secondary float-end" id="count-general">0</span>
                            </button>
                            <button class="list-group-item list-group-item-action" onclick="filterFAQs('account')">
                                <i class="fas fa-user me-2"></i>Hesap
                                <span class="badge bg-secondary float-end" id="count-account">0</span>
                            </button>
                            <button class="list-group-item list-group-item-action" onclick="filterFAQs('content')">
                                <i class="fas fa-newspaper me-2"></i>İçerik
                                <span class="badge bg-secondary float-end" id="count-content">0</span>
                            </button>
                            <button class="list-group-item list-group-item-action" onclick="filterFAQs('technical')">
                                <i class="fas fa-cog me-2"></i>Teknik
                                <span class="badge bg-secondary float-end" id="count-technical">0</span>
                            </button>
                            <button class="list-group-item list-group-item-action" onclick="filterFAQs('privacy')">
                                <i class="fas fa-shield-alt me-2"></i>Gizlilik
                                <span class="badge bg-secondary float-end" id="count-privacy">0</span>
                            </button>
                            <button class="list-group-item list-group-item-action" onclick="filterFAQs('mobile')">
                                <i class="fas fa-mobile-alt me-2"></i>Mobil
                                <span class="badge bg-secondary float-end" id="count-mobile">0</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Hızlı İstatistikler -->
                <div class="card mb-4">
                    <div class="card-header" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: white;">
                        <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>İstatistikler</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <div class="fw-bold fs-4" style="color: #8B5CF6;" id="total-questions">0</div>
                                <small class="text-muted">Toplam Soru</small>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="fw-bold fs-4" style="color: #10B981;" id="popular-count">0</div>
                                <small class="text-muted">Popüler Soru</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hızlı Erişim -->
                <div class="card">
                    <div class="card-header" style="background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%); color: white;">
                        <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Hızlı Erişim</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="help.php" class="btn btn-outline-primary btn-sm" style="border-color: #8B5CF6; color: #8B5CF6;">
                                <i class="fas fa-life-ring me-1"></i>Yardım Merkezi
                            </a>
                            <a href="contact.php" class="btn btn-outline-success btn-sm" style="border-color: #10B981; color: #10B981;">
                                <i class="fas fa-envelope me-1"></i>İletişim
                            </a>
                            <button class="btn btn-outline-warning btn-sm" style="border-color: #F59E0B; color: #F59E0B;" onclick="scrollToPopular()">
                                <i class="fas fa-fire me-1"></i>Popüler Sorular
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ana İçerik -->
        <div class="col-lg-9">
            <!-- Arama Çubuğu -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" id="faqSearch" class="form-control border-start-0" placeholder="Sorunuzu arayın...">
                                <button class="btn" type="button" onclick="searchFAQs()" style="background: #8B5CF6; color: white;">
                                    <i class="fas fa-search me-1"></i>Ara
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="btn-group">
                                <button class="btn btn-outline-secondary btn-sm" onclick="expandAll()">
                                    <i class="fas fa-expand me-1"></i>Tümünü Aç
                                </button>
                                <button class="btn btn-outline-secondary btn-sm" onclick="collapseAll()">
                                    <i class="fas fa-compress me-1"></i>Tümünü Kapat
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Arama Sonuçları -->
            <div id="searchResults" class="card mb-4 d-none">
                <div class="card-header" style="background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%); color: white;">
                    <h6 class="mb-0">
                        <i class="fas fa-search me-2"></i>
                        Arama Sonuçları
                        <span class="badge bg-dark ms-2" id="resultCount">0</span>
                        <button class="btn btn-sm btn-outline-light float-end" onclick="clearSearch()">
                            <i class="fas fa-times me-1"></i>Temizle
                        </button>
                    </h6>
                </div>
                <div class="card-body">
                    <div id="searchResultsContent"></div>
                </div>
            </div>

            <!-- Popüler Sorular -->
            <div class="popular-faqs mb-5">
                <div class="section-header mb-4">
                    <h3 style="color: #8B5CF6;">
                        <i class="fas fa-fire me-2"></i>
                        En Popüler Sorular
                    </h3>
                    <p class="text-muted">En çok aranan ve okunan sorular</p>
                </div>

                <div class="row" id="popularFAQs">
                    <!-- Popüler sorular buraya JavaScript ile eklenecek -->
                </div>
            </div>

            <!-- Tüm SSS -->
            <div class="all-faqs">
                <div class="section-header mb-4">
                    <h3 style="color: #8B5CF6;">
                        <i class="fas fa-list-alt me-2"></i>
                        Tüm Sorular
                    </h3>
                    <p class="text-muted">Kategorilere göre düzenlenmiş tüm sorular</p>
                </div>

                <!-- Genel Sorular -->
                <div class="faq-category mb-5" data-category="general">
                    <h4 class="category-title mb-4">
                        <i class="fas fa-question-circle me-2" style="color: #8B5CF6;"></i>
                        Genel Sorular
                    </h4>
                    <div class="accordion" id="generalAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#general1" data-popular="true">
                                    <i class="fas fa-star me-2" style="color: #F59E0B;"></i>
                                    <?php echo SITE_NAME; ?> tam olarak nedir?
                                </button>
                            </h2>
                            <div id="general1" class="accordion-collapse collapse show" data-bs-parent="#generalAccordion">
                                <div class="accordion-body">
                                    <p>
                                        <strong><?php echo SITE_NAME; ?></strong>, memur bilgi platformu olarak hizmet veren kapsamlı bir web sitesidir. 
                                        Amacımız, memurlar ve kamu personeli için güncel, güvenilir ve kullanışlı bilgiler sunmaktır.
                                    </p>
                                    <p><strong>Sunulan Hizmetler:</strong></p>
                                    <ul>
                                        <li>Güncel memur haberleri ve duyuruları</li>
                                        <li>Detaylı rehberlik içerikleri</li>
                                        <li>Yasal mevzuat güncellemeleri</li>
                                        <li>Kariyer gelişim ipuçları</li>
                                        <li>Soru-cevap topluluğu</li>
                                        <li>Faydalı kaynaklar ve dokümanlar</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#general2">
                                    Siteyi kullanmak ücretsiz mi?
                                </button>
                            </h2>
                            <div id="general2" class="accordion-collapse collapse" data-bs-parent="#generalAccordion">
                                <div class="accordion-body">
                                    <p>
                                        <strong style="color: #10B981;">Evet, tamamen ücretsiz!</strong> 
                                        <?php echo SITE_NAME; ?>'da sunulan tüm içerik ve hizmetler herkes için ücretsizdir.
                                    </p>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Not:</strong> Gelecekte premium özellikler eklenirse, 
                                        bunlar her zaman şeffaf şekilde duyurulacak ve mevcut ücretsiz 
                                        içerikler etkilenmeyecektir.
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
                                    <p>İçerik güncelleme periyotlarımız:</p>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>İçerik Türü</th>
                                                    <th>Güncelleme Sıklığı</th>
                                                    <th>Son Kontrol</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Haberler</td>
                                                    <td><span class="badge" style="background: #10B981;">Günlük</span></td>
                                                    <td>Her gün</td>
                                                </tr>
                                                <tr>
                                                    <td>Rehberler</td>
                                                    <td><span class="badge" style="background: #3B82F6;">Haftalık</span></td>
                                                    <td>Her Pazartesi</td>
                                                </tr>
                                                <tr>
                                                    <td>Mevzuat</td>
                                                    <td><span class="badge" style="background: #8B5CF6;">Değişiklik Oldukça</span></td>
                                                    <td>Sürekli</td>
                                                </tr>
                                                <tr>
                                                    <td>Genel İçerik</td>
                                                    <td><span class="badge" style="background: #F59E0B;">Aylık</span></td>
                                                    <td>Her ayın ilk haftası</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hesap Soruları -->
                <div class="faq-category mb-5" data-category="account">
                    <h4 class="category-title mb-4">
                        <i class="fas fa-user me-2" style="color: #10B981;"></i>
                        Hesap İşlemleri
                    </h4>
                    <div class="accordion" id="accountAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#account1" data-popular="true">
                                    <i class="fas fa-star me-2" style="color: #F59E0B;"></i>
                                    Nasıl hesap oluşturabilirim?
                                </button>
                            </h2>
                            <div id="account1" class="accordion-collapse collapse show" data-bs-parent="#accountAccordion">
                                <div class="accordion-body">
                                    <p>Hesap oluşturmak için basit adımlar:</p>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card border-0 bg-light h-100">
                                                <div class="card-body">
                                                    <h6 class="card-title" style="color: #8B5CF6;">
                                                        <i class="fas fa-mobile-alt me-2"></i>
                                                        Mobil Cihazlarda
                                                    </h6>
                                                    <ol class="small">
                                                        <li>Sağ üst menüye tıklayın</li>
                                                        <li>"Kayıt Ol" butonunu seçin</li>
                                                        <li>Formu doldurun</li>
                                                        <li>Email doğrulaması yapın</li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card border-0 bg-light h-100">
                                                <div class="card-body">
                                                    <h6 class="card-title" style="color: #8B5CF6;">
                                                        <i class="fas fa-desktop me-2"></i>
                                                        Masaüstünde
                                                    </h6>
                                                    <ol class="small">
                                                        <li>Sağ üstte "Kayıt Ol" butonuna tıklayın</li>
                                                        <li>Gerekli bilgileri girin</li>
                                                        <li>Emailinizi doğrulayın</li>
                                                        <li>Hesabınız hazır!</li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-warning mt-3">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>Önemli:</strong> Doğrulama email'i spam klasörünüze düşmüş olabilir.
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
                                    <p>Şifre sıfırlama adımları:</p>
                                    <ol>
                                        <li>Giriş sayfasına gidin</li>
                                        <li>"Şifremi Unuttum" linkine tıklayın</li>
                                        <li>Kayıtlı email adresinizi girin</li>
                                        <li>Emailinizdeki linke tıklayın</li>
                                        <li>Yeni şifrenizi belirleyin</li>
                                    </ol>
                                    <div class="alert alert-info">
                                        <i class="fas fa-clock me-2"></i>
                                        <strong>Şifre sıfırlama linki</strong> 24 saat geçerlidir.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- İçerik Soruları -->
                <div class="faq-category mb-5" data-category="content">
                    <h4 class="category-title mb-4">
                        <i class="fas fa-newspaper me-2" style="color: #3B82F6;"></i>
                        İçerik ve Yazılar
                    </h4>
                    <div class="accordion" id="contentAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#content1">
                                    Yorumlarım neden onay bekliyor?
                                </button>
                            </h2>
                            <div id="content1" class="accordion-collapse collapse show" data-bs-parent="#contentAccordion">
                                <div class="accordion-body">
                                    <p>Yorum onay sistemi şu durumlarda devreye girer:</p>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card border-success h-100">
                                                <div class="card-header text-white py-2" style="background: #10B981;">
                                                    <h6 class="mb-0"><i class="fas fa-check me-2"></i>Otomatik Onay</h6>
                                                </div>
                                                <div class="card-body">
                                                    <ul class="mb-0 small">
                                                        <li>Doğrulanmış kullanıcılar</li>
                                                        <li>Daha önce onaylanmış yorumu olanlar</li>
                                                        <li>Belirli kelimeler içermeyen yorumlar</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card border-warning h-100">
                                                <div class="card-header text-dark py-2" style="background: #F59E0B;">
                                                    <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Manuel Onay</h6>
                                                </div>
                                                <div class="card-body">
                                                    <ul class="mb-0 small">
                                                        <li>İlk yorumlar</li>
                                                        <li>Link içeren yorumlar</li>
                                                        <li>Şüpheli içerikler</li>
                                                        <li>Belirli anahtar kelimeler</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mt-3 mb-0">
                                        <i class="fas fa-info-circle me-2" style="color: #8B5CF6;"></i>
                                        Manuel onaylar genellikle <strong>24 saat içinde</strong> tamamlanır.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Teknik Sorular -->
                <div class="faq-category mb-5" data-category="technical">
                    <h4 class="category-title mb-4">
                        <i class="fas fa-cog me-2" style="color: #F59E0B;"></i>
                        Teknik Sorunlar
                    </h4>
                    <div class="accordion" id="technicalAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#technical1" data-popular="true">
                                    <i class="fas fa-star me-2" style="color: #F59E0B;"></i>
                                    Site çok yavaş, ne yapabilirim?
                                </button>
                            </h2>
                            <div id="technical1" class="accordion-collapse collapse show" data-bs-parent="#technicalAccordion">
                                <div class="accordion-body">
                                    <p>Yavaşlık sorunu için çözüm önerileri:</p>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 style="color: #8B5CF6;">Tarayıcı Çözümleri:</h6>
                                            <ul>
                                                <li><strong>Önbelleği temizleyin:</strong> Ctrl+F5 (Windows) veya Cmd+R (Mac)</li>
                                                <li><strong>Gereksiz sekmeleri kapatın</strong></li>
                                                <li><strong>Tarayıcıyı güncelleyin</strong></li>
                                                <li><strong>Eklentileri devre dışı bırakın</strong></li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 style="color: #8B5CF6;">İnternet Çözümleri:</h6>
                                            <ul>
                                                <li><strong>Modemi yeniden başlatın</strong></li>
                                                <li><strong>WiFi sinyalini kontrol edin</strong></li>
                                                <li><strong>Farklı tarayıcı deneyin</strong></li>
                                                <li><strong>Mobil veriye geçin</strong></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="alert alert-success mt-3">
                                        <i class="fas fa-lightbulb me-2"></i>
                                        Bu çözümlerin %90'ı sorunu çözmektedir.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cevap Bulamadınız Mı? -->
                <div class="card bg-light border-0 mt-5">
                    <div class="card-body text-center p-5">
                        <i class="fas fa-question-circle fa-4x text-muted mb-4"></i>
                        <h3 class="mb-3" style="color: #8B5CF6;">Cevabını Bulamadığın Bir Soru Mu Var?</h3>
                        <p class="text-muted mb-4">
                            Tüm sorularınız için destek ekibimiz size yardımcı olmaya hazır.
                        </p>
                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <a href="contact.php" class="btn btn-lg" style="background: #8B5CF6; color: white;">
                                <i class="fas fa-envelope me-2"></i>İletişim Formu
                            </a>
                            <a href="help.php" class="btn btn-outline-primary btn-lg" style="border-color: #8B5CF6; color: #8B5CF6;">
                                <i class="fas fa-life-ring me-2"></i>Yardım Merkezi
                            </a>
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
    // FAQ verileri
    const faqData = {
        'general': [
            {
                id: 'general1',
                question: '<?php echo SITE_NAME; ?> tam olarak nedir?',
                answer: 'Memur bilgi platformu olarak hizmet veren kapsamlı bir web sitesidir.',
                popular: true,
                views: 1250
            },
            {
                id: 'general2',
                question: 'Siteyi kullanmak ücretsiz mi?',
                answer: 'Evet, tamamen ücretsiz! Tüm içerik ve hizmetler herkes için ücretsizdir.',
                popular: false,
                views: 890
            },
            {
                id: 'general3',
                question: 'İçerikler ne sıklıkla güncelleniyor?',
                answer: 'Haberler günlük, rehberler haftalık, mevzuat değişiklik oldukça güncellenir.',
                popular: true,
                views: 756
            }
        ],
        'account': [
            {
                id: 'account1',
                question: 'Nasıl hesap oluşturabilirim?',
                answer: 'Sağ üstteki Kayıt Ol butonuna tıklayarak kolayca hesap oluşturabilirsiniz.',
                popular: true,
                views: 1100
            },
            {
                id: 'account2',
                question: 'Şifremi unuttum, ne yapmalıyım?',
                answer: 'Giriş sayfasındaki Şifremi Unuttum linkini kullanarak şifrenizi sıfırlayabilirsiniz.',
                popular: false,
                views: 650
            }
        ],
        'content': [
            {
                id: 'content1',
                question: 'Yorumlarım neden onay bekliyor?',
                answer: 'İlk yorumlar ve link içeren yorumlar spam önleme için manuel onay gerektirir.',
                popular: false,
                views: 420
            }
        ],
        'technical': [
            {
                id: 'technical1',
                question: 'Site çok yavaş, ne yapabilirim?',
                answer: 'Tarayıcı önbelleğini temizleyin, gereksiz sekmeleri kapatın veya farklı tarayıcı deneyin.',
                popular: true,
                views: 980
            }
        ],
        'privacy': [],
        'mobile': []
    };

    document.addEventListener('DOMContentLoaded', function() {
        updateStatistics();
        loadPopularFAQs();
        setupSearch();
        setupCategoryFilters();
    });

    function updateStatistics() {
        let totalQuestions = 0;
        let popularCount = 0;

        // Kategori sayılarını hesapla
        Object.keys(faqData).forEach(category => {
            const count = faqData[category].length;
            totalQuestions += count;
            
            // Popüler soruları say
            const popularInCategory = faqData[category].filter(faq => faq.popular).length;
            popularCount += popularInCategory;

            // Kategori sayılarını güncelle
            const countElement = document.getElementById(`count-${category}`);
            if (countElement) {
                countElement.textContent = count;
            }
        });

        // Toplam istatistikleri güncelle
        document.getElementById('total-questions').textContent = totalQuestions;
        document.getElementById('popular-count').textContent = popularCount;
        document.getElementById('count-all').textContent = totalQuestions;
    }

    function loadPopularFAQs() {
        const popularContainer = document.getElementById('popularFAQs');
        let popularFAQs = [];

        // Tüm popüler soruları topla
        Object.keys(faqData).forEach(category => {
            const popularInCategory = faqData[category].filter(faq => faq.popular);
            popularFAQs = [...popularFAQs, ...popularInCategory];
        });

        // En çok görüntülenene göre sırala (en fazla 6 tane)
        popularFAQs.sort((a, b) => b.views - a.views).slice(0, 6);

        if (popularFAQs.length > 0) {
            popularContainer.innerHTML = popularFAQs.map((faq, index) => `
                <div class="col-md-6 mb-4">
                    <div class="card h-100" style="border-color: #F59E0B;">
                        <div class="card-header text-dark d-flex justify-content-between align-items-center" style="background: #F59E0B;">
                            <h6 class="mb-0">
                                <i class="fas fa-fire me-2"></i>
                                Popüler Soru
                            </h6>
                            <span class="badge bg-dark">${faq.views} görüntüleme</span>
                        </div>
                        <div class="card-body">
                            <h6 class="card-title">${faq.question}</h6>
                            <p class="card-text small text-muted">${faq.answer.substring(0, 100)}...</p>
                            <button class="btn btn-sm" onclick="scrollToFAQ('${faq.id}')" style="background: #F59E0B; color: white;">
                                <i class="fas fa-eye me-1"></i>Cevapla Gör
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        } else {
            popularContainer.innerHTML = `
                <div class="col-12">
                    <div class="text-center py-4">
                        <i class="fas fa-info-circle fa-2x text-muted mb-3"></i>
                        <p class="text-muted">Henüz popüler soru bulunmuyor.</p>
                    </div>
                </div>
            `;
        }
    }

    function setupSearch() {
        const searchInput = document.getElementById('faqSearch');
        
        // Enter tuşu ile arama
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchFAQs();
            }
        });

        // Real-time arama (opsiyonel)
        searchInput.addEventListener('input', function() {
            if (this.value.length >= 3) {
                // searchFAQs(); // Real-time arama aktif etmek için yorumu kaldırın
            }
        });
    }

    function searchFAQs() {
        const searchTerm = document.getElementById('faqSearch').value.trim().toLowerCase();
        const searchResults = document.getElementById('searchResults');
        const searchResultsContent = document.getElementById('searchResultsContent');
        const resultCount = document.getElementById('resultCount');

        if (searchTerm.length < 2) {
            showAlert('Lütfen en az 2 karakter giriniz.', 'warning');
            return;
        }

        let results = [];
        
        // Tüm sorularda ara
        Object.keys(faqData).forEach(category => {
            faqData[category].forEach(faq => {
                if (faq.question.toLowerCase().includes(searchTerm) || 
                    faq.answer.toLowerCase().includes(searchTerm)) {
                    results.push({
                        ...faq,
                        category: category
                    });
                }
            });
        });

        // Sonuçları göster
        if (results.length > 0) {
            searchResultsContent.innerHTML = results.map((result, index) => `
                <div class="search-result-item mb-3 p-3 border rounded">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="mb-0" style="color: #8B5CF6;">${index + 1}. ${result.question}</h6>
                        <span class="badge" style="background: ${getCategoryColor(result.category)}">${getCategoryName(result.category)}</span>
                    </div>
                    <p class="mb-2">${result.answer}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">${result.views} görüntüleme</small>
                        <button class="btn btn-sm" onclick="scrollToFAQ('${result.id}')" style="background: #8B5CF6; color: white;">
                            <i class="fas fa-arrow-right me-1"></i>Git
                        </button>
                    </div>
                </div>
            `).join('');

            resultCount.textContent = results.length;
            searchResults.classList.remove('d-none');
            searchResults.scrollIntoView({ behavior: 'smooth' });
        } else {
            searchResultsContent.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aramanızla eşleşen sonuç bulunamadı</h5>
                    <p class="text-muted">Farklı anahtar kelimelerle deneyin veya aşağıdaki butondan yardım merkezine gidin.</p>
                    <a href="help.php" class="btn" style="background: #8B5CF6; color: white;">
                        <i class="fas fa-life-ring me-2"></i>Yardım Merkezi
                    </a>
                </div>
            `;
            resultCount.textContent = '0';
            searchResults.classList.remove('d-none');
        }
    }

    function clearSearch() {
        document.getElementById('faqSearch').value = '';
        document.getElementById('searchResults').classList.add('d-none');
        document.getElementById('faqSearch').focus();
    }

    function filterFAQs(category) {
        // Aktif kategori butonunu güncelle
        document.querySelectorAll('.list-group-item').forEach(item => {
            item.classList.remove('active');
        });
        event.target.classList.add('active');

        // Tüm kategorileri göster/gizle
        document.querySelectorAll('.faq-category').forEach(cat => {
            if (category === 'all' || cat.dataset.category === category) {
                cat.style.display = 'block';
            } else {
                cat.style.display = 'none';
            }
        });

        // Popüler soruları gizle (filtreleme sırasında)
        if (category !== 'all') {
            document.querySelector('.popular-faqs').style.display = 'none';
        } else {
            document.querySelector('.popular-faqs').style.display = 'block';
        }
    }

    function setupCategoryFilters() {
        // İlk yüklemede tümünü göster
        filterFAQs('all');
    }

    function scrollToFAQ(faqId) {
        const faqElement = document.getElementById(faqId);
        if (faqElement) {
            // Accordion'u aç
            const accordionButton = faqElement.previousElementSibling;
            if (accordionButton && !accordionButton.classList.contains('collapsed')) {
                accordionButton.click();
            }

            // Scroll yap
            faqElement.scrollIntoView({ behavior: 'smooth', block: 'center' });

            // Vurgula
            faqElement.style.backgroundColor = '#fff3cd';
            setTimeout(() => {
                faqElement.style.backgroundColor = '';
            }, 2000);
        }
    }

    function scrollToPopular() {
        document.querySelector('.popular-faqs').scrollIntoView({ behavior: 'smooth' });
    }

    function expandAll() {
        document.querySelectorAll('.accordion-button.collapsed').forEach(button => {
            button.click();
        });
    }

    function collapseAll() {
        document.querySelectorAll('.accordion-button:not(.collapsed)').forEach(button => {
            button.click();
        });
    }

    function getCategoryColor(category) {
        const colors = {
            'general': '#8B5CF6',
            'account': '#10B981',
            'content': '#3B82F6',
            'technical': '#F59E0B',
            'privacy': '#EF4444',
            'mobile': '#6B7280'
        };
        return colors[category] || '#6B7280';
    }

    function getCategoryName(category) {
        const names = {
            'general': 'Genel',
            'account': 'Hesap',
            'content': 'İçerik',
            'technical': 'Teknik',
            'privacy': 'Gizlilik',
            'mobile': 'Mobil'
        };
        return names[category] || category;
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
</script>

<style>
    .category-title {
        padding-bottom: 0.5rem;
        border-bottom: 2px solid;
        border-image: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%) 1;
    }

    .faq-category[data-category="general"] .category-title {
        border-image: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%) 1;
    }

    .faq-category[data-category="account"] .category-title {
        border-image: linear-gradient(135deg, #10B981 0%, #059669 100%) 1;
    }

    .faq-category[data-category="content"] .category-title {
        border-image: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%) 1;
    }

    .faq-category[data-category="technical"] .category-title {
        border-image: linear-gradient(135deg, #F59E0B 0%, #D97706 100%) 1;
    }

    .accordion-button:not(.collapsed) {
        background-color: rgba(139, 92, 246, 0.1);
        color: #8B5CF6;
        font-weight: 600;
    }

    .search-result-item {
        transition: all 0.3s ease;
        border-left: 4px solid #8B5CF6 !important;
    }

    .search-result-item:hover {
        transform: translateX(5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .section-header {
        padding-bottom: 1rem;
        border-bottom: 2px solid #8B5CF6;
        margin-bottom: 2rem;
    }

    .list-group-item.active {
        background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%) !important;
        border-color: #8B5CF6 !important;
        color: white !important;
    }

    .list-group-item {
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .list-group-item:hover:not(.active) {
        background-color: #f8f9fa;
        transform: translateX(5px);
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
        
        .faq-category {
            margin-bottom: 2rem !important;
        }
    }

    .badge {
        font-size: 0.7em;
    }

    .table th {
        border-top: none;
        font-weight: 600;
    }

    /* Özel renk paleti */
    :root {
        --primary-color: #8B5CF6;
        --secondary-color: #10B981;
        --accent-color: #3B82F6;
        --warning-color: #F59E0B;
        --danger-color: #EF4444;
        --muted-color: #6B7280;
    }
</style>