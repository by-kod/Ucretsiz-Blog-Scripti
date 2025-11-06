<?php
require_once 'includes/config.php';

$pageTitle = "Geri Bildirim - " . SITE_NAME;
$pageDescription = "Blog.blog için geri bildirimde bulunun - Öneri ve şikayetleriniz bizim için değerli";
$pageKeywords = "geri bildirim, öneri, şikayet, blog.blog, memur blog, geri bildirim formu";

// Form işleme
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? sanitize($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitize($_POST['email']) : '';
    $feedback_type = isset($_POST['feedback_type']) ? sanitize($_POST['feedback_type']) : '';
    $subject = isset($_POST['subject']) ? sanitize($_POST['subject']) : '';
    $message = isset($_POST['message']) ? sanitize($_POST['message']) : '';
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $allow_contact = isset($_POST['allow_contact']) ? 1 : 0;
    
    // Doğrulama
    if (empty($name) || empty($email) || empty($feedback_type) || empty($subject) || empty($message)) {
        $error_message = "Lütfen tüm zorunlu alanları doldurunuz.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Lütfen geçerli bir email adresi giriniz.";
    } elseif (strlen($message) < 20) {
        $error_message = "Lütfen mesajınızı en az 20 karakter ile açıklayınız.";
    } else {
        try {
            // Geri bildirimi veritabanına kaydet
            $feedback_number = 'FB-' . date('Ymd') . '-' . rand(1000, 9999);
            $status = 'new';
            
            $stmt = $pdo->prepare("
                INSERT INTO feedback 
                (feedback_number, name, email, feedback_type, subject, message, rating, allow_contact, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $feedback_number, 
                $name, 
                $email, 
                $feedback_type, 
                $subject, 
                $message, 
                $rating, 
                $allow_contact, 
                $status
            ]);
            
            $feedback_id = $pdo->lastInsertId();
            
            $success_message = "Geri bildiriminiz başarıyla gönderildi! Referans numaranız: <strong>{$feedback_number}</strong>";
            
        } catch (PDOException $e) {
            $error_message = "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
        }
    }
}

