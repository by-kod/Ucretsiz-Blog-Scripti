<?php
require_once 'includes/config.php';

$pageTitle = "İletişim - " . SITE_NAME;
$pageDescription = SITE_NAME . " ile iletişime geçin. Soru, görüş ve önerileriniz için bize ulaşın.";
$pageKeywords = "iletişim, bize ulaşın, soru, görüş, öneri, " . SITE_KEYWORDS;

// İletişim formu işleme
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_contact'])) {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $subject = sanitize($_POST['subject']);
    $message = sanitize($_POST['message']);
    $spam_answer = strtolower(trim($_POST['spam_answer']));
    
    // Spam kontrolü
    if($spam_answer !== 'ankara') {
        $_SESSION['error_message'] = "Spam sorusunu yanlış cevapladınız!";
    } elseif(empty($name) || empty($email) || empty($subject) || empty($message)) {
        $_SESSION['error_message'] = "Lütfen tüm zorunlu alanları doldurun!";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "Lütfen geçerli bir e-posta adresi girin!";
    } else {
        try {
            // Mesajı veritabanına kaydet
            $stmt = $pdo->prepare("
                INSERT INTO contact_messages (name, email, subject, message, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $name,
                $email,
                $subject,
                $message,
                $_SERVER['REMOTE_ADDR'],
                $_SERVER['HTTP_USER_AGENT']
            ]);
            
            $_SESSION['success_message'] = "Mesajınız başarıyla gönderildi! En kısa sürede size dönüş yapacağız.";
            
            // Formu temizle
            unset($_POST);
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = "Mesajınız gönderilirken bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
        }
    }
}

