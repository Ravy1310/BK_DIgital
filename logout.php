<?php
// Mulai session untuk mengaksesnya
session_start();

// HAPUS CACHE HEADER UNTUK MENCEGAH BACK BUTTON
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

// Hapus semua variabel session
$_SESSION = array();

// Hancurkan session
session_destroy();

// Hapus cookie session jika ada
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect ke halaman login dengan parameter no-cache
header("Location: login.php?nocache=" . time());
exit;
?>