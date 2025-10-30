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
                <div class="fw-bold">SuperAdmin</div>
                
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
            <a href="#" class="active fw-bolder my-2" data-file="manajemen_account.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-kanban" viewBox="0 0 16 16">
                    <path d="M13.5 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1h-11a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zm-11-1a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                    <path d="M6.5 3a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1zm-4 0a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1zm8 0a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1z"/>
                </svg>
                <span class="ps-2">Manajemen Akun</span>
            </a>
        </li>
        <li>
            <a href="#" class="fw-bolder my-2" data-file="log_aktivitas.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-substack" viewBox="0 0 16 16">
                    <path d="M15 3.604H1v1.891h14v-1.89ZM1 7.208V16l7-3.926L15 16V7.208zM15 0H1v1.89h14z"/>
                </svg>
                <span class="ps-2">Log Aktivitas</span>
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

// Handle logout
logoutBtn.addEventListener('click', () => {
    if (confirm('Apakah Anda yakin ingin logout?')) {
        // Tampilkan loading state
        const originalText = logoutBtn.innerHTML;
        logoutBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Logging out...';
        logoutBtn.disabled = true;
        
        // Simulasi proses logout
        setTimeout(() => {
            // Redirect ke halaman logout
            window.location.href = '../../login.php'; // Sesuaikan dengan URL logout Anda
        }, 1000);
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
        if (file.includes('manajemen_account.php')) {
            if (typeof initManajemenAccount === 'function') {
                initManajemenAccount();
            }
        }
        else if (file.includes('log_aktivitas.php')) {
            // Gunakan singleton instance
            setTimeout(() => {
                window.logSystem = getLogAktivitasInstance();
                window.logSystem.init();
            }, 200);
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

<script src="../../includes/js/developer/log_aktivitas.js"></script>
<script src="../../includes/js/developer/manajemen_account.js"></script>


</body>
</html>