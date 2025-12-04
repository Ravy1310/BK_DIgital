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
    
    /* Style untuk loading */
    .content-loading {
        min-height: 300px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
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
            <a href="#" class="active fw-bolder my-2" data-file="Dashboard.php" data-js="">
               <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-pie-chart-fill" viewBox="0 0 16 16">
                    <path d="M15.985 8.5H8.207l-5.5 5.5a8 8 0 0 0 13.277-5.5zM2 13.292A8 8 0 0 1 7.5.015v7.778zM8.5.015V7.5h7.485A8 8 0 0 0 8.5.015"/>
                </svg>
                <span class="ps-2">Dashboard</span>
            </a>
        </li>
        <li>
            <a href="#" class="fw-bolder my-2" data-file="manajemenpengaduan.php" data-js="pengaduan">
               <i class="fas fa-headset" style="font-size: 24px;"></i><span class="ps-2">Pengaduan</span>
            </a>
        </li>
        <li>
            <a href="#" class="fw-bolder my-2" data-file="manajemen jadwal konseling.php" data-js="jadwalkonseling">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-calendar-week" viewBox="0 0 16 16">
  <path d="M11 6.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm-3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm-5 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5z"/>
  <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
</svg>
                <span class="ps-2">Jadwal Konseling</span>
            </a>
        </li>
        <li>
            <a href="#" class="fw-bolder my-2" data-file="Laporankonseling.php" data-js="laporankonseling">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-journals" viewBox="0 0 16 16">
  <path d="M5 0h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2 2 2 0 0 1-2 2H3a2 2 0 0 1-2-2h1a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1H1a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v9a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1H3a2 2 0 0 1 2-2"/>
  <path d="M1 6v-.5a.5.5 0 0 1 1 0V6h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V9h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 2.5v.5H.5a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1H2v-.5a.5.5 0 0 0-1 0"/>
</svg>
                <span class="ps-2">Laporan Konseling</span>
            </a>
        </li>
         <li>
            <a href="#" class="fw-bolder my-2" data-file="hasiltes.php" data-js="hasiltes">
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// JS untuk SideMenu
const sidebar = document.getElementById('sidebar');
const sidebarMinimize = document.getElementById('sidebarMinimize');
const mainContent = document.getElementById('mainContent');
const contentArea = document.getElementById('contentArea');
const menuLinks = document.querySelectorAll('.sidebar-menu a');
const profileBtn = document.getElementById('profileBtn');
const profileDropdown = document.getElementById('profileDropdown');
const logoutBtn = document.getElementById('logoutBtn');

// Store untuk menyimpan instance aplikasi yang aktif
window.activeApp = null;
window.loadedScripts = new Map(); // Menggunakan Map untuk menyimpan info lebih detail
window.appInitializers = new Map(); // Untuk menyimpan fungsi inisialisasi

// Mapping antara nama JS dan fungsi inisialisasi
const appInitializers = {
    'pengaduan': 'initManajemenPengaduan',
    'jadwalkonseling': 'initManajemenJadwalKonseling', 
    'laporankonseling': 'initManajemenLaporanKonseling',
    'hasiltes': 'initHasilTes'
};

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
        const originalText = logoutBtn.innerHTML;
        logoutBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Logging out...';
        logoutBtn.disabled = true;
        
        setTimeout(() => { 
            window.location.href = '../../includes/logout.php?nocache=' + Date.now();
        }, 500);
    }
});

// Fungsi untuk mendapatkan nama file JS yang benar (case-insensitive)
function getActualJsFileName(jsName) {
    // Coba beberapa kemungkinan case
    const possibleNames = [
        jsName,
        jsName.toLowerCase(),
        jsName.charAt(0).toUpperCase() + jsName.slice(1).toLowerCase(),
        jsName.toUpperCase()
    ];
    
    return possibleNames[0]; // Mengembalikan yang pertama, akan diverifikasi saat load
}

