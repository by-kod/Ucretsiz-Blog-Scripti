<?php
require_once 'includes/config.php';

$pageTitle = "Yasal Uyarılar - " . SITE_NAME;
$pageDescription = "Site kullanımı, telif hakları ve yasal sorumluluklar hakkında detaylı bilgiler";
$pageKeywords = "yasal uyarılar, telif hakları, hukuki bildirim, yasal sorumluluk, site kullanım şartları";

// Header dosyasını include et
include 'themes/google-modern/header.php';
?>

<div class="container mt-4">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <h1 class="hero-title fade-in-up">Yasal Uyarılar</h1>
            <p class="hero-subtitle fade-in-up">Site kullanımı, telif hakları ve yasal sorumluluklar hakkında detaylı bilgiler</p>
        </div>
    </div>

    <div class="row justify-content-center mt-5">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light py-4">
                    <h3 class="card-title mb-0 text-center">
                        <i class="fas fa-gavel me-2" style="color: #8B4513;"></i>
                        Yasal Uyarılar ve Hukuki Bildirim
                    </h3>
                </div>
                <div class="card-body p-5">
                    <!-- Önemli Uyarı -->
                    <div class="alert alert-warning border-0 mb-5" style="background: linear-gradient(135deg, #FFF3CD 0%, #FFEAA7 100%); border-left: 4px solid #FFC107;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle fa-2x me-3" style="color: #B7950B;"></i>
                            <div>
                                <h5 class="alert-heading mb-2" style="color: #856404;">Önemli Yasal Uyarı</h5>
                                <p class="mb-0" style="color: #856404;">
                                    Lütfen bu sayfayı dikkatlice okuyunuz. Sitemizi kullanmaya devam etmeniz, 
                                    aşağıdaki yasal uyarıları tamamen anladığınızı ve kabul ettiğinizi gösterir.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Hızlı Navigasyon -->
                    <div class="quick-navigation mb-5">
                        <h5 class="mb-3 text-center">
                            <i class="fas fa-map-signs me-2" style="color: #2C7873;"></i>
                            Hızlı Navigasyon
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-3 col-6">
                                <a href="#copyright" class="btn btn-outline-primary w-100 text-decoration-none" style="border-color: #6A5ACD; color: #6A5ACD;">
                                    <i class="fas fa-copyright me-1"></i>Telif Hakları
                                </a>
                            </div>
                            <div class="col-md-3 col-6">
                                <a href="#liability" class="btn btn-outline-success w-100 text-decoration-none" style="border-color: #228B22; color: #228B22;">
                                    <i class="fas fa-balance-scale me-1"></i>Sorumluluk
                                </a>
                            </div>
                            <div class="col-md-3 col-6">
                                <a href="#usage" class="btn btn-outline-info w-100 text-decoration-none" style="border-color: #20B2AA; color: #20B2AA;">
                                    <i class="fas fa-laptop me-1"></i>Kullanım Şartları
                                </a>
                            </div>
                            <div class="col-md-3 col-6">
                                <a href="#intellectual" class="btn btn-outline-warning w-100 text-decoration-none" style="border-color: #DAA520; color: #DAA520;">
                                    <i class="fas fa-lightbulb me-1"></i>Fikri Mülkiyet
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- İçerik -->
                    <div class="legal-content">
                        <!-- Telif Hakları -->
                        <section id="copyright" class="mb-5">
                            <h4 class="mb-4" style="color: #6A5ACD;">
                                <i class="fas fa-copyright me-2"></i>
                                1. Telif Hakları ve Lisans
                            </h4>
                            <div class="ps-4">
                                <div class="card border-0 mb-4" style="background: linear-gradient(135deg, #F0F8FF 0%, #E6E6FA 100%);">
                                    <div class="card-body">
                                        <h6 class="card-title" style="color: #6A5ACD;">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Genel Bilgilendirme
                                        </h6>
                                        <p class="mb-0">
                                            <?php echo SITE_NAME; ?> web sitesinde yer alan tüm içerikler (metin, görsel, video, 
                                            tasarım, logo, kod vb.) 5846 sayılı Fikir ve Sanat Eserleri Kanunu ve ilgili 
                                            mevzuat hükümleri ile korunmaktadır.
                                        </p>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100" style="border-color: #228B22;">
                                            <div class="card-header text-white" style="background: linear-gradient(135deg, #32CD32 0%, #228B22 100%);">
                                                <h6 class="mb-0">
                                                    <i class="fas fa-check-circle me-2"></i>
                                                    İzin Verilen Kullanımlar
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <ul class="mb-0">
                                                    <li class="mb-2">Kişisel, ticari olmayan kullanım</li>
                                                    <li class="mb-2">Kaynak göstererek alıntı yapma</li>
                                                    <li class="mb-2">Eğitim ve araştırma amaçlı kullanım</li>
                                                    <li class="mb-2">Sosyal medyada paylaşım (kaynak belirterek)</li>
                                                    <li class="mb-0">Basılı materyallerde referans gösterme</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100" style="border-color: #DC143C;">
                                            <div class="card-header text-white" style="background: linear-gradient(135deg, #FF6B6B 0%, #DC143C 100%);">
                                                <h6 class="mb-0">
                                                    <i class="fas fa-ban me-2"></i>
                                                    Yasaklanan Kullanımlar
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <ul class="mb-0">
                                                    <li class="mb-2">Ticari kullanım (izin gerektirir)</li>
                                                    <li class="mb-2">Kaynak göstermeden kopyalama</li>
                                                    <li class="mb-2">İçeriği değiştirerek veya tahrif ederek kullanma</li>
                                                    <li class="mb-2">Başka web sitelerinde yayınlama</li>
                                                    <li class="mb-0">Telif hakkı uyarılarını kaldırma</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert border-0" style="background: linear-gradient(135deg, #E0FFFF 0%, #AFEEEE 100%); border-left: 4px solid #20B2AA;">
                                    <h6 class="alert-heading" style="color: #008B8B;">
                                        <i class="fas fa-handshake me-2"></i>
                                        İzin ve Lisans Talepleri
                                    </h6>
                                    <p class="mb-0" style="color: #008B8B;">
                                        Ticari kullanım veya özel lisans talepleriniz için 
                                        <a href="mailto:mail@blog.blog" class="alert-link" style="color: #2C7873;">mail@blog.blog</a> 
                                        adresinden bizimle iletişime geçebilirsiniz.
                                    </p>
                                </div>
                            </div>
                        </section>

                        <!-- Sorumluluk Reddi -->
                        <section id="liability" class="mb-5">
                            <h4 class="mb-4" style="color: #228B22;">
                                <i class="fas fa-balance-scale me-2"></i>
                                2. Sorumluluk Reddi
                            </h4>
                            <div class="ps-4">
                                <div class="table-responsive mb-4">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Konu</th>
                                                <th>Sorumluluk Durumu</th>
                                                <th>Açıklama</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><strong>İçerik Doğruluğu</strong></td>
                                                <td><span class="badge" style="background: #FFA500; color: white;">Sınırlı</span></td>
                                                <td>İçerikler makul çaba gösterilerek hazırlanır, ancak kesin doğruluk garanti edilmez</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Güncellik</strong></td>
                                                <td><span class="badge" style="background: #FFA500; color: white;">Sınırlı</span></td>
                                                <td>Bilgiler zamanla değişebilir, güncellemeler periyodik olarak yapılır</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Teknik Hatalar</strong></td>
                                                <td><span class="badge" style="background: #DC143C; color: white;">Sorumluluk Reddi</span></td>
                                                <td>Teknik hatalar ve kesintilerden kaynaklanan sorumluluk kabul edilmez</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Üçüncü Taraf Linkler</strong></td>
                                                <td><span class="badge" style="background: #DC143C; color: white;">Sorumluluk Reddi</span></td>
                                                <td>Bağlantı verilen sitelerin içeriğinden sorumlu değiliz</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Kullanıcı Yorumları</strong></td>
                                                <td><span class="badge" style="background: #DC143C; color: white;">Sorumluluk Reddi</span></td>
                                                <td>Kullanıcı yorumları yazan kişilerin şahsi görüşleridir</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="card" style="border-color: #FFA500;">
                                    <div class="card-header text-dark" style="background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);">
                                        <h6 class="mb-0">
                                            <i class="fas fa-exclamation-circle me-2"></i>
                                            Önemli Uyarı
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-3">
                                            Sitemizde yer alan hiçbir bilgi aşağıdaki konularda profesyonel tavsiye olarak 
                                            değerlendirilmemelidir:
                                        </p>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <ul>
                                                    <li class="mb-2">Hukuki tavsiye</li>
                                                    <li class="mb-2">Mali danışmanlık</li>
                                                    <li class="mb-2">Tıbbi teşhis ve tedavi</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <ul>
                                                    <li class="mb-2">Teknik danışmanlık</li>
                                                    <li class="mb-2">Yatırım tavsiyesi</li>
                                                    <li class="mb-2">Resmi belge niteliği</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <p class="mb-0 fw-bold" style="color: #DC143C;">
                                            Bu tür konularda mutlaka ilgili uzmanlara ve yetkili kurumlara başvurunuz.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Site Kullanım Şartları -->
                        <section id="usage" class="mb-5">
                            <h4 class="mb-4" style="color: #20B2AA;">
                                <i class="fas fa-laptop me-2"></i>
                                3. Site Kullanım Şartları
                            </h4>
                            <div class="ps-4">
                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100 border-0" style="background: linear-gradient(135deg, #F0FFF0 0%, #98FB98 100%);">
                                            <div class="card-body text-center">
                                                <i class="fas fa-user-check fa-2x mb-3" style="color: #228B22;"></i>
                                                <h6>Kabul Edilen Davranışlar</h6>
                                                <ul class="list-unstyled small mb-0">
                                                    <li class="mb-1">✓ Yapıcı eleştiri ve yorumlar</li>
                                                    <li class="mb-1">✓ Bilgi paylaşımı ve tartışma</li>
                                                    <li class="mb-1">✓ Medeni dil kullanımı</li>
                                                    <li class="mb-1">✓ Fikir ve görüş beyanı</li>
                                                    <li class="mb-0">✓ Soru sorma ve yardım isteme</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100 border-0" style="background: linear-gradient(135deg, #FFF0F5 0%, #FFB6C1 100%);">
                                            <div class="card-body text-center">
                                                <i class="fas fa-user-slash fa-2x mb-3" style="color: #DC143C;"></i>
                                                <h6>Yasaklanan Davranışlar</h6>
                                                <ul class="list-unstyled small mb-0">
                                                    <li class="mb-1">✗ Hakaret ve küfür içeren dil</li>
                                                    <li class="mb-1">✗ Spam ve reklam içerikleri</li>
                                                    <li class="mb-1">✗ Yasa dışı içerik paylaşımı</li>
                                                    <li class="mb-1">✗ Kişisel saldırı ve taciz</li>
                                                    <li class="mb-0">✗ Sahte bilgi ve yanıltıcı içerik</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h5 class="mb-3">Teknik Kullanım Kuralları:</h5>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-0 h-100" style="background: #F8F9FA;">
                                            <div class="card-body text-center">
                                                <i class="fas fa-robot fa-2x mb-3" style="color: #6A5ACD;"></i>
                                                <h6>Bot ve Otomasyon</h6>
                                                <p class="small mb-0">İzinsiz bot, crawler veya otomasyon yazılımları kullanılamaz</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-0 h-100" style="background: #F8F9FA;">
                                            <div class="card-body text-center">
                                                <i class="fas fa-server fa-2x mb-3" style="color: #228B22;"></i>
                                                <h6>Sunucu Yükü</h6>
                                                <p class="small mb-0">Sunucu performansını olumsuz etkileyecek işlemler yasaktır</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-0 h-100" style="background: #F8F9FA;">
                                            <div class="card-body text-center">
                                                <i class="fas fa-shield-alt fa-2x mb-3" style="color: #FFA500;"></i>
                                                <h6>Güvenlik İhlali</h6>
                                                <p class="small mb-0">Güvenlik açığı arama veya exploit denemeleri yasaktır</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert mt-4" style="background: linear-gradient(135deg, #FFE4E1 0%, #FFB6C1 100%); border-left: 4px solid #DC143C;">
                                    <h6 class="alert-heading" style="color: #8B0000;">
                                        <i class="fas fa-ban me-2"></i>
                                        İhlal Durumunda Yaptırımlar
                                    </h6>
                                    <p class="mb-0" style="color: #8B0000;">
                                        Yukarıdaki kuralları ihlal eden kullanıcıların erişimi engellenebilir, 
                                        yorumları silinebilir ve gerekli durumlarda yasal işlem başlatılabilir.
                                    </p>
                                </div>
                            </div>
                        </section>

                        <!-- Fikri Mülkiyet -->
                        <section id="intellectual" class="mb-5">
                            <h4 class="mb-4" style="color: #DAA520;">
                                <i class="fas fa-lightbulb me-2"></i>
                                4. Fikri Mülkiyet Hakları
                            </h4>
                            <div class="ps-4">
                                <div class="card border-0 text-white mb-4" style="background: linear-gradient(135deg, #DAA520 0%, #B8860B 100%);">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <i class="fas fa-gem me-2"></i>
                                            Korunan Unsurlar
                                        </h5>
                                        <p class="mb-0">
                                            Aşağıdaki tüm unsurlar <?php echo SITE_NAME; ?>'a ait fikri mülkiyet hakları 
                                            kapsamında korunmaktadır.
                                        </p>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-3 mb-3">
                                        <div class="card text-center h-100 border-0" style="background: #F0F8FF;">
                                            <div class="card-body">
                                                <i class="fas fa-font fa-2x mb-3" style="color: #4169E1;"></i>
                                                <h6>Metin İçerik</h6>
                                                <p class="small mb-0">Makaleler, rehberler, açıklamalar</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="card text-center h-100 border-0" style="background: #F0FFF0;">
                                            <div class="card-body">
                                                <i class="fas fa-image fa-2x mb-3" style="color: #228B22;"></i>
                                                <h6>Görsel Materyaller</h6>
                                                <p class="small mb-0">Fotoğraflar, infografikler, çizimler</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="card text-center h-100 border-0" style="background: #FFFACD;">
                                            <div class="card-body">
                                                <i class="fas fa-code fa-2x mb-3" style="color: #DAA520;"></i>
                                                <h6>Yazılım ve Kod</h6>
                                                <p class="small mb-0">Web sitesi kodu, eklentiler, tema</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="card text-center h-100 border-0" style="background: #FFF0F5;">
                                            <div class="card-body">
                                                <i class="fas fa-palette fa-2x mb-3" style="color: #DC143C;"></i>
                                                <h6>Tasarım Öğeleri</h6>
                                                <p class="small mb-0">Logo, renk şeması, arayüz tasarımı</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h5 class="mb-3">Lisans Türleri:</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>İçerik Türü</th>
                                                <th>Lisans</th>
                                                <th>Kullanım Koşulları</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><strong>Özgün Makaleler</strong></td>
                                                <td>© <?php echo SITE_NAME; ?></td>
                                                <td>Kaynak gösterilerek kişisel kullanım için izin verilir</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Görsel Materyaller</strong></td>
                                                <td>© <?php echo SITE_NAME; ?></td>
                                                <td>Özel izin gerektirir</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Site Tasarımı</strong></td>
                                                <td>© <?php echo SITE_NAME; ?></td>
                                                <td>Kopyalanamaz, taklit edilemez</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Logo ve Marka</strong></td>
                                                <td>® <?php echo SITE_NAME; ?></td>
                                                <td>Ticari marka koruması altındadır</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </section>

                        <!-- Ticari Marka -->
                        <section class="mb-5">
                            <h4 class="mb-4" style="color: #8B4513;">
                                <i class="fas fa-trademark me-2"></i>
                                5. Ticari Marka Hakları
                            </h4>
                            <div class="ps-4">
                                <div class="card" style="border-color: #DAA520;">
                                    <div class="card-header text-dark" style="background: linear-gradient(135deg, #FFD700 0%, #DAA520 100%);">
                                        <h6 class="mb-0">
                                            <i class="fas fa-registered me-2"></i>
                                            Tescilli Markalar
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6 class="mb-3">Sahip Olduğumuz Markalar:</h6>
                                                <ul class="list-unstyled">
                                                    <li class="mb-2">
                                                        <i class="fas fa-copyright me-2" style="color: #6A5ACD;"></i>
                                                        <strong>"<?php echo SITE_NAME; ?>"</strong> - Tescilli Marka
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-copyright me-2" style="color: #6A5ACD;"></i>
                                                        <strong>Site Logosu</strong> - Tescilli Tasarım
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-copyright me-2" style="color: #6A5ACD;"></i>
                                                        <strong>Slogan ve Tagline'lar</strong> - Ticari Marka
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="mb-3">Marka Kullanım Kuralları:</h6>
                                                <ul class="list-unstyled">
                                                    <li class="mb-2">✓ Referans gösterme amaçlı kullanılabilir</li>
                                                    <li class="mb-2">✓ Ticari kullanım için izin gereklidir</li>
                                                    <li class="mb-2">✓ Marka itibarına zarar verilemez</li>
                                                    <li class="mb-0">✓ Yanıltıcı şekilde kullanılamaz</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Yasal İşlem -->
                        <section class="mb-5">
                            <h4 class="mb-4" style="color: #2C7873;">
                                <i class="fas fa-balance-scale-left me-2"></i>
                                6. Yasal İşlem ve Uyuşmazlık Çözümü
                            </h4>
                            <div class="ps-4">
                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100" style="border-color: #20B2AA;">
                                            <div class="card-header text-white" style="background: linear-gradient(135deg, #48D1CC 0%, #20B2AA 100%);">
                                                <h6 class="mb-0">
                                                    <i class="fas fa-handshake me-2"></i>
                                                    Dostane Çözüm
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <p class="small mb-0">
                                                    Herhangi bir uyuşmazlık durumunda öncelikle dostane çözüm yolları 
                                                    aranacaktır. İletişim kanallarımız üzerinden bize ulaşabilirsiniz.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100" style="border-color: #8B0000;">
                                            <div class="card-header text-white" style="background: linear-gradient(135deg, #DC143C 0%, #8B0000 100%);">
                                                <h6 class="mb-0">
                                                    <i class="fas fa-gavel me-2"></i>
                                                    Yasal Süreç
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <p class="small mb-0">
                                                    Çözülemeyen uyuşmazlıklarda Türkiye Cumhuriyeti yasaları uygulanacak 
                                                    ve Ankara Mahkemeleri yetkili olacaktır.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert" style="background: linear-gradient(135deg, #F5F5F5 0%, #D3D3D3 100%); border-left: 4px solid #696969;">
                                    <h6 class="alert-heading" style="color: #2F4F4F;">
                                        <i class="fas fa-clock me-2"></i>
                                        Zamanaşımı ve Hak Düşürücü Süreler
                                    </h6>
                                    <p class="mb-0" style="color: #2F4F4F;">
                                        Türk Borçlar Kanunu ve ilgili mevzuatta öngörülen zamanaşımı süreleri geçerlidir. 
                                        Haklarınızı zamanında kullanmanız önemle tavsiye edilir.
                                    </p>
                                </div>
                            </div>
                        </section>

                        <!-- İletişim -->
                        <section>
                            <h4 class="mb-4" style="color: #4169E1;">
                                <i class="fas fa-envelope me-2"></i>
                                7. İletişim ve Bildirim
                            </h4>
                            <div class="ps-4">
                                <p class="mb-3">
                                    Yasal uyarılarımızla ilgili sorularınız veya bildirimleriniz için:
                                </p>
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6 class="mb-3">İletişim Bilgileri:</h6>
                                                <ul class="list-unstyled">
                                                    <li class="mb-2">
                                                        <i class="fas fa-building me-2" style="color: #6A5ACD;"></i>
                                                        <strong>Kurum:</strong> <?php echo SITE_NAME; ?>
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-envelope me-2" style="color: #6A5ACD;"></i>
                                                        <strong>Yasal İletişim:</strong> mail@blog.blog
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-phone me-2" style="color: #6A5ACD;"></i>
                                                        <strong>Telefon:</strong> +90 555 555 5555
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-map-marker-alt me-2" style="color: #6A5ACD;"></i>
                                                        <strong>Adres:</strong> Ankara, Türkiye
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="mb-3">İlgili Sayfalar:</h6>
                                                <ul class="list-unstyled">
                                                    <li class="mb-2">
                                                        <i class="fas fa-shield-alt me-2" style="color: #228B22;"></i>
                                                        <a href="privacy.php" class="text-decoration-none" style="color: #228B22;">Gizlilik Politikası</a>
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-file-contract me-2" style="color: #228B22;"></i>
                                                        <a href="terms.php" class="text-decoration-none" style="color: #228B22;">Kullanım Şartları</a>
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-cookie me-2" style="color: #228B22;"></i>
                                                        <a href="cookie-policy.php" class="text-decoration-none" style="color: #228B22;">Çerez Politikası</a>
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-user-shield me-2" style="color: #228B22;"></i>
                                                        <a href="gdpr.php" class="text-decoration-none" style="color: #228B22;">KVKK & GDPR</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>

                    <!-- Onay Butonu -->
                    <div class="text-center mt-5">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="understandLegal">
                            <label class="form-check-label fw-bold" for="understandLegal">
                                Bu yasal uyarıları okudum, anladım ve kabul ediyorum
                            </label>
                        </div>
                        <div class="mt-3">
                            <a href="index.php" class="btn btn-primary btn-lg me-3" style="background: linear-gradient(135deg, #6A5ACD 0%, #483D8B 100%); border: none;">
                                <i class="fas fa-home me-2"></i>
                                Ana Sayfaya Dön
                            </a>
                            <a href="contact.php" class="btn btn-outline-primary btn-lg" style="border-color: #6A5ACD; color: #6A5ACD;">
                                <i class="fas fa-question-circle me-2"></i>
                                Soru Sor
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light text-center py-3">
                    <small class="text-muted">
                        Son Güncelleme: <?php echo date('d/m/Y'); ?> | 
                        <i class="fas fa-history me-1"></i>
                        Bu sayfa en son <?php echo date('d/m/Y'); ?> tarihinde güncellenmiştir.
                    </small>
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
    document.addEventListener('DOMContentLoaded', function() {
        // Onay kutusu animasyonu
        const understandCheckbox = document.getElementById('understandLegal');
        
        understandCheckbox.addEventListener('change', function() {
            if (this.checked) {
                this.parentElement.classList.add('text-success');
                this.parentElement.classList.remove('text-danger');
            } else {
                this.parentElement.classList.remove('text-success');
                this.parentElement.classList.add('text-danger');
            }
        });

        // Smooth scroll for navigation
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Section highlight on scroll
        const sections = document.querySelectorAll('.legal-content section');
        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('section-visible');
                    
                    // Update active navigation
                    const id = entry.target.getAttribute('id');
                    document.querySelectorAll('.quick-navigation a').forEach(link => {
                        link.classList.remove('active');
                        if (link.getAttribute('href') === `#${id}`) {
                            link.classList.add('active');
                            link.style.background = 'linear-gradient(135deg, #6A5ACD 0%, #483D8B 100%)';
                            link.style.color = 'white';
                            link.style.borderColor = '#6A5ACD';
                        } else {
                            link.style.background = '';
                            link.style.color = '';
                            link.style.borderColor = '';
                        }
                    });
                }
            });
        }, observerOptions);

        sections.forEach(section => {
            observer.observe(section);
        });

        // Print functionality
        const printButton = document.createElement('button');
        printButton.innerHTML = '<i class="fas fa-print me-2"></i>Sayfayı Yazdır';
        printButton.className = 'btn btn-outline-secondary position-fixed';
        printButton.style.bottom = '20px';
        printButton.style.right = '20px';
        printButton.style.zIndex = '1000';
        printButton.style.background = 'white';
        
        printButton.addEventListener('click', function() {
            window.print();
        });
        
        document.body.appendChild(printButton);
    });
