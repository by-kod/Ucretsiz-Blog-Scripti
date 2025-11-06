<?php
require_once '../includes/config.php';

// Eğer zaten giriş yapılmışsa admin panele yönlendir
if(isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    redirect('index.php');
}

// Giriş işlemi
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    $errors = [];
    
    if(empty($username) || empty($password)) {
        $errors[] = "Kullanıcı adı ve şifre gereklidir.";
    }
    
    if(empty($errors)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND role IN ('admin', 'editor')");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_role'] = $user['role'];
            
            $_SESSION['success_message'] = "Hoş geldiniz, " . $user['display_name'] . "!";
            redirect('index.php');
        } else {
            $errors[] = "Kullanıcı adı veya şifre hatalı.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Giriş - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #7E57C2;
            --primary-dark: #5E35B1;
            --primary-light: #B39DDB;
            --accent-color: #FF4081;
            --success-color: #4CAF50;
            --warning-color: #FF9800;
            --danger-color: #F44336;
            --dark-color: #263238;
            --light-color: #ECEFF1;
            --gray-color: #78909C;
            --text-dark: #37474F;
            --text-light: #90A4AE;
        }
        
        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 420px;
            position: relative;
            overflow: hidden;
        }
        
        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-logo {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 10px 20px rgba(126, 87, 194, 0.3);
        }
        
        .login-logo i {
            color: white;
            font-size: 2rem;
        }
        
        .login-title {
            color: var(--text-dark);
            font-weight: 800;
            margin-bottom: 0.5rem;
            font-size: 1.8rem;
        }
        
        .login-subtitle {
            color: var(--gray-color);
            font-size: 0.95rem;
            font-weight: 500;
        }
        
        .form-control {
            padding: 0.75rem 1rem;
            border: 2px solid var(--light-color);
            border-radius: 12px;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(126, 87, 194, 0.15);
        }
        
        .input-group-text {
            background: var(--light-color);
            border: 2px solid var(--light-color);
            border-right: none;
            color: var(--primary-color);
            font-size: 1rem;
        }
        
        .form-control.border-start-0 {
            border-left: none;
        }
        
        .btn-login {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            padding: 0.85rem;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(126, 87, 194, 0.3);
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(126, 87, 194, 0.4);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .login-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--light-color);
        }
        
        .alert {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #FFEBEE, #FFCDD2);
            color: var(--danger-color);
            border-left: 4px solid var(--danger-color);
        }
        
        .form-label {
            color: var(--text-dark);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-container {
            animation: fadeInUp 0.6s ease-out;
        }
        
        /* Responsive */
        @media (max-width: 480px) {
            .login-container {
                padding: 2rem 1.5rem;
            }
            
            .login-logo {
                width: 70px;
                height: 70px;
            }
            
            .login-logo i {
                font-size: 1.7rem;
            }
            
            .login-title {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="login-logo">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h2 class="login-title">Admin Girişi</h2>
            <p class="login-subtitle"><?php echo SITE_NAME; ?> Yönetim Paneli</p>
        </div>

        <?php if(isset($errors) && !empty($errors)): ?>
            <div class="alert alert-danger mb-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <div>
                        <?php foreach($errors as $error): ?>
                            <div class="fw-bold"><?php echo $error; ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-4">
                <label for="username" class="form-label">Kullanıcı Adı</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-user"></i>
                    </span>
                    <input type="text" class="form-control border-start-0" id="username" name="username" 
                           value="<?php echo isset($_POST['username']) ? $_POST['username'] : ''; ?>" 
                           placeholder="Kullanıcı adınız" required>
                </div>
            </div>
            
            <div class="mb-4">
                <label for="password" class="form-label">Şifre</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" class="form-control border-start-0" id="password" name="password" 
                           placeholder="Şifreniz" required>
                </div>
            </div>
            
            <button type="submit" class="btn btn-login w-100 mb-3">
                <i class="fas fa-sign-in-alt me-2"></i>Giriş Yap
            </button>
        </form>
        
        <div class="login-footer">
            <small class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                Sadece yetkili personel erişebilir
            </small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Input focus efekti
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.form-control');
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
</body>
</html>