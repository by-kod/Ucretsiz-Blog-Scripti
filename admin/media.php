<?php
require_once '../includes/config.php';

// Giriş kontrolü
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    redirect('login.php');
}

$pageTitle = "Medya Yönetimi - " . SITE_NAME;

// Dosya yükleme
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['media_files'])) {
    $uploaded_files = [];
    $errors = [];
    
    foreach($_FILES['media_files']['name'] as $key => $name) {
        if($_FILES['media_files']['error'][$key] === UPLOAD_ERR_OK) {
            $upload_result = uploadMediaFile([
                'name' => $_FILES['media_files']['name'][$key],
                'type' => $_FILES['media_files']['type'][$key],
                'tmp_name' => $_FILES['media_files']['tmp_name'][$key],
                'error' => $_FILES['media_files']['error'][$key],
                'size' => $_FILES['media_files']['size'][$key]
            ]);
            
            if($upload_result['success']) {
                $uploaded_files[] = $upload_result;
            } else {
                $errors[] = $name . ': ' . $upload_result['error'];
            }
        }
    }
    
    if(!empty($uploaded_files)) {
        $_SESSION['success_message'] = count($uploaded_files) . " dosya başarıyla yüklendi.";
    }
    
    if(!empty($errors)) {
        $_SESSION['error_message'] = implode('<br>', $errors);
    }
    
    redirect('media.php');
}

// Dosya silme
if(isset($_GET['delete'])) {
    $file_path = sanitize($_GET['delete']);
    $full_path = '../' . $file_path;
    
    if(file_exists($full_path) && is_file($full_path)) {
        if(unlink($full_path)) {
            $_SESSION['success_message'] = "Dosya başarıyla silindi.";
        } else {
            $_SESSION['error_message'] = "Dosya silinirken hata oluştu.";
        }
    } else {
        $_SESSION['error_message'] = "Dosya bulunamadı.";
    }
    
    redirect('media.php');
}

// Medya dosyalarını listele
$media_files = [];
$upload_dirs = [
    'uploads/featured/' => 'Öne Çıkan Görseller',
    'uploads/images/' => 'İçerik Resimleri',
    'uploads/media/' => 'Genel Medya'
];

foreach($upload_dirs as $dir => $label) {
    if(file_exists('../' . $dir)) {
        $files = scandir('../' . $dir);
        foreach($files as $file) {
            if($file != '.' && $file != '..') {
                $file_path = $dir . $file;
                $full_path = '../' . $file_path;
                
                if(is_file($full_path)) {
                    $file_info = [
                        'path' => $file_path,
                        'name' => $file,
                        'size' => filesize($full_path),
                        'type' => mime_content_type($full_path),
                        'modified' => filemtime($full_path),
                        'url' => SITE_URL . '/' . $file_path,
                        'category' => $label
                    ];
                    $media_files[] = $file_info;
                }
            }
        }
    }
}

// Dosya boyutunu okunabilir formata çevir
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        return $bytes . ' bytes';
    } elseif ($bytes == 1) {
        return '1 byte';
    } else {
        return '0 bytes';
    }
}