// Fungsi untuk load script secara dinamis dengan retry
function loadScript(jsName) {
    return new Promise((resolve, reject) => {
        const normalizedName = jsName.toLowerCase();
        
        // Cek apakah script sudah di-load
        if (window.loadedScripts.has(normalizedName)) {
            console.log(`Script ${jsName} sudah di-load sebelumnya`);
            resolve(true); // true berarti sudah di-load sebelumnya
            return;
        }
        
        // Coba beberapa kemungkinan nama file
        const possibleFiles = [
            `${jsName}.js`,
            `${jsName.toLowerCase()}.js`,
            `${jsName.charAt(0).toUpperCase() + jsName.slice(1).toLowerCase()}.js`
        ];
        
        let loaded = false;
        let errors = [];
        
        // Coba load dari setiap kemungkinan
        const tryLoad = (index) => {
            if (index >= possibleFiles.length) {
                // Semua percobaan gagal
                console.error(`Semua percobaan gagal untuk: ${jsName}`, errors);
                reject(new Error(`Failed to load ${jsName}. Tried: ${possibleFiles.join(', ')}`));
                return;
            }
            
            const fileName = possibleFiles[index];
            const scriptPath = `../../includes/js/guru/${fileName}`;
            console.log(`Mencoba load script: ${scriptPath}`);
            
            const script = document.createElement('script');
            script.src = scriptPath + '?v=' + Date.now();
            script.async = true;
            
            script.onload = () => {
                console.log(`Berhasil load script: ${fileName}`);
                window.loadedScripts.set(normalizedName, {
                    fileName: fileName,
                    loadedAt: new Date(),
                    jsName: jsName
                });
                loaded = true;
                resolve(false); // false berarti baru di-load
            };
            
            script.onerror = (err) => {
                console.warn(`Gagal load ${fileName}, mencoba alternatif...`);
                errors.push(`${fileName}: ${err}`);
                
                // Hapus script yang gagal
                if (script.parentNode) {
                    script.parentNode.removeChild(script);
                }
                
                // Coba file berikutnya
                setTimeout(() => tryLoad(index + 1), 100);
            };
            
            document.body.appendChild(script);
        };
        
        // Mulai percobaan pertama
        tryLoad(0);
    });
}

// Fungsi untuk membersihkan aplikasi sebelumnya
function cleanupPreviousApp() {
    // Hentikan aplikasi sebelumnya jika ada
    if (window.activeApp) {
        if (typeof window.activeApp.destroy === 'function') {
            try {
                window.activeApp.destroy();
                console.log('Aplikasi sebelumnya di-destroy');
            } catch (e) {
                console.error('Error destroying previous app:', e);
            }
        } else if (typeof window.activeApp.cleanup === 'function') {
            try {
                window.activeApp.cleanup();
                console.log('Aplikasi sebelumnya di-cleanup');
            } catch (e) {
                console.error('Error cleaning up previous app:', e);
            }
        }
    }
    
    // Reset activeApp
    window.activeApp = null;
    
    // Cleanup event listeners khusus
    const cleanupSelectors = [
        '.dynamic-event-listener',
        '[data-dynamic="true"]',
        '.temp-event-handler'
    ];
    
    cleanupSelectors.forEach(selector => {
        document.querySelectorAll(selector).forEach(el => {
            try {
                el.remove();
            } catch (e) {
                // Ignore errors
            }
        });
    });
    
    // Clear any global intervals/timeouts
    if (window.appIntervals) {
        window.appIntervals.forEach(interval => clearInterval(interval));
        window.appIntervals = [];
    }
}

// Fungsi untuk inisialisasi aplikasi berdasarkan tipe
function initializeApp(appType) {
    try {
        console.log(`Menginisialisasi aplikasi: ${appType}`);
        
        // Cleanup aplikasi sebelumnya
        cleanupPreviousApp();
        
        // Cari fungsi inisialisasi berdasarkan appType
        const normalizedType = appType.toLowerCase();
        const initFunctionName = appInitializers[normalizedType];
        
        if (!initFunctionName) {
            console.warn(`Tidak ada inisialisator untuk: ${appType}`);
            return;
        }
        
        // Cek apakah fungsi tersedia di window
        if (typeof window[initFunctionName] === 'function') {
            window.activeApp = window[initFunctionName]();
            console.log(`Aplikasi ${appType} diinisialisasi dengan fungsi: ${initFunctionName}`);
        } else {
            console.error(`Fungsi ${initFunctionName} tidak ditemukan di window`);
            
            // Coba cari dengan case insensitive
            const functionNames = Object.keys(window).filter(key => 
                typeof window[key] === 'function' && 
                key.toLowerCase().includes(normalizedType)
            );
            
            if (functionNames.length > 0) {
                console.log(`Mencoba fungsi alternatif: ${functionNames[0]}`);
                window.activeApp = window[functionNames[0]]();
            } else {
                console.error(`Tidak ada fungsi inisialisasi yang cocok untuk ${appType}`);
            }
        }
    } catch (error) {
        console.error('Error initializing app:', error);
        
        // Tampilkan error di UI
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger mt-3';
        errorDiv.innerHTML = `
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Error Inisialisasi:</strong> ${error.message}
            <button class="btn btn-sm btn-outline-danger ms-2" onclick="window.retryInitialize('${appType}')">
                <i class="fas fa-redo me-1"></i>Coba Lagi
            </button>
        `;
        
        contentArea.appendChild(errorDiv);
    }
}

