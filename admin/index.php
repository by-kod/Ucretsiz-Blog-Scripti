<?php
require_once '../includes/config.php';

// Giriş kontrolü
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    redirect('login.php');
}

$pageTitle = "Dashboard - " . SITE_NAME;

// İstatistikleri getir - NULL değerleri 0 yap
$stats = $pdo->query("
    SELECT 
        (SELECT COUNT(*) FROM posts) as total_posts,
        (SELECT COUNT(*) FROM posts WHERE status = 'published') as published_posts,
        (SELECT COUNT(*) FROM posts WHERE status = 'draft') as draft_posts,
        (SELECT COUNT(*) FROM categories) as total_categories,
        (SELECT COUNT(*) FROM tags) as total_tags,
        (SELECT COUNT(*) FROM comments) as total_comments,
        (SELECT COUNT(*) FROM comments WHERE status = 'pending') as pending_comments,
        (SELECT COUNT(*) FROM users) as total_users,
        COALESCE((SELECT SUM(COALESCE(view_count, 0)) FROM posts), 0) as total_views
")->fetch(PDO::FETCH_ASSOC);

// İletişim mesajları istatistiği (tablo varsa)
try {
    $contact_stats = $pdo->query("
        SELECT 
            COUNT(*) as total_messages,
            SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) as new_messages
        FROM contact_messages
    ")->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $contact_stats = ['total_messages' => 0, 'new_messages' => 0];
}

// Son yazılar
$recent_posts = $pdo->query("
    SELECT p.*, u.display_name, c.name as category_name 
    FROM posts p 
    LEFT JOIN users u ON p.author_id = u.id 
    LEFT JOIN categories c ON p.category_id = c.id 
    ORDER BY p.created_at DESC 
    LIMIT 5
")->fetchAll();

// Son yorumlar
$recent_comments = $pdo->query("
    SELECT c.*, p.title as post_title 
    FROM comments c 
    LEFT JOIN posts p ON c.post_id = p.id 
    ORDER BY c.created_at DESC 
    LIMIT 5
")->fetchAll();

// Popüler yazılar
$popular_posts = $pdo->query("
    SELECT title, slug, COALESCE(view_count, 0) as view_count 
    FROM posts 
    WHERE status = 'published' 
    ORDER BY COALESCE(view_count, 0) DESC 
    LIMIT 5
")->fetchAll();
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
            --primary-blue: #2C5AA0;
            --secondary-blue: #4A7BC8;
            --accent-purple: #8B5FBF;
            --success-green: #27AE60;
            --warning-orange: #F39C12;
            --danger-red: #E74C3C;
            --info-cyan: #3498DB;
            --light-gray: #F8F9FA;
            --medium-gray: #6C757D;
            --dark-gray: #343A40;
            --sidebar-bg: #1E2A38;
            --sidebar-hover: #2C3E50;
        }
        
        body {
            background-color: var(--light-gray);
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar {
            background: linear-gradient(180deg, var(--sidebar-bg) 0%, #1A2432 100%);
            min-height: 100vh;
            box-shadow: 2px 0 15px rgba(0,0,0,0.1);
            position: fixed;
            width: 250px;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        
        .navbar {
            background: white;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        
        .sidebar-brand {
            padding: 1.5rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }
        
        .sidebar-brand h4 {
            color: white;
            margin: 0;
            font-weight: 700;
            font-size: 1.3rem;
        }
        
        .sidebar-brand small {
            color: rgba(255,255,255,0.7);
        }
        
        .sidebar-nav {
            padding: 1rem 0;
        }
        
        .nav-item {
            margin-bottom: 0.3rem;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.75rem 1.5rem;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .nav-link:hover, .nav-link.active {
            color: white;
            background: var(--sidebar-hover);
            border-left-color: var(--secondary-blue);
            transform: translateX(5px);
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 10px;
            color: var(--secondary-blue);
        }
        
        .nav-link.active i {
            color: white;
        }
        
        .badge {
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.03);
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-blue), var(--accent-purple));
        }
        
        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.12);
        }
        
        .stat-icon {
            width: 70px;
            height: 70px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            color: white;
            box-shadow: 0 5px 15px rgba(44, 90, 160, 0.3);
        }
        
        .stat-number {
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--dark-gray), var(--medium-gray));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .stat-label {
            color: var(--medium-gray);
            font-size: 0.95rem;
            font-weight: 600;
        }
        
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 1.5rem;
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--dark-gray);
        }
        
        .table th {
            border-top: none;
            font-weight: 700;
            color: var(--dark-gray);
            font-size: 0.9rem;
            background: rgba(44, 90, 160, 0.03);
        }
        
        .badge-published {
            background: linear-gradient(135deg, var(--success-green), #2ECC71);
        }
        
        .badge-draft {
            background: linear-gradient(135deg, var(--warning-orange), #F1C40F);
        }
        
        .badge-pending {
            background: linear-gradient(135deg, var(--danger-red), #E67E22);
        }
        
        .badge-new {
            background: linear-gradient(135deg, var(--danger-red), #C0392B);
            animation: pulse 2s infinite;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            border: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(44, 90, 160, 0.4);
        }
        
        .btn-sm {
            border-radius: 8px;
            font-weight: 600;
        }
        
        .navbar-text {
            color: var(--dark-gray);
            font-weight: 600;
        }
        
        .text-muted {
            color: var(--medium-gray) !important;
        }
        
        @keyframes pulse {
            0% { 
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(231, 76, 60, 0.7);
            }
            70% { 
                transform: scale(1.05);
                box-shadow: 0 0 0 10px rgba(231, 76, 60, 0);
            }
            100% { 
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(231, 76, 60, 0);
            }
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .stat-card {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <h4><i class="fas fa-cog me-2"></i>Admin Panel</h4>
            <small class="text-muted"><?php echo SITE_NAME; ?></small>
        </div>
        
        <div class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php">
                        <i class="fas fa-tachometer-alt"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="posts.php">
                        <i class="fas fa-newspaper"></i>Yazılar
                        <span class="badge bg-light text-dark float-end"><?php echo $stats['total_posts']; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="categories.php">
                        <i class="fas fa-folder"></i>Kategoriler
                        <span class="badge bg-light text-dark float-end"><?php echo $stats['total_categories']; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="tags.php">
                        <i class="fas fa-tags"></i>Etiketler
                        <span class="badge bg-light text-dark float-end"><?php echo $stats['total_tags']; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="comments.php">
                        <i class="fas fa-comments"></i>Yorumlar
                        <span class="badge bg-warning float-end"><?php echo $stats['pending_comments']; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contact_messages.php">
                        <i class="fas fa-envelope"></i>İletişim Mesajları
                        <?php if($contact_stats['new_messages'] > 0): ?>
                            <span class="badge badge-new float-end"><?php echo $contact_stats['new_messages']; ?></span>
                        <?php else: ?>
                            <span class="badge bg-light text-dark float-end"><?php echo $contact_stats['total_messages']; ?></span>
                        <?php endif; ?>
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
                <li class="nav-item mt-4">
                    <a class="nav-link" href="../index.php" target="_blank">
                        <i class="fas fa-external-link-alt"></i>Siteyi Görüntüle
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-danger" href="logout.php" style="color: #E74C3C !important;">
                        <i class="fas fa-sign-out-alt"></i>Çıkış Yap
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light mb-4">
            <div class="container-fluid">
                <div class="navbar-nav">
                    <span class="navbar-text">
                        Hoş geldiniz, <strong><?php echo $_SESSION['admin_username']; ?></strong>
                        <span class="badge bg-primary ms-2"><?php echo $_SESSION['admin_role']; ?></span>
                    </span>
                </div>
                <div class="navbar-nav ms-auto">
                    <span class="navbar-text">
                        <i class="far fa-clock me-1"></i>
                        <?php echo date('d M Y H:i'); ?>
                    </span>
                </div>
            </div>
        </nav>

        <!-- İstatistik Kartları -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <div class="stat-number"><?php echo $stats['total_posts']; ?></div>
                    <div class="stat-label">Toplam Yazı</div>
                    <small class="text-muted">
                        <?php echo $stats['published_posts']; ?> yayında, 
                        <?php echo $stats['draft_posts']; ?> taslak
                    </small>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="stat-number"><?php echo number_format($stats['total_views']); ?></div>
                    <div class="stat-label">Toplam Görüntülenme</div>
                    <small class="text-muted">Tüm zamanlar</small>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="stat-number"><?php echo $stats['total_comments']; ?></div>
                    <div class="stat-label">Toplam Yorum</div>
                    <small class="text-muted">
                        <?php echo $stats['pending_comments']; ?> onay bekliyor
                    </small>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="stat-number"><?php echo $contact_stats['total_messages']; ?></div>
                    <div class="stat-label">İletişim Mesajı</div>
                    <small class="text-muted">
                        <?php echo $contact_stats['new_messages']; ?> yeni mesaj
                    </small>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Son Yazılar -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Son Yazılar</span>
                        <a href="posts.php" class="btn btn-sm btn-primary">Tümünü Gör</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Başlık</th>
                                        <th>Durum</th>
                                        <th>Tarih</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($recent_posts as $post): ?>
                                        <tr>
                                            <td>
                                                <a href="../<?php echo $post['slug']; ?>.html" target="_blank" 
                                                   class="text-decoration-none" title="<?php echo sanitize($post['title']); ?>">
                                                    <?php echo strlen($post['title']) > 30 ? substr($post['title'], 0, 30) . '...' : $post['title']; ?>
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge <?php echo $post['status'] == 'published' ? 'badge-published' : 'badge-draft'; ?>">
                                                    <?php echo $post['status'] == 'published' ? 'Yayında' : 'Taslak'; ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d/m/Y', strtotime($post['created_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Son Yorumlar -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Son Yorumlar</span>
                        <a href="comments.php" class="btn btn-sm btn-primary">Tümünü Gör</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Yorum</th>
                                        <th>Durum</th>
                                        <th>Tarih</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($recent_comments as $comment): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <i class="fas fa-user-circle text-muted"></i>
                                                    </div>
                                                    <div class="flex-grow-1 ms-2">
                                                        <div class="fw-bold"><?php echo sanitize($comment['author_name']); ?></div>
                                                        <small class="text-muted" title="<?php echo sanitize($comment['content']); ?>">
                                                            <?php echo strlen($comment['content']) > 30 ? substr($comment['content'], 0, 30) . '...' : $comment['content']; ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge <?php echo $comment['status'] == 'approved' ? 'badge-published' : 'badge-pending'; ?>">
                                                    <?php echo $comment['status'] == 'approved' ? 'Onaylı' : 'Bekliyor'; ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d/m/Y', strtotime($comment['created_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Popüler Yazılar -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <span>En Popüler Yazılar</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Başlık</th>
                                        <th>Görüntülenme</th>
                                        <th>URL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($popular_posts as $post): ?>
                                        <tr>
                                            <td><?php echo sanitize($post['title']); ?></td>
                                            <td>
                                                <span class="badge bg-primary"><?php echo $post['view_count']; ?></span>
                                            </td>
                                            <td>
                                                <a href="../<?php echo $post['slug']; ?>.html" target="_blank" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Real-time saat güncelleme
        function updateClock() {
            const now = new Date();
            const options = { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric',
                hour: '2-digit', 
                minute: '2-digit',
                second: '2-digit'
            };
            document.querySelector('.navbar-text .fa-clock').parentNode.innerHTML = 
                '<i class="far fa-clock me-1"></i>' + now.toLocaleDateString('tr-TR', options);
        }
        
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>