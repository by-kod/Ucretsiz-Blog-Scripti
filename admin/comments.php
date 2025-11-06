<?php
require_once '../includes/config.php';

// Giriş kontrolü
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    redirect('login.php');
    exit;
}

$pageTitle = "Yorum Yönetimi - " . SITE_NAME;

// CSRF token kontrolü için fonksiyon
function verifyCsrfToken() {
    if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = "Güvenlik hatası: Geçersiz token!";
        redirect('comments.php');
        exit;
    }
}

// Yorum durumu değiştirme işlemleri
if(isset($_GET['action']) && isset($_GET['id'])) {
    $comment_id = (int)$_GET['id'];
    $action = sanitize($_GET['action']);
    
    // CSRF token kontrolü
    verifyCsrfToken();
    
    // Yorumun varlığını kontrol et
    $checkComment = $pdo->prepare("SELECT id FROM comments WHERE id = ?");
    $checkComment->execute([$comment_id]);
    
    if($checkComment->rowCount() === 0) {
        $_SESSION['error_message'] = "Yorum bulunamadı.";
        redirect('comments.php');
        exit;
    }
    
    switch($action) {
        case 'approve':
            $pdo->prepare("UPDATE comments SET status = 'approved' WHERE id = ?")->execute([$comment_id]);
            $_SESSION['success_message'] = "Yorum onaylandı.";
            break;
            
        case 'spam':
            $pdo->prepare("UPDATE comments SET status = 'spam' WHERE id = ?")->execute([$comment_id]);
            $_SESSION['success_message'] = "Yorum spam olarak işaretlendi.";
            break;
            
        case 'delete':
            $pdo->prepare("DELETE FROM comments WHERE id = ?")->execute([$comment_id]);
            $_SESSION['success_message'] = "Yorum silindi.";
            break;
            
        default:
            $_SESSION['error_message'] = "Geçersiz işlem!";
            break;
    }
    
    redirect('comments.php');
    exit;
}

// Toplu işlemler
if(isset($_POST['bulk_action']) && isset($_POST['selected_comments'])) {
    // CSRF token kontrolü
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = "Güvenlik hatası: Geçersiz token!";
        redirect('comments.php');
        exit;
    }
    
    $bulk_action = sanitize($_POST['bulk_action']);
    $selected_comments = array_map('intval', $_POST['selected_comments']);
    $placeholders = str_repeat('?,', count($selected_comments) - 1) . '?';
    
    switch($bulk_action) {
        case 'approve':
            $stmt = $pdo->prepare("UPDATE comments SET status = 'approved' WHERE id IN ($placeholders)");
            $stmt->execute($selected_comments);
            $_SESSION['success_message'] = count($selected_comments) . " yorum onaylandı.";
            break;
            
        case 'spam':
            $stmt = $pdo->prepare("UPDATE comments SET status = 'spam' WHERE id IN ($placeholders)");
            $stmt->execute($selected_comments);
            $_SESSION['success_message'] = count($selected_comments) . " yorum spam olarak işaretlendi.";
            break;
            
        case 'delete':
            $stmt = $pdo->prepare("DELETE FROM comments WHERE id IN ($placeholders)");
            $stmt->execute($selected_comments);
            $_SESSION['success_message'] = count($selected_comments) . " yorum silindi.";
            break;
            
        default:
            $_SESSION['error_message'] = "Geçersiz toplu işlem!";
            break;
    }
    
    redirect('comments.php');
    exit;
}

// CSRF token oluştur
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Filtreleme ve arama
$status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$search_query = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// SQL sorgusu oluşturma
$where_conditions = [];
$params = [];

if ($status_filter && in_array($status_filter, ['pending', 'approved', 'spam'])) {
    $where_conditions[] = "c.status = ?";
    $params[] = $status_filter;
}