// Fungsi untuk retry inisialisasi
window.retryInitialize = function(appType) {
    console.log(`Retrying initialization for: ${appType}`);
    initializeApp(appType);
};

// Load content dengan lazy loading JS
async function loadContent(file, jsType) {
    try {
        console.log(`=== LOADING: ${file} (JS: ${jsType || 'none'}) ===`);
        
        // Tampilkan loading state
        contentArea.innerHTML = `
            <div class="content-loading">
                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 fs-5">Memuat konten...</p>
                <p class="text-muted small">${file}</p>
            </div>
        `;
        
        // Load HTML content
        console.log(`Fetching HTML: ${file}`);
        const res = await fetch(file);
        if (!res.ok) {
            throw new Error(`HTTP ${res.status}: ${res.statusText}`);
        }
        
        const html = await res.text();
        contentArea.innerHTML = html;
        window.scrollTo({ top: 0, behavior: 'smooth' });
        
        console.log(`HTML loaded successfully`);
        
        // Load JS jika diperlukan
        if (jsType) {
            try {
                console.log(`Loading JS: ${jsType}`);
                const alreadyLoaded = await loadScript(jsType);
                
                if (alreadyLoaded) {
                    console.log(`JS ${jsType} sudah di-load sebelumnya, langsung inisialisasi`);
                    initializeApp(jsType);
                } else {
                    console.log(`JS ${jsType} baru di-load, tunggu sebentar lalu inisialisasi`);
                    // Beri waktu untuk script di-parse dan dieksekusi
                    setTimeout(() => {
                        initializeApp(jsType);
                    }, 300);
                }
            } catch (jsError) {
                console.error('Error loading JS:', jsError);
                contentArea.innerHTML += `
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> Script JavaScript gagal dimuat.
                        <div class="small mt-1">${jsError.message}</div>
                        <button class="btn btn-sm btn-outline-warning mt-2" onclick="loadContent('${file}', '${jsType}')">
                            <i class="fas fa-redo me-1"></i>Coba Load Ulang
                        </button>
                    </div>
                `;
            }
        } else {
            console.log('No JS to load for this page');
        }
        
        console.log(`=== LOAD COMPLETE: ${file} ===`);
        
    } catch (error) {
        console.error('Error loading content:', error);
        contentArea.innerHTML = `
            <div class="alert alert-danger">
                <h4 class="alert-heading"><i class="fas fa-times-circle me-2"></i>Gagal Memuat Konten</h4>
                <p><strong>File:</strong> ${file}</p>
                <p><strong>Error:</strong> ${error.message}</p>
                <hr>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary" onclick="loadContent('${file}', '${jsType}')">
                        <i class="fas fa-redo me-1"></i>Coba Lagi
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                        <i class="fas fa-sync-alt me-1"></i>Refresh Halaman
                    </button>
                </div>
            </div>
        `;
    }
}

// Debounce function untuk mencegah multiple clicks
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Debounced load content
const debouncedLoadContent = debounce(loadContent, 300);

// Set active & load on click dengan debounce
menuLinks.forEach(link => {
    link.addEventListener('click', (e) => {
        e.preventDefault();
        
        // Cegah multiple clicks
        if (link.classList.contains('loading')) {
            console.log('Link sedang loading, skip...');
            return;
        }
        
        // Update active class
        menuLinks.forEach(l => {
            l.classList.remove('active');
            l.classList.remove('loading');
        });
        link.classList.add('active');
        link.classList.add('loading');
        
        // Get file and js type
        const file = link.dataset.file.trim();
        const jsType = link.dataset.js || '';
        
        // Reset loading class setelah selesai
        setTimeout(() => {
            link.classList.remove('loading');
        }, 1000);
        
        // Load content dengan debounce
        debouncedLoadContent(file, jsType);
    });
});

// Load default content saat pertama kali
document.addEventListener('DOMContentLoaded', () => {
    console.log('=== SIDEMENU INITIALIZED ===');
    
    const defaultLink = document.querySelector('.sidebar-menu a.active');
    if (defaultLink) {
        const file = defaultLink.dataset.file.trim();
        const jsType = defaultLink.dataset.js || '';
        
        // Load default content setelah sedikit delay
        setTimeout(() => {
            loadContent(file, jsType);
        }, 500);
    }
});

// Cleanup saat halaman ditutup
window.addEventListener('beforeunload', () => {
    cleanupPreviousApp();
});

// Debug helper
window.debugSideMenu = function() {
    console.log('=== SIDEMENU DEBUG INFO ===');
    console.log('Loaded scripts:', Array.from(window.loadedScripts.entries()));
    console.log('Active app:', window.activeApp);
    console.log('Current content:', contentArea.innerHTML.substring(0, 200) + '...');
};
</script>

</body>
</html>