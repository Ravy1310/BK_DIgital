<?php
// File: login.php

// MULAI SESSION
session_start();

// CEK JIKA ADA PARAMETER LOGOUT, HAPUS SESSION
if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
    // Hapus semua session data
    $_SESSION = [];
    session_destroy();
    
    // Hapus session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
}

// HANYA REDIRECT JIKA BENAR-BENAR MASIH LOGIN
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    // VERIFIKASI ULANG DATA SESSION
    if (isset($_SESSION['admin_role'])) {
        if ($_SESSION['admin_role'] === 'superadmin') {
            header("Location: pages/Developer/sideMenu_dev.php");
            exit;
        } else if ($_SESSION['admin_role'] === 'admin') {
            header("Location: pages/admin/sideMenu_admin.php");
            exit;
        } else if ($_SESSION['admin_role'] === 'user') {
            header("Location: pages/guru/SideMenu.php");
            exit;
        }
    }
}

// JIKA ADA SESSION TAPI TIDAK VALID, HAPUS
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] !== true) {
    session_destroy();
}
// CEGAH CACHING
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Tampilkan pesan logout jika ada

?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>Login BK Digital - SMA AL-ISLAM KRIAN</title>

    <!-- CEGAH CACHING -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    
<style>
:root {
    --primary-blue: #004d9c;
    --off-white: #f5f5f5;
}

* {
    -webkit-tap-highlight-color: transparent;
}

body {
    font-family: 'Poppins', sans-serif;
    background-color: var(--off-white);
    overflow-x: hidden;
    min-height: 100vh;
}

/* --- KONTAINER UTAMA --- */
.container-fluid {
    min-height: 100vh;
}

.row.g-0 {
    min-height: 100vh;
}

/* --- LEFT PANEL ANIMASI --- */
.left-panel {
    background: linear-gradient(to top right, #002E6E, #0045B0, #0059D4) !important;
    background-size: 200% 200%;
    animation: gradientSlideIn 1.8s ease forwards, gradientMove 10s ease infinite;
    transform: translateX(-100%);
    opacity: 0;
    min-height: 100vh;
    padding: 2rem 1.5rem !important;
    position: relative;
    z-index: 1;
}

.left-panel * {
    opacity: 0;
    transform: translateX(-40px);
    animation: slideInLeft 1.2s ease forwards;
}

@keyframes gradientSlideIn {
    0% { transform: translateX(-100%); opacity: 0; }
    100% { transform: translateX(0); opacity: 1; }
}

@keyframes gradientMove {
    0% { background-position: 0% 100%; }
    50% { background-position: 100% 0%; }
    100% { background-position: 0% 100%; }
}

@keyframes slideInLeft {
    from { opacity: 0; transform: translateX(-40px); }
    to { opacity: 1; transform: translateX(0); }
}

.left-panel header { animation-delay: 0.8s; }
.left-panel main h2 { animation-delay: 1.1s; }
.left-panel main h6 { animation-delay: 1.3s; }

/* Header logo */
.left-panel header {
    position: absolute;
    top: 1.5rem;
    left: 1.5rem;
    display: flex;
    align-items: center;
    width: auto;
}

.left-panel header .me-2 {
    width: 50px;
    height: 50px;
    min-width: 50px;
    min-height: 50px;
}

.left-panel header div {
    max-width: calc(100% - 60px);
}

.left-panel header .small {
    font-size: 0.75rem;
}

.left-panel header h1 {
    font-size: 0.9rem;
    line-height: 1.2;
}

/* Konten utama left panel */
.left-panel main {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: flex-start;
    width: 100%;
    padding: 0 1rem;
    margin-top: 5rem;
}

.left-panel main h2 {
    font-size: 2rem;
    font-weight: 900;
    margin-bottom: 0.5rem;
    line-height: 1.2;
}

.left-panel main h6 {
    font-size: 1.25rem;
    font-weight: 500;
    margin-bottom: 0;
    opacity: 0.9;
}

/* --- RIGHT PANEL ANIMASI --- */
.right-panel {
    opacity: 0;
    transform: translate(50px, 30px);
    animation: fadeInRight 1.2s ease-out forwards;
    background-color: #f5f5f5 !important;
    min-height: 100vh;
    display: flex;
    align-items: center;
    padding: 2rem 1.5rem !important;
    position: relative;
    z-index: 1;
}

@keyframes fadeInRight {
    from { opacity: 0; transform: translate(50px, 30px); }
    to { opacity: 1; transform: translate(0,0); }
}

.right-panel form > * {
    opacity: 0;
    transform: translateY(20px);
    animation: fadeInUp 0.8s ease forwards;
}

.right-panel form > *:nth-child(1) { animation-delay: 0.3s; }
.right-panel form > *:nth-child(2) { animation-delay: 0.5s; }
.right-panel form > *:nth-child(3) { animation-delay: 0.7s; }
.right-panel form > *:nth-child(4) { animation-delay: 0.9s; }
.right-panel form > *:nth-child(5) { animation-delay: 1.1s; }

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Form styling */
.right-panel form {
    width: 100%;
    max-width: 420px;
    margin: 0 auto;
}

.right-panel h1.login-title {
    font-size: 2rem;
    font-weight: 900;
    margin-bottom: 2rem;
    color: #333;
}

/* Input groups */
.input-group {
    border-radius: 50px !important;
    overflow: hidden;
    border: 1px solid #e0e0e0;
    transition: all 0.3s ease;
}

.input-group:focus-within {
    border-color: var(--primary-blue);
    box-shadow: 0 0 0 3px rgba(0, 77, 156, 0.1);
}

.input-group-text {
    background-color: white;
    border: none;
    padding: 0.75rem 1rem;
}

.form-control {
    border: none;
    padding: 0.75rem 1rem;
    font-size: 1rem;
}

.form-control:focus {
    box-shadow: none;
    outline: none;
}

/* Labels */
.form-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #555;
    margin-bottom: 0.5rem;
}

