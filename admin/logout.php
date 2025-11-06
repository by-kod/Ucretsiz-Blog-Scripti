<?php
require_once '../includes/config.php';

// Oturumu temizle
session_destroy();

// Giriş sayfasına yönlendir
redirect('login.php');
?>