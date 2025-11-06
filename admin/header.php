<?php
// admin/header.php
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Admin Panel'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #2c5aa0;
            --primary-purple: #8B5CF6;
            --primary-teal: #0D9488;
            --primary-orange: #EA580C;
            --primary-pink: #DB2777;
            --primary-cyan: #0891B2;
            --primary-emerald: #059669;
            --primary-amber: #D97706;
            --primary-rose: #E11D48;
            --primary-indigo: #4F46E5;
            --gray-light: #F8FAFC;
            --gray-medium: #64748B;
            --gray-dark: #334155;
            --border-light: #E2E8F0;
            --shadow-soft: 0 2px 15px rgba(0,0,0,0.08);
            --shadow-hover: 0 8px 30px rgba(0,0,0,0.12);
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }
        
        .admin-container {
            background: white;
            min-height: 100vh;
            box-shadow: 0 0 50px rgba(0,0,0,0.1);
        }
        
        .sidebar {
            background: linear-gradient(180deg, var(--primary-blue) 0%, var(--primary-indigo) 100%);
            min-height: 100vh;
            box-shadow: var(--shadow-soft);
            position: fixed;
            width: 280px;
            z-index: 1000;
        }
        
        .main-content {
            margin-left: 280px;
            padding: 0;
            background: var(--gray-light);
        }
        
        .navbar {
            background: white;
            box-shadow: var(--shadow-soft);
            padding: 1rem 2rem;
            border-bottom: 1px solid var(--border-light);
        }
        
        .sidebar-brand {
            padding: 2rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
            background: rgba(0,0,0,0.1);
        }
        
        .sidebar-brand h4 {
            color: white;
            margin: 0;
            font-weight: 700;
            font-size: 1.4rem;
        }
        
        .sidebar-brand small {
            color: rgba(255,255,255,0.7);
            font-size: 0.85rem;
        }
        
        .sidebar-nav {
            padding: 1.5rem 0;
        }
        
        .nav-item {
            margin-bottom: 0.25rem;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.9rem 1.5rem;
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }
        
        .nav-link::before {
            content: '';
            position: absolute;
            left: -100%;
            top: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transition: left 0.5s ease;
        }
        
        .nav-link:hover::before {
            left: 100%;
        }
        
        .nav-link:hover, .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
            border-left-color: white;
            transform: translateX(5px);
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 12px;
            font-size: 1.1rem;
            transition: transform 0.3s ease;
        }
        
        .nav-link:hover i, .nav-link.active i {
            transform: scale(1.1);
            color: var(--primary-amber);
        }
        
        .nav-link .badge {
            margin-left: auto;
            font-size: 0.7rem;
            padding: 0.3rem 0.6rem;
        }
        
        .nav-divider {
            height: 1px;
            background: rgba(255,255,255,0.1);
            margin: 1rem 1.5rem;
        }
        
        .content-wrapper {
            padding: 2rem;
            min-height: calc(100vh - 80px);
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-purple), var(--primary-pink));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .real-time-clock {
            background: linear-gradient(135deg, var(--primary-teal), var(--primary-cyan));
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        @media (max-width: 1024px) {
            .sidebar {
                width: 250px;
            }
            
            .main-content {
                margin-left: 250px;
            }
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
                min-height: auto;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .sidebar-nav {
                max-height: 400px;
                overflow-y: auto;
            }
            
            .content-wrapper {
                padding: 1.5rem;
            }
        }
        
        /* Scrollbar stilleri */
        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }
        
        .sidebar-nav::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
        }
        
        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 2px;
        }
        
        .sidebar-nav::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.5);
        }
        
        /* Animasyonlar */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .nav-item {
            animation: fadeIn 0.5s ease forwards;
        }
        
        .nav-item:nth-child(1) { animation-delay: 0.1s; }
        .nav-item:nth-child(2) { animation-delay: 0.2s; }
        .nav-item:nth-child(3) { animation-delay: 0.3s; }
        .nav-item:nth-child(4) { animation-delay: 0.4s; }
        .nav-item:nth-child(5) { animation-delay: 0.5s; }
        .nav-item:nth-child(6) { animation-delay: 0.6s; }
        .nav-item:nth-child(7) { animation-delay: 0.7s; }
        .nav-item:nth-child(8) { animation-delay: 0.8s; }
        .nav-item:nth-child(9) { animation-delay: 0.9s; }
        .nav-item:nth-child(10) { animation-delay: 1.0s; }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-brand">
                <h4><i class="fas fa-shield-alt me-2"></i>Admin Panel</h4>
                <small><?php echo getSiteSetting('name'); ?></small>
            </div>
            
            <div class="sidebar-nav">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-tachometer-alt"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="posts.php">
                            <i class="fas fa-newspaper"></i>Yazılar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="categories.php">
                            <i class="fas fa-folder"></i>Kategoriler
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tags.php">
                            <i class="fas fa-tags"></i>Etiketler
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="comments.php">
                            <i class="fas fa-comments"></i>Yorumlar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="media.php">
                            <i class="fas fa-images"></i>Medya
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="analytics.php">
                            <i class="fas fa-chart-bar"></i>Analitik
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="seo.php">
                            <i class="fas fa-search"></i>SEO
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="settings.php">
                            <i class="fas fa-cogs"></i>Ayarlar
                        </a>
                    </li>
                    
                    <div class="nav-divider"></div>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php" target="_blank">
                            <i class="fas fa-external-link-alt"></i>Siteyi Görüntüle
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-warning" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i>Çıkış Yap
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light">
                <div class="container-fluid">
                    <div class="navbar-nav">
                        <div class="user-info">
                            <div class="user-avatar">
                                <?php 
                                $username = $_SESSION['admin_username'] ?? 'Admin';
                                echo strtoupper(substr($username, 0, 1)); 
                                ?>
                            </div>
                            <div>
                                <div class="fw-bold"><?php echo $username; ?></div>
                                <small class="text-muted"><?php echo $_SESSION['admin_role'] ?? 'Administrator'; ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="navbar-nav ms-auto">
                        <div class="real-time-clock">
                            <i class="far fa-clock me-2"></i>
                            <span id="liveClock"><?php echo date('d M Y H:i:s'); ?></span>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Content Wrapper -->
            <div class="content-wrapper">
                <!-- Page content will be included here -->