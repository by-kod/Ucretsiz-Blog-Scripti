<?php
require_once 'includes/config.php';

$pageTitle = "Teknik Destek - " . SITE_NAME;
$pageDescription = "Teknik destek talepleriniz için profesyonel yardım - Hızlı çözüm ve destek";
$pageKeywords = "teknik destek, destek talebi, yardım, sorun çözümü, müşteri hizmetleri";

// Form işleme
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? sanitize($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitize($_POST['email']) : '';
    $subject = isset($_POST['subject']) ? sanitize($_POST['subject']) : '';
    $category = isset($_POST['category']) ? sanitize($_POST['category']) : '';
    $priority = isset($_POST['priority']) ? sanitize($_POST['priority']) : 'medium';
    $description = isset($_POST['description']) ? sanitize($_POST['description']) : '';
    $attachments = isset($_FILES['attachments']) ? $_FILES['attachments'] : null;
    
    // Doğrulama
    if (empty($name) || empty($email) || empty($subject) || empty($category) || empty($description)) {
        $error_message = "Lütfen tüm zorunlu alanları doldurunuz.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Lütfen geçerli bir email adresi giriniz.";
    } elseif (strlen($description) < 20) {
        $error_message = "Lütfen sorununuzu en az 20 karakter ile açıklayınız.";
    } else {
        try {
            // Destek talebini veritabanına kaydet
            $ticket_number = 'TICKET-' . date('Ymd') . '-' . rand(1000, 9999);
            $status = 'open';
            
            $stmt = $pdo->prepare("
                INSERT INTO support_tickets 
                (ticket_number, name, email, subject, category, priority, description, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $ticket_number, 
                $name, 
                $email, 
                $subject, 
                $category, 
                $priority, 
                $description, 
                $status
            ]);
            
            $ticket_id = $pdo->lastInsertId();
            
            // Dosya yükleme işlemi
            if ($attachments && is_array($attachments['name'])) {
                for ($i = 0; $i < count($attachments['name']); $i++) {
                    if ($attachments['error'][$i] === UPLOAD_ERR_OK) {
                        $file_name = $attachments['name'][$i];
                        $file_tmp = $attachments['tmp_name'][$i];
                        $file_size = $attachments['size'][$i];
                        $file_type = $attachments['type'][$i];
                        
                        // Dosya boyutu kontrolü (5MB)
                        if ($file_size > 5 * 1024 * 1024) {
                            continue; // Dosya çok büyükse atla
                        }
                        
                        // Dosya uzantısı kontrolü
                        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'txt'];
                        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                        
                        if (in_array($file_extension, $allowed_extensions)) {
                            $new_file_name = $ticket_id . '_' . uniqid() . '.' . $file_extension;
                            $upload_path = 'uploads/support/' . $new_file_name;
                            
                            // Klasörü kontrol et ve oluştur
                            if (!file_exists('uploads/support')) {
                                mkdir('uploads/support', 0755, true);
                            }
                            
                            if (move_uploaded_file($file_tmp, $upload_path)) {
                                // Dosya bilgisini veritabanına kaydet
                                $file_stmt = $pdo->prepare("
                                    INSERT INTO support_attachments 
                                    (ticket_id, file_name, file_path, file_size, file_type) 
                                    VALUES (?, ?, ?, ?, ?)
                                ");
                                $file_stmt->execute([
                                    $ticket_id, 
                                    $file_name, 
                                    $upload_path, 
                                    $file_size, 
                                    $file_type
                                ]);
                            }
                        }
                    }
                }
            }
            
            $success_message = "Destek talebiniz başarıyla oluşturuldu! Ticket numaranız: <strong>{$ticket_number}</strong>";
            
            // Email gönderimi (simülasyon)
            // sendSupportEmail($email, $name, $ticket_number, $subject);
            
        } catch (PDOException $e) {
            $error_message = "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
        }
    }
}

// Destek istatistikleri
try {
    $stats = $pdo->query("
        SELECT 
            (SELECT COUNT(*) FROM support_tickets WHERE status = 'open') as open_tickets,
            (SELECT COUNT(*) FROM support_tickets WHERE status = 'closed') as closed_tickets,
            (SELECT AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) FROM support_tickets WHERE status = 'closed') as avg_response_time
    ")->fetch();
} catch (PDOException $e) {
    // Tablolar yoksa varsayılan değerler
    $stats = [
        'open_tickets' => 12,
        'closed_tickets' => 345,
        'avg_response_time' => 4.5
    ];
}

