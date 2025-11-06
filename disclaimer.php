<?php
require_once 'includes/config.php';

$pageTitle = "Sorumluluk Reddi - " . SITE_NAME;
$pageDescription = "Sitemizde yer alan içeriklerle ilgili sorumluluk reddi ve yasal uyarılar";
$pageKeywords = "sorumluluk reddi, yasal uyarı, disclaimer, hukuki bilgilendirme";

// Header dosyasını include et
include 'themes/google-modern/header.php';
?>

<div class="container mt-4">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <h1 class="hero-title fade-in-up">Sorumluluk Reddi</h1>
            <p class="hero-subtitle fade-in-up">Yasal uyarılar ve sorumluluk sınırlamaları hakkında önemli bilgiler</p>
        </div>
    </div>

    <div class="row justify-content-center mt-5">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light py-4">
                    <h3 class="card-title mb-0 text-center">
                        <i class="fas fa-balance-scale me-2" style="color: #8B5CF6;"></i>
                        Hukuki Bildirim ve Sorumluluk Reddi
                    </h3>
                </div>
                <div class="card-body p-5">
                    <!-- Genel Uyarı -->
                    <div class="alert alert-warning border-0 mb-5" style="background: linear-gradient(135deg, #FEF3C7, #FDE68A); border-left: 4px solid #F59E0B;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle fa-2x me-3" style="color: #D97706;"></i>
                            <div>
                                <h5 class="alert-heading mb-2" style="color: #92400E;">Önemli Uyarı</h5>
                                <p class="mb-0" style="color: #92400E;">Lütfen bu sayfayı dikkatlice okuyunuz. Sitemizi kullanmadan önce aşağıdaki sorumluluk reddi koşullarını kabul etmiş sayılırsınız.</p>
                            </div>
                        </div>
                    </div>

                    <!-- İçerik -->
                    <div class="disclaimer-content">
                        <!-- Genel Hükümler -->
                        <section class="mb-5">
                            <h4 class="mb-4" style="color: #7C3AED;">
                                <i class="fas fa-file-contract me-2"></i>
                                1. Genel Hükümler
                            </h4>
                            <div class="ps-4">
                                <p class="mb-3">
                                    <?php echo SITE_NAME; ?> ("Site"), yalnızca bilgilendirme amaçlı içerik sunan bir platformdur. 
                                    Sitede yer alan tüm içerikler genel bilgi amaçlı olup, hiçbir şekilde profesyonel tavsiye 
                                    niteliği taşımamaktadır.
                                </p>
                                <p class="mb-3">
                                    Site yöneticileri, yazarları ve çalışanları, sitede yer alan bilgilerin kullanımından 
                                    kaynaklanabilecek doğrudan veya dolaylı her türlü zarar, kayıp veya masraftan sorumlu 
                                    tutulamaz.
                                </p>
                            </div>
                        </section>

                        <!-- İçerik Doğruluğu -->
                        <section class="mb-5">
                            <h4 class="mb-4" style="color: #059669;">
                                <i class="fas fa-check-circle me-2"></i>
                                2. İçerik Doğruluğu ve Güncelliği
                            </h4>
                            <div class="ps-4">
                                <p class="mb-3">
                                    Sitemizde yayınlanan içerikler titizlikle hazırlanmakta olup, doğruluk ve güncellik 
                                    konusunda makul çaba gösterilmektedir. Ancak:
                                </p>
                                <ul class="mb-3">
                                    <li class="mb-2">Bilgiler zamanla değişebilir ve güncelliğini yitirebilir</li>
                                    <li class="mb-2">Teknik hatalar veya yazım yanlışları bulunabilir</li>
                                    <li class="mb-2">İçerikler tamamen kapsayıcı olmayabilir</li>
                                    <li class="mb-2">Bazı bilgiler üçüncü taraf kaynaklardan derlenmiştir</li>
                                </ul>
                                <p class="mb-0">
                                    Önemli kararlar vermeden önce resmi kaynaklardan ve yetkili mercilerden doğrulama 
                                    yapmanız önemle tavsiye edilir.
                                </p>
                            </div>
                        </section>

                        <!-- Profesyonel Tavsiye Reddi -->
                        <section class="mb-5">
                            <h4 class="mb-4" style="color: #DC2626;">
                                <i class="fas fa-user-md me-2"></i>
                                3. Profesyonel Tavsiye Reddi
                            </h4>
                            <div class="ps-4">
                                <p class="mb-3">
                                    Sitemizde yer alan hiçbir içerik aşağıdaki konularda profesyonel tavsiye olarak 
                                    değerlendirilmemelidir:
                                </p>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <ul>
                                            <li class="mb-2">Hukuki tavsiye</li>
                                            <li class="mb-2">Mali danışmanlık</li>
                                            <li class="mb-2">Tıbbi teşhis ve tedavi</li>
                                            <li class="mb-2">Mesleki rehberlik</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul>
                                            <li class="mb-2">Teknik danışmanlık</li>
                                            <li class="mb-2">Yatırım tavsiyesi</li>
                                            <li class="mb-2">Sağlık önerileri</li>
                                            <li class="mb-2">Resmi belge niteliği</li>
                                        </ul>
                                    </div>
                                </div>
                                <p class="mb-0 fw-bold" style="color: #DC2626;">
                                    Bu tür konularda mutlaka ilgili uzmanlara ve yetkili kurumlara başvurunuz.
                                </p>
                            </div>
                        </section>

                        <!-- Üçüncü Taraf Bağlantılar -->
                        <section class="mb-5">
                            <h4 class="mb-4" style="color: #7C3AED;">
                                <i class="fas fa-external-link-alt me-2"></i>
                                4. Üçüncü Taraf Bağlantıları
                            </h4>
                            <div class="ps-4">
                                <p class="mb-3">
                                    Sitemizde diğer web sitelerine bağlantılar (linkler) bulunabilir. Bu bağlantılar:
                                </p>
                                <ul class="mb-3">
                                    <li class="mb-2">Yalnızca kullanıcı kolaylığı için sağlanmıştır</li>
                                    <li class="mb-2">Bağlantı verilen sitelerin içeriğinden sorumlu değiliz</li>
                                    <li class="mb-2">Bağlantı verilen sitelerin gizlilik politikalarını kontrol etmenizi öneririz</li>
                                    <li class="mb-2">Bağlantıların mevcudiyeti veya erişilebilirliği garanti edilmez</li>
                                </ul>
                                <p class="mb-0">
                                    Üçüncü taraf sitelerdeki içerik, ürün veya hizmetlerden kaynaklanan her türlü 
                                    sorumluluk ilgili siteye aittir.
                                </p>
                            </div>
                        </section>

                        <!-- Fikri Mülkiyet -->
                        <section class="mb-5">
                            <h4 class="mb-4" style="color: #059669;">
                                <i class="fas fa-copyright me-2"></i>
                                5. Fikri Mülkiyet Hakları
                            </h4>
                            <div class="ps-4">
                                <p class="mb-3">
                                    Sitemizde yer alan tüm içerik (yazılar, görseller, logo, tasarım vb.) 
                                    <?php echo SITE_NAME; ?>'a aittir ve telif hukuku ile korunmaktadır.
                                </p>
                                <div class="alert alert-info border-0" style="background: linear-gradient(135deg, #DBEAFE, #E0F2FE); border-left: 4px solid #0EA5E9;">
                                    <h6 class="alert-heading" style="color: #0369A1;">İzin ve Kullanım Koşulları:</h6>
                                    <ul class="mb-0" style="color: #0369A1;">
                                        <li>Kişisel, ticari olmayan kullanım için içerik paylaşılabilir</li>
                                        <li>Kaynak gösterilmesi zorunludur</li>
                                        <li>İçerikler değiştirilemez veya tahrif edilemez</li>
                                        <li>Ticari kullanım için yazılı izin gereklidir</li>
                                    </ul>
                                </div>
                            </div>
                        </section>

                        <!-- Yorumlar ve Kullanıcı İçeriği -->
                        <section class="mb-5">
                            <h4 class="mb-4" style="color: #7C3AED;">
                                <i class="fas fa-comments me-2"></i>
                                6. Yorumlar ve Kullanıcı İçeriği
                            </h4>
                            <div class="ps-4">
                                <p class="mb-3">
                                    Kullanıcılar tarafından gönderilen yorumlar ve içerikler:
                                </p>
                                <ul class="mb-3">
                                    <li class="mb-2">Gönderenin kişisel görüşlerini yansıtır</li>
                                    <li class="mb-2"><?php echo SITE_NAME; ?> görüşlerini temsil etmez</li>
                                    <li class="mb-2">Ön incelemeden sonra yayınlanır</li>
                                    <li class="mb-2">Uygunsuz içerikler silinebilir</li>
                                </ul>
                                <p class="mb-0">
                                    Kullanıcılar, gönderdikleri içeriklerden kendileri sorumludur. Yasalara aykırı 
                                    veya zararlı içerik tespit edilmesi durumunda ilgili makamlarla işbirliği yapılacaktır.
                                </p>
                            </div>
                        </section>

                        <!-- Hizmet Kesintileri -->
                        <section class="mb-5">
                            <h4 class="mb-4" style="color: #DC2626;">
                                <i class="fas fa-server me-2"></i>
                                7. Hizmet Kesintileri ve Değişiklikler
                            </h4>
                            <div class="ps-4">
                                <p class="mb-3">
                                    Sitemizin kesintisiz hizmet vermesi için makul çaba gösterilmekle birlikte:
                                </p>
                                <ul class="mb-3">
                                    <li class="mb-2">Teknik bakım ve güncellemeler nedeniyle kesintiler yaşanabilir</li>
                                    <li class="mb-2">İçerikler önceden haber verilmeksizin değiştirilebilir veya kaldırılabilir</li>
                                    <li class="mb-2">Site yapısı ve tasarımı değiştirilebilir</li>
                                    <li class="mb-2">Hizmet herhangi bir zamanda sonlandırılabilir</li>
                                </ul>
                                <p class="mb-0">
                                    Bu tür değişikliklerden veya kesintilerden kaynaklanan her türlü sorumluluk reddedilir.
                                </p>
                            </div>
                        </section>

                        <!-- Yargı Yetkisi -->
                        <section class="mb-5">
                            <h4 class="mb-4" style="color: #059669;">
                                <i class="fas fa-gavel me-2"></i>
                                8. Yargı Yetkisi ve Uygulanacak Hukuk
                            </h4>
                            <div class="ps-4">
                                <p class="mb-3">
                                    Bu sorumluluk reddi ve sitemizin kullanımı ile ilgili tüm anlaşmazlıklarda:
                                </p>
                                <ul class="mb-3">
                                    <li class="mb-2">Türkiye Cumhuriyeti yasaları uygulanacaktır</li>
                                    <li class="mb-2">Anlaşmazlıkların çözüm yeri Türkiye mahkemeleridir</li>
                                    <li class="mb-2">Kullanıcılar bu koşulları kabul etmiş sayılır</li>
                                </ul>
                                <p class="mb-0">
                                    Yasal ihtilaflarda Türkçe metin esas alınacaktır.
                                </p>
                            </div>
                        </section>

                        <!-- Değişiklik Hakkı -->
                        <section class="mb-5">
                            <h4 class="mb-4" style="color: #7C3AED;">
                                <i class="fas fa-sync-alt me-2"></i>
                                9. Değişiklik Hakkı
                            </h4>
                            <div class="ps-4">
                                <p class="mb-3">
                                    <?php echo SITE_NAME; ?>, bu sorumluluk reddi metnini herhangi bir zamanda 
                                    önceden haber vermeksizin değiştirme hakkını saklı tutar.
                                </p>
                                <p class="mb-0">
                                    Değişiklikler yayınlandığı tarihte yürürlüğe girer. Değişikliklerden haberdar 
                                    olmak için bu sayfayı periyodik olarak kontrol etmeniz önerilir.
                                </p>
                            </div>
                        </section>

                        <!-- İletişim -->
                        <section>
                            <h4 class="mb-4" style="color: #DC2626;">
                                <i class="fas fa-envelope me-2"></i>
                                10. İletişim
                            </h4>
                            <div class="ps-4">
                                <p class="mb-3">
                                    Bu sorumluluk reddi ile ilgili sorularınız veya endişeleriniz için:
                                </p>
                                <div class="card border-0" style="background: linear-gradient(135deg, #F3F4F6, #E5E7EB);">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6 class="mb-3" style="color: #374151;">İletişim Bilgileri:</h6>
                                                <ul class="list-unstyled">
                                                    <li class="mb-2">
                                                        <i class="fas fa-envelope me-2" style="color: #7C3AED;"></i>
                                                        <a href="mailto:mail@blog.blog" class="text-decoration-none" style="color: #7C3AED;">mail@blog.blog</a>
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-globe me-2" style="color: #7C3AED;"></i>
                                                        <a href="contact.php" class="text-decoration-none" style="color: #7C3AED;">İletişim Formu</a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="mb-3" style="color: #374151;">İlgili Sayfalar:</h6>
                                                <ul class="list-unstyled">
                                                    <li class="mb-2">
                                                        <i class="fas fa-shield-alt me-2" style="color: #059669;"></i>
                                                        <a href="privacy.php" class="text-decoration-none" style="color: #059669;">Gizlilik Politikası</a>
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-file-contract me-2" style="color: #059669;"></i>
                                                        <a href="terms.php" class="text-decoration-none" style="color: #059669;">Kullanım Şartları</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>

                    <!-- Son Uyarı -->
                    <div class="alert border-0 mt-5" style="background: linear-gradient(135deg, #FEE2E2, #FECACA); border-left: 4px solid #DC2626;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-ban fa-2x me-3" style="color: #DC2626;"></i>
                            <div>
                                <h5 class="alert-heading mb-2" style="color: #991B1B;">Son Uyarı</h5>
                                <p class="mb-0" style="color: #991B1B;">
                                    Sitemizi kullanmaya devam etmeniz, bu sorumluluk reddi koşullarını tamamen 
                                    anladığınızı ve kabul ettiğinizi gösterir. Kabul etmiyorsanız, lütfen sitemizi 
                                    kullanmayı derhal bırakın.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Onay Butonu -->
                    <div class="text-center mt-4">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="understandDisclaimer">
                            <label class="form-check-label fw-bold" for="understandDisclaimer">
                                Bu sorumluluk reddi koşullarını okudum ve anladım
                            </label>
                        </div>
                        <div class="mt-3">
                            <a href="index.php" class="btn btn-primary btn-lg me-3" style="background: linear-gradient(135deg, #8B5CF6, #7C3AED); border: none;">
                                <i class="fas fa-home me-2"></i>
                                Ana Sayfaya Dön
                            </a>
                            <a href="contact.php" class="btn btn-outline-primary btn-lg" style="border-color: #8B5CF6; color: #8B5CF6;">
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

    <!-- Hızlı Navigasyon -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0" style="background: linear-gradient(135deg, #F8FAFC, #F1F5F9);">
                <div class="card-body text-center">
                    <h5 class="card-title mb-3" style="color: #374151;">İlgili Diğer Sayfalar</h5>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="privacy.php" class="btn btn-outline-primary" style="border-color: #8B5CF6; color: #8B5CF6;">
                            <i class="fas fa-shield-alt me-2"></i>Gizlilik Politikası
                        </a>
                        <a href="terms.php" class="btn btn-outline-success" style="border-color: #059669; color: #059669;">
                            <i class="fas fa-file-contract me-2"></i>Kullanım Şartları
                        </a>
                        <a href="cookie-policy.php" class="btn btn-outline-info" style="border-color: #0EA5E9; color: #0EA5E9;">
                            <i class="fas fa-cookie me-2"></i>Çerez Politikası
                        </a>
                        <a href="gdpr.php" class="btn btn-outline-warning" style="border-color: #D97706; color: #D97706;">
                            <i class="fas fa-user-shield me-2"></i>KVKK
                        </a>
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
    document.addEventListener('DOMContentLoaded', function() {
        // Onay kutusu animasyonu
        const understandCheckbox = document.getElementById('understandDisclaimer');
        
        understandCheckbox.addEventListener('change', function() {
            if (this.checked) {
                this.parentElement.classList.add('text-success');
                this.parentElement.classList.remove('text-danger');
            } else {
                this.parentElement.classList.remove('text-success');
                this.parentElement.classList.add('text-danger');
            }
        });

        // Smooth scroll for anchor links
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
        const sections = document.querySelectorAll('section');
        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('section-visible');
                }
            });
        }, observerOptions);

        sections.forEach(section => {
            observer.observe(section);
        });

        // Print functionality
        const printButton = document.createElement('button');
        printButton.innerHTML = '<i class="fas fa-print me-2"></i>Sayfayı Yazdır';
        printButton.className = 'btn position-fixed';
        printButton.style.background = 'linear-gradient(135deg, #8B5CF6, #7C3AED)';
        printButton.style.color = 'white';
        printButton.style.border = 'none';
        printButton.style.bottom = '20px';
        printButton.style.right = '20px';
        printButton.style.zIndex = '1000';
        
        printButton.addEventListener('click', function() {
            window.print();
        });
        
        document.body.appendChild(printButton);
    });
</script>

<style>
    .disclaimer-content section {
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.6s ease;
    }
    
    .disclaimer-content section.section-visible {
        opacity: 1;
        transform: translateY(0);
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
        color: #8B5CF6;
        font-weight: bold;
        position: absolute;
        left: 0.5rem;
    }
    
    @media print {
        .hero-section, .card-footer, .btn, .alert, .row.mt-4 {
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
        background-color: #059669;
        border-color: #059669;
    }
    
    .text-success {
        color: #059669 !important;
    }
    
    .text-danger {
        color: #DC2626 !important;
    }
    
    /* Özel renk paleti */
    .hero-section {
        background: linear-gradient(135deg, #8B5CF6, #7C3AED) !important;
    }
</style>