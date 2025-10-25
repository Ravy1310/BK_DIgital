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

<style>
:root {
    --primary-color: #0059D4;
    --sidebar-width: 270px;
    --sidebar-min-width: 90px;
}

/* ==========================
   Global & Body
========================== */
body {
    font-family: 'Poppins', sans-serif;
    background-color: #e8eef3;
    overflow-x: hidden;
}

/* ==========================
   Navbar
========================== */
.navbar-custom {
    position: fixed !important;
    top: 0; left: 0; right: 0;
    z-index: 1049;
    background-color: #fff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 30px !important;
    color: var(--primary-color);
}

/* ==========================
   Sidebar
========================== */
.sidebar {
    position: fixed;
    top: 56px;
    left: -20px;
    width: var(--sidebar-width);
    height: 100%;
    background: linear-gradient(to top right, #002E6E, #0050BC);
    border-top-right-radius: 90px;
    padding-top: 80px;
    transition: width 0.3s ease;
    overflow-y: auto;
}

.sidebar.minimized {
    width: var(--sidebar-min-width);
}

/* Sidebar Menu Items */
.sidebar-menu li {
    list-style: none;
}

.sidebar a {
    display: flex;
    align-items: center;
    color: #fff;
    text-decoration: none;
    padding: 20px;
    border-radius: 25px 0 0 25px;
    transition: all 0.3s ease;
    font-size: 14px;
}

/* Icon & Text */
.sidebar a i,
.sidebar a svg {
    width: 30px;
    min-width: 30px;
    text-align: center;
    font-size: 18px;
}

.sidebar a span {
    margin-left: 10px;
    transition: opacity 0.3s, width 0.3s;
}

/* Active Menu */
.sidebar a.active {
    background-color: #fff;
    color: var(--primary-color);
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.sidebar a.active:hover {
    background-color: #fff;
    color: var(--primary-color);
}

/* Hover effect non-active */
.sidebar a:not(.active):hover {
    background-color: rgba(255,255,255,0.2);
    color: #fff;
}

/* ==========================
   Sidebar Minimized Adjustments
========================== */
.sidebar.minimized a {
    justify-content: center;
    padding: 10px 0; /* lebih ramping */
    border-radius: 20px 0 0 20px;
    font-size: 13px;
}

/* Hide text on minimized */
.sidebar.minimized a span {
    display: none;
}

/* Icon ukuran lebih kecil di minimize */
.sidebar.minimized a i,
.sidebar.minimized a svg {
    width: 22px;
    min-width: 22px;
    font-size: 16px;
}

/* Active menu ketika minimize */
.sidebar.minimized a.active {
    padding: 10px 0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.12);
}

/* Hover effect non-active ketika minimize */
.sidebar.minimized a:not(.active):hover {
    background-color: rgba(255,255,255,0.15);
}

/* ==========================
   Sidebar Minimize Button
========================== */
.sidebar-minimize {
    position: absolute;
    top: 20px;
    right: 0;
    width: 70px;
    height: 50px;
    border-radius: 15px 25px 0 15px;
    background-color: rgba(0,30,80,0.8);
    color: #fff;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 3;
    transition: transform 0.3s ease;
}

.sidebar.minimized .sidebar-minimize i {
    transform: rotate(180deg);
}

/* ==========================
   Main Content
========================== */
#mainContent {
    margin-left: var(--sidebar-width);
    padding: 20px 30px 30px 30px;
    transition: margin-left 0.3s ease;
}

#mainContent.minimized {
    margin-left: var(--sidebar-min-width);
}

/* ==========================
   Profile Button
========================== */
#profileBtn {
    border-width: 2px;
}
</style>
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
                <span class="ps-2">Manajemen Akun</span>
            </a>
        </li>
        <li>
            <a href="#" class="fw-bolder my-2" data-file="manajemen_account.php">
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

// Toggle sidebar
sidebarMinimize.addEventListener('click', () => {
    sidebar.classList.toggle('minimized');
    mainContent.classList.toggle('minimized');
});

// Load content
async function loadContent(file) {
    try {
        const res = await fetch(file);
        const html = await res.text();
        contentArea.innerHTML = html;
        window.scrollTo({ top: 0, behavior: 'smooth' });
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
</script>

</body>
</html>