// Medya dosyası yükleme fonksiyonu
function uploadMediaFile($file) {
    $upload_dir = '../uploads/media/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $allowed_types = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
        'application/pdf', 
        'video/mp4', 'video/mpeg', 'video/quicktime',
        'audio/mpeg', 'audio/wav', 'audio/ogg',
        'application/zip', 'application/x-rar-compressed'
    ];
    
    $max_size = 10 * 1024 * 1024; // 10MB
    
    if(!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'error' => 'Desteklenmeyen dosya formatı.'];
    }
    
    if($file['size'] > $max_size) {
        return ['success' => false, 'error' => 'Dosya boyutu 10MB\'dan küçük olmalıdır.'];
    }
    
    // Dosya adını güvenli hale getir
    $file_name = pathinfo($file['name'], PATHINFO_FILENAME);
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    
    // Türkçe karakterleri ve özel karakterleri temizle
    $file_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $file_name);
    $file_name = substr($file_name, 0, 100); // Maximum 100 karakter
    $safe_file_name = $file_name . '_' . uniqid() . '.' . $file_extension;
    
    $file_path = $upload_dir . $safe_file_name;
    
    if(move_uploaded_file($file['tmp_name'], $file_path)) {
        return [
            'success' => true, 
            'file_path' => 'uploads/media/' . $safe_file_name,
            'file_name' => $safe_file_name,
            'original_name' => $file['name']
        ];
    } else {
        return ['success' => false, 'error' => 'Dosya yüklenirken hata oluştu.'];
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
            --border-color: #dee2e6;
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
            border-bottom: 1px solid var(--border-color);
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
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid var(--border-color);
            padding: 1.25rem;
            font-weight: 600;
        }
        
        .media-thumbnail {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            transition: transform 0.3s ease;
        }
        
        .media-item:hover .media-thumbnail {
            transform: scale(1.05);
        }
        
        .file-icon {
            font-size: 3rem;
            color: var(--gray-color);
        }
        
        .upload-area {
            border: 2px dashed var(--border-color);
            border-radius: 12px;
            padding: 3rem 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: var(--light-color);
        }
        
        .upload-area:hover {
            border-color: var(--primary-color);
            background: rgba(102, 126, 234, 0.05);
        }
        
        .upload-area.dragover {
            border-color: var(--primary-color);
            background: rgba(102, 126, 234, 0.1);
        }
        
        .media-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        
        .media-item {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .media-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        
        .media-actions {
            position: absolute;
            top: 10px;
            right: 10px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .media-item:hover .media-actions {
            opacity: 1;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .badge-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        }
        
        .badge-success {
            background: var(--success-color);
        }
        
        .badge-warning {
            background: var(--warning-color);
            color: var(--dark-color);
        }
        
        .badge-danger {
            background: var(--danger-color);
        }
        
        .badge-info {
            background: var(--info-color);
        }
        
        .badge-secondary {
            background: var(--gray-color);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .media-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }
        }
        
        .list-view .media-item {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .list-view .media-thumbnail {
            width: 80px;
            height: 80px;
            flex-shrink: 0;
        }
        
        .list-view .media-actions {
            position: static;
            opacity: 1;
            margin-left: auto;
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
                    <a class="nav-link active" href="media.php">
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
            <h1 class="h3">Medya Yönetimi</h1>
            <div class="stat-badge">
                <span class="badge badge-primary"><?php echo count($media_files); ?> dosya</span>
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

        <!-- Dosya Yükleme -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-cloud-upload-alt me-2"></i>Dosya Yükle
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data" id="uploadForm">
                    <div class="upload-area" onclick="document.getElementById('media_files').click()">
                        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                        <h5>Dosyaları sürükleyip bırakın veya tıklayarak seçin</h5>
                        <p class="text-muted mb-3">
                            PNG, JPG, GIF, PDF, MP4, MP3, ZIP dosyalarını yükleyebilirsiniz<br>
                            Maximum dosya boyutu: 10MB
                        </p>
                        <button type="button" class="btn btn-primary">
                            <i class="fas fa-folder-open me-2"></i>Dosya Seç
                        </button>
                    </div>
                    <input type="file" id="media_files" name="media_files[]" 
                           multiple accept="image/*,video/*,audio/*,.pdf,.zip,.rar" 
                           style="display: none;" onchange="handleFileSelect(this)">
                    
                    <!-- Seçilen dosyalar listesi -->
                    <div id="fileList" class="mt-3" style="display: none;">
                        <h6>Seçilen Dosyalar:</h6>
                        <div id="selectedFiles" class="mb-3"></div>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-upload me-2"></i>Dosyaları Yükle
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Medya Kitaplığı -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-images me-2"></i>Medya Kitaplığı
                </h5>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-secondary active" data-view="grid">
                        <i class="fas fa-th"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-view="list">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <?php if(empty($media_files)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Henüz dosya bulunmuyor</h5>
                        <p class="text-muted">Yukarıdan ilk dosyalarınızı yükleyin.</p>
                    </div>
                <?php else: ?>
                    <!-- Filtreler -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="searchMedia" placeholder="Dosya adında ara...">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="filterCategory">
                                <option value="">Tüm Kategoriler</option>
                                <option value="Öne Çıkan Görseller">Öne Çıkan Görseller</option>
                                <option value="İçerik Resimleri">İçerik Resimleri</option>
                                <option value="Genel Medya">Genel Medya</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="sortMedia">
                                <option value="newest">En Yeni</option>
                                <option value="oldest">En Eski</option>
                                <option value="name">İsim (A-Z)</option>
                                <option value="size">Boyut</option>
                            </select>
                        </div>
                    </div>

                    <!-- Medya Grid -->
                    <div class="media-grid" id="mediaGrid">
                        <?php foreach($media_files as $file): ?>
                            <div class="media-item" data-category="<?php echo $file['category']; ?>" 
                                 data-name="<?php echo strtolower($file['name']); ?>"
                                 data-date="<?php echo $file['modified']; ?>"
                                 data-size="<?php echo $file['size']; ?>">
                                <div class="position-relative">
                                    <?php if(strpos($file['type'], 'image/') === 0): ?>
                                        <img src="<?php echo $file['url']; ?>" 
                                             alt="<?php echo $file['name']; ?>" 
                                             class="media-thumbnail"
                                             onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjE1MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGVlMmU2Ii8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPlJlc2ltIFl1a2xlbmVtZWRpPC90ZXh0Pjwvc3ZnPg=='">
                                    <?php elseif(strpos($file['type'], 'video/') === 0): ?>
                                        <div class="media-thumbnail bg-light d-flex align-items-center justify-content-center">
                                            <i class="fas fa-file-video file-icon text-primary"></i>
                                        </div>
                                    <?php elseif(strpos($file['type'], 'audio/') === 0): ?>
                                        <div class="media-thumbnail bg-light d-flex align-items-center justify-content-center">
                                            <i class="fas fa-file-audio file-icon text-success"></i>
                                        </div>
                                    <?php elseif($file['type'] === 'application/pdf'): ?>
                                        <div class="media-thumbnail bg-light d-flex align-items-center justify-content-center">
                                            <i class="fas fa-file-pdf file-icon text-danger"></i>
                                        </div>
                                    <?php else: ?>
                                        <div class="media-thumbnail bg-light d-flex align-items-center justify-content-center">
                                            <i class="fas fa-file file-icon text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="media-actions">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-light" 
                                                    onclick="copyToClipboard('<?php echo $file['url']; ?>')"
                                                    title="URL'yi Kopyala">
                                                <i class="fas fa-link"></i>
                                            </button>
                                            <a href="<?php echo $file['url']; ?>" 
                                               target="_blank" class="btn btn-sm btn-light" title="Görüntüle">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-light" 
                                                    onclick="confirmDelete('<?php echo $file['path']; ?>')"
                                                    title="Sil">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-2">
                                    <h6 class="media-filename mb-1" title="<?php echo $file['name']; ?>">
                                        <?php echo strlen($file['name']) > 20 ? substr($file['name'], 0, 20) . '...' : $file['name']; ?>
                                    </h6>
                                    <div class="small text-muted">
                                        <div><?php echo formatFileSize($file['size']); ?></div>
                                        <div><?php echo date('d/m/Y H:i', $file['modified']); ?></div>
                                        <div class="badge bg-secondary"><?php echo $file['category']; ?></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Dosya seçimi
        function handleFileSelect(input) {
            const fileList = document.getElementById('fileList');
            const selectedFiles = document.getElementById('selectedFiles');
            
            if(input.files.length > 0) {
                selectedFiles.innerHTML = '';
                
                for(let i = 0; i < input.files.length; i++) {
                    const file = input.files[i];
                    const fileItem = document.createElement('div');
                    fileItem.className = 'alert alert-light d-flex justify-content-between align-items-center';
                    fileItem.innerHTML = `
                        <div>
                            <strong>${file.name}</strong>
                            <small class="text-muted">(${formatBytes(file.size)})</small>
                        </div>
                        <span class="badge bg-secondary">${file.type || 'Bilinmeyen'}</span>
                    `;
                    selectedFiles.appendChild(fileItem);
                }
                
                fileList.style.display = 'block';
            } else {
                fileList.style.display = 'none';
            }
        }

        // Dosya boyutunu formatla
        function formatBytes(bytes, decimals = 2) {
            if (bytes === 0) return '0 Bytes';
            
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }

        // Sürükle bırak özelliği
        const uploadArea = document.querySelector('.upload-area');
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, unhighlight, false);
        });
        
        function highlight() {
            uploadArea.classList.add('dragover');
        }
        
        function unhighlight() {
            uploadArea.classList.remove('dragover');
        }
        
        uploadArea.addEventListener('drop', handleDrop, false);
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            document.getElementById('media_files').files = files;
            handleFileSelect(document.getElementById('media_files'));
        }

        // URL kopyalama
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('URL panoya kopyalandı!');
            }, function(err) {
                console.error('Kopyalama hatası: ', err);
            });
        }

        // Silme onayı
        function confirmDelete(filePath) {
            if(confirm('Bu dosyayı silmek istediğinizden emin misiniz? Bu işlem geri alınamaz!')) {
                window.location.href = 'media.php?delete=' + encodeURIComponent(filePath);
            }
        }

        // Filtreleme ve arama
        document.getElementById('searchMedia').addEventListener('input', filterMedia);
        document.getElementById('filterCategory').addEventListener('change', filterMedia);
        document.getElementById('sortMedia').addEventListener('change', sortMedia);
        
        function filterMedia() {
            const searchTerm = document.getElementById('searchMedia').value.toLowerCase();
            const categoryFilter = document.getElementById('filterCategory').value;
            const mediaItems = document.querySelectorAll('.media-item');
            
            mediaItems.forEach(item => {
                const name = item.getAttribute('data-name');
                const category = item.getAttribute('data-category');
                
                const nameMatch = name.includes(searchTerm);
                const categoryMatch = !categoryFilter || category === categoryFilter;
                
                if(nameMatch && categoryMatch) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }
        
        function sortMedia() {
            const sortBy = document.getElementById('sortMedia').value;
            const mediaGrid = document.getElementById('mediaGrid');
            const mediaItems = Array.from(document.querySelectorAll('.media-item'));
            
            mediaItems.sort((a, b) => {
                switch(sortBy) {
                    case 'newest':
                        return b.getAttribute('data-date') - a.getAttribute('data-date');
                    case 'oldest':
                        return a.getAttribute('data-date') - b.getAttribute('data-date');
                    case 'name':
                        return a.getAttribute('data-name').localeCompare(b.getAttribute('data-name'));
                    case 'size':
                        return b.getAttribute('data-size') - a.getAttribute('data-size');
                    default:
                        return 0;
                }
            });
            
            // Öğeleri yeniden sırala
            mediaItems.forEach(item => mediaGrid.appendChild(item));
        }

        // Görünüm değiştirme
        document.querySelectorAll('[data-view]').forEach(btn => {
            btn.addEventListener('click', function() {
                const view = this.getAttribute('data-view');
                
                // Butonları güncelle
                document.querySelectorAll('[data-view]').forEach(b => {
                    b.classList.remove('active');
                });
                this.classList.add('active');
                
                // Görünümü değiştir
                const mediaGrid = document.getElementById('mediaGrid');
                if(view === 'list') {
                    mediaGrid.style.gridTemplateColumns = '1fr';
                    mediaGrid.classList.add('list-view');
                } else {
                    mediaGrid.style.gridTemplateColumns = 'repeat(auto-fill, minmax(200px, 1fr))';
                    mediaGrid.classList.remove('list-view');
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>