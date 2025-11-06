<?php
require_once '../includes/config.php';

// Giriş kontrolü
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    die('Forbidden');
}

// Upload dizini
$upload_dir = '../uploads/images/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// CKEditor image upload
if(isset($_FILES['upload'])) {
    $file = $_FILES['upload'];
    
    // Dosya kontrolü
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if(!in_array($file['type'], $allowed_types)) {
        echo '<script>alert("Sadece JPEG, PNG, GIF ve WebP formatları desteklenir.");</script>';
        exit;
    }
    
    if($file['size'] > $max_size) {
        echo '<script>alert("Dosya boyutu 5MB\'dan küçük olmalıdır.");</script>';
        exit;
    }
    
    // Dosya adını güvenli hale getir
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $file_name = uniqid() . '_' . time() . '.' . $file_extension;
    $file_path = $upload_dir . $file_name;
    
    if(move_uploaded_file($file['tmp_name'], $file_path)) {
        // Başarılı upload - CKEditor formatında cevap
        $image_url = SITE_URL . '/uploads/images/' . $file_name;
        $response = [
            'uploaded' => 1,
            'fileName' => $file_name,
            'url' => $image_url
        ];
        echo json_encode($response);
    } else {
        echo '<script>alert("Dosya yüklenirken hata oluştu.");</script>';
    }
} else {
    echo '<script>alert("Dosya bulunamadı.");</script>';
}
?>