<?php
require_once '../includes/config.php';

// Giriş kontrolü
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    redirect('login.php');
}

$pageTitle = "Etiket Yönetimi - " . SITE_NAME;

// Etiket ekleme/düzenleme
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $slug = sanitize($_POST['slug']);
    
    if(empty($name) || empty($slug)) {
        $_SESSION['error_message'] = "Etiket adı ve URL gereklidir.";
    } else {
        if(isset($_POST['tag_id'])) {
            // Düzenleme
            $tag_id = (int)$_POST['tag_id'];
            $stmt = $pdo->prepare("UPDATE tags SET name = ?, slug = ? WHERE id = ?");
            $stmt->execute([$name, $slug, $tag_id]);
            $_SESSION['success_message'] = "Etiket başarıyla güncellendi.";
        } else {
            // Ekleme
            $stmt = $pdo->prepare("INSERT INTO tags (name, slug) VALUES (?, ?)");
            $stmt->execute([$name, $slug]);
            $_SESSION['success_message'] = "Etiket başarıyla eklendi.";
        }
        redirect('tags.php');
    }
}

// Etiket silme
if(isset($_GET['delete'])) {
    $tag_id = (int)$_GET['delete'];
    
    // Etikete ait yazıları kontrol et
    $post_count = $pdo->prepare("SELECT COUNT(*) as count FROM post_tags WHERE tag_id = ?");
    $post_count->execute([$tag_id]);
    $count = $post_count->fetch()['count'];
    
    if($count > 0) {
        $_SESSION['error_message'] = "Bu etikete ait yazılar bulunuyor. Önce yazılardan bu etiketi kaldırın.";
    } else {
        $pdo->prepare("DELETE FROM tags WHERE id = ?")->execute([$tag_id]);
        $_SESSION['success_message'] = "Etiket başarıyla silindi.";
    }
    redirect('tags.php');
}

// Etiketleri getir
$tags = $pdo->query("SELECT * FROM tags ORDER BY name ASC")->fetchAll();