</script>

<style>
    .legal-content section {
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.6s ease;
    }
    
    .legal-content section.section-visible {
        opacity: 1;
        transform: translateY(0);
    }
    
    .quick-navigation .btn.active {
        background: linear-gradient(135deg, #6A5ACD 0%, #483D8B 100%);
        color: white;
        border-color: #6A5ACD;
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
    
    ul li {
        position: relative;
        padding-left: 1.5rem;
    }
    
    ul li:before {
        content: '•';
        color: #6A5ACD;
        font-weight: bold;
        position: absolute;
        left: 0.5rem;
    }
    
    .list-unstyled li:before {
        content: none;
    }
    
    @media print {
        .hero-section, .card-footer, .btn, .alert, .quick-navigation {
            display: none !important;
        }
        
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        
        .container {
            max-width: 100% !important;
        }
    }
    
    .form-check-input:checked {
        background-color: #228B22;
        border-color: #228B22;
    }
    
    .text-success {
        color: #228B22 !important;
    }
    
    .text-danger {
        color: #DC143C !important;
    }
    
    .badge {
        font-size: 0.75em;
    }
    
    .table th {
        border-top: none;
        font-weight: 600;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #6A5ACD 0%, #483D8B 100%);
        border: none;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #483D8B 0%, #6A5ACD 100%);
        transform: translateY(-2px);
    }
</style>