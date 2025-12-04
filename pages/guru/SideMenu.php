<?php

// File: SideMenu.php


session_start();

// CEK APAKAH USER SUDAH LOGIN (menggunakan admin_logged_in)
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../../login.php");
    exit;
}

// CEK APAKAH USER ADALAH GURU (role 'user')
if ($_SESSION['admin_role'] !== 'user') {
    header("Location: ../../login.php");
    exit;
}

// CEK APAKAH USER ADALAH GURU YANG VALID
if (!isset($_SESSION['is_guru']) || $_SESSION['is_guru'] !== true) {
    header("Location: ../../login.php");
    exit;
}
// CEGAH CACHING
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");


// CEGAH CACHING
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Guru - BK Digital</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../../assets/css/sidebar.css">
 <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    /* Additional styles for profile dropdown */
    .profile-dropdown {
        position: relative;
        display: inline-block;
    }
    
    .dropdown-menu-custom {
        border: none;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        padding: 10px 0;
        min-width: 200px;
    }
    
    .dropdown-item-custom {
        padding: 10px 20px;
        transition: all 0.3s ease;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
    }
    
    .dropdown-item-custom:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
    }
    
    .dropdown-item-custom.text-danger:hover {
        background-color: #ffe6e6;
    }
    
    .dropdown-divider-custom {
        margin: 8px 0;
        border-top: 1px solid #e9ecef;
    }
</style>

</head>
<body>

<nav class="navbar navbar-custom">
    <span class="h5 mb-0 fw-bolder">BK Digital</span>
    
    <!-- Profile Dropdown -->
    <div class="profile-dropdown">
        <button class="btn btn-light rounded-circle border border-black" id="profileBtn">
            <i class="fas fa-user"></i>
        </button>
        <div class="dropdown-menu-custom" id="profileDropdown" style="display: none; position: absolute; right: 0; top: 100%; z-index: 1000; background: white;">
            <div class="px-3 py-2 border-bottom">
                <div class="fw-bold"><?php echo $_SESSION['admin_name'] ?? 'SuperAdmin'; ?></div>
                <small class="text-muted"><?php echo $_SESSION['admin_role'] ?? 'superadmin'; ?></small>
            </div>
            <div class="p-2">
                <div class="dropdown-divider-custom"></div>
                <button class="dropdown-item-custom text-danger" id="logoutBtn">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </button>
            </div>
        </div>
    </div>
</nav>

<aside class="sidebar" id="sidebar">
    <button class="sidebar-minimize" id="sidebarMinimize">
        <i class="fas fa-chevron-left fs-3"></i>
    </button>
    <ul class="sidebar-menu">
        <li>
            <a href="#" class="active fw-bolder my-2" data-file="Dashboard.php">
               <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-pie-chart-fill" viewBox="0 0 16 16">
                    <path d="M15.985 8.5H8.207l-5.5 5.5a8 8 0 0 0 13.277-5.5zM2 13.292A8 8 0 0 1 7.5.015v7.778zM8.5.015V7.5h7.485A8 8 0 0 0 8.5.015"/>
                </svg>
                <span class="ps-2">Dashboard</span>
            </a>
        </li>
        <li>
            <a href="#" class="fw-bolder my-2" data-file="manajemenpengaduan.php">
               <i class="fas fa-headset" style="font-size: 24px;"></i></i><span class="ps-2">Pengaduan</span>
            </a>
        </li>
        <li>
            <a href="#" class="fw-bolder my-2" data-file="manajemen jadwal konseling.php">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-calendar-week" viewBox="0 0 16 16">
  <path d="M11 6.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm-3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm-5 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5z"/>
  <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
</svg>
                <span class="ps-2">Jadwal Konseling</span>
            </a>
        </li>
        <li>
            <a href="#" class="fw-bolder my-2" data-file="Laporankonseling.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-journals" viewBox="0 0 16 16">
  <path d="M5 0h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2 2 2 0 0 1-2 2H3a2 2 0 0 1-2-2h1a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1H1a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v9a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1H3a2 2 0 0 1 2-2"/>
  <path d="M1 6v-.5a.5.5 0 0 1 1 0V6h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V9h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 2.5v.5H.5a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1H2v-.5a.5.5 0 0 0-1 0"/>
