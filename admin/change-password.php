<?php
require_once '../includes/config.php';

// Giriş kontrolü
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    redirect('login.php');
}

$pageTitle = "Şifre Değiştir - " . SITE_NAME;

$success_message = '';
$error_message = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Doğrulama
    if(empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error_message = "Lütfen tüm alanları doldurun.";
    } elseif($new_password !== $confirm_password) {
        $error_message = "Yeni şifreler eşleşmiyor.";
    } elseif(strlen($new_password) < 6) {
        $error_message = "Yeni şifre en az 6 karakter olmalıdır.";
    } else {
        try {
            // Mevcut şifreyi kontrol et
            $stmt = $pdo->prepare("SELECT password FROM users WHERE username = ? AND role = 'admin'");
            $stmt->execute([$_SESSION['admin_username']]);
            $user = $stmt->fetch();
            
            if($user && password_verify($current_password, $user['password'])) {
                // Yeni şifreyi hashle ve güncelle
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $update_stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ? AND role = 'admin'");
                $update_stmt->execute([$new_password_hash, $_SESSION['admin_username']]);
                
                $success_message = "Şifreniz başarıyla güncellendi.";
            } else {
                $error_message = "Mevcut şifre hatalı.";
            }
        } catch(PDOException $e) {
            $error_message = "Bir hata oluştu: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #667eea;
            --primary-light: rgba(102, 126, 234, 0.1);
            --secondary-color: #764ba2;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Inter', sans-serif;
        }
        
        .password-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
        }
        
        .password-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
        }
        
        .password-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 2rem;
            text-align: center;
        }
        
        .password-strength {
            height: 5px;
            border-radius: 5px;
            margin-top: 5px;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            padding: 12px 30px;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="main-content">
        <div class="password-container">
            <div class="password-card">
                <div class="password-header">
                    <h3><i class="fas fa-lock me-2"></i>Şifre Değiştir</h3>
                    <p class="mb-0">Hesap güvenliğiniz için düzenli olarak şifrenizi güncelleyin</p>
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

                    <form method="POST" id="passwordForm">
                        <div class="mb-3">
                            <label for="current_password" class="form-label fw-bold">
                                <i class="fas fa-key me-2 text-primary"></i>Mevcut Şifre
                            </label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_password" class="form-label fw-bold">
                                <i class="fas fa-lock me-2 text-primary"></i>Yeni Şifre
                            </label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                            <div class="password-strength" id="passwordStrength"></div>
                            <div class="form-text">
                                Şifre en az 6 karakter uzunluğunda olmalıdır.
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="confirm_password" class="form-label fw-bold">
                                <i class="fas fa-lock me-2 text-primary"></i>Yeni Şifre (Tekrar)
                            </label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            <div class="form-text" id="passwordMatch"></div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Şifreyi Güncelle
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Güvenlik İpuçları -->
            <div class="card mt-4">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-shield-alt me-2 text-primary"></i>Güvenlik İpuçları
                    </h6>
                    <ul class="list-unstyled small text-muted">
                        <li><i class="fas fa-check text-success me-2"></i>Şifreniz en az 6 karakter olmalı</li>
                        <li><i class="fas fa-check text-success me-2"></i>Büyük/küçük harf, sayı ve sembol kullanın</li>
                        <li><i class="fas fa-check text-success me-2"></i>Kişisel bilgilerinizi şifre olarak kullanmayın</li>
                        <li><i class="fas fa-check text-success me-2"></i>Şifrenizi düzenli olarak güncelleyin</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Şifre güçlülük kontrolü
        document.getElementById('new_password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('passwordStrength');
            let strength = 0;
            
            if(password.length >= 6) strength += 25;
            if(/[A-Z]/.test(password)) strength += 25;
            if(/[0-9]/.test(password)) strength += 25;
            if(/[^A-Za-z0-9]/.test(password)) strength += 25;
            
            strengthBar.style.width = strength + '%';
            
            if(strength < 50) {
                strengthBar.style.backgroundColor = '#dc3545';
            } else if(strength < 75) {
                strengthBar.style.backgroundColor = '#ffc107';
            } else {
                strengthBar.style.backgroundColor = '#28a745';
            }
        });
        
        // Şifre eşleşme kontrolü
        document.getElementById('confirm_password').addEventListener('input', function() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = this.value;
            const matchText = document.getElementById('passwordMatch');
            
            if(confirmPassword === '') {
                matchText.innerHTML = '';
            } else if(newPassword === confirmPassword) {
                matchText.innerHTML = '<i class="fas fa-check text-success me-1"></i>Şifreler eşleşiyor';
                matchText.className = 'form-text text-success';
            } else {
                matchText.innerHTML = '<i class="fas fa-times text-danger me-1"></i>Şifreler eşleşmiyor';
                matchText.className = 'form-text text-danger';
            }
        });
        
        // Form gönderim kontrolü
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if(newPassword !== confirmPassword) {
                e.preventDefault();
                alert('Şifreler eşleşmiyor!');
                return false;
            }
            
            if(newPassword.length < 6) {
                e.preventDefault();
                alert('Şifre en az 6 karakter olmalıdır!');
                return false;
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>