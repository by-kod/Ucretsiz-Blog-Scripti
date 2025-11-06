<?php
require_once '../includes/config.php';

// Giriş kontrolü
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    redirect('login.php');
}

$pageTitle = "Kategori Yönetimi - " . SITE_NAME;

// Kategori ekleme/düzenleme
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $slug = sanitize($_POST['slug']);
    $description = sanitize($_POST['description']);
    $color = sanitize($_POST['color']);
    $parent_id = isset($_POST['parent_id']) && $_POST['parent_id'] != '' ? (int)$_POST['parent_id'] : null;
    $sort_order = isset($_POST['sort_order']) ? (int)$_POST['sort_order'] : 0;
    
    if(empty($name) || empty($slug)) {
        $_SESSION['error_message'] = "Kategori adı ve URL gereklidir.";
    } else {
        // Kendi kendine parent olamaz kontrolü
        if(isset($_POST['category_id']) && $parent_id == $_POST['category_id']) {
            $_SESSION['error_message'] = "Bir kategori kendisinin alt kategorisi olamaz.";
            redirect('categories.php');
        }
        
        // Sonsuz döngü kontrolü
        if($parent_id) {
            $current_parent = $parent_id;
            $depth = 0;
            while($current_parent && $depth < 10) {
                if($current_parent == $_POST['category_id']) {
                    $_SESSION['error_message'] = "Geçersiz kategori hiyerarşisi!";
                    redirect('categories.php');
                }
                $stmt = $pdo->prepare("SELECT parent_id FROM categories WHERE id = ?");
                $stmt->execute([$current_parent]);
                $current_parent = $stmt->fetchColumn();
                $depth++;
            }
        }
        
        if(isset($_POST['category_id'])) {
            // Düzenleme
            $category_id = (int)$_POST['category_id'];
            $stmt = $pdo->prepare("UPDATE categories SET name = ?, slug = ?, description = ?, color = ?, parent_id = ?, sort_order = ? WHERE id = ?");
            $stmt->execute([$name, $slug, $description, $color, $parent_id, $sort_order, $category_id]);
            $_SESSION['success_message'] = "Kategori başarıyla güncellendi.";
        } else {
            // Ekleme
            $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description, color, parent_id, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $slug, $description, $color, $parent_id, $sort_order]);
            $_SESSION['success_message'] = "Kategori başarıyla eklendi.";
        }
        redirect('categories.php');
    }
}

// Kategori silme
if(isset($_GET['delete'])) {
    $category_id = (int)$_GET['delete'];
    
    // Alt kategorileri kontrol et
    $child_count = $pdo->prepare("SELECT COUNT(*) as count FROM categories WHERE parent_id = ?");
    $child_count->execute([$category_id]);
    $child_count_result = $child_count->fetch()['count'];
    
    if($child_count_result > 0) {
        $_SESSION['error_message'] = "Bu kategoriye ait alt kategoriler bulunuyor. Önce alt kategorileri silin veya taşıyın.";
        redirect('categories.php');
    }
    
    // Kategoriye ait yazıları kontrol et
    $post_count = $pdo->prepare("SELECT COUNT(*) as count FROM posts WHERE category_id = ?");
    $post_count->execute([$category_id]);
    $post_count_result = $post_count->fetch()['count'];
    
    if($post_count_result > 0) {
        $_SESSION['error_message'] = "Bu kategoriye ait yazılar bulunuyor. Önce yazıları silin veya başka kategoriye taşıyın.";
    } else {
        $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$category_id]);
        $_SESSION['success_message'] = "Kategori başarıyla silindi.";
    }
    redirect('categories.php');
}

// Kategorileri getir (hierarchical olarak)
function getCategoriesHierarchical($pdo, $parent_id = null, $level = 0) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE parent_id " . ($parent_id === null ? "IS NULL" : "= ?") . " ORDER BY sort_order ASC, name ASC");
    $params = $parent_id === null ? [] : [$parent_id];
    $stmt->execute($params);
    $categories = $stmt->fetchAll();
    
    $result = [];
    foreach($categories as $category) {
        $category['level'] = $level;
        $result[] = $category;
        // Alt kategorileri recursive olarak getir
        $children = getCategoriesHierarchical($pdo, $category['id'], $level + 1);
        $result = array_merge($result, $children);
    }
    
    return $result;
}

// Tüm kategorileri hierarchical olarak getir
$categories = getCategoriesHierarchical($pdo);

// Ana kategorileri getir (parent seçimi için) - düzenlenen kategori hariç
$main_categories = $pdo->query("SELECT * FROM categories WHERE parent_id IS NULL ORDER BY name ASC")->fetchAll();

// Düzenlenecek kategori
$edit_category = null;
if(isset($_GET['edit'])) {
    $category_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$category_id]);
    $edit_category = $stmt->fetch();
}

