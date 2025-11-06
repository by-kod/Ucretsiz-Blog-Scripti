<?php
require_once '../includes/config.php';

// Giriş kontrolü
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    redirect('login.php');
}

$pageTitle = "İletişim Mesajları - " . SITE_NAME;

// Sayfalama
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 15;
$offset = ($page - 1) * $limit;

// Durum filtreleme
$status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : 'all';

// Mesajları getir
$where_conditions = [];
$params = [];

if($status_filter != 'all') {
    $where_conditions[] = "status = ?";
    $params[] = $status_filter;
}

$where_sql = '';
if(!empty($where_conditions)) {
    $where_sql = 'WHERE ' . implode(' AND ', $where_conditions);
}

$messages_sql = "SELECT * FROM contact_messages $where_sql ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$messages_stmt = $pdo->prepare($messages_sql);
$messages_stmt->execute($params);
$messages = $messages_stmt->fetchAll();

// Toplam mesaj sayısı
$total_stmt = $pdo->prepare("SELECT COUNT(*) as total FROM contact_messages $where_sql");
$total_stmt->execute($params);
$totalMessages = $total_stmt->fetch()['total'];
$totalPages = ceil($totalMessages / $limit);

// İstatistikler
$stats = $pdo->query("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) as new,
        SUM(CASE WHEN status = 'read' THEN 1 ELSE 0 END) as read_count,
        SUM(CASE WHEN status = 'replied' THEN 1 ELSE 0 END) as replied
    FROM contact_messages
")->fetch();

// Mesaj durumu güncelleme
if(isset($_GET['mark_as'])) {
    $message_id = (int)$_GET['id'];
    $new_status = sanitize($_GET['mark_as']);
    
    if(in_array($new_status, ['read', 'replied'])) {
        $update_stmt = $pdo->prepare("UPDATE contact_messages SET status = ? WHERE id = ?");
        $update_stmt->execute([$new_status, $message_id]);
        $_SESSION['success_message'] = "Mesaj durumu güncellendi!";
        redirect('contact_messages.php');
    }
}

// Mesaj silme
if(isset($_GET['delete'])) {
    $message_id = (int)$_GET['delete'];
    $delete_stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
    $delete_stmt->execute([$message_id]);
    $_SESSION['success_message'] = "Mesaj başarıyla silindi!";
    redirect('contact_messages.php');
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
            --secondary-color: #764ba2;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
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
            color: var(--dark-color);
            font-size: 0.9rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
            transform: translateY(-1px);
        }
        
        .badge {
            font-size: 0.75rem;
        }
        
        .btn-group-sm > .btn {
            padding: 0.25rem 0.5rem;
        }
        
        .modal-body .border {
            background: var(--light-color);
            white-space: pre-wrap;
            font-family: inherit;
        }
        
        .text-truncate {
            max-width: 200px;
        }
        
        .table-warning {
            background-color: rgba(255, 193, 7, 0.1) !important;
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
                    <a class="nav-link" href="comments.php">
                        <i class="fas fa-comments"></i>Yorumlar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="contact_messages.php">
                        <i class="fas fa-envelope"></i>İletişim Mesajları
                        <?php if($stats['new'] > 0): ?>
                            <span class="badge bg-warning float-end"><?php echo $stats['new']; ?></span>
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
            <h1 class="h3">İletişim Mesajları</h1>
            <div class="btn-group">
                <a href="contact_messages.php?status=all" class="btn btn-outline-primary <?php echo $status_filter == 'all' ? 'active' : ''; ?>">
                    Tümü (<?php echo $stats['total']; ?>)
                </a>
                <a href="contact_messages.php?status=new" class="btn btn-outline-warning <?php echo $status_filter == 'new' ? 'active' : ''; ?>">
                    Yeni (<?php echo $stats['new']; ?>)
                </a>
                <a href="contact_messages.php?status=read" class="btn btn-outline-info <?php echo $status_filter == 'read' ? 'active' : ''; ?>">
                    Okundu (<?php echo $stats['read_count']; ?>)
                </a>
                <a href="contact_messages.php?status=replied" class="btn btn-outline-success <?php echo $status_filter == 'replied' ? 'active' : ''; ?>">
                    Yanıtlandı (<?php echo $stats['replied']; ?>)
                </a>
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

        <?php if(empty($messages)): ?>
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Henüz mesaj bulunmuyor</h4>
                    <p class="text-muted">İletişim formundan henüz mesaj gönderilmemiş.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">ID</th>
                                    <th>Gönderen</th>
                                    <th>Konu</th>
                                    <th width="120">Durum</th>
                                    <th width="150">Tarih</th>
                                    <th width="200" class="text-center">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($messages as $message): ?>
                                    <tr class="<?php echo $message['status'] == 'new' ? 'table-warning' : ''; ?>">
                                        <td class="fw-bold">#<?php echo $message['id']; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <i class="fas fa-user-circle text-muted fa-lg"></i>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <div class="fw-bold"><?php echo htmlspecialchars($message['name']); ?></div>
                                                    <div class="text-muted small"><?php echo htmlspecialchars($message['email']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold"><?php echo htmlspecialchars($message['subject']); ?></div>
                                            <div class="text-muted small text-truncate" style="max-width: 200px;">
                                                <?php echo htmlspecialchars(substr($message['message'], 0, 100)); ?>...
                                            </div>
                                        </td>
                                        <td>
                                            <?php if($message['status'] == 'new'): ?>
                                                <span class="badge bg-warning">Yeni</span>
                                            <?php elseif($message['status'] == 'read'): ?>
                                                <span class="badge bg-info">Okundu</span>
                                            <?php elseif($message['status'] == 'replied'): ?>
                                                <span class="badge bg-success">Yanıtlandı</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="small text-muted">
                                                <?php echo date('d.m.Y', strtotime($message['created_at'])); ?>
                                            </div>
                                            <div class="small text-muted">
                                                <?php echo date('H:i', strtotime($message['created_at'])); ?>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <!-- Görüntüle -->
                                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#messageModal<?php echo $message['id']; ?>">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                
                                                <!-- Durum Güncelle -->
                                                <?php if($message['status'] == 'new'): ?>
                                                    <a href="?id=<?php echo $message['id']; ?>&mark_as=read" class="btn btn-outline-info" title="Okundu olarak işaretle">
                                                        <i class="fas fa-check"></i>
                                                    </a>
                                                <?php elseif($message['status'] == 'read'): ?>
                                                    <a href="?id=<?php echo $message['id']; ?>&mark_as=replied" class="btn btn-outline-success" title="Yanıtlandı olarak işaretle">
                                                        <i class="fas fa-reply"></i>
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <!-- Sil -->
                                                <a href="?delete=<?php echo $message['id']; ?>" class="btn btn-outline-danger" onclick="return confirm('Bu mesajı silmek istediğinizden emin misiniz?')" title="Sil">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Mesaj Detay Modal -->
                                    <div class="modal fade" id="messageModal<?php echo $message['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Mesaj Detayı #<?php echo $message['id']; ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row mb-4">
                                                        <div class="col-md-6">
                                                            <strong>Gönderen:</strong><br>
                                                            <?php echo htmlspecialchars($message['name']); ?>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong>E-posta:</strong><br>
                                                            <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>">
                                                                <?php echo htmlspecialchars($message['email']); ?>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row mb-4">
                                                        <div class="col-md-6">
                                                            <strong>Konu:</strong><br>
                                                            <?php echo htmlspecialchars($message['subject']); ?>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong>Tarih:</strong><br>
                                                            <?php echo date('d.m.Y H:i:s', strtotime($message['created_at'])); ?>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mb-4">
                                                        <strong>Mesaj:</strong>
                                                        <div class="border rounded p-3 bg-light mt-2">
                                                            <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row text-muted small">
                                                        <div class="col-md-6">
                                                            <strong>IP Adresi:</strong><br>
                                                            <?php echo $message['ip_address']; ?>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong>Tarayıcı:</strong><br>
                                                            <span class="text-truncate d-inline-block" style="max-width: 200px;" title="<?php echo htmlspecialchars($message['user_agent']); ?>">
                                                                <?php echo htmlspecialchars(substr($message['user_agent'], 0, 50)); ?>...
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                                                    <?php if($message['status'] == 'new'): ?>
                                                        <a href="?id=<?php echo $message['id']; ?>&mark_as=read" class="btn btn-primary">
                                                            <i class="fas fa-check me-1"></i>Okundu Olarak İşaretle
                                                        </a>
                                                    <?php elseif($message['status'] == 'read'): ?>
                                                        <a href="?id=<?php echo $message['id']; ?>&mark_as=replied" class="btn btn-success">
                                                            <i class="fas fa-reply me-1"></i>Yanıtlandı Olarak İşaretle
                                                        </a>
                                                    <?php endif; ?>
                                                    <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>?subject=Re: <?php echo urlencode($message['subject']); ?>" class="btn btn-info">
                                                        <i class="fas fa-envelope me-1"></i>Yanıtla
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sayfalama -->
            <?php if($totalPages > 1): ?>
                <nav aria-label="Sayfalama" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $status_filter != 'all' ? '&status=' . $status_filter : ''; ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php 
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);
                        
                        for($i = $startPage; $i <= $endPage; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?><?php echo $status_filter != 'all' ? '&status=' . $status_filter : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $status_filter != 'all' ? '&status=' . $status_filter : ''; ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>