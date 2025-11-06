<?php
require_once '../includes/config.php';

// Giriş kontrolü
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    redirect('login.php');
}

$pageTitle = "Analitik ve İstatistikler - " . SITE_NAME;

// Tarih aralığı filtresi
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Genel İstatistikler
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
        COALESCE((SELECT SUM(COALESCE(view_count, 0)) FROM posts), 0) as total_views,
        COALESCE((SELECT SUM(COALESCE(comment_count, 0)) FROM posts), 0) as total_post_comments,
        (SELECT COUNT(*) FROM posts WHERE featured_image IS NOT NULL AND featured_image != '') as posts_with_images
")->fetch(PDO::FETCH_ASSOC);

// Popüler Yazılar
$popular_posts = $pdo->query("
    SELECT title, slug, view_count, comment_count, published_at 
    FROM posts 
    WHERE status = 'published' 
    ORDER BY view_count DESC 
    LIMIT 10
")->fetchAll();

// Son 30 günlük görüntülenme istatistikleri
$views_data = $pdo->query("
    SELECT 
        DATE(published_at) as date,
        COUNT(*) as post_count,
        SUM(view_count) as daily_views
    FROM posts 
    WHERE status = 'published' 
    AND published_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY DATE(published_at)
    ORDER BY date DESC
")->fetchAll();

// Kategori bazlı istatistikler
$category_stats = $pdo->query("
    SELECT 
        c.name as category_name,
        c.color,
        COUNT(p.id) as post_count,
        COALESCE(SUM(p.view_count), 0) as total_views,
        COALESCE(SUM(p.comment_count), 0) as total_comments
    FROM categories c
    LEFT JOIN posts p ON c.id = p.category_id AND p.status = 'published'
    GROUP BY c.id, c.name, c.color
    ORDER BY post_count DESC
")->fetchAll();

// Yazar bazlı istatistikler
$author_stats = $pdo->query("
    SELECT 
        u.display_name,
        COUNT(p.id) as post_count,
        COALESCE(SUM(p.view_count), 0) as total_views,
        COALESCE(SUM(p.comment_count), 0) as total_comments,
        AVG(p.view_count) as avg_views
    FROM users u
    LEFT JOIN posts p ON u.id = p.author_id AND p.status = 'published'
    GROUP BY u.id, u.display_name
    HAVING post_count > 0
    ORDER BY total_views DESC
")->fetchAll();

// Aylık istatistikler
$monthly_stats = $pdo->query("
    SELECT 
        DATE_FORMAT(published_at, '%Y-%m') as month,
        COUNT(*) as post_count,
        SUM(view_count) as monthly_views,
        SUM(comment_count) as monthly_comments
    FROM posts 
    WHERE status = 'published'
    GROUP BY DATE_FORMAT(published_at, '%Y-%m')
    ORDER BY month DESC
    LIMIT 12
")->fetchAll();

// Etiket kullanım istatistikleri
$tag_stats = $pdo->query("
    SELECT 
        t.name,
        COUNT(pt.post_id) as usage_count
    FROM tags t
    LEFT JOIN post_tags pt ON t.id = pt.tag_id
    LEFT JOIN posts p ON pt.post_id = p.id AND p.status = 'published'
    GROUP BY t.id, t.name
    HAVING usage_count > 0
    ORDER BY usage_count DESC
    LIMIT 15
")->fetchAll();

// Ortalama okuma süresi
$reading_stats = $pdo->query("
    SELECT 
        AVG(reading_time) as avg_reading_time,
        MIN(reading_time) as min_reading_time,
        MAX(reading_time) as max_reading_time
    FROM posts 
    WHERE status = 'published' AND reading_time > 0
")->fetch(PDO::FETCH_ASSOC);

// En çok yorum alan yazılar
$most_commented = $pdo->query("
    SELECT title, slug, comment_count, view_count
    FROM posts 
    WHERE status = 'published' AND comment_count > 0
    ORDER BY comment_count DESC 
    LIMIT 10
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
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #3b82f6;
            --purple-color: #8b5cf6;
            --pink-color: #ec4899;
            --indigo-color: #6366f1;
            --teal-color: #14b8a6;
            --orange-color: #f97316;
            --gray-light: #f8fafc;
            --gray-medium: #64748b;
            --gray-dark: #334155;
        }
        
        body {
            background-color: var(--gray-light);
            font-family: 'Inter', sans-serif;
        }
        
        .sidebar {
            background: white;
            min-height: 100vh;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            position: fixed;
            width: 250px;
            z-index: 1000;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        
        .navbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .sidebar-brand {
            padding: 1.5rem 1rem;
            border-bottom: 1px solid #eee;
            text-align: center;
        }
        
        .sidebar-nav {
            padding: 1rem 0;
        }
        
        .nav-item {
            margin-bottom: 0.5rem;
        }
        
        .nav-link {
            color: var(--gray-medium);
            padding: 0.75rem 1.5rem;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
            text-decoration: none;
            display: block;
        }
        
        .nav-link:hover, .nav-link.active {
            color: var(--primary-color);
            background: rgba(102, 126, 234, 0.1);
            border-left-color: var(--primary-color);
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid #eee;
            padding: 1.25rem;
            font-weight: 600;
        }
        
        .stat-card {
            text-align: center;
            padding: 1.5rem;
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin: 0 auto 1rem;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: var(--gray-medium);
            font-size: 0.9rem;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        
        .progress-bar {
            height: 8px;
            border-radius: 4px;
        }
        
        .analytics-table th {
            border-top: none;
            font-weight: 600;
            color: var(--gray-medium);
            font-size: 0.9rem;
        }
        
        .badge-view {
            background: var(--info-color);
        }
        
        .badge-comment {
            background: var(--success-color);
        }
        
        .badge-post {
            background: var(--warning-color);
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
            transform: translateY(-1px);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .chart-container {
                height: 250px;
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
                    <a class="nav-link active" href="analytics.php">
                        <i class="fas fa-chart-bar"></i>Analitik
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <a class="nav-link" href="../index.php" target="_blank">
                        <i class="fas fa-external-link-alt"></i>Siteyi Görüntüle
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-danger" href="logout.php">
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
                        <span class="badge gradient-bg ms-2"><?php echo $_SESSION['admin_role']; ?></span>
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

        <!-- Başlık ve Filtreler -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Analitik ve İstatistikler</h1>
            <div class="btn-group">
                <button type="button" class="btn btn-outline-primary active" data-range="30">30 Gün</button>
                <button type="button" class="btn btn-outline-primary" data-range="90">90 Gün</button>
                <button type="button" class="btn btn-outline-primary" data-range="365">1 Yıl</button>
            </div>
        </div>

        <!-- Genel İstatistikler -->
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card">
                    <div class="stat-icon" style="background: rgba(59, 130, 246, 0.1); color: var(--info-color);">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="stat-number"><?php echo number_format($stats['total_views']); ?></div>
                    <div class="stat-label">Toplam Görüntülenme</div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card">
                    <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--success-color);">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="stat-number"><?php echo number_format($stats['total_comments']); ?></div>
                    <div class="stat-label">Toplam Yorum</div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card">
                    <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: var(--warning-color);">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <div class="stat-number"><?php echo $stats['published_posts']; ?></div>
                    <div class="stat-label">Yayınlanan Yazı</div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card">
                    <div class="stat-icon" style="background: rgba(139, 92, 246, 0.1); color: var(--purple-color);">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-number"><?php echo round($reading_stats['avg_reading_time'], 1); ?> dk</div>
                    <div class="stat-label">Ort. Okuma Süresi</div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Görüntülenme Grafiği -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Görüntülenme İstatistikleri</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="viewsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kategori Dağılımı -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Kategori Dağılımı</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Popüler Yazılar -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">En Popüler Yazılar</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table analytics-table">
                                <thead>
                                    <tr>
                                        <th>Yazı</th>
                                        <th>Görüntülenme</th>
                                        <th>Yorum</th>
                                        <th>Oran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($popular_posts as $post): 
                                        $max_views = $popular_posts[0]['view_count'];
                                        $percentage = $max_views > 0 ? ($post['view_count'] / $max_views) * 100 : 0;
                                    ?>
                                        <tr>
                                            <td>
                                                <a href="../<?php echo $post['slug']; ?>.html" target="_blank" 
                                                   class="text-decoration-none" title="<?php echo sanitize($post['title']); ?>">
                                                    <?php echo strlen($post['title']) > 40 ? substr($post['title'], 0, 40) . '...' : $post['title']; ?>
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge badge-view"><?php echo number_format($post['view_count']); ?></span>
                                            </td>
                                            <td>
                                                <span class="badge badge-comment"><?php echo $post['comment_count']; ?></span>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 8px;">
                                                    <div class="progress-bar" style="width: <?php echo $percentage; ?>%; background-color: var(--info-color);"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Yazar İstatistikleri -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Yazar Performansı</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table analytics-table">
                                <thead>
                                    <tr>
                                        <th>Yazar</th>
                                        <th>Yazı</th>
                                        <th>Görüntülenme</th>
                                        <th>Ort. Görüntülenme</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($author_stats as $author): ?>
                                        <tr>
                                            <td><?php echo sanitize($author['display_name']); ?></td>
                                            <td>
                                                <span class="badge badge-post"><?php echo $author['post_count']; ?></span>
                                            </td>
                                            <td><?php echo number_format($author['total_views']); ?></td>
                                            <td><?php echo number_format(round($author['avg_views'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Aylık İstatistikler -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Aylık İstatistikler</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table analytics-table">
                                <thead>
                                    <tr>
                                        <th>Ay</th>
                                        <th>Yazı</th>
                                        <th>Görüntülenme</th>
                                        <th>Yorum</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($monthly_stats as $month): ?>
                                        <tr>
                                            <td><?php echo date('M Y', strtotime($month['month'] . '-01')); ?></td>
                                            <td><?php echo $month['post_count']; ?></td>
                                            <td><?php echo number_format($month['monthly_views']); ?></td>
                                            <td><?php echo $month['monthly_comments']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Etiket İstatistikleri -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Popüler Etiketler</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach($tag_stats as $tag): 
                                $max_usage = $tag_stats[0]['usage_count'];
                                $percentage = $max_usage > 0 ? ($tag['usage_count'] / $max_usage) * 100 : 0;
                                $opacity = max(0.3, $percentage / 100);
                            ?>
                                <span class="badge" style="background: rgba(102, 126, 234, <?php echo $opacity; ?>); font-size: 0.9rem; padding: 0.5rem 0.8rem;">
                                    <?php echo sanitize($tag['name']); ?>
                                    <span class="badge bg-light text-dark ms-1"><?php echo $tag['usage_count']; ?></span>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detaylı İstatistikler -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Detaylı İstatistikler</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3 mb-3">
                                <div class="border rounded p-3">
                                    <div class="h4" style="color: var(--primary-color);"><?php echo $stats['total_posts']; ?></div>
                                    <div class="text-muted">Toplam Yazı</div>
                                    <small class="text-muted"><?php echo $stats['published_posts']; ?> yayında, <?php echo $stats['draft_posts']; ?> taslak</small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="border rounded p-3">
                                    <div class="h4" style="color: var(--success-color);"><?php echo $stats['total_categories']; ?></div>
                                    <div class="text-muted">Kategori</div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="border rounded p-3">
                                    <div class="h4" style="color: var(--warning-color);"><?php echo $stats['total_tags']; ?></div>
                                    <div class="text-muted">Etiket</div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="border rounded p-3">
                                    <div class="h4" style="color: var(--info-color);"><?php echo $stats['posts_with_images']; ?></div>
                                    <div class="text-muted">Resimli Yazı</div>
                                    <small class="text-muted"><?php echo round(($stats['posts_with_images'] / $stats['total_posts']) * 100, 1); ?>%</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Görüntülenme Grafiği
        const viewsCtx = document.getElementById('viewsChart').getContext('2d');
        const viewsChart = new Chart(viewsCtx, {
            type: 'line',
            data: {
                labels: [<?php 
                    $dates = array_reverse(array_column($views_data, 'date'));
                    foreach($dates as $date) {
                        echo "'" . date('d M', strtotime($date)) . "',";
                    }
                ?>],
                datasets: [{
                    label: 'Günlük Görüntülenme',
                    data: [<?php 
                        $views = array_reverse(array_column($views_data, 'daily_views'));
                        foreach($views as $view) {
                            echo $view . ',';
                        }
                    ?>],
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Kategori Dağılımı Grafiği
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        const categoryChart = new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: [<?php 
                    foreach($category_stats as $cat) {
                        echo "'" . addslashes($cat['category_name']) . "',";
                    }
                ?>],
                datasets: [{
                    data: [<?php 
                        foreach($category_stats as $cat) {
                            echo $cat['post_count'] . ',';
                        }
                    ?>],
                    backgroundColor: [
                        '#667eea', '#764ba2', '#f59e0b', '#10b981',
                        '#8b5cf6', '#ec4899', '#14b8a6', '#f97316',
                        '#6366f1', '#ef4444', '#3b82f6', '#84cc16'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 15
                        }
                    }
                },
                cutout: '60%'
            }
        });

        // Tarih aralığı değiştirme
        document.querySelectorAll('[data-range]').forEach(btn => {
            btn.addEventListener('click', function() {
                const range = parseInt(this.getAttribute('data-range'));
                
                // Butonları güncelle
                document.querySelectorAll('[data-range]').forEach(b => {
                    b.classList.remove('active');
                });
                this.classList.add('active');
                
                // Burada AJAX ile verileri yeniden yükleyebilirsiniz
                // Şu anlık sayfayı yeniden yönlendiriyoruz
                const startDate = new Date();
                startDate.setDate(startDate.getDate() - range);
                
                const startStr = startDate.toISOString().split('T')[0];
                const endStr = new Date().toISOString().split('T')[0];
                
                window.location.href = `analytics.php?start_date=${startStr}&end_date=${endStr}`;
            });
        });

        // Sayfa yüklendiğinde otomatik refresh (opsiyonel)
        setTimeout(() => {
            // 5 dakikada bir sayfayı yenile
            // window.location.reload();
        }, 300000);
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>