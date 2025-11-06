<?php
require_once '../includes/config.php';

// Giriş kontrolü
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    redirect('login.php');
}

$pageTitle = "Yazı Ekle - " . SITE_NAME;
$post = null;
$post_tags = [];


// Düzenleme modu
if(isset($_GET['id'])) {
    $post_id = (int)$_GET['id'];
    $stmt = $pdo->prepare("
        SELECT p.*, u.display_name, c.name as category_name 
        FROM posts p 
        LEFT JOIN users u ON p.author_id = u.id 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.id = ?
    ");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();
    
    if($post) {
        $pageTitle = "Yazı Düzenle - " . sanitize($post['title']);
        
        // Etiketleri getir
        $tags_stmt = $pdo->prepare("
            SELECT t.id, t.name 
            FROM tags t 
            INNER JOIN post_tags pt ON t.id = pt.tag_id 
            WHERE pt.post_id = ?
        ");
        $tags_stmt->execute([$post_id]);
        $post_tags = $tags_stmt->fetchAll(PDO::FETCH_COLUMN, 1);
    }
}

// Kategorileri getir
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

// Tüm etiketleri getir
$all_tags = $pdo->query("SELECT * FROM tags ORDER BY name ASC")->fetchAll();

// Form gönderimi
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize($_POST['title']);
    $slug = sanitize($_POST['slug']);
    $content = $_POST['content'];
    $excerpt = sanitize($_POST['excerpt']);
    $category_id = (int)$_POST['category_id'];
    $status = sanitize($_POST['status']);
    $meta_title = sanitize($_POST['meta_title']);
    $meta_description = sanitize($_POST['meta_description']);
    $meta_keywords = sanitize($_POST['meta_keywords']);
    $tags = isset($_POST['tags']) ? $_POST['tags'] : [];
    
    // Öne çıkan görsel işleme
    $featured_image = $post ? $post['featured_image'] : '';
    if(isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === 0) {
        $upload_result = uploadFeaturedImage($_FILES['featured_image']);
        if($upload_result['success']) {
            $featured_image = $upload_result['file_path'];
            
            // Eski resmi sil
            if($post && $post['featured_image'] && file_exists('../' . $post['featured_image'])) {
                unlink('../' . $post['featured_image']);
            }
        } else {
            $_SESSION['error_message'] = $upload_result['error'];
        }
    }
    
    // Slug kontrolü - eğer boşsa otomatik oluştur
    if(empty($slug)) {
        $slug = createSlug($title);
    } else {
        $slug = createSlug($slug); // Manuel girileni de temizle
    }
    
    // Benzersiz slug kontrolü
    $slug_check_sql = "SELECT id FROM posts WHERE slug = ?";
    $slug_params = [$slug];
    
    if($post) {
        $slug_check_sql .= " AND id != ?";
        $slug_params[] = $post['id'];
    }
    
    $slug_check = $pdo->prepare($slug_check_sql);
    $slug_check->execute($slug_params);
    $existing_slug = $slug_check->fetch();
    
    if($existing_slug) {
        // Benzersiz slug oluştur
        $counter = 1;
        $original_slug = $slug;
        
        while($existing_slug) {
            $slug = $original_slug . '-' . $counter;
            $slug_check->execute([$slug]);
            $existing_slug = $slug_check->fetch();
            $counter++;
        }
    }
    
    // Okuma süresi hesapla (yaklaşık 200 kelime/dakika)
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200);
    
    if(empty($title) || empty($content)) {
        $_SESSION['error_message'] = "Lütfen zorunlu alanları doldurun.";
    } else {
        try {
            $pdo->beginTransaction();
            
            if($post) {
                // Güncelleme
                $stmt = $pdo->prepare("
                    UPDATE posts SET 
                    title = ?, slug = ?, content = ?, excerpt = ?, category_id = ?, 
                    status = ?, meta_title = ?, meta_description = ?, meta_keywords = ?, 
                    reading_time = ?, featured_image = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([
                    $title, $slug, $content, $excerpt, $category_id,
                    $status, $meta_title, $meta_description, $meta_keywords,
                    $reading_time, $featured_image, $post['id']
                ]);
                $post_id = $post['id'];
                $message = "Yazı başarıyla güncellendi.";
            } else {
                // Yeni yazı
                $stmt = $pdo->prepare("
                    INSERT INTO posts 
                    (title, slug, content, excerpt, category_id, author_id, status, 
                     meta_title, meta_description, meta_keywords, reading_time, featured_image, published_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $published_at = $status == 'published' ? date('Y-m-d H:i:s') : null;
                $stmt->execute([
                    $title, $slug, $content, $excerpt, $category_id, 
                    $_SESSION['admin_user_id'], $status,
                    $meta_title, $meta_description, $meta_keywords, 
                    $reading_time, $featured_image, $published_at
                ]);
                $post_id = $pdo->lastInsertId();
                $message = "Yazı başarıyla eklendi.";
            }
            
            // Etiketleri işle
            if($post) {
                // Önce mevcut etiketleri temizle
                $pdo->prepare("DELETE FROM post_tags WHERE post_id = ?")->execute([$post_id]);
            }
            
            foreach($tags as $tag_name) {
                $tag_name = trim(sanitize($tag_name));
                if(empty($tag_name)) continue;
                
                // Etiket var mı kontrol et
                $tag_stmt = $pdo->prepare("SELECT id FROM tags WHERE name = ?");
                $tag_stmt->execute([$tag_name]);
                $tag = $tag_stmt->fetch();
                
                if($tag) {
                    $tag_id = $tag['id'];
                } else {
                    // Yeni etiket oluştur
                    $tag_slug = createSlug($tag_name);
                    $tag_stmt = $pdo->prepare("INSERT INTO tags (name, slug) VALUES (?, ?)");
                    $tag_stmt->execute([$tag_name, $tag_slug]);
                    $tag_id = $pdo->lastInsertId();
                }
                
                // Yazı-etiket ilişkisi
                $pdo->prepare("INSERT IGNORE INTO post_tags (post_id, tag_id) VALUES (?, ?)")->execute([$post_id, $tag_id]);
            }
            
            // Etiket sayılarını güncelle
            $pdo->prepare("UPDATE tags t 
                          SET count = (SELECT COUNT(*) FROM post_tags WHERE tag_id = t.id)
                          WHERE id IN (SELECT tag_id FROM post_tags WHERE post_id = ?)")
                 ->execute([$post_id]);
            
            $pdo->commit();
            $_SESSION['success_message'] = $message;
            redirect('posts.php');
            
        } catch(Exception $e) {
            $pdo->rollBack();
            $_SESSION['error_message'] = "Bir hata oluştu: " . $e->getMessage();
        }
    }
}

// Öne çıkan görsel yükleme fonksiyonu (WebP dönüşümü ile)
function uploadFeaturedImage($file) {
    $upload_dir = '../uploads/featured/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if(!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'error' => 'Sadece JPEG, PNG, GIF ve WebP formatları desteklenir.'];
    }
    
    if($file['size'] > $max_size) {
        return ['success' => false, 'error' => 'Dosya boyutu 5MB\'dan küçük olmalıdır.'];
    }
    
    try {
        // WebP formatına dönüştür
        $webp_result = convertToWebP($file['tmp_name']);
        
        if($webp_result['success']) {
            $file_name = uniqid() . '_' . time() . '.webp';
            $file_path = $upload_dir . $file_name;
            
            if(imagewebp($webp_result['image'], $file_path, 80)) { // %80 kalite
                // Belleği temizle
                imagedestroy($webp_result['image']);
                
                return [
                    'success' => true, 
                    'file_path' => 'uploads/featured/' . $file_name,
                    'original_size' => $file['size'],
                    'webp_size' => filesize($file_path)
                ];
            } else {
                imagedestroy($webp_result['image']);
                return ['success' => false, 'error' => 'WebP dönüşümü başarısız.'];
            }
        } else {
            return $webp_result;
        }
        
    } catch(Exception $e) {
        return ['success' => false, 'error' => 'Dosya işlenirken hata oluştu: ' . $e->getMessage()];
    }
}

// WebP dönüşüm fonksiyonu
function convertToWebP($source_path) {
    // MIME tipini belirle
    $image_info = getimagesize($source_path);
    $mime_type = $image_info['mime'];
    
    switch($mime_type) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($source_path);
            break;
        case 'image/png':
            $image = imagecreatefrompng($source_path);
            // PNG şeffaflığını koru
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($source_path);
            break;
        case 'image/webp':
            $image = imagecreatefromwebp($source_path);
            break;
        default:
            return ['success' => false, 'error' => 'Desteklenmeyen resim formatı: ' . $mime_type];
    }
    
    if(!$image) {
        return ['success' => false, 'error' => 'Resim yüklenirken hata oluştu.'];
    }
    
    return ['success' => true, 'image' => $image];
}

// Resim yükleme için API endpoint (Quill editor için)
if(isset($_POST['upload_editor_image'])) {
    header('Content-Type: application/json');
    
    if(isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload_result = uploadEditorImage($_FILES['image']);
        echo json_encode($upload_result);
    } else {
        echo json_encode(['success' => false, 'error' => 'Dosya yüklenirken hata oluştu.']);
    }
    exit;
}

// Editor resim yükleme fonksiyonu (WebP dönüşümü ile)
function uploadEditorImage($file) {
    $upload_dir = '../uploads/editor/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if(!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'error' => 'Sadece JPEG, PNG, GIF ve WebP formatları desteklenir.'];
    }
    
    if($file['size'] > $max_size) {
        return ['success' => false, 'error' => 'Dosya boyutu 5MB\'dan küçük olmalıdır.'];
    }
    
    try {
        // WebP formatına dönüştür
        $webp_result = convertToWebP($file['tmp_name']);
        
        if($webp_result['success']) {
            $file_name = uniqid() . '_' . time() . '.webp';
            $file_path = $upload_dir . $file_name;
            
            if(imagewebp($webp_result['image'], $file_path, 85)) { // %85 kalite
                // Belleği temizle
                imagedestroy($webp_result['image']);
                
                return [
                    'success' => true, 
                    'url' => SITE_URL . '/uploads/editor/' . $file_name,
                    'original_size' => $file['size'],
                    'webp_size' => filesize($file_path)
                ];
            } else {
                imagedestroy($webp_result['image']);
                return ['success' => false, 'error' => 'WebP dönüşümü başarısız.'];
            }
        } else {
            return $webp_result;
        }
        
    } catch(Exception $e) {
        return ['success' => false, 'error' => 'Dosya işlenirken hata oluştu: ' . $e->getMessage()];
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
    <!-- Quill Editor -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #3b82f6;
            --light-color: #f8fafc;
            --dark-color: #1e293b;
            --gray-color: #64748b;
            --border-color: #e2e8f0;
            --shadow-color: rgba(0, 0, 0, 0.08);
            --hover-shadow: rgba(0, 0, 0, 0.12);
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            background-color: var(--light-color);
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
            color: var(--dark-color);
        }
        
        .sidebar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            width: 250px;
            min-height: 100vh;
            box-shadow: 2px 0 20px rgba(0,0,0,0.15);
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            overflow-y: auto;
        }
        
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
            min-height: 100vh;
            background-color: var(--light-color);
            transition: margin-left 0.3s ease;
        }
        
        .navbar {
            background: white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            border-radius: 12px;
            padding: 1rem 1.5rem;
            border: none;
        }
        
        .sidebar-brand {
            padding: 1.5rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            text-align: center;
            background: rgba(255,255,255,0.1);
            position: sticky;
            top: 0;
            z-index: 1001;
            backdrop-filter: blur(10px);
        }
        
        .sidebar-brand h4 {
            color: white;
            margin: 0;
            font-weight: 700;
            font-size: 1.25rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .sidebar-brand small {
            font-size: 0.875rem;
            color: rgba(255,255,255,0.8);
        }
        
        .sidebar-nav {
            padding: 1rem 0;
        }
        
        .nav-item {
            margin-bottom: 0.5rem;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.9);
            padding: 0.75rem 1.5rem;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
            text-decoration: none;
            display: block;
            border-radius: 0;
            font-size: 0.9rem;
            backdrop-filter: blur(5px);
        }
        
        .nav-link:hover, .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.15);
            border-left-color: white;
            transform: translateX(5px);
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 10px;
            text-align: center;
        }
        
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px var(--shadow-color);
            margin-bottom: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: white;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px var(--hover-shadow);
        }
        
        .card-header {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-bottom: 1px solid var(--border-color);
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            border-radius: 16px 16px 0 0 !important;
            color: var(--dark-color);
        }
        
        .tag-badge {
            margin: 2px;
            cursor: pointer;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border-radius: 20px;
            padding: 0.4rem 0.8rem;
            transition: all 0.3s ease;
        }
        
        .tag-badge:hover {
            transform: scale(1.05);
        }
        
        .tag-input-container {
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 0.5rem 1rem;
            min-height: 46px;
            background: white;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
            transition: border-color 0.3s ease;
        }
        
        .tag-input-container:focus-within {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .slug-preview {
            background: var(--light-color);
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            margin-top: 0.5rem;
            word-break: break-all;
            font-family: 'Courier New', monospace;
        }
        
        /* Quill Editor Stilleri */
        .ql-editor {
            min-height: 400px;
            font-size: 16px;
            line-height: 1.7;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .ql-toolbar {
            border-top: 2px solid var(--border-color) !important;
            border-left: 2px solid var(--border-color) !important;
            border-right: 2px solid var(--border-color) !important;
            border-bottom: none !important;
            border-radius: 12px 12px 0 0;
            background: var(--light-color);
        }
        
        .ql-container {
            border-bottom: 2px solid var(--border-color) !important;
            border-left: 2px solid var(--border-color) !important;
            border-right: 2px solid var(--border-color) !important;
            border-top: none !important;
            border-radius: 0 0 12px 12px;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .featured-image-preview {
            max-width: 100%;
            max-height: 200px;
            border-radius: 12px;
            margin-top: 10px;
            border: 2px solid var(--border-color);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .image-upload-area {
            border: 3px dashed var(--border-color);
            border-radius: 16px;
            padding: 2.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.4s ease;
            background: white;
            position: relative;
            overflow: hidden;
        }
        
        .image-upload-area::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.1), transparent);
            transition: left 0.6s ease;
        }
        
        .image-upload-area:hover::before {
            left: 100%;
        }
        
        .image-upload-area:hover {
            border-color: var(--primary-color);
            background: rgba(102, 126, 234, 0.03);
            transform: translateY(-2px);
        }
        
        .image-upload-area.dragover {
            border-color: var(--primary-color);
            background: rgba(102, 126, 234, 0.1);
            transform: scale(1.02);
        }
        
        .webp-info {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            border: 1px solid var(--success-color);
            border-radius: 12px;
            padding: 12px 16px;
            margin-top: 12px;
            font-size: 0.875rem;
            color: #065f46;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.15);
        }
        
        .webp-info i {
            color: var(--success-color);
            margin-right: 8px;
        }

        /* Buton Stilleri */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, var(--gray-color) 0%, #475569 100%);
            border: none;
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(100, 116, 139, 0.3);
        }

        /* Form Stilleri */
        .form-control, .form-select {
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }

        /* Responsive Düzenlemeler */
        @media (max-width: 992px) {
            body {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                position: relative;
                min-height: auto;
                max-height: 300px;
            }
            
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
            
            .navbar {
                margin-bottom: 15px;
            }
        }
        
        @media (max-width: 768px) {
            .main-content {
                padding: 10px;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            .ql-editor {
                min-height: 300px;
            }
            
            .navbar {
                padding: 0.75rem 1rem;
            }
            
            .sidebar-brand {
                padding: 1rem;
            }
            
            .sidebar-brand h4 {
                font-size: 1.1rem;
            }
            
            .image-upload-area {
                padding: 1.5rem;
            }
        }

        /* Özel Scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 3px;
        }
        
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.5);
        }

        /* Badge Renkleri */
        .bg-primary { background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important; }
        .bg-success { background: linear-gradient(135deg, var(--success-color), #059669) !important; }
        .bg-warning { background: linear-gradient(135deg, var(--warning-color), #d97706) !important; }
        .bg-danger { background: linear-gradient(135deg, var(--danger-color), #dc2626) !important; }
        .bg-info { background: linear-gradient(135deg, var(--info-color), #2563eb) !important; }
        .bg-secondary { background: linear-gradient(135deg, var(--gray-color), #475569) !important; }
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
                <li class="nav-item mt-3">
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
            <h1 class="h3 fw-bold text-dark"><?php echo $post ? 'Yazı Düzenle' : 'Yeni Yazı'; ?></h1>
            <a href="posts.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Geri Dön
            </a>
        </div>

        <!-- Mesajlar -->
        <?php if(isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST" id="postForm" enctype="multipart/form-data">
            <div class="row">
                <!-- Ana İçerik -->
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="fas fa-edit me-2"></i>İçerik</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="title" class="form-label">Başlık *</label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?php echo $post ? $post['title'] : ''; ?>" required
                                       placeholder="Yazı başlığını girin">
                            </div>
                            
                            <div class="mb-3">
                                <label for="slug" class="form-label">URL *</label>
                                <input type="text" class="form-control" id="slug" name="slug" 
                                       value="<?php echo $post ? $post['slug'] : ''; ?>" required
                                       placeholder="url-ornegi">
                                <div class="form-text">
                                    SEO için önemli! Sadece küçük harf, tire ve rakam kullanın. 
                                    <a href="#" id="generateSlugBtn" class="text-decoration-none fw-bold">
                                        <i class="fas fa-sync-alt me-1"></i>Otomatik oluştur
                                    </a>
                                </div>
                                <div class="slug-preview">
                                    <strong>Önizleme:</strong> 
                                    <span id="slugPreview"><?php echo SITE_URL; ?>/<span id="slugText"><?php echo $post ? $post['slug'] : 'ornek-url'; ?></span>.html</span>
                                </div>
                            </div>
                            
                            <!-- Öne Çıkan Görsel -->
                            <div class="mb-4">
                                <label class="form-label">Öne Çıkan Görsel</label>
                                <div class="image-upload-area" id="imageUploadArea">
                                    <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-3"></i>
                                    <p class="mb-2 fw-semibold">Resmi sürükleyip bırakın veya tıklayarak seçin</p>
                                    <small class="text-muted">PNG, JPG, GIF, WebP - Max 5MB</small>
                                    <div class="webp-info mt-3">
                                        <i class="fas fa-bolt"></i>
                                        <strong>Otomatik WebP Dönüşümü:</strong> Tüm resimler otomatik olarak WebP formatına dönüştürülür
                                    </div>
                                </div>
                                <input type="file" id="featured_image" name="featured_image" accept="image/*" style="display: none;">
                                
                                <?php if($post && $post['featured_image']): ?>
                                    <div class="mt-3">
                                        <p class="mb-2 fw-semibold"><i class="fas fa-image me-2"></i>Mevcut Görsel:</p>
                                        <img src="../<?php echo $post['featured_image']; ?>" 
                                             alt="Öne çıkan görsel" 
                                             class="featured-image-preview">
                                        <div class="webp-info mt-2">
                                            <i class="fas fa-check-circle"></i>
                                            Bu görsel WebP formatında optimize edilmiştir
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <div id="imagePreview" class="mt-3"></div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="excerpt" class="form-label">Özet</label>
                                <textarea class="form-control" id="excerpt" name="excerpt" 
                                          rows="3" placeholder="Kısa açıklama (opsiyonel)"><?php echo $post ? $post['excerpt'] : ''; ?></textarea>
                                <div class="form-text">Ana sayfada ve arama sonuçlarında görünecek kısa açıklama</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="content" class="form-label">İçerik *</label>
                                <div class="webp-info mb-3">
                                    <i class="fas fa-magic"></i>
                                    <strong>Akıllı Editor:</strong> Yüklenen tüm resimler otomatik olarak WebP formatına dönüştürülür
                                </div>
                                <div id="editor"></div>
                                <textarea id="content" name="content" style="display: none;"><?php echo $post ? $post['content'] : ''; ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Yayınlama -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="fas fa-paper-plane me-2"></i>Yayınlama</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="status" class="form-label">Durum</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="draft" <?php echo ($post && $post['status'] == 'draft') ? 'selected' : ''; ?>>Taslak</option>
                                    <option value="published" <?php echo ($post && $post['status'] == 'published') ? 'selected' : ''; ?>>Yayında</option>
                                </select>
                            </div>
                            
                            <?php if($post): ?>
                                <div class="mb-3">
                                    <label class="form-label">İstatistikler</label>
                                    <div class="small text-muted">
                                        <div><i class="far fa-eye me-1"></i> <?php echo $post['view_count'] ?: 0; ?> görüntülenme</div>
                                        <div><i class="far fa-comment me-1"></i> <?php echo $post['comment_count'] ?: 0; ?> yorum</div>
                                        <div><i class="far fa-clock me-1"></i> <?php echo $post['reading_time'] ?: '3'; ?> dk okuma süresi</div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-2"></i>
                                    <?php echo $post ? 'Güncelle' : 'Yayınla'; ?>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Kategori -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="fas fa-folder me-2"></i>Kategori</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">Kategori Seçin</option>
                                    <?php foreach($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" 
                                            <?php echo ($post && $post['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                            <?php echo sanitize($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <a href="categories.php" class="btn btn-sm btn-outline-primary w-100">
                                <i class="fas fa-plus me-1"></i>Yeni Kategori
                            </a>
                        </div>
                    </div>

                    <!-- Etiketler -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="fas fa-tags me-2"></i>Etiketler</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Etiketler</label>
                                <div class="tag-input-container" id="tagContainer">
                                    <!-- Etiketler buraya eklenecek -->
                                </div>
                                <input type="text" class="form-control mt-2" id="tagInput" 
                                       placeholder="Etiket ekle...">
                                <div class="form-text">Enter tuşu ile ekleyin</div>
                            </div>
                            
                            <!-- Mevcut etiketler -->
                            <?php if(!empty($all_tags)): ?>
                                <div class="mt-3">
                                    <label class="form-label">Mevcut Etiketler:</label>
                                    <div class="d-flex flex-wrap gap-1">
                                        <?php foreach($all_tags as $tag): ?>
                                            <span class="badge bg-secondary tag-badge" 
                                                  data-tag-name="<?php echo $tag['name']; ?>">
                                                <?php echo sanitize($tag['name']); ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- SEO -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="fas fa-search me-2"></i>SEO Ayarları</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="meta_title" class="form-label">Meta Başlık</label>
                                <input type="text" class="form-control" id="meta_title" name="meta_title" 
                                       value="<?php echo $post ? $post['meta_title'] : ''; ?>"
                                       placeholder="SEO için meta başlık">
                                <div class="form-text">Boş bırakılırsa yazı başlığı kullanılır</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="meta_description" class="form-label">Meta Açıklama</label>
                                <textarea class="form-control" id="meta_description" name="meta_description" 
                                          rows="3" placeholder="SEO için meta açıklama"><?php echo $post ? $post['meta_description'] : ''; ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="meta_keywords" class="form-label">Meta Anahtar Kelimeler</label>
                                <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" 
                                       value="<?php echo $post ? $post['meta_keywords'] : ''; ?>"
                                       placeholder="kelime1, kelime2, kelime3">
                                <div class="form-text">Virgülle ayırarak yazın</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Quill Editor
        const quill = new Quill('#editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                    [{ 'font': [] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'script': 'sub'}, { 'script': 'super' }],
                    ['blockquote', 'code-block'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                    [{ 'align': [] }],
                    ['link', 'image', 'video'],
                    ['clean']
                ]
            },
            placeholder: 'Yazı içeriğini buraya yazın...',
        });

        // Mevcut içeriği editor'e yükle
        const contentTextarea = document.getElementById('content');
        if (contentTextarea) {
            quill.root.innerHTML = contentTextarea.value || '';

            // Editor değiştiğinde textarea'yı güncelle
            quill.on('text-change', function() {
                contentTextarea.value = quill.root.innerHTML;
            });
        }

        // Resim yükleme işlemi (WebP dönüşümü ile)
        const imageHandler = function() {
            const input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            input.click();
            
            input.onchange = async function() {
                const file = input.files[0];
                if (file) {
                    const formData = new FormData();
                    formData.append('image', file);
                    formData.append('upload_editor_image', 'true');
                    
                    try {
                        // Yükleme göstergesi
                        const range = quill.getSelection();
                        quill.insertText(range.index, '🔄 Resim yükleniyor...');
                        
                        const response = await fetch('post-edit.php', {
                            method: 'POST',
                            body: formData
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            // Yükleme metnini sil
                            quill.deleteText(range.index, 18);
                            // Resmi ekle
                            quill.insertEmbed(range.index, 'image', result.url);
                            
                            // Başarı mesajı
                            showNotification('✅ Resim başarıyla WebP formatına dönüştürüldü!', 'success');
                        } else {
                            quill.deleteText(range.index, 18);
                            showNotification('❌ Resim yüklenirken hata oluştu: ' + result.error, 'error');
                        }
                    } catch (error) {
                        quill.deleteText(range.index, 18);
                        showNotification('❌ Resim yüklenirken hata oluştu: ' + error.message, 'error');
                    }
                }
            };
        };

        // Toolbar'daki resim butonuna tıklama olayını ekle
        quill.getModule('toolbar').addHandler('image', imageHandler);

        // Bildirim fonksiyonu
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.main-content').insertBefore(notification, document.querySelector('.main-content').firstChild);
            
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        }

        // Slug oluşturma fonksiyonu
        function createSlug(text) {
            return text
                .toLowerCase()
                .replace(/ı/g, 'i')
                .replace(/ğ/g, 'g')
                .replace(/ü/g, 'u')
                .replace(/ş/g, 's')
                .replace(/ö/g, 'o')
                .replace(/ç/g, 'c')
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .replace(/^-|-$/g, '');
        }

        // Slug önizleme güncelleme
        function updateSlugPreview() {
            const slugInput = document.getElementById('slug');
            const slugText = document.getElementById('slugText');
            if (slugInput && slugText) {
                const slug = slugInput.value || 'ornek-url';
                slugText.textContent = slug;
            }
        }

        // Otomatik slug oluşturma
        function generateSlug() {
            const titleInput = document.getElementById('title');
            const slugInput = document.getElementById('slug');
            
            if (titleInput && slugInput) {
                const title = titleInput.value;
                if (title) {
                    const slug = createSlug(title);
                    slugInput.value = slug;
                    updateSlugPreview();
                    
                    // Meta title'ı da otomatik doldur
                    const metaTitleInput = document.getElementById('meta_title');
                    if (metaTitleInput && !metaTitleInput.value) {
                        metaTitleInput.value = title;
                    }
                }
            }
        }

        // Slug otomatik oluşturma butonu
        document.getElementById('generateSlugBtn').addEventListener('click', function(e) {
            e.preventDefault();
            generateSlug();
        });

        // Başlık değiştiğinde otomatik slug oluştur
        document.getElementById('title').addEventListener('input', function() {
            const slugInput = document.getElementById('slug');
            if (slugInput && !slugInput.value) {
                generateSlug();
            }
        });

        // Slug değiştiğinde önizlemeyi güncelle
        document.getElementById('slug').addEventListener('input', updateSlugPreview);

        // Öne çıkan görsel önizleme
        function previewFeaturedImage(input) {
            const preview = document.getElementById('imagePreview');
            if (!preview) return;
            
            preview.innerHTML = '';
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'featured-image-preview';
                    img.alt = 'Öne çıkan görsel önizleme';
                    preview.appendChild(img);
                    
                    const info = document.createElement('div');
                    info.className = 'webp-info mt-2';
                    info.innerHTML = '<i class="fas fa-sync-alt fa-spin"></i> <strong>İşleniyor:</strong> Bu resim WebP formatına dönüştürülecek';
                    preview.appendChild(info);
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Öne çıkan görsel yükleme alanı
        const imageUploadArea = document.getElementById('imageUploadArea');
        const featuredImageInput = document.getElementById('featured_image');

        if (imageUploadArea && featuredImageInput) {
            // Tıklama ile dosya seçme
            imageUploadArea.addEventListener('click', function() {
                featuredImageInput.click();
            });

            // Dosya seçildiğinde önizleme
            featuredImageInput.addEventListener('change', function() {
                previewFeaturedImage(this);
            });

            // Sürükle bırak özelliği
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                imageUploadArea.addEventListener(eventName, preventDefaults, false);
            });
            
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            ['dragenter', 'dragover'].forEach(eventName => {
                imageUploadArea.addEventListener(eventName, highlight, false);
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                imageUploadArea.addEventListener(eventName, unhighlight, false);
            });
            
            function highlight() {
                imageUploadArea.classList.add('dragover');
            }
            
            function unhighlight() {
                imageUploadArea.classList.remove('dragover');
            }
            
            imageUploadArea.addEventListener('drop', handleDrop, false);
            
            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                featuredImageInput.files = files;
                previewFeaturedImage(featuredImageInput);
            }
        }

        // Etiket yönetimi
        let tags = <?php echo json_encode($post_tags); ?>;
        
        function renderTags() {
            const container = document.getElementById('tagContainer');
            if (!container) return;
            
            container.innerHTML = '';
            
            tags.forEach(tag => {
                const badge = document.createElement('span');
                badge.className = 'badge bg-primary tag-badge';
                badge.innerHTML = `${tag} <span class="remove-tag" style="cursor:pointer; margin-left: 5px;">×</span>`;
                
                const removeBtn = badge.querySelector('.remove-tag');
                removeBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    removeTag(tag);
                });
                
                container.appendChild(badge);
                
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'tags[]';
                hiddenInput.value = tag;
                container.appendChild(hiddenInput);
            });
        }
        
        function addTag(tag) {
            tag = tag.trim();
            if(tag && !tags.includes(tag)) {
                tags.push(tag);
                renderTags();
            }
            const tagInput = document.getElementById('tagInput');
            if (tagInput) {
                tagInput.value = '';
            }
        }
        
        function removeTag(tag) {
            tags = tags.filter(t => t !== tag);
            renderTags();
        }
        
        // Enter ile etiket ekleme
        const tagInput = document.getElementById('tagInput');
        if (tagInput) {
            tagInput.addEventListener('keypress', function(e) {
                if(e.key === 'Enter') {
                    e.preventDefault();
                    addTag(this.value);
                }
            });
        }

        // Mevcut etiketlere tıklama ile ekleme
        document.querySelectorAll('.tag-badge[data-tag-name]').forEach(badge => {
            badge.addEventListener('click', function() {
                const tagName = this.getAttribute('data-tag-name');
                addTag(tagName);
            });
        });
        
        // İlk yüklemede etiketleri render et ve slug önizlemesini güncelle
        document.addEventListener('DOMContentLoaded', function() {
            renderTags();
            updateSlugPreview();
        });
    </script>
</body>
</html>