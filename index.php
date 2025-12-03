<?php 
include __DIR__ . '/pages/Siswa/header.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BK Digital - Dashboard Siswa</title>

  <!-- LOCAL ASSETS -->
  <link href="assets/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="assets/css/all.min.css" rel="stylesheet"/>

  <style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');

  * { margin: 0; padding: 0; box-sizing: border-box; scroll-behavior: smooth; }
  body {
    font-family: 'Poppins', sans-serif;
    background-color: #ffffff;
    overflow-x: hidden;
    color: #ffff;
  }
 a {
  text-decoration: none;
 }
  /* Hero */
  .hero-section {
    height: 80vh;
    background: linear-gradient(rgba(0,80,188,0.25), rgba(0,80,188,0.25)),
                url("assets/image/SekolahYapalis.jpeg") center/cover no-repeat;
    display: flex; align-items: center; justify-content: center;
    text-align: center; color: white;
  }

  .hero-content { 
    max-width: 700px; 
    animation: fadeInUp 1.5s ease; 
    background: rgba(0,0,0,0.25);
    padding: 25px;
    border-radius: 20px;
    backdrop-filter: blur(3px);
  }

  .hero-content h1 {
    font-weight: 700;
    margin-bottom: 10px;
  }

  .hero-content p {
    font-weight: 400;
    font-size: 1.05rem;
    color: #f1f1f1;
  }

  .btn-utama {
    background-color: #0050BC;
    border: none;
    border-radius: 50px;
    padding: 12px 30px;
    font-weight: 600;
    color: white;
    box-shadow: 0 0 15px rgba(0,80,188,0.3);
    transition: all 0.3s ease;
    margin-top: 15px;
  }

  .btn-utama:hover {
    transform: translateY(-3px);
    background-color: #003c91;
    box-shadow: 0 0 25px rgba(0,80,188,0.55);
  }

  @media (max-width: 768px) {
    .hero-content {
      background: rgba(0,0,0,0.15);
      padding: 20px;
    }
    .hero-content h1 { font-size: 1.6rem; }
    .hero-content p { font-size: 0.95rem; }
  }

  .section {
    padding: 80px 0;
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.8s ease;
  }

  .section.show {
    opacity: 1;
    transform: translateY(0);
  }

  .section-title {
    font-weight: 700;
    font-size: 1.8rem;
    margin-bottom: 40px;
    text-align: center;
    color: #0050BC;
    position: relative;
  }

  .section-title::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 4px;
    background-color: #0050BC;
    border-radius: 2px;
  }

  .feature-card {
    text-align: center;
    padding: 30px;
    border-radius: 20px;
    background: white;
    border: 1px solid #e9ecef;
    box-shadow: 0 6px 25px rgba(0,80,188,0.08);
    transition: all 0.3s ease;
    opacity: 0;
    transform: translateY(40px);
  }

  .feature-card.show {
    opacity: 1;
    transform: translateY(0);
    transition: all 0.8s ease;
  }

  .feature-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 10px 30px rgba(0,80,188,0.18);
  }

  .feature-card i {
    font-size: 2.5rem;
    color: #0050BC;
    margin-bottom: 15px;
    animation: pulseIcon 2s infinite ease-in-out;
  }

  .feature-card h5 {
    font-weight: 700;
    color: #0050BC;
  }
  
  footer {
    background: #0050BC;
    color: white;
    text-align: center;
    padding: 40px 0 20px;
    margin-top: 60px;
  }

  @keyframes fadeInUp {
    from { opacity: 0; transform: translateY(40px); }
    to { opacity: 1; transform: translateY(0); }
  }

  @keyframes pulseIcon {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.15); opacity: 0.9; }
  }

  /* Menyamakan tinggi ikon */
.feature-card i {
    font-size: 2.8rem !important;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px auto;
}

/* Menyamakan jarak antar elemen di dalam card */
.feature-card a.btn-utama {
    display: block;
    margin: 15px auto 10px auto;
    padding: 12px 15px;
    width: 85%;
    border-radius: 50px;
}

/* Menyamakan tinggi seluruh card */
.feature-card {
    min-height: 310px;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
}

/* Membuat paragraf agar tetap sejajar */
.feature-card p {
    margin-top: auto;
}

  </style>
</head>

<body>

  <!-- HERO -->
  <section class="hero-section">
    <div class="hero-content">
      <h1>Selamat Datang di BK Digital</h1>
      <p>Layanan Bimbingan dan Konseling SMA Al Islam Krian dengan pendekatan digital yang ramah siswa.</p>
      <a href="#layanan" class="btn btn-utama">Jelajahi Layanan</a>
    </div>
  </section>

  <!-- TENTANG -->
  <section id="tentang" class="section">
    <div class="container">
      <h2 class="section-title">Tentang BK Digital</h2>
      <p class="text-center text-muted">
        BK Digital SMA Al Islam Krian adalah sistem bimbingan dan konseling berbasis teknologi
        yang membantu siswa memahami potensi diri, menghadapi tantangan, dan membangun masa depan yang lebih baik.
      </p>
    </div>
  </section>

  <!-- LAYANAN -->
  <section id="layanan" class="section bg-light">
    <div class="container">
      <h2 class="section-title">Layanan Kami</h2>
      <div class="row g-4 justify-content-center">

        <!-- CARD 1 -->
        <div class="col-md-4">
          <div class="feature-card">
            <i class="fas fa-comments"></i>

            <a href="pages/Siswa/verifikasi_jadwal.php" class="btn btn-utama w-75 mt-3 mx-auto d-block text-center">
              Layanan Konseling
            </a>

            <p class="mt-3 text-muted">
              Bimbingan profesional untuk membantu siswa dalam hal emosional, sosial, dan akademik.
            </p>
          </div>
        </div>

        <!-- CARD 2 -->
        <div class="col-md-4">
          <div class="feature-card">
            <i class="fas fa-bullhorn"></i>

            <a href="pages/Siswa/verifikasi_pengaduan.php" class="btn btn-utama w-75 mt-3 mx-auto d-block text-center">
              Pengaduan Siswa
            </a>

            <p class="mt-3 text-muted">
              Tempat aman dan rahasia untuk menyampaikan masalah atau kekhawatiran di sekolah.
            </p>
          </div>
        </div>

        <!-- CARD 3 -->
        <div class="col-md-4">
          <div class="feature-card">
            <i class="fas fa-file-alt"></i>

            <a href="pages/Siswa/verifikasi_tes.php" class="btn btn-utama w-75 mt-3 mx-auto d-block text-center">
              Tes dan Evaluasi
            </a>

            <p class="mt-3 text-muted">
              Beragam tes untuk membantu menentukan jurusan dan karier masa depan.
            </p>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- KONTAK -->
  <section id="kontak" class="section">
    <div class="container text-center">
      <h2 class="section-title">Hubungi Kami</h2>
      <p class="text-muted mb-4">
        Ingin tahu lebih banyak tentang layanan BK Digital SMA Al Islam Krian?
      </p>
      <a href="mailto:bkdigital@alislamkrian.sch.id" class="btn btn-utama"><i class="fas fa-envelope"></i> Email Kami</a>
    </div>
  </section>

  <!-- FOOTER -->
  <footer>
    <p>© 2025 BK Digital — SMA Al Islam Krian</p>
  </footer>

  <script src="assets/js/bootstrap.bundle.min.js"></script>

  <script>
    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) entry.target.classList.add('show');
      });
    }, { threshold: 0.2 });

    document.querySelectorAll('.section, .feature-card').forEach(el => observer.observe(el));
  </script>

</body>
</html>