require_once 'themes/google-modern/header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Ana Sayfa</a></li>
                    <li class="breadcrumb-item active">İletişim</li>
                </ol>
            </nav>

            <!-- Sayfa Başlığı -->
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold mb-3">İletişim</h1>
                <p class="lead text-muted">Sorularınız, görüşleriniz ve önerileriniz için bize ulaşın</p>
            </div>

            <div class="row">
                <!-- İletişim Bilgileri -->
                <div class="col-lg-4 mb-4">
                    <div class="contact-info-card">
                        <h3 class="h4 mb-4">İletişim Bilgileri</h3>
                        
                        <div class="contact-item mb-4">
                            <div class="contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="contact-content">
                                <h5 class="h6 mb-1">E-posta</h5>
                                <p class="text-muted mb-0"><?php echo SITE_EMAIL; ?></p>
                            </div>
                        </div>
                        
                        <div class="contact-item mb-4">
                            <div class="contact-icon">
                                <i class="fas fa-globe"></i>
                            </div>
                            <div class="contact-content">
                                <h5 class="h6 mb-1">Web Sitesi</h5>
                                <p class="text-muted mb-0"><?php echo SITE_URL; ?></p>
                            </div>
                        </div>
                        
                        <div class="contact-item mb-4">
                            <div class="contact-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="contact-content">
                                <h5 class="h6 mb-1">Yanıt Süresi</h5>
                                <p class="text-muted mb-0">24-48 saat içinde</p>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-headset"></i>
                            </div>
                            <div class="contact-content">
                                <h5 class="h6 mb-1">Destek</h5>
                                <p class="text-muted mb-0">7/24 e-posta desteği</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- İletişim Formu -->
                <div class="col-lg-8">
                    <div class="contact-form-card">
                        <h3 class="h4 mb-4">Mesaj Gönderin</h3>
                        
                        <!-- Mesajlar -->
                        <?php if(isset($_SESSION['success_message'])): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if(isset($_SESSION['error_message'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Ad Soyad <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>" 
                                           required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">E-posta <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" 
                                           required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="subject" class="form-label">Konu <span class="text-danger">*</span></label>
                                <select class="form-select" id="subject" name="subject" required>
                                    <option value="">Konu seçin</option>
                                    <option value="Genel Soru" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Genel Soru') ? 'selected' : ''; ?>>Genel Soru</option>
                                    <option value="Teknik Destek" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Teknik Destek') ? 'selected' : ''; ?>>Teknik Destek</option>
                                    <option value="İş Birliği" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'İş Birliği') ? 'selected' : ''; ?>>İş Birliği</option>
                                    <option value="Reklam" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Reklam') ? 'selected' : ''; ?>>Reklam</option>
                                    <option value="Şikayet" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Şikayet') ? 'selected' : ''; ?>>Şikayet</option>
                                    <option value="Öneri" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Öneri') ? 'selected' : ''; ?>>Öneri</option>
                                    <option value="Diğer" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Diğer') ? 'selected' : ''; ?>>Diğer</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="message" class="form-label">Mesajınız <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="message" name="message" 
                                          rows="6" required 
                                          placeholder="Mesajınızı detaylı bir şekilde yazın..."><?php echo isset($_POST['message']) ? $_POST['message'] : ''; ?></textarea>
                            </div>
                            
                            <!-- Spam Koruması -->
                            <div class="mb-4 p-3 border rounded bg-light">
                                <label for="spam_answer" class="form-label fw-bold">
                                    <i class="fas fa-shield-alt me-1 text-warning"></i>
                                    Spam Koruması: Türkiye'nin başkenti neresidir? <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="spam_answer" name="spam_answer" 
                                       value="<?php echo isset($_POST['spam_answer']) ? $_POST['spam_answer'] : ''; ?>"
                                       placeholder="Cevabı yazın..." required>
                                <div class="form-text">Lütfen soruyu cevaplayın (büyük/küçük harf fark etmez)</div>
                            </div>
                            
                            <button type="submit" name="submit_contact" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-paper-plane me-2"></i>Mesajı Gönder
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- SSS Bölümü -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="faq-section">
                        <h3 class="h4 mb-4 text-center">Sıkça Sorulan Sorular</h3>
                        
                        <div class="accordion" id="faqAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="faq1">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1">
                                        Yanıt süresi ne kadar?
                                    </button>
                                </h2>
                                <div id="faqCollapse1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Mesajlarınıza genellikle 24-48 saat içinde yanıt veriyoruz. Yoğunluk durumuna göre bu süre değişebilir.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="faq2">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2">
                                        Hangi konularda iletişim kurabilirim?
                                    </button>
                                </h2>
                                <div id="faqCollapse2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Teknik destek, içerik önerileri, iş birlikleri, reklam, şikayet ve önerileriniz için iletişim kurabilirsiniz.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="faq3">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3">
                                        Mesajlarım güvende mi?
                                    </button>
                                </h2>
                                <div id="faqCollapse3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Evet, tüm mesajlarınız güvenli veritabanımızda saklanır ve üçüncü şahıslarla paylaşılmaz.
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

<style>
:root {
    --primary-color: #6C63FF;
    --secondary-color: #FF6584;
    --accent-color: #36D1DC;
    --success-color: #4CAF50;
    --warning-color: #FF9800;
    --danger-color: #F44336;
    --info-color: #2196F3;
    --dark-color: #2D3748;
    --light-color: #F7FAFC;
    --gray-color: #718096;
}

.contact-info-card,
.contact-form-card {
    background: white;
    padding: 2rem;
    border-radius: 16px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
    height: 100%;
    border: 1px solid rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.contact-info-card:hover,
.contact-form-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
}

.contact-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.contact-item:hover {
    background: var(--light-color);
    transform: translateX(5px);
}

.contact-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    flex-shrink: 0;
    box-shadow: 0 4px 15px rgba(108, 99, 255, 0.3);
}

.contact-content h5 {
    color: var(--dark-color);
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.faq-section {
    background: white;
    padding: 2.5rem;
    border-radius: 16px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
    border: 1px solid rgba(0,0,0,0.05);
}

.accordion-button {
    font-weight: 600;
    color: var(--dark-color);
    background: var(--light-color);
    border: 1px solid rgba(0,0,0,0.1);
    margin-bottom: 0.5rem;
    border-radius: 8px !important;
    transition: all 0.3s ease;
}

.accordion-button:not(.collapsed) {
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    color: white;
    box-shadow: 0 4px 15px rgba(108, 99, 255, 0.3);
}

.accordion-button:hover {
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    color: white;
    transform: translateY(-2px);
}

.accordion-body {
    color: var(--gray-color);
    line-height: 1.6;
    background: var(--light-color);
    border-radius: 0 0 8px 8px;
    border: 1px solid rgba(0,0,0,0.1);
    border-top: none;
}

.form-label {
    font-weight: 600;
    color: var(--dark-color);
    margin-bottom: 0.5rem;
}

.form-control,
.form-select {
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.form-control:focus,
.form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.1);
    transform: translateY(-2px);
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    border: none;
    padding: 1rem 2rem;
    font-weight: 600;
    border-radius: 12px;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(108, 99, 255, 0.3);
}

.btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(108, 99, 255, 0.4);
    background: linear-gradient(135deg, var(--accent-color), var(--primary-color));
}

.alert {
    border-radius: 12px;
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.alert-success {
    background: linear-gradient(135deg, var(--success-color), #8BC34A);
    color: white;
}

.alert-danger {
    background: linear-gradient(135deg, var(--danger-color), #E91E63);
    color: white;
}

.breadcrumb {
    background: var(--light-color);
    border-radius: 8px;
    padding: 1rem;
}

.breadcrumb-item a {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
}

.breadcrumb-item.active {
    color: var(--gray-color);
}

.display-4 {
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.text-muted {
    color: var(--gray-color) !important;
}

/* Responsive tasarım */
@media (max-width: 768px) {
    .contact-info-card,
    .contact-form-card,
    .faq-section {
        padding: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .contact-item {
        padding: 0.75rem;
    }
    
    .contact-icon {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
    
    .btn-primary {
        padding: 0.875rem 1.5rem;
        font-size: 1rem;
    }
}
</style>

<?php require_once 'themes/google-modern/footer.php'; ?>