</svg>
                <span class="ps-2">Laporan Konseling</span>
            </a>
        </li>
         <li>
            <a href="#" class="fw-bolder my-2" data-file="hasiltes.php">
               <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-journal-text" viewBox="0 0 16 16">
  <path d="M5 10.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5m0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5"/>
  <path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2"/>
  <path d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1z"/>
</svg>
                <span class="ps-2">Hasil Test</span>
            </a>
        </li>
    </ul>
</aside>

<main id="mainContent">
    <div id="contentArea">
        <h2>Selamat Datang di Dashboard</h2>
        <p>Pilih menu di sidebar untuk melihat konten.</p>
    </div>
</main>

<script>
const sidebar = document.getElementById('sidebar');
const sidebarMinimize = document.getElementById('sidebarMinimize');
const mainContent = document.getElementById('mainContent');
const contentArea = document.getElementById('contentArea');
const menuLinks = document.querySelectorAll('.sidebar-menu a');
const profileBtn = document.getElementById('profileBtn');
const profileDropdown = document.getElementById('profileDropdown');
const logoutBtn = document.getElementById('logoutBtn');

// Toggle sidebar
sidebarMinimize.addEventListener('click', () => {
    sidebar.classList.toggle('minimized');
    mainContent.classList.toggle('minimized');
});

// Toggle profile dropdown
profileBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    profileDropdown.style.display = profileDropdown.style.display === 'block' ? 'none' : 'block';
});

// Close dropdown when clicking outside
document.addEventListener('click', (e) => {
    if (!profileBtn.contains(e.target) && !profileDropdown.contains(e.target)) {
        profileDropdown.style.display = 'none';
    }
});

// Handle logout - DIPERBAIKI
// Handle logout - VERSI LEBIH ROBUST
logoutBtn.addEventListener('click', () => {
    if (confirm('Apakah Anda yakin ingin logout?')) {
        // Tampilkan loading state
        const originalText = logoutBtn.innerHTML;
        logoutBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Logging out...';
        logoutBtn.disabled = true;
        
        // Force redirect dengan parameter cache bust
        setTimeout(() => {
            window.location.href = '../../includes/logout.php?nocache=' + Date.now();
        }, 500);
    }
});
// Load content (FUNGSI INI DIMODIFIKASI)
async function loadContent(file) {
    try {
        // CLEANUP: Hentikan sistem sebelumnya jika ada
        if (window.logSystem && typeof window.logSystem.destroy === 'function') {
            window.logSystem.destroy();
        }
        
        const res = await fetch(file);
        const html = await res.text();
        contentArea.innerHTML = html;
        window.scrollTo({ top: 0, behavior: 'smooth' });

        // Init sistem yang sesuai
        if (file.includes('manajemenpengaduan.php')) {
            if (typeof initManajemenPengaduan === 'function') {
                initManajemenPengaduan();
            }
        }
        else if (file.includes('manajemen jadwal konseling.php')) {
      

            // Gunakan singleton instance
            if (typeof initManajemenJadwalKonseling === 'function') {
                initManajemenJadwalKonseling();
            }
        }
        else if (file.includes('Laporankonseling.php')) {
      

            // Gunakan singleton instance
            if (typeof initManajemenLaporanKonseling === 'function') {
                initManajemenLaporanKonseling();
            }
        }

    } catch (error) {
        console.error('Error loading content:', error);
        contentArea.innerHTML = `<div class="alert alert-danger">Gagal memuat konten: ${file}</div>`;
    }
}

// Set active & load on click
menuLinks.forEach(link => {
    link.addEventListener('click', e => {
        e.preventDefault();
        menuLinks.forEach(l => l.classList.remove('active'));
        link.classList.add('active');
        loadContent(link.dataset.file.trim());
    });
});

// Load default content
document.addEventListener('DOMContentLoaded', () => {
    const defaultLink = document.querySelector('.sidebar-menu a.active');
    if (defaultLink) loadContent(defaultLink.dataset.file.trim());
});

// Hapus fungsi loadPage() yang tidak terpakai
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="../../includes/js/guru/pengaduan.js"></script>
<script src="../../includes/js/guru/JadwalKonseling.js"></script>
<script src="../../includes/js/guru/LaporanKonseling.js"></script>
</body>
</html>