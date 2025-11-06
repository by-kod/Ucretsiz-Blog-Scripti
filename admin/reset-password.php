<?php
require_once 'includes/config.php';

$username = 'admin';
$new_password = 'yeni_sifreniz_123';

$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ? AND role = 'admin'");
$stmt->execute([$hashed_password, $username]);

echo "Şifre başarıyla güncellendi: " . $new_password;
?>