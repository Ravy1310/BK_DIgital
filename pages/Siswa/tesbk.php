<?php
// dashboard_tesbk.php
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BK Digital - Tes BK</title>

  <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="../../assets/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <style>
    body {
      background-color: #f4f6f9;
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 0;
    }

    /* === NAVBAR (IDENTIK DENGAN DASHBOARD SISWA) === */
    .navbar {
      background-color: #0050BC;
      padding: 18px 80px; /* padding kiri-kanan besar */
    }

    .navbar-brand {
      color: #ffffff !important;
      font-weight: 700;
      font-size: 1.25rem;
      letter-spacing: 0.3px;
      text-transform: none;
    }

    .navbar-toggler {
      border: none;
      outline: none;
    }

    .navbar-toggler:focus {
      box-shadow: none;
    }

    .navbar-nav .nav-link {
      color: #ffffff !important;
      font-weight: 500;
      margin-left: 25px;
      transition: color 0.3s ease;
    }

    .navbar-nav .nav-link:hover {
      color: #dceaff !important;
    }

    /* === KONTEN UTAMA === */
    h3.section-title {
      color: #000;
      font-weight: 700;
      text-align: center;
      margin: 40px 0 30px;
    }

    .section-container {
      background-color: #eaf1ff;
      border-radius: 20px;
      padding: 40px 30px;
      margin-bottom: 50px;
    }

    /* === CARD STYLING === */
    .test-card {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 6px 16px rgba(0,80,188,0.08);
      padding: 25px;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      transition: transform 0.25s ease, box-shadow 0.3s ease;
    }

    .test-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 12px 28px rgba(0,80,188,0.15);
    }

    .test-card h5 {
      font-weight: 600;
      color: #000;
      font-size: 1.05rem;
      margin-bottom: 6px;
    }

    .test-card p {
      color: #555;
      font-size: 0.93rem;
      margin-bottom: 8px;
    }

    .status {
      color: #0050BC;
      font-weight: 600;
      font-size: 0.85rem;
      margin-bottom: 8px;
    }

    .btn-utama {
      background-color: #0050BC;
      color: #fff;
      font-weight: 600;
      border: none;
      padding: 8px 20px;
      border-radius: 8px;
      width: 100%;
      transition: background 0.3s ease;
      margin-top: auto;
    }

    .btn-utama:hover {
      background-color: #003f92;
    }

    footer {
      text-align: center;
      padding: 25px 0;
      background: #fff;
      border-top: 1px solid #e1e1e1;
      color: #777;
      font-size: 0.9rem;
      margin-top: 50px;
    }

    @media (max-width: 991px) {
      .navbar {
        padding: 16px 25px;
      }
    }
  </style>
</head>

<body>
  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand" href="dashboard_siswa.php">BK Digital</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link active" href="#">Tes BK</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Layanan</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Profil</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Keluar</a></li>
      </ul>
    </div>
  </nav>

  <!-- KONTEN -->
  <div class="container mt-4 mb-5">
    <h3 class="section-title">Test BK</h3>
    <div class="section-container">
      <div class="row g-4 justify-content-center">
        <?php
        $tesAktif = [
          ["judul" => "Test Minat Belajar", "deskripsi" => "Untuk Mengetahui Minat Belajar Siswa", "status" => "Belum Dikerjakan", "total" => 3],
          ["judul" => "Test Gaya Belajar", "deskripsi" => "Mengenali Gaya Belajar yang Paling Cocok", "status" => "Belum Dikerjakan", "total" => 4],
        ];
        foreach ($tesAktif as $tes) {
          echo '
          <div class="col-sm-6 col-md-4 col-lg-3 d-flex">
            <div class="test-card flex-fill">
              <div>
                <h5>'.$tes["judul"].'</h5>
                <p>'.$tes["deskripsi"].'</p>
                <div class="status">'.$tes["status"].'</div>
                <p class="text-muted small mb-3">Total Soal: '.$tes["total"].' Soal</p>
              </div>
              <button class="btn btn-utama">Mulai Test</button>
            </div>
          </div>';
        }
        ?>
      </div>
    </div>

    <h3 class="section-title">Riwayat</h3>
    <div class="section-container">
      <div class="row g-4 justify-content-center">
        <?php
        $riwayatTes = [
          ["judul" => "Test Penjurusan", "deskripsi" => "Mengetahui Penjurusan yang Sesuai", "status" => "1 Tahun Lalu", "total" => 5],
          ["judul" => "Test Kepribadian", "deskripsi" => "Untuk Mengenali Kepribadian Diri", "status" => "6 Bulan Lalu", "total" => 7],
        ];
        foreach ($riwayatTes as $tes) {
          echo '
          <div class="col-sm-6 col-md-4 col-lg-3 d-flex">
            <div class="test-card flex-fill">
              <div>
                <h5>'.$tes["judul"].'</h5>
                <p>'.$tes["deskripsi"].'</p>
                <div class="status text-dark">'.$tes["status"].'</div>
                <p class="text-muted small mb-3">Total Soal: '.$tes["total"].' Soal</p>
              </div>
              <button class="btn btn-utama">Lihat Hasil</button>
            </div>
          </div>';
        }
        ?>
      </div>
    </div>
  </div>

  <footer>
    © 2025 BK Digital — SMA Al Islam Krian
  </footer>

  <script src="../../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
