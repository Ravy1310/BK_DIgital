<?php
// =========================
// AUTO-PATH
// =========================
$BASE_URL = "/BK_Digital/";
$ASSETS_URL = $BASE_URL . "assets/";
?>

<!-- =========================
      CSS NAVBAR (SAMA 100%)
     ========================= -->
<style>
  .navbar {
    background-color: #0050BC !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    padding: 0.3rem 0 !important;
    transition: all 0.3s ease;
    position: sticky;
    top: 0;
    z-index: 9999;
  }

  .navbar.scrolled {
    padding: 0.8rem 0 !important;
    background-color: #0046a3 !important;
    box-shadow: 0 6px 20px rgba(0,0,0,0.2);
  }

  .navbar-brand {
    font-weight: 700;
    color: white !important;
    font-size: 1.3rem;
    letter-spacing: 0.5px;
  }

  .nav-link {
    color: white !important;
    font-weight: 500;
    margin: 0 12px;
    font-size: 1rem;
    transition: color 0.3s ease, transform 0.2s ease;
  }

  .nav-link:hover {
    color: #dceaff !important;
    transform: translateY(-2px);
  }

  .dropdown-menu {
    border-radius: 12px;
    border: none;
    box-shadow: 0 4px 25px rgba(0,0,0,0.15);
  }

  .dropdown-item {
    color: #333;
    font-weight: 500;
    padding: 10px 18px;
    transition: background 0.3s ease, color 0.3s ease;
  }

  .dropdown-item:hover {
    background-color: #0050BC;
    color: #fff;
  }
</style>

<!-- =========================
      HTML NAVBAR
     ========================= -->
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container">
    
    <!-- LOGO -->
    <a class="navbar-brand" href="<?php echo $BASE_URL; ?>">BK Digital</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto">
        
        <!-- Tidak ada menu lain sesuai permintaan -->

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