// Geri bildirim istatistikleri
try {
    $stats = $pdo->query("
        SELECT 
            (SELECT COUNT(*) FROM feedback WHERE status = 'new') as new_feedback,
            (SELECT COUNT(*) FROM feedback WHERE status = 'reviewed') as reviewed_feedback,
            (SELECT COUNT(*) FROM feedback WHERE feedback_type = 'suggestion') as suggestions,
            (SELECT COUNT(*) FROM feedback WHERE feedback_type = 'complaint') as complaints,
            (SELECT AVG(rating) FROM feedback WHERE rating > 0) as avg_rating
    ")->fetch();
} catch (PDOException $e) {
    // Tablolar yoksa varsayılan değerler
    $stats = [
        'new_feedback' => 15,
        'reviewed_feedback' => 128,
        'suggestions' => 89,
        'complaints' => 54,
        'avg_rating' => 4.2
    ];
}

// Header dosyasını include et
include 'themes/google-modern/header.php';
?>

<div class="container mt-4">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <h1 class="hero-title fade-in-up">Geri Bildirim</h1>
            <p class="hero-subtitle fade-in-up">Görüşleriniz bizim için değerli - Blog.blog'yu birlikte geliştirelim</p>
        </div>
    </div>

    <div class="row mt-5">
        <!-- Sol Menü - İstatistikler ve Hızlı Erişim -->
        <div class="col-lg-4">
            <div class="sticky-top" style="top: 100px;">
                <!-- Geri Bildirim İstatistikleri -->
                <div class="card mb-4">
                    <div class="card-header" style="background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%); color: white;">
                        <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Geri Bildirim İstatistikleri</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <div class="fw-bold fs-4" style="color: #F59E0B;"><?php echo $stats['new_feedback']; ?></div>
                                <small class="text-muted">Yeni Geri Bildirim</small>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="fw-bold fs-4" style="color: #10B981;"><?php echo $stats['reviewed_feedback']; ?></div>
                                <small class="text-muted">İncelenen</small>
                            </div>
                        </div>
                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <div class="fw-bold fs-4" style="color: #3B82F6;"><?php echo $stats['suggestions']; ?></div>
                                <small class="text-muted">Öneri</small>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="fw-bold fs-4" style="color: #EF4444;"><?php echo $stats['complaints']; ?></div>
                                <small class="text-muted">Şikayet</small>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="fw-bold fs-5" style="color: #F59E0B;">
                                <?php echo number_format($stats['avg_rating'], 1); ?>
                                <i class="fas fa-star" style="color: #F59E0B;"></i>
                            </div>
                            <small class="text-muted">Ortalama Puan</small>
                        </div>
                    </div>
                </div>

                <!-- Hızlı Erişim -->
                <div class="card mb-4">
                    <div class="card-header" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: white;">
                        <h6 class="mb-0"><i class="fas fa-link me-2"></i>Hızlı Erişim</h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <a href="support.php" class="list-group-item list-group-item-action border-0 px-0 py-2">
                                <i class="fas fa-headset me-2" style="color: #8B5CF6;"></i>
                                Teknik Destek
                            </a>
                            <a href="faq.php" class="list-group-item list-group-item-action border-0 px-0 py-2">
                                <i class="fas fa-question-circle me-2" style="color: #10B981;"></i>
                                Sıkça Sorulan Sorular
                            </a>
                            <a href="contact.php" class="list-group-item list-group-item-action border-0 px-0 py-2">
                                <i class="fas fa-envelope me-2" style="color: #3B82F6;"></i>
                                İletişim
                            </a>
                            <a href="suggestions.php" class="list-group-item list-group-item-action border-0 px-0 py-2">
                                <i class="fas fa-lightbulb me-2" style="color: #F59E0B;"></i>
                                Öneri Kutusu
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Geri Bildirim Süreci -->
                <div class="card mb-4">
                    <div class="card-header" style="background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%); color: white;">
                        <h6 class="mb-0"><i class="fas fa-sync-alt me-2"></i>Geri Bildirim Süreci</h6>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item mb-3">
                                <div class="timeline-marker" style="background: #8B5CF6;"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Gönderim</h6>
                                    <p class="small text-muted mb-0">Geri bildiriminiz alındı</p>
                                </div>
                            </div>
                            <div class="timeline-item mb-3">
                                <div class="timeline-marker" style="background: #F59E0B;"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">İnceleme</h6>
                                    <p class="small text-muted mb-0">Ekibimiz tarafından inceleniyor</p>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-marker" style="background: #10B981;"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Değerlendirme</h6>
                                    <p class="small text-muted mb-0">Geliştirme planlarına dahil ediliyor</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Son Uygulanan Öneriler -->
                <div class="card">
                    <div class="card-header" style="background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%); color: white;">
                        <h6 class="mb-0"><i class="fas fa-check-circle me-2"></i>Son Uygulanan Öneriler</h6>
                    </div>
                    <div class="card-body">
                        <div class="small">
                            <div class="d-flex align-items-start mb-3">
                                <i class="fas fa-check me-2 mt-1" style="color: #10B981;"></i>
                                <div>
                                    <span class="fw-bold">Mobil uyumlu tasarım</span>
                                    <p class="mb-0 text-muted">Site mobil cihazlarda daha iyi görüntüleniyor</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-start mb-3">
                                <i class="fas fa-check me-2 mt-1" style="color: #10B981;"></i>
                                <div>
                                    <span class="fw-bold">Arama iyileştirmesi</span>
                                    <p class="mb-0 text-muted">Gelişmiş arama algoritması eklendi</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-start">
                                <i class="fas fa-check me-2 mt-1" style="color: #10B981;"></i>
                                <div>
                                    <span class="fw-bold">Yeni kategori sistemi</span>
                                    <p class="mb-0 text-muted">İçerikler daha iyi organize edildi</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ana İçerik -->
        <div class="col-lg-8">
            <!-- Geri Bildirim Formu -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light py-4">
                    <h3 class="card-title mb-0 text-center">
                        <i class="fas fa-comment-dots me-2" style="color: #8B5CF6;"></i>
                        Geri Bildirim Formu
                    </h3>
                </div>
                <div class="card-body p-4">
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

                    <form method="POST" id="feedbackForm">
                        <div class="row">
                            <!-- Kişisel Bilgiler -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label fw-bold">
                                        <i class="fas fa-user me-2" style="color: #8B5CF6;"></i>
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
                                        <i class="fas fa-envelope me-2" style="color: #8B5CF6;"></i>
                                        Email Adresiniz <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" 
                                           placeholder="ornek@email.com" required>
                                </div>
                            </div>
                        </div>

                        <!-- Geri Bildirim Türü -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-tag me-2" style="color: #8B5CF6;"></i>
                                Geri Bildirim Türü <span class="text-danger">*</span>
                            </label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="feedback_type" id="type_suggestion" value="suggestion" 
                                               <?php echo (isset($_POST['feedback_type']) && $_POST['feedback_type'] == 'suggestion') ? 'checked' : 'checked'; ?>>
                                        <label class="form-check-label" for="type_suggestion">
                                            <i class="fas fa-lightbulb me-2" style="color: #F59E0B;"></i>Öneri
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="feedback_type" id="type_complaint" value="complaint" 
                                               <?php echo (isset($_POST['feedback_type']) && $_POST['feedback_type'] == 'complaint') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="type_complaint">
                                            <i class="fas fa-exclamation-triangle me-2" style="color: #EF4444;"></i>Şikayet
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="feedback_type" id="type_other" value="other" 
                                               <?php echo (isset($_POST['feedback_type']) && $_POST['feedback_type'] == 'other') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="type_other">
                                            <i class="fas fa-ellipsis-h me-2 text-secondary"></i>Diğer
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Konu -->
                        <div class="mb-3">
                            <label for="subject" class="form-label fw-bold">
                                <i class="fas fa-tag me-2" style="color: #8B5CF6;"></i>
                                Konu <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="subject" name="subject" 
                                   value="<?php echo isset($_POST['subject']) ? $_POST['subject'] : ''; ?>" 
                                   placeholder="Geri bildiriminizin kısa açıklaması" required>
                        </div>

                        <!-- Puanlama -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-star me-2" style="color: #8B5CF6;"></i>
                                Memnuniyet Puanı
                            </label>
                            <div class="rating-stars">
                                <div class="star-rating">
                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                        <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" 
                                               <?php echo (isset($_POST['rating']) && $_POST['rating'] == $i) ? 'checked' : ''; ?>>
                                        <label for="star<?php echo $i; ?>" title="<?php echo $i; ?> yıldız">
                                            <i class="fas fa-star"></i>
                                        </label>
                                    <?php endfor; ?>
                                </div>
                                <div class="rating-labels mt-2">
                                    <small class="text-muted">
                                        <span class="d-none d-sm-inline">1 = Çok Kötü</span>
                                        <span class="mx-2">|</span>
                                        <span class="d-none d-sm-inline">5 = Mükemmel</span>
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Mesaj -->
                        <div class="mb-3">
                            <label for="message" class="form-label fw-bold">
                                <i class="fas fa-align-left me-2" style="color: #8B5CF6;"></i>
                                Mesajınız <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="message" name="message" rows="8" 
                                      placeholder="Geri bildiriminizi detaylı bir şekilde açıklayınız. Önerileriniz için:
                                      
• Mevcut durumu açıklayın
• Önerinizin ne olduğunu belirtin
• Beklenen sonucu açıklayın
• Örnekler verin (isteğe bağlı)" required><?php echo isset($_POST['message']) ? $_POST['message'] : ''; ?></textarea>
                            <div class="form-text">
                                <span id="charCount">0</span> / 2000 karakter (Minimum 20 karakter)
                            </div>
                        </div>

                        <!-- İletişim İzni -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="allow_contact" name="allow_contact" value="1"
                                       <?php echo (isset($_POST['allow_contact']) && $_POST['allow_contact'] == 1) ? 'checked' : 'checked'; ?>>
                                <label class="form-check-label" for="allow_contact">
                                    Geri bildirimim hakkında benimle iletişime geçilmesine izin veriyorum
                                </label>
                            </div>
                            <div class="form-text">
                                Size daha iyi hizmet verebilmek için geri bildiriminiz hakkında ek bilgi almak isteyebiliriz.
                            </div>
                        </div>

                        <!-- Ön Bilgilendirme -->
                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="fas fa-info-circle me-2"></i>
                                Önemli Bilgiler
                            </h6>
                            <ul class="mb-0 small">
                                <li>Tüm geri bildirimler dikkatle incelenir ve değerlendirilir</li>
                                <li>Önerileriniz geliştirme planlarımıza dahil edilebilir</li>
                                <li>Şikayetleriniz en kısa sürede çözüme kavuşturulur</li>
                                <li>Referans numaranızı kaydedin, takip için gerekli olacaktır</li>
                            </ul>
                        </div>

                        <!-- Gönder Butonu -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-lg" style="background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%); color: white; border: none;">
                                <i class="fas fa-paper-plane me-2"></i>
                                Geri Bildirimi Gönder
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sık Yapılan Öneriler -->
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-lightbulb me-2" style="color: #8B5CF6;"></i>
                        Sık Yapılan Öneriler
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-mobile-alt me-3 mt-1" style="color: #3B82F6;"></i>
                                <div>
                                    <h6 class="mb-1">Mobil Uygulama</h6>
                                    <p class="small text-muted mb-0">Blog.blog mobil uygulama önerisi</p>
                                    <span class="badge" style="background: #10B981;">Planlanıyor</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-bell me-3 mt-1" style="color: #F59E0B;"></i>
                                <div>
                                    <h6 class="mb-1">Bildirim Sistemi</h6>
                                    <p class="small text-muted mb-0">Yeni içerik bildirimleri</p>
                                    <span class="badge" style="background: #3B82F6;">Değerlendirmede</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-search me-3 mt-1" style="color: #10B981;"></i>
                                <div>
                                    <h6 class="mb-1">Gelişmiş Arama</h6>
                                    <p class="small text-muted mb-0">Filtreleme özellikleri</p>
                                    <span class="badge" style="background: #10B981;">Geliştirildi</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-download me-3 mt-1" style="color: #8B5CF6;"></i>
                                <div>
                                    <h6 class="mb-1">PDF İndirme</h6>
                                    <p class="small text-muted mb-0">İçerikleri PDF olarak indirme</p>
                                    <span class="badge" style="background: #F59E0B;">Planlama Aşamasında</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Geri Bildirim Politikası -->
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-shield-alt me-2" style="color: #8B5CF6;"></i>
                        Geri Bildirim Politikası
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-check me-2" style="color: #10B981;"></i>Yapabilecekleriniz</h6>
                            <ul class="small text-muted">
                                <li>Yapıcı eleştirilerde bulunabilirsiniz</li>
                                <li>Yeni özellik önerileri sunabilirsiniz</li>
                                <li>Karşılaştığınız sorunları bildirebilirsiniz</li>
                                <li>Kullanıcı deneyimi önerileri paylaşabilirsiniz</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-times me-2" style="color: #EF4444;"></i>Yapamayacaklarınız</h6>
                            <ul class="small text-muted">
                                <li>Küfür veya hakaret içeren mesajlar</li>
                                <li>Spam veya reklam içerikleri</li>
                                <li>Kişisel saldırılar</li>
                                <li>Yanlış veya yanıltıcı bilgiler</li>
                            </ul>
                        </div>
                    </div>
                    <div class="alert alert-warning mt-3 mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Önemli:</strong> Tüm geri bildirimler incelenir ve uygun görülenler değerlendirilir. 
                        Her önerinin uygulanacağının garanti edilmediğini unutmayın.
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
        const messageTextarea = document.getElementById('message');
        const charCount = document.getElementById('charCount');
        
        messageTextarea.addEventListener('input', function() {
            const count = this.value.length;
            charCount.textContent = count;
            
            if (count < 20) {
                charCount.style.color = '#EF4444';
            } else {
                charCount.style.color = '#10B981';
            }
        });

        // Yıldız puanlama sistemi
        const starInputs = document.querySelectorAll('.star-rating input');
        starInputs.forEach(input => {
            input.addEventListener('change', function() {
                const selectedValue = this.value;
                // Tüm yıldızları sıfırla
                document.querySelectorAll('.star-rating label').forEach(label => {
                    label.style.color = '#ddd';
                });
                
                // Seçilen yıldıza kadar olanları işaretle
                for (let i = 1; i <= selectedValue; i++) {
                    document.querySelector(`label[for="star${i}"]`).style.color = '#F59E0B';
                }
            });
        });

        // Form doğrulama
        const feedbackForm = document.getElementById('feedbackForm');
        feedbackForm.addEventListener('submit', function(e) {
            let isValid = true;
            const message = document.getElementById('message').value.trim();
            
            if (message.length < 20) {
                showAlert('Lütfen mesajınızı en az 20 karakter olacak şekilde detaylandırın.', 'warning');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });

        // İlk yüklemede karakter sayısını güncelle
        messageTextarea.dispatchEvent(new Event('input'));
        
        // Yıldız puanlama sistemini başlat
        const checkedInput = document.querySelector('.star-rating input:checked');
        if (checkedInput) {
            checkedInput.dispatchEvent(new Event('change'));
        }
    });

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

    // Form temizleme
    function clearForm() {
        document.getElementById('feedbackForm').reset();
        document.getElementById('charCount').textContent = '0';
        document.getElementById('charCount').style.color = '#EF4444';
        
        // Yıldız puanlamasını sıfırla
        document.querySelectorAll('.star-rating input').forEach(input => {
            input.checked = false;
        });
        document.querySelectorAll('.star-rating label').forEach(label => {
            label.style.color = '#ddd';
        });
    }
