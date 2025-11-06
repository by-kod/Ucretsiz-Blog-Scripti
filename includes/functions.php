<?php
// ... diğer fonksiyonlar ...

/**
 * Güvenli metin temizleme fonksiyonu (HTML entity korumalı)
 */
function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    
    // HTML entity'leri koru
    $input = htmlspecialchars_decode($input, ENT_QUOTES);
    
    // Temizleme işlemleri
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    
    return $input;
}

/**
 * Slug oluşturma fonksiyonu (Türkçe karakter desteği ile)
 */
function createSlug($text) {
    // HTML entity'leri decode et
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    
    // Türkçe karakter dönüşümü
    $text = str_replace(
        ['ı', 'ğ', 'ü', 'ş', 'ö', 'ç', 'İ', 'Ğ', 'Ü', 'Ş', 'Ö', 'Ç'],
        ['i', 'g', 'u', 's', 'o', 'c', 'i', 'g', 'u', 's', 'o', 'c'],
        $text
    );
    
    // Slug oluşturma
    $text = preg_replace('/[^a-zA-Z0-9\s-]/', '', $text);
    $text = strtolower(trim($text));
    $text = preg_replace('/[\s-]+/', '-', $text);
    $text = preg_replace('/^-+|-+$/', '', $text);
    
    return $text;
}

/**
 * Güvenli çıktı fonksiyonu (HTML entity'leri korur)
 */
function safeOutput($text) {
    return htmlspecialchars_decode($text, ENT_QUOTES | ENT_HTML5);
}

// ... diğer fonksiyonlar ...
?>