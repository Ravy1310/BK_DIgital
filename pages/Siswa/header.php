<?php
// =========================
// AUTO-PATH
// =========================
$BASE_URL = "/BK_Digital/";
$ASSETS_URL = $BASE_URL . "assets/";
?>
<!-- =========================
      CSS NAVBAR (FIXED - TIDAK IKUT SCROLL)
     ========================= -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
  /* TAMBAHKAN INI - Padding compensation untuk fixed header */
  body {
    padding-top: 70px !important;
  }

  .navbar {
    font-family: 'Poppins', sans-serif;
    background-color: #ffffff !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    padding: 0.5rem 0 !important;
    transition: all 0.3s ease;
    
    /* UBAH INI: sticky jadi fixed */
    position: fixed !important;
    top: 0;
    left: 0;
    right: 0;
    z-index: 9999;
    width: 100%;
  }

  .navbar.scrolled {
    padding: 0.3rem 0 !important;
    background-color: #ffffff !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  }

  a {
    text-decoration: none;
  }

  /* UBAH INI: navbar-dark jadi navbar-light */
  .navbar-brand {
    font-weight: 700;
    color: blue !important;
    font-size: 1.3rem;
    letter-spacing: 0.5px;
  }

  /* UBAH INI: Sesuaikan warna untuk navbar-light */
  .nav-link {
    color: blue !important;
    font-weight: 500;
    margin: 0 12px;
    font-size: 1rem;
    transition: color 0.3s ease, transform 0.2s ease;
  }

  .nav-link:hover {
    color: #0050BC !important; /* Ubah warna hover */
    transform: translateY(-2px);
  }

  .dropdown-menu {
    border-radius: 12px;
    border: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  }

  .dropdown-item {
    color: #333;
    font-weight: 500;
    padding: 5px 9px;
    transition: background 0.3s ease, color 0.3s ease;
  }

  .dropdown-item:hover {
    background-color: #0050BC;
    color: #fff;
  }

  /* TAMBAHKAN INI - Style untuk navbar toggler */
  .navbar-toggler {
    border: 1px solid blue;
  }

  .navbar-toggler-icon {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='blue' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
  }
</style>

<!-- =========================
      HTML NAVBAR - FIXED
     ========================= -->
<!-- UBAH INI: navbar-dark jadi navbar-light -->
<nav class="navbar navbar-expand-lg navbar-light">
  <div class="container">
    
    <!-- LOGO -->
    <a class="navbar-brand" href="../../index.php">BK Digital</a>

    <!-- UNCOMMENT INI - Tombol toggle untuk mobile -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto">
        
        <!-- TAMBAHKAN INI - Menu navigasi
        <li class="nav-item">
          <a class="nav-link" href="jadwaltemu.php">Jadwal Saya</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="profil_siswa.php">Profil</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="logout.php">Logout</a>
        </li> -->

      </ul>
    </div>
  </div>
</nav>

<!-- OPTIONAL: Auto shrink on scroll -->
<script>
  window.addEventListener("scroll", function () {
    const nav = document.querySelector(".navbar");
    if (window.scrollY > 10) nav.classList.add("scrolled");
    else nav.classList.remove("scrolled");
  });
</script>