// Düzenlenecek etiket
$edit_tag = null;
if(isset($_GET['edit'])) {
    $tag_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM tags WHERE id = ?");
    $stmt->execute([$tag_id]);
    $edit_tag = $stmt->fetch();
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
            --info-color: #17a2b8;
            --dark-color: #343a40;
            --light-color: #f8f9fa;
            --gray-color: #6c757d;
            --gray-light: #e9ecef;
            --border-color: #dee2e6;
        }
        
        body {
            background-color: var(--light-color);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
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
            border-bottom: 1px solid var(--border-color);
            text-align: center;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }
        
        .sidebar-brand h4 {
            margin: 0;
            font-weight: 700;
        }
        
        .sidebar-brand small {
            color: rgba(255,255,255,0.8);
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
            background: var(--primary-light);
            border-left-color: var(--primary-color);
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 25px rgba(0,0,0,0.12);
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid var(--border-color);
            padding: 1.25rem;
            font-weight: 600;
        }
        
        .tag-badge {
            font-size: 0.9rem;
            padding: 0.5rem 0.8rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
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
        
        .badge-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }
        
        .badge-success {
            background: linear-gradient(135deg, var(--success-color), #20c997);
        }
        
        .badge-warning {
            background: linear-gradient(135deg, var(--warning-color), #fd7e14);
        }
        
        .badge-danger {
            background: linear-gradient(135deg, var(--danger-color), #e83e8c);
        }
        
        .badge-info {
            background: linear-gradient(135deg, var(--info-color), #6f42c1);
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
            color: var(--gray-color);
            font-size: 0.9rem;
            background: var(--light-color);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <h4><i class="fas fa-cog me-2"></i>Admin Panel</h4>
            <small><?php echo SITE_NAME; ?></small>
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
                    <a class="nav-link active" href="tags.php">
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

        <div class="container-fluid">
            <div class="row">
                <!-- Etiket Formu -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-<?php echo $edit_tag ? 'edit' : 'plus'; ?> me-2"></i>
                                <?php echo $edit_tag ? 'Etiket Düzenle' : 'Yeni Etiket'; ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if(isset($_SESSION['error_message'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <form method="POST">
                                <?php if($edit_tag): ?>
                                    <input type="hidden" name="tag_id" value="<?php echo $edit_tag['id']; ?>">
                                <?php endif; ?>
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label fw-bold">
                                        <i class="fas fa-tag me-2 text-primary"></i>Etiket Adı *
                                    </label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php echo $edit_tag ? $edit_tag['name'] : ''; ?>" 
                                           placeholder="Etiket adını girin" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="slug" class="form-label fw-bold">
                                        <i class="fas fa-link me-2 text-primary"></i>URL *
                                    </label>
                                    <input type="text" class="form-control" id="slug" name="slug" 
                                           value="<?php echo $edit_tag ? $edit_tag['slug'] : ''; ?>" 
                                           placeholder="url-bicimi" required>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Örnek: php, web-gelistirme, seo-optimizasyonu
                                    </div>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-<?php echo $edit_tag ? 'save' : 'plus'; ?> me-2"></i>
                                        <?php echo $edit_tag ? 'Güncelle' : 'Etiket Ekle'; ?>
                                    </button>
                                    <?php if($edit_tag): ?>
                                        <a href="tags.php" class="btn btn-secondary">
                                            <i class="fas fa-times me-2"></i>İptal
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- İstatistikler -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-chart-bar me-2"></i>İstatistikler
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php
                            $total_tags = $pdo->query("SELECT COUNT(*) as count FROM tags")->fetch()['count'];
                            $total_used_tags = $pdo->query("SELECT COUNT(DISTINCT tag_id) as count FROM post_tags")->fetch()['count'];
                            $most_used_tag = $pdo->query("SELECT t.name, COUNT(pt.tag_id) as usage_count FROM tags t LEFT JOIN post_tags pt ON t.id = pt.tag_id GROUP BY t.id ORDER BY usage_count DESC LIMIT 1")->fetch();
                            ?>
                            <div class="row text-center">
                                <div class="col-6 mb-3">
                                    <div class="fw-bold text-primary fs-4"><?php echo $total_tags; ?></div>
                                    <small class="text-muted">Toplam Etiket</small>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="fw-bold text-success fs-4"><?php echo $total_used_tags; ?></div>
                                    <small class="text-muted">Kullanılan</small>
                                </div>
                            </div>
                            <?php if($most_used_tag): ?>
                                <hr>
                                <div class="text-center">
                                    <small class="text-muted">
                                        <i class="fas fa-crown me-1 text-warning"></i>En Çok Kullanılan
                                    </small>
                                    <div class="fw-bold mt-1"><?php echo $most_used_tag['name']; ?></div>
                                    <small class="text-muted">(<?php echo $most_used_tag['usage_count']; ?> yazı)</small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Etiket Listesi -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-list me-2"></i>Etiketler
                            </h5>
                            <span class="badge bg-primary"><?php echo count($tags); ?> etiket</span>
                        </div>
                        <div class="card-body">
                            <?php if(isset($_SESSION['success_message'])): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <?php if(empty($tags)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Henüz etiket bulunmuyor.</p>
                                    <p class="text-muted small">
                                        <i class="fas fa-lightbulb me-1"></i>
                                        Yazı eklerken etiketler otomatik olarak oluşturulur veya yukarıdan manuel ekleyebilirsiniz.
                                    </p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Etiket</th>
                                                <th>URL</th>
                                                <th>Kullanım</th>
                                                <th>Oluşturulma</th>
                                                <th>İşlemler</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($tags as $tag): ?>
                                                <?php
                                                $usage_count = $pdo->prepare("SELECT COUNT(*) as count FROM post_tags WHERE tag_id = ?");
                                                $usage_count->execute([$tag['id']]);
                                                $count = $usage_count->fetch()['count'];
                                                ?>
                                                <tr>
                                                    <td>
                                                        <span class="badge tag-badge"><?php echo sanitize($tag['name']); ?></span>
                                                    </td>
                                                    <td>
                                                        <code class="text-primary">/etiket/<?php echo sanitize($tag['slug']); ?></code>
                                                    </td>
                                                    <td>
                                                        <?php if($count > 0): ?>
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-file-alt me-1"></i><?php echo $count; ?> yazı
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="badge bg-light text-dark">
                                                                <i class="fas fa-times me-1"></i>Kullanılmıyor
                                                            </span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">
                                                            <i class="far fa-clock me-1"></i>
                                                            <?php echo date('d/m/Y', strtotime($tag['created_at'])); ?>
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="tags.php?edit=<?php echo $tag['id']; ?>" 
                                                               class="btn btn-outline-warning" title="Düzenle">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <button onclick="confirmDelete(<?php echo $tag['id']; ?>)" 
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
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Popüler Etiketler -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-fire me-2"></i>Popüler Etiketler
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php
                            $popular_tags = $pdo->query("
                                SELECT t.name, t.slug, COUNT(pt.tag_id) as usage_count 
                                FROM tags t 
                                LEFT JOIN post_tags pt ON t.id = pt.tag_id 
                                GROUP BY t.id 
                                HAVING usage_count > 0 
                                ORDER BY usage_count DESC 
                                LIMIT 10
                            ")->fetchAll();
                            ?>
                            
                            <?php if(empty($popular_tags)): ?>
                                <p class="text-muted text-center">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Henüz popüler etiket bulunmuyor.
                                </p>
                            <?php else: ?>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach($popular_tags as $tag): ?>
                                        <a href="../search.php?tag=<?php echo $tag['slug']; ?>" 
                                           target="_blank" 
                                           class="badge bg-primary text-decoration-none d-flex align-items-center">
                                            <i class="fas fa-hashtag me-1"></i>
                                            <?php echo sanitize($tag['name']); ?>
                                            <span class="badge bg-light text-dark ms-2"><?php echo $tag['usage_count']; ?></span>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(tagId) {
            if(confirm('Bu etiketi silmek istediğinizden emin misiniz?')) {
                window.location.href = 'tags.php?delete=' + tagId;
            }
        }

        // URL otomatik oluşturma
        document.getElementById('name').addEventListener('input', function() {
            if(!document.getElementById('slug').value) {
                const slug = this.value
                    .toLowerCase()
                    .replace(/ğ/g, 'g')
                    .replace(/ü/g, 'u')
                    .replace(/ş/g, 's')
                    .replace(/ı/g, 'i')
                    .replace(/ö/g, 'o')
                    .replace(/ç/g, 'c')
                    .replace(/[^a-z0-9]/g, '-')
                    .replace(/-+/g, '-')
                    .replace(/^-|-$/g, '');
                document.getElementById('slug').value = slug;
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>