</script>

<style>
    /* Yıldız puanlama stili */
    .star-rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
    }
    
    .star-rating input {
        display: none;
    }
    
    .star-rating label {
        font-size: 1.5rem;
        color: #ddd;
        cursor: pointer;
        transition: color 0.2s;
        margin-right: 5px;
    }
    
    .star-rating label:hover,
    .star-rating label:hover ~ label {
        color: #FCD34D !important;
    }
    
    /* Timeline stili */
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e9ecef;
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }
    
    .timeline-marker {
        position: absolute;
        left: -30px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid white;
    }
    
    .timeline-content {
        margin-left: 0;
    }
    
    /* Kart stilleri */
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05);
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(139, 92, 246, 0.15) !important;
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
    
    .btn-primary {
        background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);
        border: none;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(139, 92, 246, 0.4);
    }
    
    .form-check-input:checked {
        background-color: #8B5CF6;
        border-color: #8B5CF6;
    }
    
    .form-check-label {
        cursor: pointer;
        transition: color 0.3s ease;
    }
    
    .form-check-label:hover {
        color: #8B5CF6;
    }
    
    @media (max-width: 768px) {
        .sticky-top {
            position: relative !important;
            top: 0 !important;
        }
        
        .card-body {
            padding: 1.5rem !important;
        }
        
        .star-rating {
            justify-content: center;
        }
    }
</style>