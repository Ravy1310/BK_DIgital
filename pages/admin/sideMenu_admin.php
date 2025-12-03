<?php
// TAMBAHKAN DI AWAL FILE
session_start();

// CEK APAKAH SUDAH LOGIN
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../../login.php");
    exit;
}

// CEK ROLE (admin atau superadmin bisa akses)
if ($_SESSION['admin_role'] !== 'admin' && $_SESSION['admin_role'] !== 'superadmin') {
    header("Location: ../../login.php?error=unauthorized");
    exit;
}

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
<title>Dashboard Admin - BK Digital</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../../assets/css/sidebar.css">

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

    #mainContent {
        margin-top: 20px !important;
        transition: all 0.3s ease;
        margin-left: 250px;
        padding: 20px;
    }

    #mainContent.minimized {
        margin-left: 70px;
    }
   

    /* Loading indicator */
    .loading {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid #f3f3f3;
        border-top: 3px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
</style>

</head>
<body>

<nav class="navbar navbar-custom">
    <span class="h5 mb-0 fw-bolder">BK Digital</span>
    
    <div class="profile-dropdown">
        <button class="btn btn-light rounded-circle border border-black" id="profileBtn">
            <i class="fas fa-user"></i>
        </button>
        <div class="dropdown-menu-custom" id="profileDropdown" style="display: none; position: absolute; right: 0; top: 100%; z-index: 1000; background: white;">
            <div class="px-3 py-2 border-bottom">
                <div class="fw-bold"><?php echo $_SESSION['admin_name'] ?? 'Admin'; ?></div>
                <small class="text-muted"><?php echo $_SESSION['admin_role'] ?? 'admin'; ?></small>
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
            <a href="#" class="active fw-bolder my-2 d-flex align-items-center" data-file="admin.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-pie-chart-fill" viewBox="0 0 16 16">
                    <path d="M15.985 8.5H8.207l-5.5 5.5a8 8 0 0 0 13.277-5.5zM2 13.292A8 8 0 0 1 7.5.015v7.778zM8.5.015V7.5h7.485A8 8 0 0 0 8.5.015"/>
                </svg>
                <span class="ps-2 fs-6">Dashboard</span>
            </a>
        </li>

        <li>
            <a href="#" class="fw-bolder my-2" data-file="kelola_Siswa.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-folder-fill" viewBox="0 0 16 16">
                    <path d="M9.828 3h3.982a2 2 0 0 1 1.992 2.181l-.637 7A2 2 0 0 1 13.174 14H2.825a2 2 0 0 1-1.991-1.819l-.637-7a2 2 0 0 1 .342-1.31L.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3m-8.322.12q.322-.119.684-.12h5.396l-.707-.707A1 1 0 0 0 6.172 2H2.5a1 1 0 0 0-1 .981z"/>
                </svg>
                <span class="ps-2">Kelola data Siswa</span>
            </a>
        </li>

        <li>
            <a href="#" class="fw-bolder my-2" data-file="Kelola-Guru.php"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-folder-fill" viewBox="0 0 16 16">
                    <path d="M9.828 3h3.982a2 2 0 0 1 1.992 2.181l-.637 7A2 2 0 0 1 13.174 14H2.825a2 2 0 0 1-1.991-1.819l-.637-7a2 2 0 0 1 .342-1.31L.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3m-8.322.12q.322-.119.684-.12h5.396l-.707-.707A1 1 0 0 0 6.172 2H2.5a1 1 0 0 0-1 .981z"/>
                </svg>
                <span class="ps-2">Kelola data Guru</span>
            </a>
        </li>
        
        <li>
            <a href="#" class="fw-bolder my-2" data-file="kelolaTes.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-journal-bookmark-fill" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M6 1h6v7a.5.5 0 0 1-.757.429L9 7.083 6.757 8.43A.5.5 0 0 1 6 8z"/>
                    <path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2"/>
                    <path d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1z"/>
                </svg>
                <span class="ps-2">Kelola Tes</span>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Simple state management
let currentPage = '';
let kelolaGuruHandler = null;
let kelolaSiswaHandler = null;
let kelolaTesHandler = null;
let kelolaSoalHandler = null;
let tambahTesHandler = null;
// DOM Elements
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
logoutBtn.addEventListener('click', () => {
    if (confirm('Apakah Anda yakin ingin logout?')) {
        const originalText = logoutBtn.innerHTML;
        logoutBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Logging out...';
        logoutBtn.disabled = true;
        
        // Redirect ke file logout.php yang benar
        setTimeout(() => {
            window.location.href = '../../includes/logout.php';
        }, 1000);
    }
});

// Cleanup function
function cleanupPage() {
    console.log('🧹 Cleaning up previous page...');
    
    // Remove Kelola Guru event handler if exists
    if (kelolaGuruHandler) {
        const container = document.getElementById('contentArea');
        if (container) {
            container.removeEventListener('click', kelolaGuruHandler);
        }
        kelolaGuruHandler = null;
    }
    
    // Remove Kelola Siswa event handler if exists - DIPERBAIKI
    if (kelolaSiswaHandler) {
        const container = document.getElementById('contentArea');
        if (container) {
            container.removeEventListener('click', kelolaSiswaHandler);
        }
        kelolaSiswaHandler = null;
    }
    if (kelolaTesHandler) {
        const container = document.getElementById('contentArea');
        if (container) {
            container.removeEventListener('click', kelolaTesHandler);
        }

        kelolaTesHandler = null;
    }
    if (kelolaSoalHandler) {
        const container = document.getElementById('contentArea');
        if (container) {
            container.removeEventListener('click', kelolaSoalHandler);
        }
        kelolaSoalHandler = null;
    }
   if (tambahTesHandler) { // TAMBAHKAN INI
        const container = document.getElementById('contentArea');
        if (container) {
            container.removeEventListener('click', tambahTesHandler);
        }
        tambahTesHandler = null;
    }
    

    
    // Remove any dynamically added scripts
    const dynamicScripts = document.querySelectorAll('script[data-dynamic="true"]');
    dynamicScripts.forEach(script => {
        script.remove();
        console.log('✅ Removed dynamic script:', script.src);
    });
    
    console.log('✅ Page cleanup completed');
}


// Load script dengan prevention untuk multiple loading
function loadScript(src) {
    return new Promise((resolve, reject) => {
        // Remove existing script first
        const existingScript = document.querySelector(`script[src="${src}"]`);
        if (existingScript) {
            existingScript.remove();
            console.log('✅ Removed existing script:', src);
        }
        
        const script = document.createElement('script');
        script.src = src;
        script.setAttribute('data-dynamic', 'true');
        
        script.onload = () => {
            console.log(`✅ ${src} loaded successfully`);
            resolve();
        };
        
        script.onerror = () => {
            console.error(`❌ Failed to load ${src}`);
            reject(new Error(`Failed to load ${src}`));
        };
        
        document.body.appendChild(script);
    });
}

// Load content dengan approach yang sederhana
async function loadContent(file) {
    try {
        console.log('🔄 Loading file:', file);
        
        // Cleanup previous page
        cleanupPage();
        
        // Tampilkan loading indicator
        contentArea.innerHTML = `
            <div class="text-center py-5">
                <div class="loading mb-3"></div>
                <p>Memuat ${file}...</p>
            </div>
        `;
        
        // Load HTML content
        const res = await fetch(file);
        if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
        }
        
        const html = await res.text();
        contentArea.innerHTML = html;
        currentPage = file;
        
        window.scrollTo({ top: 0, behavior: 'smooth' });

        // Handle Kelola Guru initialization
        if (file.includes('Kelola-Guru.php')) {
            console.log('🔄 Setting up Kelola Guru...');
            
            // Tunggu sebentar untuk memastikan DOM siap
            setTimeout(async () => {
                try {
                    await loadScript('../../includes/js/admin/kelola_Guru.js');
                    
                    // Initialize Kelola Guru
                    if (typeof window.initKelolaGuru === 'function') {
                        console.log('🚀 Initializing Kelola Guru...');
                        window.initKelolaGuru();
                    } else {
                        console.error('❌ initKelolaGuru function not found');
                    }
                } catch (error) {
                    console.error('❌ Error setting up Kelola Guru:', error);
                }
            }, 300);
        }
        // Handle Kelola Siswa initialization - DIPERBAIKI
        else if (file.includes('kelola_Siswa.php')) {
            console.log('🔄 Setting up Kelola Siswa...');
            
            setTimeout(async () => {
                try {
                    // Pastikan path konsisten
                    await loadScript('../../includes/js/admin/kelola_Siswa.js');
                    
                    if (typeof window.initKelolaSiswa === 'function') {
                        console.log('🚀 Initializing Kelola Siswa...');
                        window.initKelolaSiswa();
                    } else {
                        console.error('❌ initKelolaSiswa function not found');
                    }
                } catch (error) {
                    console.error('❌ Error setting up Kelola Siswa:', error);
                }
            }, 300); // ✅ FIXED: menggunakan angka, bukan variable yang tidak terdefinisi
        }
    

// Handle Kelola Tes initialization
else if (file.includes('kelolaTes.php')) {
    console.log('🔄 Setting up Kelola Tes...');
    
    setTimeout(() => {
        try {
        
            // Setup event handlers untuk tombol-tombol di kelolaTes
            const contentContainer = document.getElementById('contentArea');
            if (contentContainer) {
                kelolaTesHandler = function(e) {
                    const target = e.target;
                    
                    // Handle tombol "Kelola Tes BK"
                    if (target.classList.contains('action-btn') && target.textContent.includes('Kelola Soal Tes')) {
                        e.preventDefault();
                        loadContent('kelolasoal.php');
                    }
                    
                    // Handle tombol "Tambah Tes Baru"
                    if (target.classList.contains('action-btn') && target.textContent.includes('Tambah Tes Baru')) {
                        e.preventDefault();
                        loadContent('tambahtes.php');
                    }
                };
                contentContainer.addEventListener('click', kelolaTesHandler);
                console.log('✅ Kelola Tes event handler setup completed');
            }
        } catch (error) {
            console.error('❌ Error setting up Kelola Tes:', error);
        }
    }, 300);
}

// Dalam loadContent function, di bagian else if (file.includes('kelolasoal.php'))
else if (file.includes('kelolasoal.php')) {
    console.log('🔄 Setting up Kelola Soal...');
    
    setTimeout(() => {
        try {
            const contentContainer = document.getElementById('contentArea');
            if (contentContainer) {
                
                const kelolaSoalHandler = function(e) {
                    const button = e.target.closest('button');
                    if (!button) return;
                    
                    // Handle tombol Toggle Status
                    if (button.classList.contains('btn-toggle-status')) {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        
                        const idTes = button.getAttribute('data-tes-id');
                        const action = button.getAttribute('data-action');
                        const tesName = button.closest('.kelola-card')?.querySelector('h5')?.textContent || 'Tes';
                        
                        if (idTes && action) {
                            const actionText = action === 'aktif' ? 'mengaktifkan' : 'menonaktifkan';
                            
                            if (confirm(`Apakah Anda yakin ingin ${actionText} tes "${tesName}"?`)) {
                                // SELALU gunakan fallbackToggleStatusTes
                                if (typeof fallbackToggleStatusTes === 'function') {
                                    console.log('✅ Calling fallbackToggleStatusTes');
                                    fallbackToggleStatusTes(idTes, action, button);
                                } else {
                                    console.error('❌ fallbackToggleStatusTes not found');
                                    // Fallback langsung
                                    directToggleStatus(idTes, action, button);
                                }
                            }
                        }
                        return;
                    }
                    
                    // Handle tombol Edit
                    if (button.classList.contains('btn-edit-tes')) {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        const idTes = button.getAttribute('data-tes-id');
                        if (idTes) {
                            loadContent(`editsoal.php?id_tes=${idTes}`);
                        }
                        return;
                    }
                    
                    // Handle tombol Hapus - DIPERBAIKI
                    if (button.classList.contains('btn-hapus-tes')) {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        const idTes = button.getAttribute('data-tes-id');
                        const tesName = button.getAttribute('data-tes-name') || 'Tes';
                        
                        if (idTes && confirm(`Apakah Anda yakin ingin menghapus tes "${tesName}" beserta semua soalnya?`)) {
                            // Gunakan window.hapusTes dari kelolasoal.php jika tersedia
                            if (typeof window.hapusTes === 'function') {
                                window.hapusTes(idTes, button);
                            } 
                            // Jika tidak tersedia, gunakan fallback
                            else if (typeof fallbackHapusTes === 'function') {
                                fallbackHapusTes(idTes, button);
                            } 
                            // Jika tidak ada sama sekali, tampilkan error
                            else {
                                console.error('❌ Tidak ada fungsi hapusTes yang tersedia');
                                alert('Fungsi hapus tidak tersedia. Silakan refresh halaman.');
                            }
                        }
                        return;
                    }
                    
                    // Handle tombol Kembali
                    if (button.id === 'btn-kembali') {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        window.loadContent('kelolaTes.php');
                        return;
                    }
                };
                
                contentContainer.addEventListener('click', kelolaSoalHandler);
                console.log('✅ Kelola Soal event handler setup completed');
            }
        } catch (error) {
            console.error('❌ Error setting up Kelola Soal:', error);
        }
    }, 300);
}

// Dalam bagian loadContent di sideMenu_admin.php
else if (file.includes('editsoal.php')) {
    console.log('🔄 Loading Edit Soal Page...');
    
    setTimeout(async () => {
        try {
            // Load JavaScript untuk edit soal
            await loadScript('../../includes/js/admin/editsoal.js');
            console.log('✅ Edit Soal JS loaded successfully');
        } catch (error) {
            console.error('❌ Error loading Edit Soal JS:', error);
        }
    }, 300);
}
// Di dalam function loadContent(file), tambahkan:

// Handle Tambah Tes initialization
else if (file.includes('tambahtes.php')) {
    console.log('🔄 Setting up Tambah Tes...');
    
    setTimeout(async () => {
        try {
            // Load JavaScript untuk tambah tes
            await loadScript('../../includes/js/admin/tambahTes.js');
            
            // Initialize Tambah Tes
            if (typeof window.initTambahTes === 'function') {
                console.log('🚀 Initializing Tambah Tes...');
                window.initTambahTes();
            } else {
                console.error('❌ initTambahTes function not found');
            }
        } catch (error) {
            console.error('❌ Error setting up Tambah Tes:', error);
        }
    }, 300);
}
// sideMenu_admin.php - PERBAIKAN FUNGSI showAlert
 window.showAlert = function(type, message) {
    console.log('=== SHOW ALERT CALLED ===');
    console.log('Type:', type);
    console.log('Message:', message);
    console.log('========================');
    
    // Hapus alert sebelumnya jika ada
    const existingAlert = document.querySelector('.custom-alert');
    if (existingAlert) {
        existingAlert.remove();
    }
    
    // Normalize type - VERSI PERBAIKAN
    let normalizedType = '';
    
    if (typeof type === 'string') {
        normalizedType = type.toLowerCase().trim();
    } else if (type === true || type === 1) {
        normalizedType = 'success';
    } else {
        normalizedType = 'info'; // default
    }
    
    console.log('Normalized type:', normalizedType);
    
    // Tentukan kelas dan icon - VERSI LEBIH TELITI
    let alertClass, icon;
    
    // Cek success dengan berbagai variasi
    if (normalizedType === 'success' || 
        normalizedType === 'sukses' || 
        normalizedType === 'berhasil' ||
        message.includes('✅') ||
        message.toLowerCase().includes('berhasil') ||
        message.toLowerCase().includes('sukses')) {
        alertClass = 'alert-success';
        icon = 'fa-check-circle';
    }
    // Cek error/danger
    else if (normalizedType === 'danger' || 
             normalizedType === 'error' || 
             normalizedType === 'gagal' ||
             normalizedType === 'failed' ||
             message.includes('❌') ||
             message.toLowerCase().includes('gagal') ||
             message.toLowerCase().includes('error')) {
        alertClass = 'alert-danger';
        icon = 'fa-exclamation-triangle';
    }
    // Cek warning
    else if (normalizedType === 'warning' || 
             normalizedType === 'peringatan' ||
             message.includes('⚠️') ||
             message.toLowerCase().includes('peringatan')) {
        alertClass = 'alert-warning';
        icon = 'fa-exclamation-circle';
    }
    // Default ke info
    else {
        alertClass = 'alert-info';
        icon = 'fa-info-circle';
    }
    
    console.log('Using alert class:', alertClass);
    
    // Buat alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `custom-alert alert ${alertClass} alert-dismissible fade show`;
    alertDiv.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        z-index: 99999;
        min-width: 350px;
        max-width: 500px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border-radius: 10px;
        border: none;
        animation: slideInRight 0.3s ease;
    `;
    
    alertDiv.innerHTML = `
        <i class="fas ${icon} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto remove setelah 5 detik untuk success
    if (alertClass === 'alert-success') {
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
    
    return alertDiv;
};

// Fallback function untuk toggle status - PASTIKAN INI DI GLOBAL SCOPE
function fallbackToggleStatusTes(id, action, btn) {
    console.log('🔄 Using fallback toggle status for ID:', id, 'Action:', action);
    
    // Tampilkan loading state
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Memproses...';
    btn.disabled = true;

    fetch('../../includes/admin_control/ToggleStatusTes_Controller.php', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id_tes=' + encodeURIComponent(id) + '&action=' + encodeURIComponent(action)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(json => {
        if (json.success) {
            alert(json.message || 'Status tes berhasil diubah.');
            // Reload konten
            window.loadContent('kelolasoal.php');
        } else {
            throw new Error(json.message || 'Gagal mengubah status tes.');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Terjadi kesalahan saat mengubah status: ' + err.message);
        btn.innerHTML = originalHTML;
        btn.disabled = false;
    });
}

// Fallback langsung jika fallbackToggleStatusTes tidak tersedia
function directToggleStatus(id, action, btn) {
    console.log('🔧 Using direct toggle status');
    
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Memproses...';
    btn.disabled = true;

    fetch('includes/admin_control/ToggleStatusTes_Controller.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id_tes=' + encodeURIComponent(id) + '&action=' + encodeURIComponent(action)
    })
    .then(response => response.json())
    .then(json => {
        if (json.success) {
            alert(json.message || 'Status tes berhasil diubah.');
            window.loadContent('kelolasoal.php');
        } else {
            throw new Error(json.message);
        }
    })
    .catch(err => {
        alert('Error: ' + err.message);
        btn.innerHTML = originalHTML;
        btn.disabled = false;
    });
}
// Fallback function untuk toggle status jika function utama tidak tersedia
function fallbackToggleStatusTes(id, action, btn) {
    console.log('🔄 Using fallback toggle status for ID:', id, 'Action:', action);
    
    // Tampilkan loading state
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Memproses...';
    btn.disabled = true;

    fetch('../../includes/admin_control/ToggleStatusTes_Controller.php', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id_tes=' + encodeURIComponent(id) + '&action=' + encodeURIComponent(action)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(json => {
        if (json.success) {
            alert(json.message || 'Status tes berhasil diubah.');
            // Reload konten
            window.loadContent('kelolasoal.php');
        } else {
            throw new Error(json.message || 'Gagal mengubah status tes.');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Terjadi kesalahan saat mengubah status: ' + err.message);
        btn.innerHTML = originalHTML;
        btn.disabled = false;
    });
}

// Pastikan function tersedia di global scope
window.fallbackToggleStatusTes = fallbackToggleStatusTes;

// Fallback function untuk hapus tes
function fallbackHapusTes(id, btn) {
    const tesName = btn.getAttribute('data-tes-name') || 'Tes';
    
    console.log('🗑️ Fallback: Menghapus tes ID:', id);
    
    // Tampilkan loading state
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menghapus...';
    btn.disabled = true;

    fetch('../../includes/admin_control/HapusTes_Controller.php', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id_tes=' + encodeURIComponent(id)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(json => {
        if (json.success) {
            alert(json.message || 'Tes berhasil dihapus.');
            
            // Hapus card dari DOM
            const tesCard = document.getElementById(`tes-card-${id}`);
            if (tesCard) {
                tesCard.style.transition = 'all 0.3s ease';
                tesCard.style.opacity = '0';
                tesCard.style.height = '0';
                tesCard.style.margin = '0';
                tesCard.style.padding = '0';
                tesCard.style.overflow = 'hidden';
                
                setTimeout(() => {
                    tesCard.remove();
                    
                    // Jika tidak ada tes lagi, reload content
                    const remainingCards = document.querySelectorAll('[id^="tes-card-"]');
                    if (remainingCards.length === 0) {
                        setTimeout(() => {
                            window.loadContent('kelolaTes.php');
                        }, 1000);
                    }
                }, 300);
            } else {
                window.loadContent('kelolasoal.php');
            }
        } else {
            throw new Error(json.message || 'Gagal menghapus tes.');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Terjadi kesalahan saat menghapus: ' + err.message);
        btn.innerHTML = originalHTML;
        btn.disabled = false;
    });
} 


// Helper function untuk extract id_tes dari current page
function extractIdTesFromCurrentPage() {
    const contentArea = document.getElementById('contentArea');
    if (contentArea) {
        // Cari link atau element yang mengandung id_tes
        const links = contentArea.querySelectorAll('a[href*="id_tes="]');
        for (let link of links) {
            const match = link.href.match(/id_tes=(\d+)/);
            if (match) return match[1];
        }
    }
    return null;
}

    } catch (error) {
        console.error('❌ Error loading content:', error);
        contentArea.innerHTML = `
            <div class="alert alert-danger">
                <h4>Gagal memuat konten</h4>
                <p><strong>File:</strong> ${file}</p>
                <p><strong>Error:</strong> ${error.message}</p>
            </div>`;
        currentPage = '';
    }
}
// Set active & load on click
menuLinks.forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Update active state
        menuLinks.forEach(l => l.classList.remove('active'));
        link.classList.add('active');
        
        // Load content
        const file = link.dataset.file.trim();
        loadContent(file);
    });
});

// Load default content
document.addEventListener('DOMContentLoaded', () => {
    console.log('🚀 Application started');
    
    const defaultLink = document.querySelector('.sidebar-menu a.active');
    if (defaultLink) {
        const file = defaultLink.dataset.file.trim();
        // Tunggu sebentar untuk memastikan semua resource siap
        setTimeout(() => {
            loadContent(file);
        }, 100);
    }
});

// Export untuk akses global
window.loadContent = loadContent;
window.cleanupPage = cleanupPage;
</script>

</body>
</html>