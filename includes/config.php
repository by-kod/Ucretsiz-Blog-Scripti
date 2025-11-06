<?php
// includes/config.php

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'muhenxju_deneme');
define('DB_USER', 'muhenxju_deneme');
define('DB_PASS', 'Deneme123.!');

// Site Configuration - Varsayılan değerler (sadece bir kez tanımla)
if (!defined('SITE_NAME')) define('SITE_NAME', 'Merhaba Blog Scripti');
if (!defined('SITE_URL')) define('SITE_URL', 'https://siteadresiniz.com/');
if (!defined('SITE_DESCRIPTION')) define('SITE_DESCRIPTION', 'Merhaba Blog Scripti');
if (!defined('SITE_KEYWORDS')) define('SITE_KEYWORDS', 'blog, script, ücretsiz');

// includes/config.php dosyasına bu satırı ekleyin:
define('SITE_EMAIL', 'mail@blog.blog');

// Security
define('SECRET_KEY', 'memur-blog-secret-key-2024');

// Session start
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Error reporting (development)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// YENİ: HTML entity decode fonksiyonu
function decodeHtml($data) {
    if($data === null) return '';
    return htmlspecialchars_decode(trim($data), ENT_QUOTES | ENT_HTML5);
}

// GÜNCELLENMİŞ: sanitize fonksiyonu - sadece güvenlik için
function sanitize($data) {
    if($data === null) return '';
    $data = trim($data);
    $data = stripslashes($data);
    // Sadece XSS koruması için, HTML entity'lere dönüştürme
    return $data;
}

// YENİ: Form display için güvenli fonksiyon
function displaySafe($data) {
    if($data === null) return '';
    // HTML entity'leri decode et ve sadece temel güvenlik için encode et
    $data = htmlspecialchars_decode(trim($data), ENT_QUOTES | ENT_HTML5);
    return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

// Safe array value getter
function getSafe($array, $key, $default = '') {
    return isset($array[$key]) ? $array[$key] : $default;
}

// Slug oluşturma fonksiyonu - GÜNCELLENMİŞ
function createSlug($text) {
    if(empty($text)) return '';
    
    // HTML entity'leri decode et
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    
    // Türkçe karakterleri değiştir
    $text = str_replace(
        ['ı', 'ğ', 'ü', 'ş', 'ö', 'ç', 'İ', 'Ğ', 'Ü', 'Ş', 'Ö', 'Ç', ' '],
        ['i', 'g', 'u', 's', 'o', 'c', 'i', 'g', 'u', 's', 'o', 'c', '-'],
        $text
    );
    
    // Tüm harfleri küçült
    $text = strtolower($text);
    
    // Özel karakterleri ve gereksiz boşlukları temizle
    $text = preg_replace('/[^a-z0-9\-]/', '', $text);
    
    // Birden fazla tireyi tek tireye çevir
    $text = preg_replace('/-+/', '-', $text);
    
    // Baştaki ve sondaki tireleri temizle
    $text = trim($text, '-');
    
    // Eğer slug boşsa, timestamp kullan
    if (empty($text)) {
        $text = 'category-' . time();
    }
    
    return $text;
}

// Database connection
try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Ayarları JSON dosyasından yükle
$settings_file = __DIR__ . '/settings.json';
$default_settings = [
    'site' => [
        'name' => SITE_NAME,
        'description' => SITE_DESCRIPTION,
        'url' => SITE_URL,
        'email' => 'mail@blog.blog',
        'language' => 'tr',
        'timezone' => 'Europe/Istanbul',
        'posts_per_page' => 10,
        'maintenance_mode' => false
    ],
    'social' => [
        'facebook' => '',
        'twitter' => '',
        'instagram' => '',
        'youtube' => '',
        'linkedin' => ''
    ],
    'seo' => [
        'meta_description' => SITE_DESCRIPTION,
        'meta_keywords' => SITE_KEYWORDS,
        'google_analytics' => '',
        'google_site_verification' => '',
        'bing_verification' => ''
    ],
    'comments' => [
        'enabled' => true,
        'approval_required' => true,
        'max_length' => 1000,
        'nested_comments' => true,
        'nesting_level' => 3
    ],
    'system' => [
        'cache_enabled' => true,
        'cache_duration' => 3600,
        'image_compression' => true,
        'max_upload_size' => 5,
        'allowed_file_types' => 'jpg,jpeg,png,gif,pdf,doc,docx'
    ]
];

// JSON ayarlarını yükle
if(file_exists($settings_file)) {
    $json_settings = json_decode(file_get_contents($settings_file), true);
    if($json_settings) {
        $settings = array_replace_recursive($default_settings, $json_settings);
    } else {
        $settings = $default_settings;
        // JSON dosyasını oluştur
        file_put_contents($settings_file, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
} else {
    $settings = $default_settings;
    // JSON dosyasını oluştur
    if(!is_dir(dirname($settings_file))) {
        mkdir(dirname($settings_file), 0755, true);
    }
    file_put_contents($settings_file, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Global settings değişkeni
$GLOBALS['site_settings'] = $settings;

// Ayarları almak için fonksiyon
function getSetting($category, $key = null, $default = null) {
    global $site_settings;
    
    if($key === null) {
        return isset($site_settings[$category]) ? $site_settings[$category] : $default;
    }
    
    return isset($site_settings[$category][$key]) ? $site_settings[$category][$key] : $default;
}

// Sosyal medya linklerini almak için fonksiyon
function getSocialLink($platform) {
    return getSetting('social', $platform, '');
}

// SEO ayarlarını almak için fonksiyon
function getSeoSetting($key) {
    return getSetting('seo', $key, '');
}

// Site ayarlarını almak için fonksiyon
function getSiteSetting($key) {
    return getSetting('site', $key, '');
}
?>