<?php
require_once '../includes/config.php';

// Giriş kontrolü
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    redirect('login.php');
}

$pageTitle = "Yazı Yönetimi - " . SITE_NAME;

// Yazı silme
if(isset($_GET['delete'])) {
    $post_id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM posts WHERE id = ?")->execute([$post_id]);
    $_SESSION['success_message'] = "Yazı başarıyla silindi.";
    redirect('posts.php');
}

// Yazı durumu değiştirme
if(isset($_GET['toggle_status'])) {
    $post_id = (int)$_GET['toggle_status'];
    $stmt = $pdo->prepare("SELECT status FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();
    
    $new_status = $post['status'] == 'published' ? 'draft' : 'published';
    $pdo->prepare("UPDATE posts SET status = ? WHERE id = ?")->execute([$new_status, $post_id]);
    
    $_SESSION['success_message'] = "Yazı durumu değiştirildi.";
    redirect('posts.php');
}

// Yazıları getir
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : '';

$where_conditions = [];
$params = [];

if(!empty($search)) {
    $where_conditions[] = "(p.title LIKE ? OR p.content LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if(!empty($status_filter) && in_array($status_filter, ['published', 'draft'])) {
    $where_conditions[] = "p.status = ?";
    $params[] = $status_filter;
}

$where_sql = '';
if(!empty($where_conditions)) {
    $where_sql = "WHERE " . implode(" AND ", $where_conditions);
}

// Sayfalama
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Toplam yazı sayısı
$count_sql = "SELECT COUNT(*) as total FROM posts p $where_sql";
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total_posts = $count_stmt->fetch()['total'];
$total_pages = ceil($total_posts / $limit);

// Yazıları getir
$posts_sql = "SELECT p.*, u.display_name, c.name as category_name 
              FROM posts p 
              LEFT JOIN users u ON p.author_id = u.id 
              LEFT JOIN categories c ON p.category_id = c.id 
              $where_sql 
              ORDER BY p.created_at DESC 
              LIMIT $limit OFFSET $offset";

$stmt = $pdo->prepare($posts_sql);
$stmt->execute($params);
$posts = $stmt->fetchAll();
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
            --primary-color: #6366F1;
            --primary-hover: #4F46E5;
            --success-color: #10B981;
            --warning-color: #F59E0B;
            --danger-color: #EF4444;
            --info-color: #06B6D4;
            --light-color: #F8FAFC;
            --dark-color: #1E293B;
            --gray-color: #64748B;
            --border-color: #E2E8F0;
            --sidebar-width: 250px;
        }
        
        body {
            background-color: var(--light-color);
            font-family: 'Inter', sans-serif;
            color: var(--dark-color);
        }
        
        .sidebar {
            background: white;
            min-height: 100vh;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            position: fixed;
            width: var(--sidebar-width);
            z-index: 1000;
            border-right: 1px solid var(--border-color);
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            background-color: var(--light-color);
            min-height: 100vh;
        }
        
        .navbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-bottom: 1px solid var(--border-color);
        }
        
        .sidebar-brand {
            padding: 1.5rem 1rem;
            border-bottom: 1px solid var(--border-color);
            text-align: center;
            background: linear-gradient(135deg, var(--primary-color), var(--info-color));
        }
        
        .sidebar-brand h4 {
            color: white;
            margin: 0;
            font-weight: 700;
            font-size: 1.25rem;
        }
        
        .sidebar-brand small {
            color: rgba(255,255,255,0.8);
            font-size: 0.85rem;
        }
        
        .sidebar-nav {
            padding: 1rem 0;
        }
        
        .nav-item {
            margin-bottom: 0.25rem;
        }
        
        .nav-link {
            color: var(--gray-color);
            padding: 0.75rem 1.5rem;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
            text-decoration: none;
            display: block;
            font-weight: 500;
            border-radius: 0 8px 8px 0;
            margin: 0 0.5rem;
        }
        
        .nav-link:hover {
            color: var(--primary-color);
            background: rgba(99, 102, 241, 0.08);
            border-left-color: var(--primary-color);
        }
        
        .nav-link.active {
            color: var(--primary-color);
            background: rgba(99, 102, 241, 0.12);
            border-left-color: var(--primary-color);
            font-weight: 600;
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 10px;
            color: inherit;
        }
        
        .table-actions {
            white-space: nowrap;
        }
        
        .status-badge {
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .status-badge:hover {
            transform: scale(1.05);
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.08);
            border: 1px solid var(--border-color);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid var(--border-color);
            padding: 1.25rem;
            font-weight: 600;
            color: var(--dark-color);
            border-radius: 12px 12px 0 0 !important;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            border: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
            background: linear-gradient(135deg, var(--primary-hover), var(--primary-color));
        }
        
        .badge {
            font-weight: 500;
            padding: 0.5em 0.75em;
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
            color: var(--gray-color);
            font-size: 0.9rem;
            background-color: var(--light-color);
            border-bottom: 2px solid var(--border-color);
        }
        
        .table td {
            vertical-align: middle;
            border-color: var(--border-color);
        }
        
        .alert {
            border: none;
            border-radius: 8px;
            font-weight: 500;
        }
        
        .pagination .page-link {
            border: 1px solid var(--border-color);
            color: var(--gray-color);
            font-weight: 500;
        }
        
        .pagination .page-item.active .page-link {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .form-control, .form-select {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 0.75rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
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
            
            .sidebar-brand {
                padding: 1rem;
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
                    <a class="nav-link active" href="posts.php">
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

        <!-- Başlık ve Buton -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0" style="color: var(--dark-color); font-weight: 700;">
                <i class="fas fa-newspaper me-2" style="color: var(--primary-color);"></i>Yazı Yönetimi
            </h1>
            <a href="post-edit.php" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Yeni Yazı
            </a>
        </div>

        <!-- Filtreler -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Yazı ara..." 
                               value="<?php echo $search; ?>">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">Tüm Durumlar</option>
                            <option value="published" <?php echo $status_filter == 'published' ? 'selected' : ''; ?>>Yayında</option>
                            <option value="draft" <?php echo $status_filter == 'draft' ? 'selected' : ''; ?>>Taslak</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>Filtrele
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="posts.php" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-refresh me-2"></i>Sıfırla
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Mesajlar -->
        <?php if(isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Yazılar Tablosu -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Başlık</th>
                                <th>Kategori</th>
                                <th>Yazar</th>
                                <th>Durum</th>
                                <th>Görüntülenme</th>
                                <th>Tarih</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($posts)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x mb-3" style="color: var(--gray-color);"></i>
                                        <p class="text-muted mb-3">Henüz yazı bulunmuyor.</p>
                                        <a href="post-edit.php" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>İlk Yazınızı Ekleyin
                                        </a>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($posts as $post): ?>
                                    <tr>
                                        <td>
                                            <strong style="color: var(--dark-color);"><?php echo sanitize($post['title']); ?></strong>
                                            <?php if($post['featured_image']): ?>
                                                <br><small class="text-muted"><i class="fas fa-image me-1"></i> Resimli</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge" style="background: rgba(99, 102, 241, 0.1); color: var(--primary-color);">
                                                <?php echo sanitize($post['category_name'] ?: '-'); ?>
                                            </span>
                                        </td>
                                        <td><?php echo sanitize($post['display_name']); ?></td>
                                        <td>
                                            <span class="badge status-badge <?php echo $post['status'] == 'published' ? 'bg-success' : 'bg-warning'; ?>"
                                                  onclick="toggleStatus(<?php echo $post['id']; ?>)">
                                                <?php echo $post['status'] == 'published' ? 'Yayında' : 'Taslak'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge" style="background: rgba(6, 182, 212, 0.1); color: var(--info-color);">
                                                <i class="fas fa-eye me-1"></i><?php echo $post['view_count'] ?: 0; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted"><?php echo date('d/m/Y', strtotime($post['created_at'])); ?></small>
                                        </td>
                                        <td class="table-actions">
                                            <a href="../<?php echo $post['slug']; ?>.html" target="_blank" 
                                               class="btn btn-sm btn-outline-primary" title="Görüntüle">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="post-edit.php?id=<?php echo $post['id']; ?>" 
                                               class="btn btn-sm btn-outline-warning" title="Düzenle">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button onclick="confirmDelete(<?php echo $post['id']; ?>)" 
                                                    class="btn btn-sm btn-outline-danger" title="Sil">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Sayfalama -->
                <?php if($total_pages > 1): ?>
                    <nav aria-label="Sayfalama" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo $search; ?>&status=<?php echo $status_filter; ?>">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>&status=<?php echo $status_filter; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <?php if($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo $search; ?>&status=<?php echo $status_filter; ?>">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(postId) {
            if(confirm('Bu yazıyı silmek istediğinizden emin misiniz? Bu işlem geri alınamaz!')) {
                window.location.href = 'posts.php?delete=' + postId;
            }
        }

        function toggleStatus(postId) {
            window.location.href = 'posts.php?toggle_status=' + postId;
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>