/* Forgot password link */
.right-panel a.text-primary {
    font-size: 0.875rem;
    font-weight: 500;
}

/* Login button */
.btn-primary {
    background-color: var(--primary-blue);
    border-color: var(--primary-blue);
    transition: all .15s ease;
    padding: 0.75rem;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 50px;
    margin-top: 0.5rem;
}

.btn-primary:hover,
.btn-primary:focus {
    background-color: #003a7a;
    border-color: #003a7a;
    transform: translateY(-2px);
    box-shadow: 0 8px 18px rgba(0, 77, 156, 0.18);
}

.btn-primary:active {
    transform: translateY(0);
}

/* Alert messages */
.alert {
    border-radius: 12px;
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
}

.alert-success {
    background: linear-gradient(135deg, #d4edda, #c3e6cb);
    color: #155724;
    border: none;
}

.alert-danger {
    background: linear-gradient(135deg, #f8d7da, #f5c6cb);
    color: #721c24;
    border: none;
}

/* --- SHAPE DIVIDER --- */
.shapedividers_com-652 {
    overflow: hidden;
    position: relative;
}

.shapedividers_com-652::before {
    content: '';
    position: absolute;
    inset: -0.1vw;
    z-index: 3;
    pointer-events: none;
    background-repeat: no-repeat;
    background-size: 215px 225%;
    background-position: 100% 50%;
    background-image: url('data:image/svg+xml;charset=utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2.17 35.28" preserveAspectRatio="none"><path d="M1.16 0c-.8 3.17.4 7.29.56 10.04C1.89 12.8.25 19.3.42 22.71c.16 3.43.84 4.65.86 7.05.03 2.4-.88 5.52-.88 5.52h1.77V0z" fill="%23f5f5f5"/></svg>');
}

/* --- RESPONSIVE DESIGN --- */

/* Tablet Landscape & Desktop */
@media (min-width: 992px) {
    .left-panel header {
        top: 2rem;
        left: 2rem;
    }
    
    .left-panel header .me-2 {
        width: 60px;
        height: 60px;
        min-width: 60px;
        min-height: 60px;
    }
    
    .left-panel main h2 {
        font-size: 2.5rem;
    }
    
    .left-panel main h6 {
        font-size: 1.5rem;
    }
    
    .right-panel form {
        max-width: 480px;
    }
    
    .right-panel h1.login-title {
        font-size: 2.5rem;
    }
}

/* Tablet Portrait */
@media (max-width: 991.98px) and (min-width: 768px) {
    .left-panel,
    .right-panel {
        min-height: 50vh;
    }
    
    .left-panel {
        padding: 3rem 2rem !important;
    }
    
    .right-panel {
        padding: 3rem 2rem !important;
    }
    
    .right-panel form {
        max-width: 400px;
    }
}

/* Mobile Devices */
@media (max-width: 767.98px) {
    body {
        overflow-y: auto;
    }
    
    .row.g-0 {
        flex-direction: column;
        min-height: 100vh;
    }
    
    .left-panel,
    .right-panel {
        min-height: auto;
        width: 100%;
        padding: 2rem 1.5rem !important;
        animation: none;
        transform: none;
        opacity: 1;
    }
    
    /* Hapus shape divider di mobile */
    .shapedividers_com-652::before {
        display: none;
    }
    
    /* Left panel mobile */
    .left-panel {
        height: 40vh;
        min-height: 40vh;
        padding: 1.5rem !important;
        background: linear-gradient(to bottom right, #002E6E, #0045B0) !important;
        animation: gradientMove 10s ease infinite;
    }
    
    .left-panel header {
        position: relative;
        top: 0;
        left: 0;
        margin: 0;
        margin-bottom: 1.5rem;
        justify-content: center;
        width: 100%;
    }
    
    .left-panel main {
        margin-top: 0;
        align-items: center;
        text-align: center;
    }
    
    .left-panel main h2 {
        font-size: 1.75rem;
        text-align: center;
    }
    
    .left-panel main h6 {
        font-size: 1.1rem;
        text-align: center;
    }
    
    /* Right panel mobile */
    .right-panel {
        height: 60vh;
        min-height: 60vh;
        padding: 1.5rem !important;
        animation: none;
        transform: none;
        opacity: 1;
    }
    
    .right-panel form {
        max-width: 100%;
    }
    
    .right-panel h1.login-title {
        font-size: 1.75rem;
        margin-bottom: 1.5rem;
    }
    
    /* Input groups mobile */
    .input-group {
        border-radius: 12px !important;
    }
    
    .input-group-text {
        padding: 0.75rem 0.875rem;
    }
    
    .form-control {
        padding: 0.75rem 0.875rem;
        font-size: 16px; /* Prevent zoom on iOS */
    }
    
    .btn-primary {
        border-radius: 12px;
        padding: 0.875rem;
    }
    
    /* Animasi untuk mobile */
    .left-panel *,
    .right-panel form > * {
        animation: fadeInUp 0.8s ease forwards;
    }
    
    .left-panel header { animation-delay: 0.2s; }
    .left-panel main h2 { animation-delay: 0.4s; }
    .left-panel main h6 { animation-delay: 0.6s; }
    .right-panel form > *:nth-child(1) { animation-delay: 0.3s; }
    .right-panel form > *:nth-child(2) { animation-delay: 0.5s; }
    .right-panel form > *:nth-child(3) { animation-delay: 0.7s; }
    .right-panel form > *:nth-child(4) { animation-delay: 0.9s; }
    .right-panel form > *:nth-child(5) { animation-delay: 1.1s; }
}

/* Very small mobile devices */
@media (max-width: 375px) {
    .left-panel,
    .right-panel {
        padding: 1.25rem !important;
    }
    
    .left-panel header .me-2 {
        width: 45px;
        height: 45px;
        min-width: 45px;
        min-height: 45px;
    }
    
    .left-panel header .small {
        font-size: 0.7rem;
    }
    
    .left-panel header h1 {
        font-size: 0.8rem;
    }
    
    .left-panel main h2 {
        font-size: 1.5rem;
    }
    
    .left-panel main h6 {
        font-size: 1rem;
    }
    
    .right-panel h1.login-title {
        font-size: 1.5rem;
    }
    
    .form-label,
    .right-panel a.text-primary {
        font-size: 0.8rem;
    }
    
    .btn-primary {
        font-size: 0.9rem;
        padding: 0.75rem;
    }
}

/* Landscape mode for mobile */
@media (max-height: 600px) and (orientation: landscape) {
    .row.g-0 {
        flex-direction: row;
    }
    
    .left-panel,
    .right-panel {
        height: 100vh;
        min-height: 100vh;
    }
    
    .left-panel {
        width: 40%;
        padding: 1rem !important;
    }
    
    .right-panel {
        width: 60%;
        padding: 1rem !important;
    }
    
    .left-panel header {
        position: relative;
        top: 0;
        left: 0;
        margin-bottom: 1rem;
    }
    
    .left-panel main {
        margin-top: 0;
    }
    
    .left-panel main h2 {
        font-size: 1.5rem;
    }
    
    .left-panel main h6 {
        font-size: 1rem;
    }
    
    .right-panel h1.login-title {
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .mb-4, .mb-5 {
        margin-bottom: 0.75rem !important;
    }
}
</style>

</head>

<body>
<div class="container-fluid p-0">
  <div class="row g-0">

    <!-- LEFT PANEL -->
    <div class="col-12 col-lg-5 left-panel shapedividers_com-652 d-flex flex-column justify-content-start align-items-start text-white">
      <header class="d-flex align-items-center">
        <div class="me-2 d-flex justify-content-center align-items-center bg-white rounded-circle shadow-sm overflow-hidden">
          <img src="assets/image/logo sekolah.png" alt="Logo Sekolah" class="img-fluid" style="max-width: 100%; height: auto;">
        </div>
        <div>
          <p class="mb-0 small fw-semibold">SMA</p>
          <h1 class="h6 fw-bold mb-0">AL-ISLAM KRIAN</h1>
        </div>
      </header>

      <main class="flex-grow-1 d-flex flex-column justify-content-center align-items-start w-100">
        <h2 class="fw-bolder mb-2">Selamat Datang</h2>
        <h6 class="fw-medium">di BK Digital</h6>
      </main>
    </div>

    <!-- RIGHT PANEL -->
    <div class="col-12 col-lg-7 right-panel d-flex align-items-center justify-content-center">
      <form id="loginForm" class="w-100">
        <h1 class="display-6 fw-bolder mb-4 text-center login-title">Login</h1>

        <div id="statusMessage" class="alert d-none text-center rounded-3 mb-4"></div>


        <!-- Username -->
        <div class="mb-4">
          <label for="username" class="form-label small fw-semibold text-muted mb-1">Username</label>
          <div class="input-group shadow-sm">
            <span class="input-group-text"><i class="fas fa-user text-dark"></i></span>
            <input type="text" id="username" name="username" class="form-control border-0" placeholder="Username" required autocomplete="username">
          </div>
        </div>

        <!-- Password -->
        <div class="mb-4">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <label for="password" class="form-label small fw-semibold text-muted mb-0">Password</label>
            <a href="#" class="small text-decoration-none text-primary" onclick="handleForgotPassword(event)">Lupa password?</a>
          </div>
          <div class="input-group shadow-sm">
            <span class="input-group-text"><i class="fas fa-key text-dark"></i></span>
            <input type="password" id="password" name="password" class="form-control border-0" placeholder="Password" required autocomplete="current-password">
            <button type="button" class="input-group-text bg-white" id="togglePassword" style="cursor:pointer;">
              <i class="fa-solid fa-eye-slash text-dark"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-lg">Login</button>
      </form>
    </div>

  </div>
</div>

<script>
// Toggle password visibility
document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            if (icon.classList.contains('fa-eye-slash')) {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        });
    }
    
    // Handle forgot password
    window.handleForgotPassword = function(event) {
        event.preventDefault();
        alert('Silakan hubungi administrator untuk reset password.');
    };
    
    // Prevent zoom on iOS when focusing inputs
    document.addEventListener('touchstart', function() {}, {passive: true});
});
</script>

<script src="includes/js/login.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>