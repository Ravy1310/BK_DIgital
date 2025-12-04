<?php
$BASE_URL = "/pages/";
$BASE_HOME = "/../";
?>

<!-- BOOTSTRAP CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
/* RESET */
.dropdown-menu {
    background-color: white !important;
}

/* BODY */
body {
    padding-top: 70px;
    font-family: 'Poppins', sans-serif;
}

/* NAVBAR */
.navbar {
    background-color: #ffffff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
}

.navbar-brand {
    font-weight: 700;
    color: #0050BC !important;
    font-size: 1.4rem;
}

.nav-link {
    color: #0050BC !important;
    font-weight: 500;
    margin: 0 12px;
}

.nav-link:hover {
    color: #003c91 !important;
}

/* ====== DROPDOWN PERBAIKAN TOTAL ===== */

/* Dropdown container (BUKAN box besar) */
.navbar .dropdown-menu {
    padding: 4px 0 !important;          /* hilangkan kesan kartu */
    border-radius: 6px !important;      /* kecil & rapi */
    min-width: 190px !important;
    box-shadow: 0 6px 14px rgba(0,0,0,.12) !important;
    border: none !important;
    background: white !important;
}

/* Item dropdown */
.navbar .dropdown-item {
    padding: 9px 18px !important;
    font-weight: 500 !important;
    color: #0050BC !important;
    border-radius: 0 !important;         /* HILANGKAN RADIUS ITEM */
    background: transparent !important;
}

/* Hover tidak seperti card */
.navbar .dropdown-item:hover {
    background-color: rgba(0,80,188,.08) !important;
    color: #0050BC !important;
    transform: none !important;
}

/* Hilangkan shadow besar bawaan */
.dropdown-menu.shadow {
    box-shadow: none !important;
}

/* Animasi halus */
.dropdown-menu {
    animation: fadeDown .15s ease;
}

@keyframes fadeDown {
    from { opacity: 0; transform: translateY(4px); }
    to   { opacity: 1; transform: translateY(0); }
}

</style>

<nav class="navbar navbar-expand-lg fixed-top">
<div class="container">

  <a class="navbar-brand" href="<?= $BASE_HOME ?>index.php">BK Digital</a>

  <button class="navbar-toggler" type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navMenu">
    <ul class="navbar-nav ms-auto">

      <li class="nav-item">
        <a class="nav-link" href="<?= $BASE_HOME ?>index.php">Home</a>
      </li>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" role="button"
           data-bs-toggle="dropdown">
          Layanan BK
        </a>

        <ul class="dropdown-menu dropdown-menu-end shadow">
          <li>
            <a class="dropdown-item"
               href="<?= $BASE_URL ?>Siswa/verifikasi_pengaduan.php">
              Pengaduan Siswa
            </a>
          </li>
          <li>
            <a class="dropdown-item"
               href="<?= $BASE_URL ?>Siswa/verifikasi_jadwal.php">
              Jadwal Konseling
            </a>
          </li>
          <li>
            <a class="dropdown-item"
               href="<?= $BASE_URL ?>Siswa/verifikasi_tes.php">
              Tes BK
            </a>
          </li>
        </ul>
      </li>

    </ul>
  </div>

</div>
</nav>

<!-- BOOTSTRAP JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
