<?php
require_once 'includes/config.php';

$pageTitle = "Bülten Aboneliği - " . SITE_NAME;
$pageDescription = "Güncel yazılarımızdan ve duyurularımızdan haberdar olmak için bültenimize abone olun";
$pageKeywords = "bülten, email aboneliği, haber bülteni, güncellemeler, duyurular";

// Newsletter işlemleri
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? sanitize($_POST['email']) : '';
    $name = isset($_POST['name']) ? sanitize($_POST['name']) : '';
    $privacy = isset($_POST['privacy']) ? true : false;
    
    // Doğrulama
    if (empty($email)) {
        $error_message = "Lütfen email adresinizi giriniz.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Lütfen geçerli bir email adresi giriniz.";
    } elseif (empty($name)) {
        $error_message = "Lütfen isminizi giriniz.";
    } elseif (!$privacy) {
        $error_message = "Gizlilik politikasını kabul etmelisiniz.";
    } else {
        try {
            // Email kontrolü
            $check_stmt = $pdo->prepare("SELECT id FROM newsletter_subscribers WHERE email = ?");
            $check_stmt->execute([$email]);
            
            if ($check_stmt->rowCount() > 0) {
                $error_message = "Bu email adresi zaten bülten aboneliğimize kayıtlı.";
            } else {
                // Yeni abone ekle
                $token = bin2hex(random_bytes(32));
                $insert_stmt = $pdo->prepare("
                    INSERT INTO newsletter_subscribers (email, name, token, status, subscribed_at) 
                    VALUES (?, ?, ?, 'active', NOW())
                ");
                $insert_stmt->execute([$email, $name, $token]);
                
                $success_message = "Bülten aboneliğiniz başarıyla oluşturuldu! Teşekkür ederiz.";
                
                // Email gönderimi (isteğe bağlı)
                // send_welcome_email($email, $name, $token);
            }
        } catch (PDOException $e) {
            $error_message = "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
        }
    }
}

// Newsletter istatistikleri
try {
    $stats = $pdo->query("
        SELECT 
            (SELECT COUNT(*) FROM newsletter_subscribers WHERE status = 'active') as total_subscribers,
            (SELECT COUNT(*) FROM newsletter_subscribers WHERE status = 'active' AND subscribed_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)) as new_last_month,
            (SELECT COUNT(*) FROM newsletter_campaigns WHERE status = 'sent') as total_campaigns
    ")->fetch();
} catch (PDOException $e) {
    // Tablolar yoksa varsayılan değerler
    $stats = [
        'total_subscribers' => 1250,
        'new_last_month' => 45,
        'total_campaigns' => 12
    ];
}

// Header dosyasını include et
include 'themes/google-modern/header.php';
?>