// Tüm ana kategorileri getir (düzenleme için - kendisi hariç)
$all_main_categories = $pdo->query("SELECT * FROM categories WHERE parent_id IS NULL ORDER BY name ASC")->fetchAll();
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
            --primary-blue: #2E86C1;
            --primary-purple: #8E44AD;
            --primary-green: #27AE60;
            --primary-orange: #E67E22;
            --primary-red: #E74C3C;
            --primary-pink: #E84393;
            --primary-teal: #16A085;
            --primary-yellow: #F39C12;
            --gray-light: #F8F9FA;
            --gray-medium: #6C757D;
            --gray-dark: #343A40;
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
        
        .sidebar-brand h4 {
            color: var(--primary-blue);
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
            color: var(--gray-medium);
            padding: 0.75rem 1.5rem;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
            text-decoration: none;
            display: block;
        }
        
        .nav-link:hover, .nav-link.active {
            color: var(--primary-blue);
            background: rgba(46, 134, 193, 0.1);
            border-left-color: var(--primary-blue);
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid #eee;
            padding: 1.25rem;
            font-weight: 600;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
            border: none;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(46, 134, 193, 0.4);
        }
        
        .badge.bg-primary {
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple)) !important;
        }
        
        .badge.bg-success {
            background: linear-gradient(135deg, var(--primary-green), var(--primary-teal)) !important;
        }
        
        .badge.bg-warning {
            background: linear-gradient(135deg, var(--primary-yellow), var(--primary-orange)) !important;
        }
        
        .badge.bg-danger {
            background: linear-gradient(135deg, var(--primary-red), var(--primary-pink)) !important;
        }
        
        .alert-success {
            background: linear-gradient(135deg, rgba(39, 174, 96, 0.1), rgba(22, 160, 133, 0.1));
            border-color: var(--primary-green);
            color: var(--primary-green);
        }
        
        .alert-danger {
            background: linear-gradient(135deg, rgba(231, 76, 60, 0.1), rgba(232, 67, 147, 0.1));
            border-color: var(--primary-red);
            color: var(--primary-red);
        }
        
        .category-level-1 { background-color: rgba(46, 134, 193, 0.05); }
        .category-level-2 { background-color: rgba(142, 68, 173, 0.05); margin-left: 20px; }
        .category-level-3 { background-color: rgba(39, 174, 96, 0.05); margin-left: 40px; }
        .category-level-4 { background-color: rgba(230, 126, 34, 0.05); margin-left: 60px; }
        
        .category-indicator {
            display: inline-block;
            width: 20px;
            height: 20px;
            margin-right: 8px;
            text-align: center;
            line-height: 20px;
            font-weight: bold;
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
            color: var(--gray-medium);
            font-size: 0.9rem;
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
                    <a class="nav-link active" href="categories.php">
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

        <div class="container-fluid">
            <div class="row">
                <!-- Kategori Formu -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-<?php echo $edit_category ? 'edit' : 'plus'; ?> me-2"></i>
                                <?php echo $edit_category ? 'Kategori Düzenle' : 'Yeni Kategori'; ?>
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
                                <?php if($edit_category): ?>
                                    <input type="hidden" name="category_id" value="<?php echo $edit_category['id']; ?>">
                                <?php endif; ?>
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label fw-bold">
                                        <i class="fas fa-tag me-2 text-primary"></i>Kategori Adı *
                                    </label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php echo $edit_category ? decodeHtml($edit_category['name']) : ''; ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="slug" class="form-label fw-bold">
                                        <i class="fas fa-link me-2 text-primary"></i>URL *
                                    </label>
                                    <input type="text" class="form-control" id="slug" name="slug" 
                                           value="<?php echo $edit_category ? $edit_category['slug'] : ''; ?>" required>
                                    <div class="form-text">Örnek: teknoloji, yazilim-gelistirme</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="parent_id" class="form-label fw-bold">
                                        <i class="fas fa-sitemap me-2 text-primary"></i>Üst Kategori
                                    </label>
                                    <select class="form-select" id="parent_id" name="parent_id">
                                        <option value="">Ana Kategori (Üst Kategori Yok)</option>
                                        <?php foreach($all_main_categories as $cat): 
                                            if($edit_category && $cat['id'] == $edit_category['id']) continue;
                                        ?>
                                            <option value="<?php echo $cat['id']; ?>" 
                                                <?php echo ($edit_category && $edit_category['parent_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                                <?php echo decodeHtml($cat['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Alt kategori oluşturmak için üst kategori seçin</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="sort_order" class="form-label fw-bold">
                                        <i class="fas fa-sort me-2 text-primary"></i>Sıralama
                                    </label>
                                    <input type="number" class="form-control" id="sort_order" name="sort_order" 
                                           value="<?php echo $edit_category ? $edit_category['sort_order'] : '0'; ?>" min="0">
                                    <div class="form-text">Küçük sayılar önce gösterilir</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="color" class="form-label fw-bold">
                                        <i class="fas fa-palette me-2 text-primary"></i>Renk
                                    </label>
                                    <input type="color" class="form-control form-control-color" id="color" name="color" 
                                           value="<?php echo $edit_category ? $edit_category['color'] : '#2E86C1'; ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label fw-bold">
                                        <i class="fas fa-align-left me-2 text-primary"></i>Açıklama
                                    </label>
                                    <textarea class="form-control" id="description" name="description" 
                                              rows="3"><?php echo $edit_category ? decodeHtml($edit_category['description']) : ''; ?></textarea>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-<?php echo $edit_category ? 'save' : 'plus'; ?> me-2"></i>
                                        <?php echo $edit_category ? 'Güncelle' : 'Kategori Ekle'; ?>
                                    </button>
                                    <?php if($edit_category): ?>
                                        <a href="categories.php" class="btn btn-secondary">
                                            <i class="fas fa-times me-2"></i>İptal
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Kategori Listesi -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-list me-2"></i>Kategoriler
                            </h5>
                            <span class="badge bg-primary"><?php echo count($categories); ?> kategori</span>
                        </div>
                        <div class="card-body">
                            <?php if(isset($_SESSION['success_message'])): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <?php if(empty($categories)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Henüz kategori bulunmuyor.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Kategori</th>
                                                <th>URL</th>
                                                <th>Renk</th>
                                                <th>Yazı Sayısı</th>
                                                <th>Sıra</th>
                                                <th>İşlemler</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($categories as $category): ?>
                                                <?php
                                                $post_count = $pdo->prepare("SELECT COUNT(*) as count FROM posts WHERE category_id = ?");
                                                $post_count->execute([$category['id']]);
                                                $count = $post_count->fetch()['count'];
                                                
                                                $level_class = 'category-level-' . $category['level'];
                                                $indicator = str_repeat('— ', $category['level']) . '› ';
                                                ?>
                                                <tr class="<?php echo $level_class; ?>">
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="category-indicator text-muted"><?php echo $indicator; ?></span>
                                                            <div>
                                                                <strong><?php echo decodeHtml($category['name']); ?></strong>
                                                                <?php if($category['parent_id']): ?>
                                                                    <?php 
                                                                    $parent_stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
                                                                    $parent_stmt->execute([$category['parent_id']]);
                                                                    $parent_name = $parent_stmt->fetch()['name'];
                                                                    ?>
                                                                    <br><small class="text-muted">Alt kategori: <?php echo decodeHtml($parent_name); ?></small>
                                                                <?php endif; ?>
                                                                <?php if(isset($category['description']) && !empty($category['description'])): ?>
                                                                    <br><small class="text-muted"><?php echo decodeHtml($category['description']); ?></small>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <code class="bg-light p-1 rounded">/<?php echo $category['slug']; ?></code>
                                                    </td>
                                                    <td>
                                                        <span class="badge" style="background: <?php echo $category['color']; ?>; color: white; border: 1px solid #ddd;">
                                                            <?php echo $category['color']; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-primary"><?php echo $count; ?> yazı</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-secondary"><?php echo $category['sort_order']; ?></span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="categories.php?edit=<?php echo $category['id']; ?>" 
                                                               class="btn btn-outline-warning" title="Düzenle">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <button onclick="confirmDelete(<?php echo $category['id']; ?>)" 
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
                </div>
            </div>
        </div>
    </div>

<script>
    function confirmDelete(categoryId) {
        if(confirm('Bu kategoriyi silmek istediğinizden emin misiniz?\n\nAlt kategoriler ve bu kategoriye ait yazılar varsa silinemez.')) {
            window.location.href = 'categories.php?delete=' + categoryId;
        }
    }

    // URL otomatik oluşturma
    document.getElementById('name').addEventListener('input', function() {
        if(!document.getElementById('slug').value) {
            const slug = this.value
                .toLowerCase()
                .replace(/ı/g, 'i')
                .replace(/ğ/g, 'g')
                .replace(/ü/g, 'u')
                .replace(/ş/g, 's')
                .replace(/ö/g, 'o')
                .replace(/ç/g, 'c')
                .replace(/ /g, '-')
                .replace(/[^a-z0-9\-]/g, '')
                .replace(/-+/g, '-')
                .replace(/^-|-$/g, '');
            document.getElementById('slug').value = slug;
        }
    });

    // Düzenleme modunda kendi kendine parent olamaz kontrolü
    document.getElementById('parent_id').addEventListener('change', function() {
        const categoryId = document.querySelector('input[name="category_id"]');
        if(categoryId && this.value == categoryId.value) {
            alert('Bir kategori kendisinin alt kategorisi olamaz!');
            this.value = '';
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>