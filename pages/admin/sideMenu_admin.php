
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


</head>
<body>

<nav class="navbar navbar-custom">
    <span class="h5 mb-0 fw-bolder">BK Digital</span>
    <button class="btn btn-light rounded-circle border border-black" id="profileBtn">
        <i class="fas fa-user"></i>
    </button>
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
                <span class="ps-2">Dashboard</span>
            </a>
        </li>
        <li>
            <a href="#" class="fw-bolder my-2" data-file="manajemen_account.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-substack" viewBox="0 0 16 16">
                    <path d="M15 3.604H1v1.891h14v-1.89ZM1 7.208V16l7-3.926L15 16V7.208zM15 0H1v1.89h14z"/>
                </svg>
                <span class="ps-2">Kelola Data Siswa</span>
            </a>
        </li>
         <li>
            <a href="#" class="fw-bolder my-2" data-file="manajemen_account.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-substack" viewBox="0 0 16 16">
                    <path d="M15 3.604H1v1.891h14v-1.89ZM1 7.208V16l7-3.926L15 16V7.208zM15 0H1v1.89h14z"/>
                </svg>
                <span class="ps-2">Kelola Data Guru</span>
            </a>
        </li>
         <li>
            <a href="#" class="fw-bolder my-2" data-file="manajemen_account.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-substack" viewBox="0 0 16 16">
                    <path d="M15 3.604H1v1.891h14v-1.89ZM1 7.208V16l7-3.926L15 16V7.208zM15 0H1v1.89h14z"/>
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

<script>
const sidebar = document.getElementById('sidebar');
const sidebarMinimize = document.getElementById('sidebarMinimize');
const mainContent = document.getElementById('mainContent');
const contentArea = document.getElementById('contentArea');
const menuLinks = document.querySelectorAll('.sidebar-menu a');

// Toggle sidebar
sidebarMinimize.addEventListener('click', () => {
    sidebar.classList.toggle('minimized');
    mainContent.classList.toggle('minimized');
});

// Load content (FUNGSI INI DIMODIFIKASI)
async function loadContent(file) {
    try {
        const res = await fetch(file);
        const html = await res.text();
        contentArea.innerHTML = html;
        window.scrollTo({ top: 0, behavior: 'smooth' });

        // == TAMBAHAN PENTING ==
        // Setelah HTML dimuat, jalankan JS yang sesuai
        if (file.includes('manajemen_account.php')) {
            initManajemenAccount();
        }
        // Tambahkan if lain di sini jika ada halaman lain
        // else if (file.includes('log_aktivitas.php')) {
        //    initLogAktivitas(); 
        // }

    } catch {
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

// Profile button
document.getElementById('profileBtn').addEventListener('click', () => {
    alert('Profile button clicked!');
});

// Hapus fungsi loadPage() yang tidak terpakai
</script>

<script src="../../includes/js/manajemen_account.js"></script>
</body>
</html>