// Header dosyasını include et
include 'themes/google-modern/header.php';
?>

<div class="container mt-4">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <h1 class="hero-title fade-in-up">Teknik Destek</h1>
            <p class="hero-subtitle fade-in-up">Profesyonel destek ekibimiz size yardımcı olmaya hazır</p>
        </div>
    </div>

    <div class="row mt-5">
        <!-- Sol Menü - Hızlı Erişim -->
        <div class="col-lg-4">
            <div class="sticky-top" style="top: 100px;">
                <!-- Destek İstatistikleri -->
                <div class="card mb-4">
                    <div class="card-header support-stats-header">
                        <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Destek İstatistikleri</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <div class="support-open fw-bold fs-4"><?php echo $stats['open_tickets']; ?></div>
                                <small class="text-muted">Açık Ticket</small>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="support-closed fw-bold fs-4"><?php echo $stats['closed_tickets']; ?></div>
                                <small class="text-muted">Çözülen</small>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="support-avg-time fw-bold fs-5"><?php echo number_format($stats['avg_response_time'], 1); ?>s</div>
                            <small class="text-muted">Ortalama Yanıt Süresi</small>
                        </div>
                    </div>
                </div>

                <!-- Hızlı Çözümler -->
                <div class="card mb-4">
                    <div class="card-header support-quick-header">
                        <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Hızlı Çözümler</h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <a href="faq.php" class="list-group-item list-group-item-action border-0 px-0 py-2">
                                <i class="fas fa-question-circle me-2 support-faq"></i>
                                Sıkça Sorulan Sorular
                            </a>
                            <a href="help.php" class="list-group-item list-group-item-action border-0 px-0 py-2">
                                <i class="fas fa-life-ring me-2 support-help"></i>
                                Yardım Merkezi
                            </a>
                            <a href="status.php" class="list-group-item list-group-item-action border-0 px-0 py-2">
                                <i class="fas fa-server me-2 support-status"></i>
                                Sistem Durumu
                            </a>
                            <a href="tutorials.php" class="list-group-item list-group-item-action border-0 px-0 py-2">
                                <i class="fas fa-graduation-cap me-2 support-tutorials"></i>
                                Eğitimler
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Acil Durum -->
                <div class="card mb-4">
                    <div class="card-header support-urgent-header">
                        <h6 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Acil Durum</h6>
                    </div>
                    <div class="card-body">
                        <p class="small mb-3">Acil durumlar için doğrudan iletişim:</p>
                        <div class="d-grid gap-2">
                            <a href="tel:+905555555555" class="btn support-phone-btn">
                                <i class="fas fa-phone me-1"></i>+90 555 555 5555
                            </a>
                            <a href="mailto:mail@blog.blog" class="btn support-email-btn">
                                <i class="fas fa-envelope me-1"></i>mail@blog.blog
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Destek Saatleri -->
                <div class="card">
                    <div class="card-header support-hours-header">
                        <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Destek Saatleri</h6>
                    </div>
                    <div class="card-body">
                        <div class="small">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Pazartesi - Cuma:</span>
                                <span class="fw-bold">09:00 - 18:00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Cumartesi:</span>
                                <span class="fw-bold">10:00 - 16:00</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Pazar:</span>
                                <span class="fw-bold text-muted">Kapalı</span>
                            </div>
                        </div>
                        <hr>
                        <div class="text-center">
                            <small class="text-muted">
                                <i class="fas fa-globe me-1"></i>
                                GMT+3 Türkiye Saati
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ana İçerik -->
        <div class="col-lg-8">
            <!-- Destek Formu -->
            <div class="card shadow-sm border-0">
                <div class="card-header support-form-header py-4">
                    <h3 class="card-title mb-0 text-center">
                        <i class="fas fa-headset me-2"></i>
                        Destek Talebi Oluştur
                    </h3>
                </div>
                <div class="card-body p-4">
                    <?php if($success_message): ?>
                        <div class="alert support-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?php echo $success_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if($error_message): ?>
                        <div class="alert support-error alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo $error_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data" id="supportForm">
                        <div class="row">
                            <!-- Kişisel Bilgiler -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label fw-bold">
                                        <i class="fas fa-user me-2 support-icon"></i>
                                        Adınız Soyadınız <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>" 
                                           placeholder="Adınız ve soyadınız" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label fw-bold">
                                        <i class="fas fa-envelope me-2 support-icon"></i>
                                        Email Adresiniz <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" 
                                           placeholder="ornek@email.com" required>
                                </div>
                            </div>
                        </div>

                        <!-- Talep Detayları -->
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="subject" class="form-label fw-bold">
                                        <i class="fas fa-tag me-2 support-icon"></i>
                                        Talep Konusu <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="subject" name="subject" 
                                           value="<?php echo isset($_POST['subject']) ? $_POST['subject'] : ''; ?>" 
                                           placeholder="Sorunun kısa açıklaması" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="priority" class="form-label fw-bold">
                                        <i class="fas fa-flag me-2 support-icon"></i>
                                        Öncelik Durumu
                                    </label>
                                    <select class="form-select" id="priority" name="priority">
                                        <option value="low" <?php echo (isset($_POST['priority']) && $_POST['priority'] == 'low') ? 'selected' : ''; ?>>Düşük</option>
                                        <option value="medium" <?php echo (!isset($_POST['priority']) || $_POST['priority'] == 'medium') ? 'selected' : ''; ?>>Orta</option>
                                        <option value="high" <?php echo (isset($_POST['priority']) && $_POST['priority'] == 'high') ? 'selected' : ''; ?>>Yüksek</option>
                                        <option value="urgent" <?php echo (isset($_POST['priority']) && $_POST['priority'] == 'urgent') ? 'selected' : ''; ?>>Acil</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Kategori -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-folder me-2 support-icon"></i>
                                Sorun Kategorisi <span class="text-danger">*</span>
                            </label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="category" id="category_technical" value="technical" 
                                               <?php echo (isset($_POST['category']) && $_POST['category'] == 'technical') ? 'checked' : 'checked'; ?>>
                                        <label class="form-check-label" for="category_technical">
                                            <i class="fas fa-cog me-2 support-tech"></i>Teknik Sorun
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="category" id="category_account" value="account" 
                                               <?php echo (isset($_POST['category']) && $_POST['category'] == 'account') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="category_account">
                                            <i class="fas fa-user me-2 support-account"></i>Hesap Sorunu
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="category" id="category_content" value="content" 
                                               <?php echo (isset($_POST['category']) && $_POST['category'] == 'content') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="category_content">
                                            <i class="fas fa-newspaper me-2 support-content"></i>İçerik Sorunu
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="category" id="category_bug" value="bug" 
                                               <?php echo (isset($_POST['category']) && $_POST['category'] == 'bug') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="category_bug">
                                            <i class="fas fa-bug me-2 support-bug"></i>Hata Bildirimi
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="category" id="category_feature" value="feature" 
                                               <?php echo (isset($_POST['category']) && $_POST['category'] == 'feature') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="category_feature">
                                            <i class="fas fa-lightbulb me-2 support-feature"></i>Özellik Önerisi
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="category" id="category_other" value="other" 
                                               <?php echo (isset($_POST['category']) && $_POST['category'] == 'other') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="category_other">
                                            <i class="fas fa-ellipsis-h me-2 support-other"></i>Diğer
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sorun Açıklaması -->
                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">
                                <i class="fas fa-align-left me-2 support-icon"></i>
                                Sorun Açıklaması <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="description" name="description" rows="8" 
                                      placeholder="Sorununuzu detaylı bir şekilde açıklayınız. Mümkünse aşağıdaki bilgileri ekleyin:
                                      
