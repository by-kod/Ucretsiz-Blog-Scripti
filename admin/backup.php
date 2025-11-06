<?php
require_once '../includes/config.php';

// Giriş kontrolü
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    redirect('login.php');
}

// Yedekleme işlemi
if(isset($_GET['action']) && $_GET['action'] == 'download') {
    backupDatabase();
}

function backupDatabase() {
    global $pdo;
    
    $backup_dir = '../backups/';
    if (!file_exists($backup_dir)) {
        mkdir($backup_dir, 0755, true);
    }
    
    $backup_file = $backup_dir . 'backup_' . date('Y-m-d_H-i-s') . '.sql';
    
    // Basit bir yedekleme - tüm tabloları dışa aktar
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    $backup_content = "-- Database Backup\n";
    $backup_content .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
    $backup_content .= "-- Host: " . DB_HOST . "\n";
    $backup_content .= "-- Database: " . DB_NAME . "\n\n";
    
    foreach($tables as $table) {
        // Tablo yapısı
        $backup_content .= "--\n-- Table structure for table `$table`\n--\n";
        $backup_content .= "DROP TABLE IF EXISTS `$table`;\n";
        
        $create_table = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
        $backup_content .= $create_table['Create Table'] . ";\n\n";
        
        // Tablo verileri
        $backup_content .= "--\n-- Dumping data for table `$table`\n--\n";
        
        $rows = $pdo->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
        if(count($rows) > 0) {
            $backup_content .= "INSERT INTO `$table` VALUES ";
            
            $values = [];
            foreach($rows as $row) {
                $row_values = array_map(function($value) use ($pdo) {
                    if($value === null) return 'NULL';
                    return $pdo->quote($value);
                }, $row);
                
                $values[] = "(" . implode(", ", $row_values) . ")";
            }
            
            $backup_content .= implode(", ", $values) . ";\n\n";
        }
    }
    
    if(file_put_contents($backup_file, $backup_content)) {
        // Dosyayı indir
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($backup_file) . '"');
        header('Content-Length: ' . filesize($backup_file));
        readfile($backup_file);
        
        // Geçici dosyayı sil
        unlink($backup_file);
        exit;
    } else {
        $_SESSION['error_message'] = "Yedekleme oluşturulurken hata oluştu.";
        redirect('settings.php');
    }
}