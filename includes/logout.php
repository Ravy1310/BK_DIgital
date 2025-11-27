<?php
// File: includes/logout.php

// Pastikan tidak ada output sebelum header
if (ob_get_length()) {
    ob_end_clean();
}

// Mulai session
session_start();

// HAPUS SEMUA DATA SESSION
$_SESSION = array();

// HAPUS SESSION COOKIE
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 3600,
        $params["path"], 
        $params["domain"], 
        $params["secure"], 
        $params["httponly"]
    );
}

// HANCURKAN SESSION
session_destroy();

// HEADER UNTUK CEGAH CACHING
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// REDIRECT KE LOGIN DENGAN PESAN SUKSES
header("Location: ../login.php?logout=success");
exit();
?>