if ($search_query) {
    $where_conditions[] = "(c.author_name LIKE ? OR c.content LIKE ? OR c.author_email LIKE ? OR p.title LIKE ?)";
    $search_param = "%$search_query%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

$where_sql = $where_conditions ? "WHERE " . implode(" AND ", $where_conditions) : '';

// Sayfalama
$comments_per_page = 10;
$current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($current_page - 1) * $comments_per_page;

// Toplam yorum sayısı
$count_sql = "SELECT COUNT(*) as total FROM comments c LEFT JOIN posts p ON c.post_id = p.id $where_sql";
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total_comments = $count_stmt->fetch()['total'];
$total_pages = ceil($total_comments / $comments_per_page);

// Yorumları getir
$comments_sql = "
    SELECT c.*, p.title as post_title, p.slug as post_slug 
    FROM comments c 
    LEFT JOIN posts p ON c.post_id = p.id 
    $where_sql 
    ORDER BY c.created_at DESC
    LIMIT $offset, $comments_per_page
";

$comments_stmt = $pdo->prepare($comments_sql);
$comments_stmt->execute($params);
$comments = $comments_stmt->fetchAll();

// İstatistikler
$stats = $pdo->query("
    SELECT 
        (SELECT COUNT(*) FROM comments WHERE status = 'pending') as pending,
        (SELECT COUNT(*) FROM comments WHERE status = 'approved') as approved,
        (SELECT COUNT(*) FROM comments WHERE status = 'spam') as spam,
        (SELECT COUNT(*) FROM comments) as total
")->fetch();
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
            --primary-color: #6f42c1;
            --secondary-color: #e83e8c;
            --success-color: #198754;
            --warning-color: #fd7e14;
            --danger-color: #dc3545;
            --info-color: #0dcaf0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --gray-color: #6c757d;
            --gray-light: #e9ecef;
            --gray-dark: #495057;
        }
        
        body {
            background-color: var(--light-color);
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
        
        .sidebar-brand h4 {
            color: var(--primary-color);
            margin: 0;
            font-weight: 700;
        }
        
        .sidebar-nav {
            padding: 1rem 0;
        }
        
        .nav-item {
            margin-bottom: 0.5rem;
        }
        
        .nav-link {
            color: var(--gray-color);
            padding: 0.75rem 1.5rem;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
            text-decoration: none;
            display: block;
        }
        
        .nav-link:hover, .nav-link.active {
            color: var(--primary-color);
            background: rgba(111, 66, 193, 0.1);
            border-left-color: var(--primary-color);
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
            border: none;
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
            margin-bottom: 1rem;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: var(--gray-color);
            font-size: 0.9rem;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid #eee;
            padding: 1.25rem;
            font-weight: 600;
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
            color: var(--gray-color);
            font-size: 0.9rem;
        }
        
        .comment-content {
            max-height: 100px;
            overflow: hidden;
            position: relative;
            line-height: 1.5;
        }
        
        .comment-content.expanded {
            max-height: none;
        }
        
        .read-more {
            position: absolute;
            bottom: 0;
            right: 0;
            background: white;
            padding-left: 5px;
            cursor: pointer;
            color: var(--primary-color);
            font-size: 0.85rem;
        }
        
        .table-checkbox {
            width: 40px;
        }
        
        .btn-group-sm .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #5a32a3;
            border-color: #5a32a3;
        }
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-outline-primary.active {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .badge.bg-primary {
            background-color: var(--primary-color) !important;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .btn-group {
                flex-wrap: wrap;
            }
            
            .btn-group .btn {
                margin-bottom: 0.5rem;
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
                    <a class="nav-link active" href="comments.php">
                        <i class="fas fa-comments"></i>Yorumlar
                        <?php if($stats['pending'] > 0): ?>
                            <span class="badge bg-warning float-end"><?php echo $stats['pending']; ?></span>
                        <?php endif; ?>
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

        <!-- Başlık -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Yorum Yönetimi</h1>
        </div>

        <!-- İstatistikler -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(253, 126, 20, 0.1); color: var(--warning-color);">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-number"><?php echo $stats['pending']; ?></div>
                    <div class="stat-label">Onay Bekleyen</div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(25, 135, 84, 0.1); color: var(--success-color);">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="stat-number"><?php echo $stats['approved']; ?></div>
                    <div class="stat-label">Onaylı</div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(220, 53, 69, 0.1); color: var(--danger-color);">
                        <i class="fas fa-ban"></i>
                    </div>
                    <div class="stat-number"><?php echo $stats['spam']; ?></div>
                    <div class="stat-label">Spam</div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(111, 66, 193, 0.1); color: var(--primary-color);">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="stat-number"><?php echo $stats['total']; ?></div>
                    <div class="stat-label">Toplam</div>
                </div>
            </div>
        </div>

        <!-- Filtreler ve Arama -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="btn-group" role="group">
                            <a href="comments.php" class="btn btn-outline-primary <?php echo !$status_filter && !$search_query ? 'active' : ''; ?>">
                                Tümü
                            </a>
                            <a href="comments.php?status=pending" class="btn btn-outline-warning <?php echo $status_filter == 'pending' ? 'active' : ''; ?>">
                                Onay Bekleyen
                            </a>
                            <a href="comments.php?status=approved" class="btn btn-outline-success <?php echo $status_filter == 'approved' ? 'active' : ''; ?>">
                                Onaylı
                            </a>
                            <a href="comments.php?status=spam" class="btn btn-outline-danger <?php echo $status_filter == 'spam' ? 'active' : ''; ?>">
                                Spam
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <form method="GET" class="d-flex">
                            <input type="hidden" name="status" value="<?php echo $status_filter; ?>">
                            <input type="text" name="search" class="form-control me-2" placeholder="Yorum, yazar veya email ara..." value="<?php echo $search_query; ?>">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                            <?php if($search_query): ?>
                                <a href="comments.php<?php echo $status_filter ? "?status=$status_filter" : ''; ?>" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-times"></i>
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
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

        <?php if(isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Toplu İşlemler -->
        <?php if(!empty($comments)): ?>
        <div class="card mb-3">
            <div class="card-body">
                <form method="POST" id="bulkForm">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <select name="bulk_action" class="form-select" id="bulkAction">
                                <option value="">Toplu İşlem Seçin</option>
                                <option value="approve">Onayla</option>
                                <option value="spam">Spam Olarak İşaretle</option>
                                <option value="delete">Sil</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-outline-primary" onclick="selectAllComments()">
                                <i class="fas fa-check-square"></i> Tümünü Seç
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="deselectAllComments()">
                                <i class="far fa-square"></i> Seçimi Temizle
                            </button>
                        </div>
                        <div class="col-md-4 text-end">
                            <button type="submit" class="btn btn-primary" id="bulkSubmit" disabled>
                                <i class="fas fa-play"></i> Uygula
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Yorum Listesi -->
        <div class="card">
            <div class="card-body">
                <?php if(empty($comments)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Henüz yorum bulunmuyor.</p>
                        <?php if($search_query): ?>
                            <a href="comments.php" class="btn btn-primary">Tüm Yorumları Görüntüle</a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="table-checkbox">
                                        <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)">
                                    </th>
                                    <th>Yorum</th>
                                    <th>Yazı</th>
                                    <th>Yazar</th>
                                    <th>Durum</th>
                                    <th>Tarih</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($comments as $comment): ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="selected_comments[]" value="<?php echo $comment['id']; ?>" class="comment-checkbox" onchange="updateBulkSubmit()">
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-start">
                                                <div class="flex-grow-1">
                                                    <div class="fw-bold"><?php echo sanitize($comment['author_name']); ?></div>
                                                    <div class="comment-content text-muted" id="comment-<?php echo $comment['id']; ?>">
                                                        <?php echo nl2br(sanitize($comment['content'])); ?>
                                                    </div>
                                                    <?php if($comment['author_website']): ?>
                                                        <div class="small mt-1">
                                                            <a href="<?php echo sanitize($comment['author_website']); ?>" target="_blank" rel="noopener noreferrer">
                                                                <i class="fas fa-globe me-1"></i>Website
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if($comment['post_title']): ?>
                                                <a href="../<?php echo $comment['post_slug']; ?>.html" target="_blank" class="text-decoration-none" rel="noopener noreferrer">
                                                    <?php echo sanitize($comment['post_title']); ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">Yazı silinmiş</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="small">
                                                <div><?php echo sanitize($comment['author_email']); ?></div>
                                                <div class="text-muted"><?php echo $comment['author_ip']; ?></div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge 
                                                <?php echo $comment['status'] == 'approved' ? 'bg-success' : ''; ?>
                                                <?php echo $comment['status'] == 'pending' ? 'bg-warning' : ''; ?>
                                                <?php echo $comment['status'] == 'spam' ? 'bg-danger' : ''; ?>">
                                                <?php echo $comment['status'] == 'approved' ? 'Onaylı' : ''; ?>
                                                <?php echo $comment['status'] == 'pending' ? 'Bekliyor' : ''; ?>
                                                <?php echo $comment['status'] == 'spam' ? 'Spam' : ''; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small><?php echo date('d/m/Y H:i', strtotime($comment['created_at'])); ?></small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <?php if($comment['status'] != 'approved'): ?>
                                                    <a href="comments.php?action=approve&id=<?php echo $comment['id']; ?>&csrf_token=<?php echo $_SESSION['csrf_token']; ?>" 
                                                       class="btn btn-outline-success" title="Onayla">
                                                        <i class="fas fa-check"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if($comment['status'] != 'spam'): ?>
                                                    <a href="comments.php?action=spam&id=<?php echo $comment['id']; ?>&csrf_token=<?php echo $_SESSION['csrf_token']; ?>" 
                                                       class="btn btn-outline-warning" title="Spam">
                                                        <i class="fas fa-ban"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <button onclick="confirmDelete(<?php echo $comment['id']; ?>)" 
                                                        class="btn btn-outline-danger" title="Sil">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Sayfalama -->
                    <?php if($total_pages > 1): ?>
                    <nav aria-label="Sayfalama">
                        <ul class="pagination justify-content-center">
                            <?php if($current_page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page - 1])); ?>">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if($current_page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page + 1])); ?>">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(commentId) {
            if(confirm('Bu yorumu silmek istediğinizden emin misiniz?')) {
                window.location.href = 'comments.php?action=delete&id=' + commentId + '&csrf_token=<?php echo $_SESSION['csrf_token']; ?>';
            }
        }

        function toggleSelectAll(checkbox) {
            const checkboxes = document.querySelectorAll('.comment-checkbox');
            checkboxes.forEach(cb => cb.checked = checkbox.checked);
            updateBulkSubmit();
        }

        function selectAllComments() {
            const checkboxes = document.querySelectorAll('.comment-checkbox');
            checkboxes.forEach(cb => cb.checked = true);
            document.getElementById('selectAll').checked = true;
            updateBulkSubmit();
        }

        function deselectAllComments() {
            const checkboxes = document.querySelectorAll('.comment-checkbox');
            checkboxes.forEach(cb => cb.checked = false);
            document.getElementById('selectAll').checked = false;
            updateBulkSubmit();
        }

        function updateBulkSubmit() {
            const checkedBoxes = document.querySelectorAll('.comment-checkbox:checked');
            const bulkSubmit = document.getElementById('bulkSubmit');
            const bulkAction = document.getElementById('bulkAction');
            
            bulkSubmit.disabled = checkedBoxes.length === 0 || bulkAction.value === '';
        }

        // Toplu işlem form kontrolü
        document.getElementById('bulkAction').addEventListener('change', updateBulkSubmit);
        
        document.getElementById('bulkForm').addEventListener('submit', function(e) {
            const checkedBoxes = document.querySelectorAll('.comment-checkbox:checked');
            const bulkAction = document.getElementById('bulkAction').value;
            
            if(checkedBoxes.length === 0) {
                e.preventDefault();
                alert('Lütfen en az bir yorum seçin.');
                return;
            }
            
            if(!bulkAction) {
                e.preventDefault();
                alert('Lütfen bir toplu işlem seçin.');
                return;
            }
            
            if(bulkAction === 'delete' && !confirm('Seçili ' + checkedBoxes.length + ' yorumu silmek istediğinizden emin misiniz?')) {
                e.preventDefault();
            }
        });

        // Yorum içeriği genişletme/daraltma
        document.addEventListener('DOMContentLoaded', function() {
            const commentContents = document.querySelectorAll('.comment-content');
            commentContents.forEach(content => {
                if (content.scrollHeight > 100) {
                    const readMore = document.createElement('span');
                    readMore.className = 'read-more';
                    readMore.textContent = '... devamını oku';
                    readMore.onclick = function() {
                        content.classList.toggle('expanded');
                        readMore.textContent = content.classList.contains('expanded') ? 'daha az göster' : '... devamını oku';
                    };
                    content.appendChild(readMore);
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>