• Sorun ne zaman başladı?
• Hangi sayfada/ekranda oluşuyor?
• Hangi tarayıcı ve işletim sistemini kullanıyorsunuz?
• Hata mesajı alıyorsanız tam metnini yazın.
• Sorunu çözmek için neler denediniz?" required><?php echo isset($_POST['description']) ? $_POST['description'] : ''; ?></textarea>
                            <div class="form-text">
                                <span id="charCount">0</span> / 5000 karakter (Minimum 20 karakter)
                            </div>
                        </div>

                        <!-- Dosya Yükleme -->
                        <div class="mb-4">
                            <label for="attachments" class="form-label fw-bold">
                                <i class="fas fa-paperclip me-2 support-icon"></i>
                                Ek Dosyalar
                            </label>
                            <input type="file" class="form-control" id="attachments" name="attachments[]" multiple 
                                   accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.txt">
                            <div class="form-text">
                                Maksimum 5 dosya, her biri 5MB'dan küçük olmalıdır. 
                                İzin verilen formatlar: JPG, PNG, GIF, PDF, DOC, DOCX, TXT
                            </div>
                            <div id="filePreview" class="mt-2"></div>
                        </div>

                        <!-- Ön Bilgilendirme -->
                        <div class="alert support-info">
                            <h6 class="alert-heading">
                                <i class="fas fa-info-circle me-2"></i>
                                Önemli Bilgiler
                            </h6>
                            <ul class="mb-0 small">
                                <li>Destek taleplerine genellikle <strong>24 saat içinde</strong> yanıt verilir</li>
                                <li>Acil durumlar için telefon desteğimizi kullanabilirsiniz</li>
                                <li>Ticket numaranızı kaydedin, takip için gerekli olacaktır</li>
                                <li>Daha hızlı çözüm için sorununuzu mümkün olduğunca detaylı açıklayın</li>
                            </ul>
                        </div>

                        <!-- Gönder Butonu -->
                        <div class="d-grid">
                            <button type="submit" class="btn support-submit-btn btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>
                                Destek Talebini Gönder
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sık Karşılaşılan Sorunlar -->
            <div class="card mt-4">
                <div class="card-header support-common-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list-ul me-2"></i>
                        Sık Karşılaşılan Sorunlar
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-sign-in-alt support-login me-3 mt-1"></i>
                                <div>
                                    <h6 class="mb-1">Giriş Yapamıyorum</h6>
                                    <p class="small text-muted mb-0">Şifrenizi mi unuttunuz? Hemen sıfırlayın.</p>
                                    <a href="help.php#account" class="small support-link">Çözümü Gör →</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-comment support-comment me-3 mt-1"></i>
                                <div>
                                    <h6 class="mb-1">Yorumlarım Onaylanmıyor</h6>
                                    <p class="small text-muted mb-0">Yorum moderasyon süreci hakkında bilgi.</p>
                                    <a href="faq.php#content" class="small support-link">Detaylar →</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-exclamation-triangle support-error-icon me-3 mt-1"></i>
                                <div>
                                    <h6 class="mb-1">404 Sayfa Bulunamadı</h6>
                                    <p class="small text-muted mb-0">Eksik veya hatalı sayfa bağlantıları.</p>
                                    <a href="help.php#technical" class="small support-link">Çözüm Önerileri →</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-mobile-alt support-mobile me-3 mt-1"></i>
                                <div>
                                    <h6 class="mb-1">Mobil Uyumluluk</h6>
                                    <p class="small text-muted mb-0">Mobil cihazlarda yaşanan sorunlar.</p>
                                    <a href="help.php#mobile" class="small support-link">Rehber →</a>
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
    document.addEventListener('DOMContentLoaded', function() {
        // Karakter sayacı
        const descriptionTextarea = document.getElementById('description');
        const charCount = document.getElementById('charCount');
        
        descriptionTextarea.addEventListener('input', function() {
            const count = this.value.length;
            charCount.textContent = count;
            
            if (count < 20) {
                charCount.classList.add('text-danger');
                charCount.classList.remove('text-success');
            } else {
                charCount.classList.remove('text-danger');
                charCount.classList.add('text-success');
            }
        });

        // Dosya önizleme
        const fileInput = document.getElementById('attachments');
        const filePreview = document.getElementById('filePreview');
        
        fileInput.addEventListener('change', function() {
            filePreview.innerHTML = '';
            const files = this.files;
            
            if (files.length > 5) {
                showAlert('Maksimum 5 dosya seçebilirsiniz.', 'warning');
                this.value = '';
                return;
            }
            
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                
                // Dosya boyutu kontrolü
                if (file.size > 5 * 1024 * 1024) {
                    showAlert(`"${file.name}" dosyası çok büyük (maksimum 5MB)`, 'warning');
                    continue;
                }
                
                const fileItem = document.createElement('div');
                fileItem.className = 'file-item d-flex justify-content-between align-items-center p-2 border rounded mb-2';
                fileItem.innerHTML = `
                    <div>
                        <i class="fas fa-file me-2 text-muted"></i>
                        <span class="small">${file.name}</span>
                        <small class="text-muted ms-2">(${(file.size / 1024 / 1024).toFixed(2)} MB)</small>
                    </div>
                    <button type="button" class="btn btn-sm support-cancel-btn" onclick="removeFile(${i})">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                filePreview.appendChild(fileItem);
            }
        });

        // Öncelik seviyesine göre renk değişimi
        const prioritySelect = document.getElementById('priority');
        prioritySelect.addEventListener('change', function() {
            const colors = {
                'low': 'support-low',
                'medium': 'support-medium',
                'high': 'support-high',
                'urgent': 'support-urgent'
            };
            
            // Önceki renkleri temizle
            this.classList.remove('support-low', 'support-medium', 'support-high', 'support-urgent');
            
            // Yeni rengi ekle
            this.classList.add(colors[this.value]);
        });

        // Form doğrulama
        const supportForm = document.getElementById('supportForm');
        supportForm.addEventListener('submit', function(e) {
            let isValid = true;
            const description = document.getElementById('description').value.trim();
            
            if (description.length < 20) {
                showAlert('Lütfen sorun açıklamanızı en az 20 karakter olacak şekilde detaylandırın.', 'warning');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });

        // İlk yüklemede karakter sayısını güncelle
        descriptionTextarea.dispatchEvent(new Event('input'));
        prioritySelect.dispatchEvent(new Event('change'));
    });

    function removeFile(index) {
        const dt = new DataTransfer();
        const input = document.getElementById('attachments');
        const { files } = input;
        
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            if (index !== i) {
                dt.items.add(file);
            }
        }
        
        input.files = dt.files;
        
        // Önizlemeyi yenile
        input.dispatchEvent(new Event('change'));
    }

    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert support-${type} alert-dismissible fade show position-fixed`;
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

    // Form temizleme
    function clearForm() {
        document.getElementById('supportForm').reset();
        document.getElementById('filePreview').innerHTML = '';
        document.getElementById('charCount').textContent = '0';
        document.getElementById('charCount').classList.remove('text-success');
        document.getElementById('charCount').classList.add('text-danger');
    }
</script>

<style>
    /* Özgün Renk Paleti */
    :root {
        --support-primary: #8B5CF6;
        --support-secondary: #06B6D4;
        --support-success: #10B981;
        --support-warning: #F59E0B;
        --support-danger: #EF4444;
        --support-info: #3B82F6;
        --support-dark: #1F2937;
        --support-light: #F8FAFC;
        --support-accent: #EC4899;
    }

    /* Hero Section */
    .hero-section {
        background: linear-gradient(135deg, var(--support-primary) 0%, var(--support-secondary) 100%);
        color: white;
        padding: 4rem 0;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    /* Kart Başlıkları */
    .support-stats-header {
        background: linear-gradient(135deg, var(--support-primary), var(--support-secondary)) !important;
        color: white !important;
    }

    .support-quick-header {
        background: linear-gradient(135deg, var(--support-success), var(--support-info)) !important;
        color: white !important;
    }

    .support-urgent-header {
        background: linear-gradient(135deg, var(--support-danger), var(--support-warning)) !important;
        color: white !important;
    }

    .support-hours-header {
        background: linear-gradient(135deg, var(--support-info), var(--support-secondary)) !important;
        color: white !important;
    }

    .support-form-header {
        background: linear-gradient(135deg, var(--support-light), #ffffff) !important;
        border-bottom: 3px solid var(--support-primary) !important;
    }

    .support-common-header {
        background: linear-gradient(135deg, var(--support-light), #ffffff) !important;
        border-bottom: 3px solid var(--support-info) !important;
    }

    /* İstatistik Renkleri */
    .support-open {
        color: var(--support-warning) !important;
    }

    .support-closed {
        color: var(--support-success) !important;
    }

    .support-avg-time {
        color: var(--support-info) !important;
    }

    /* İkon Renkleri */
    .support-icon {
        color: var(--support-primary) !important;
    }

    .support-faq {
        color: var(--support-info) !important;
    }

    .support-help {
        color: var(--support-success) !important;
    }

    .support-status {
        color: var(--support-warning) !important;
    }

    .support-tutorials {
        color: var(--support-secondary) !important;
    }

    /* Kategori İkonları */
    .support-tech {
        color: var(--support-warning) !important;
    }

    .support-account {
        color: var(--support-success) !important;
    }

    .support-content {
        color: var(--support-info) !important;
    }

    .support-bug {
        color: var(--support-danger) !important;
    }

    .support-feature {
        color: var(--support-accent) !important;
    }

    .support-other {
        color: var(--support-dark) !important;
    }

    /* Butonlar */
    .support-submit-btn {
        background: linear-gradient(135deg, var(--support-primary) 0%, var(--support-accent) 100%) !important;
        border: none !important;
        color: white !important;
        transition: all 0.3s ease !important;
    }

    .support-submit-btn:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4) !important;
    }

    .support-phone-btn {
        background: linear-gradient(135deg, var(--support-danger), var(--support-warning)) !important;
        border: none !important;
        color: white !important;
    }

    .support-email-btn {
        background: linear-gradient(135deg, var(--support-info), var(--support-secondary)) !important;
        border: none !important;
        color: white !important;
    }

    .support-cancel-btn {
        background: var(--support-danger) !important;
        border: none !important;
        color: white !important;
    }

    /* Alert Mesajları */
    .support-success {
        background: linear-gradient(135deg, #D1FAE5, #ECFDF5) !important;
        border-left: 4px solid var(--support-success) !important;
        color: #065F46 !important;
    }

    .support-error {
        background: linear-gradient(135deg, #FEE2E2, #FEF2F2) !important;
        border-left: 4px solid var(--support-danger) !important;
        color: #991B1B !important;
    }

    .support-info {
        background: linear-gradient(135deg, #DBEAFE, #EFF6FF) !important;
        border-left: 4px solid var(--support-info) !important;
        color: #1E40AF !important;
    }

    /* Öncelik Seviyeleri */
    .support-low {
        border-color: var(--support-success) !important;
        box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25) !important;
    }

    .support-medium {
        border-color: var(--support-warning) !important;
        box-shadow: 0 0 0 0.2rem rgba(245, 158, 11, 0.25) !important;
    }

    .support-high {
        border-color: var(--support-danger) !important;
        box-shadow: 0 0 0 0.2rem rgba(239, 68, 68, 0.25) !important;
    }

    .support-urgent {
        border-color: var(--support-accent) !important;
        box-shadow: 0 0 0 0.2rem rgba(236, 72, 153, 0.25) !important;
    }

    /* Link ve İkonlar */
    .support-link {
        color: var(--support-primary) !important;
        text-decoration: none !important;
        font-weight: 500 !important;
    }

    .support-link:hover {
        color: var(--support-accent) !important;
    }

    .support-login {
        color: var(--support-success) !important;
    }

    .support-comment {
        color: var(--support-warning) !important;
    }

    .support-error-icon {
        color: var(--support-danger) !important;
    }

    .support-mobile {
        color: var(--support-info) !important;
    }

    /* Form Elementleri */
    .form-check-input:checked {
        background-color: var(--support-primary) !important;
        border-color: var(--support-primary) !important;
    }

    .form-check-label {
        cursor: pointer;
        transition: color 0.3s ease;
    }

    .form-check-label:hover {
        color: var(--support-primary) !important;
    }

    /* Genel Stiller */
    .file-item {
        background-color: var(--support-light);
        transition: all 0.3s ease;
    }

    .file-item:hover {
        background-color: #E2E8F0;
        transform: translateX(5px);
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

    textarea.form-control {
        resize: vertical;
        min-height: 120px;
    }

    @media (max-width: 768px) {
        .sticky-top {
            position: relative !important;
            top: 0 !important;
        }
        
        .card-body {
            padding: 1.5rem !important;
        }
    }
</style>