<div class="container mt-4">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <h1 class="hero-title fade-in-up">Bülten Aboneliği</h1>
            <p class="hero-subtitle fade-in-up">Güncel yazılarımızdan ve duyurularımızdan haberdar olun</p>
            <div class="hero-stats fade-in-up" style="display: flex; gap: 2rem; justify-content: center; font-size: 1.1rem;">
                <div><i class="fas fa-users me-1"></i> <?php echo number_format($stats['total_subscribers']); ?> Abone</div>
                <div><i class="fas fa-paper-plane me-1"></i> <?php echo $stats['total_campaigns']; ?> Bülten</div>
                <div><i class="fas fa-chart-line me-1"></i> Son Ayda <?php echo $stats['new_last_month']; ?> Yeni Abone</div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center mt-5">
        <div class="col-lg-8">
            <div class="row">
                <!-- Ana İçerik -->
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-gradient-custom text-white py-4">
                            <h3 class="card-title mb-0 text-center">
                                <i class="fas fa-envelope-open-text me-2"></i>
                                Bültene Abone Ol
                            </h3>
                        </div>
                        <div class="card-body p-5">
                            <?php if($success_message): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <?php echo $success_message; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <?php if($error_message): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <?php echo $error_message; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <form method="POST" id="newsletterForm">
                                <div class="mb-4">
                                    <label for="name" class="form-label fw-bold">Adınız Soyadınız *</label>
                                    <input type="text" class="form-control form-control-lg" id="name" name="name" 
                                           value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>" 
                                           placeholder="Adınız ve soyadınız" required>
                                    <div class="form-text">Lütfen gerçek adınızı giriniz.</div>
                                </div>

                                <div class="mb-4">
                                    <label for="email" class="form-label fw-bold">Email Adresiniz *</label>
                                    <input type="email" class="form-control form-control-lg" id="email" name="email" 
                                           value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" 
                                           placeholder="ornek@email.com" required>
                                    <div class="form-text">Email adresiniz asla üçüncü şahıslarla paylaşılmayacaktır.</div>
                                </div>

                                <div class="mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="privacy" name="privacy" required>
                                        <label class="form-check-label" for="privacy">
                                            <a href="privacy.php" target="_blank" class="text-decoration-none">Gizlilik politikasını</a> 
                                            okudum ve kabul ediyorum. *
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="promotions" name="promotions">
                                        <label class="form-check-label" for="promotions">
                                            Özel teklifler ve promosyonlar hakkında bilgi almak istiyorum.
                                        </label>
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-custom btn-lg">
                                        <i class="fas fa-paper-plane me-2"></i>
                                        Abone Ol
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Özellikler -->
                    <div class="row mt-5">
                        <div class="col-md-6 mb-4">
                            <div class="text-center p-4 border rounded-3 h-100 feature-card">
                                <i class="fas fa-bell fa-2x text-vibrant-blue mb-3"></i>
                                <h5>Anında Haberdar Olun</h5>
                                <p class="text-muted mb-0">Yeni yazılar yayınlandığında ilk siz haberdar olun.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="text-center p-4 border rounded-3 h-100 feature-card">
                                <i class="fas fa-chart-line fa-2x text-vibrant-green mb-3"></i>
                                <h5>Özel İstatistikler</h5>
                                <p class="text-muted mb-0">Haftalık özetler ve popüler içerik analizleri.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="text-center p-4 border rounded-3 h-100 feature-card">
                                <i class="fas fa-gift fa-2x text-vibrant-orange mb-3"></i>
                                <h5>Özel İçerikler</h5>
                                <p class="text-muted mb-0">Sadece abonelerimize özel makaleler ve rehberler.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="text-center p-4 border rounded-3 h-100 feature-card">
                                <i class="fas fa-shield-alt fa-2x text-vibrant-purple mb-3"></i>
                                <h5>Güvenli İletişim</h5>
                                <p class="text-muted mb-0">Spam yapmıyoruz, istediğiniz zaman aboneliği iptal edebilirsiniz.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Yan Menü -->
                <div class="col-lg-4">
                    <div class="sticky-top" style="top: 100px;">
                        <!-- İstatistikler -->
                        <div class="card mb-4">
                            <div class="card-header bg-vibrant-teal text-white">
                                <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Bülten İstatistikleri</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Toplam Abone:</span>
                                        <span class="fw-bold text-vibrant-blue"><?php echo number_format($stats['total_subscribers']); ?></span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Son Ayda:</span>
                                        <span class="fw-bold text-vibrant-green">+<?php echo $stats['new_last_month']; ?></span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Gönderilen Bülten:</span>
                                        <span class="fw-bold text-vibrant-orange"><?php echo $stats['total_campaigns']; ?></span>
                                    </div>
                                </div>
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar bg-vibrant-green" style="width: 85%"></div>
                                </div>
                                <small class="text-muted">Aylık açılma oranı: %85</small>
                            </div>
                        </div>

                        <!-- Sık Sorulan Sorular -->
                        <div class="card mb-4">
                            <div class="card-header bg-vibrant-yellow text-dark">
                                <h6 class="mb-0"><i class="fas fa-question-circle me-2"></i>Sık Sorulan Sorular</h6>
                            </div>
                            <div class="card-body">
                                <div class="accordion" id="faqAccordion">
                                    <div class="accordion-item border-0">
                                        <h6 class="accordion-header">
                                            <button class="accordion-button collapsed py-2" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                                Ne sıklıkla email alacağım?
                                            </button>
                                        </h6>
                                        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                            <div class="accordion-body small">
                                                Haftada 1-2 kez özet email ve önemli duyurularda bilgilendirme alacaksınız.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item border-0">
                                        <h6 class="accordion-header">
                                            <button class="accordion-button collapsed py-2" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                                Aboneliği nasıl iptal ederim?
                                            </button>
                                        </h6>
                                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                            <div class="accordion-body small">
                                                Her emailin altında bulunan "Aboneliği İptal Et" linkine tıklayarak anında iptal edebilirsiniz.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item border-0">
                                        <h6 class="accordion-header">
                                            <button class="accordion-button collapsed py-2" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                                Verilerim güvende mi?
                                            </button>
                                        </h6>
                                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                            <div class="accordion-body small">
                                                Evet, email adresiniz asla üçüncü şahıslarla paylaşılmaz ve güvenli sunucularımızda saklanır.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Abone Görüşleri -->
                        <div class="card">
                            <div class="card-header bg-vibrant-green text-white">
                                <h6 class="mb-0"><i class="fas fa-star me-2"></i>Abonelerimiz Ne Diyor?</h6>
                            </div>
                            <div class="card-body">
                                <div class="testimonial">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="flex-shrink-0">
                                            <div class="bg-vibrant-blue rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <span class="text-white fw-bold">AŞ</span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-0">Ahmet Ş.</h6>
                                            <small class="text-muted">2 aydır abone</small>
                                        </div>
                                    </div>
                                    <p class="small text-muted mb-0">
                                        "Haftalık özetler sayesinde hiçbir içeriği kaçırmıyorum. Çok faydalı!"
                                    </p>
                                </div>
                                <hr>
                                <div class="testimonial">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="flex-shrink-0">
                                            <div class="bg-vibrant-orange rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <span class="text-white fw-bold">MY</span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-0">Melek Y.</h6>
                                            <small class="text-muted">6 aydır abone</small>
                                        </div>
                                    </div>
                                    <p class="small text-muted mb-0">
                                        "Özel içerikler ve erken erişim imkanı harika. Kesinlikle tavsiye ederim."
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bülten Arşivi -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h4 class="mb-0"><i class="fas fa-archive me-2"></i>Son Gönderilen Bültenler</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <span class="badge bg-vibrant-blue mb-2">Haftalık Özet</span>
                                    <h6 class="card-title">Bu Haftanın En Popüler Yazıları</h6>
                                    <p class="card-text small text-muted">En çok okunan 5 yazı ve özel analizler</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">3 gün önce</small>
                                        <span class="badge bg-vibrant-green">%24 açılma</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <span class="badge bg-vibrant-green mb-2">Yeni Yazı</span>
                                    <h6 class="card-title">Yeni Başlayanlar İçin Rehber</h6>
                                    <p class="card-text small text-muted">Temel bilgiler ve ipuçları</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">1 hafta önce</small>
                                        <span class="badge bg-vibrant-green">%31 açılma</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <span class="badge bg-vibrant-orange mb-2">Özel İçerik</span>
                                    <h6 class="card-title">Abonelere Özel İndirim</h6>
                                    <p class="card-text small text-muted">Partnerlerimizden özel teklifler</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">2 hafta önce</small>
                                        <span class="badge bg-vibrant-green">%42 açılma</span>
                                    </div>
                                </div>
                            </div>
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
    // Form doğrulama
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('newsletterForm');
        const emailInput = document.getElementById('email');
        const nameInput = document.getElementById('name');
        
        // Real-time validation
        emailInput.addEventListener('blur', function() {
            const email = this.value.trim();
            if (email && !isValidEmail(email)) {
                this.classList.add('is-invalid');
                showValidationMessage(this, 'Lütfen geçerli bir email adresi giriniz.');
            } else {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                hideValidationMessage(this);
            }
        });
        
        nameInput.addEventListener('blur', function() {
            const name = this.value.trim();
            if (name && name.length < 2) {
                this.classList.add('is-invalid');
                showValidationMessage(this, 'İsim en az 2 karakter olmalıdır.');
            } else if (name) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                hideValidationMessage(this);
            }
        });
        
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Email validation
            if (!isValidEmail(emailInput.value.trim())) {
                emailInput.classList.add('is-invalid');
                showValidationMessage(emailInput, 'Lütfen geçerli bir email adresi giriniz.');
                isValid = false;
            }
            
            // Name validation
            if (nameInput.value.trim().length < 2) {
                nameInput.classList.add('is-invalid');
                showValidationMessage(nameInput, 'Lütfen geçerli bir isim giriniz.');
                isValid = false;
            }
            
            // Privacy check
            const privacyCheck = document.getElementById('privacy');
            if (!privacyCheck.checked) {
                privacyCheck.classList.add('is-invalid');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                // Scroll to first error
                const firstError = form.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
        
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
        
        function showValidationMessage(input, message) {
            let feedback = input.nextElementSibling;
            if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                input.parentNode.insertBefore(feedback, input.nextElementSibling);
            }
            feedback.textContent = message;
            feedback.style.display = 'block';
        }
        
        function hideValidationMessage(input) {
            const feedback = input.nextElementSibling;
            if (feedback && feedback.classList.contains('invalid-feedback')) {
                feedback.style.display = 'none';
            }
        }
        
        // Input focus effects
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });
    });
