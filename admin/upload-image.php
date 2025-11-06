<?php
require_once '../includes/config.php';

// Giriş kontrolü
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Forbidden']);
    exit;
}

// Upload dizini
$upload_dir = '../uploads/images/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Quill Editor image upload
if(isset($_FILES['image'])) {
    $file = $_FILES['image'];
    
    // Dosya kontrolü
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if(!in_array($file['type'], $allowed_types)) {
        echo json_encode(['success' => false, 'error' => 'Sadece JPEG, PNG, GIF ve WebP formatları desteklenir.']);
        exit;
    }
    
    if($file['size'] > $max_size) {
        echo json_encode(['success' => false, 'error' => 'Dosya boyutu 5MB\'dan küçük olmalıdır.']);
        exit;
    }
    
    // Dosya adını güvenli hale getir
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $file_name = uniqid() . '_' . time() . '.' . $file_extension;
    $file_path = $upload_dir . $file_name;
    
    if(move_uploaded_file($file['tmp_name'], $file_path)) {
        // Başarılı upload
        $image_url = SITE_URL . '/uploads/images/' . $file_name;
        echo json_encode(['success' => true, 'url' => $image_url]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Dosya yüklenirken hata oluştu.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Dosya bulunamadı.']);
}
?>