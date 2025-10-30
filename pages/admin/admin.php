<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard BK Digital</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">

  <style>
    body {
      background-image: url('background.jpeg'); /* Ganti URL sesuai gambar yang kamu mau */
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      font-family: 'Poppins', sans-serif;
      min-height: 100vh;
      margin: 0;
    }
    .card-stat {
      border-radius: 15px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      padding: 1.5rem;
      text-align: center;
      background-color: #fff;
      transition: transform 0.2s;
    }
    .card-stat:hover {
      transform: scale(1.03);
    }
    .card-test {
      border-radius: 10px;
      border: 1px solid #ddd;
      background-color: #fff;
      padding: 1rem;
      text-align: center;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
      transition: transform 0.2s;
    }
    .card-test:hover {
      transform: scale(1.02);
    }
    h5, h6 {
      font-weight: 600;
    }
    .text-muted {
      font-size: 0.9rem;
    }
  </style>
</head>

<body>
  <div class="container my-3 ">
    <h5 class="fw-bold pt-3">Statistik Sekilas</h5>
    <p class="text-muted">Ringkasan data utama</p>

    <!-- Statistik Sekilas -->
    <div class="row g-4 mb-5">
      <div class="col-md-4">
        <div class="card-stat">
        <img src="gambar/jumlahsiswa.svg" class="icon-img">
          <h6 class="mt-2">Jumlah Siswa</h6>
          <h4>4000</h4>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card-stat">
          <img src="gambar/jumlahguru.svg" class="icon-img">
          <h6 class="mt-2">Jumlah Guru</h6>
          <h4>3</h4>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card-stat">
          <img src="gambar/jumlahtes.svg" class="icon-img">
          <h6 class="mt-2">Jumlah Tes</h6>
          <h4>20</h4>
        </div>
      </div>
    </div>

    <!-- Tes Terpopuler -->
    <h5 class="fw-bold">Tes Terpopuler</h5>
    <p class="text-muted">Tes paling banyak yang dikerjakan siswa</p>

    <div class="row g-3">
      <div class="col-md-4"><div class="card-test"><span class="fw-bold">Tes Minat Belajar</span><br><small class="text-muted">Dikerjakan oleh 50 siswa</small></div></div>
      <div class="col-md-4"><div class="card-test"><span class="fw-bold">Tes Kokologi</span><br><small class="text-muted">Dikerjakan oleh 50 siswa</small></div></div>
      <div class="col-md-4"><div class="card-test"><span class="fw-bold">Tes Sosialisasi</span><br><small class="text-muted">Dikerjakan oleh 50 siswa</small></div></div>
      <div class="col-md-4"><div class="card-test"><span class="fw-bold">Tes Percaya Diri</span><br><small class="text-muted">Dikerjakan oleh 50 siswa</small></div></div>
      <div class="col-md-4"><div class="card-test"><span class="fw-bold">Tes Kedisiplinan</span><br><small class="text-muted">Dikerjakan oleh 50 siswa</small></div></div>
      <div class="col-md-4"><div class="card-test"><span class="fw-bold">Tes Konsentrasi</span><br><small class="text-muted">Dikerjakan oleh 50 siswa</small></div></div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