</script>

<style>
    /* Özel Canlı Renkler */
    :root {
        --vibrant-blue: #4361ee;
        --vibrant-green: #06d6a0;
        --vibrant-orange: #ff9e00;
        --vibrant-purple: #7209b7;
        --vibrant-pink: #f72585;
        --vibrant-teal: #00b4d8;
        --vibrant-yellow: #ffd60a;
        --vibrant-red: #ef476f;
    }
    
    .bg-gradient-custom {
        background: linear-gradient(135deg, var(--vibrant-blue) 0%, var(--vibrant-purple) 100%) !important;
    }
    
    .text-vibrant-blue { color: var(--vibrant-blue) !important; }
    .text-vibrant-green { color: var(--vibrant-green) !important; }
    .text-vibrant-orange { color: var(--vibrant-orange) !important; }
    .text-vibrant-purple { color: var(--vibrant-purple) !important; }
    .text-vibrant-pink { color: var(--vibrant-pink) !important; }
    .text-vibrant-teal { color: var(--vibrant-teal) !important; }
    .text-vibrant-yellow { color: var(--vibrant-yellow) !important; }
    .text-vibrant-red { color: var(--vibrant-red) !important; }
    
    .bg-vibrant-blue { background-color: var(--vibrant-blue) !important; }
    .bg-vibrant-green { background-color: var(--vibrant-green) !important; }
    .bg-vibrant-orange { background-color: var(--vibrant-orange) !important; }
    .bg-vibrant-purple { background-color: var(--vibrant-purple) !important; }
    .bg-vibrant-pink { background-color: var(--vibrant-pink) !important; }
    .bg-vibrant-teal { background-color: var(--vibrant-teal) !important; }
    .bg-vibrant-yellow { background-color: var(--vibrant-yellow) !important; }
    .bg-vibrant-red { background-color: var(--vibrant-red) !important; }
    
    .form-control:focus {
        border-color: var(--vibrant-blue);
        box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
    }
    
    .form-check-input:checked {
        background-color: var(--vibrant-blue);
        border-color: var(--vibrant-blue);
    }
    
    .btn-custom {
        background: linear-gradient(135deg, var(--vibrant-blue) 0%, var(--vibrant-purple) 100%);
        border: none;
        color: white;
        transition: all 0.3s ease;
    }
    
    .btn-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(67, 97, 238, 0.4);
        color: white;
    }
    
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-5px);
    }
    
    .feature-card {
        transition: all 0.3s ease;
        border: 2px solid transparent !important;
    }
    
    .feature-card:hover {
        border-color: var(--vibrant-blue) !important;
        transform: translateY(-5px);
    }
    
    .testimonial {
        padding: 1rem;
        border-radius: 10px;
        background: #f8f9fa;
        transition: all 0.3s ease;
    }
    
    .testimonial:hover {
        background: #e9ecef;
        transform: translateX(5px);
    }
    
    .accordion-button:not(.collapsed) {
        background-color: rgba(67, 97, 238, 0.1);
        color: var(--vibrant-blue);
    }
    
    .badge {
        font-weight: 500;
    }
    
    .progress-bar {
        background: linear-gradient(90deg, var(--vibrant-green), var(--vibrant-teal));
    }
    
    /* Hero section gradient override */
    .hero-section {
        background: linear-gradient(135deg, var(--vibrant-blue) 0%, var(--vibrant-purple) 100%) !important;
    }
</style>