<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Jadwal Konseling</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(135deg, #d9eafd, #ffffff);
      font-family: 'Poppins', sans-serif;
    }

    .section-box {
      background: rgba(255, 255, 255, 0.7);
      padding: 25px;
      border-radius: 15px;
      backdrop-filter: blur(5px);
    }

    .kartu-konseling {
      border-radius: 15px !important;
      padding: 20px;
      background: #fff;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      transition: 0.2s;
    }

    .kartu-konseling:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 14px rgba(0,0,0,0.15);
    }

    .badge-status {
      background: #007bff;
      border-radius: 20px;
      padding: 5px 12px;
      color: #fff;
      font-size: 13px;
      font-weight: 600;
    }

    .icon-waktu {
      font-size: 19px;
      margin-left: 8px;
      color: #2a4d8f;
    }

    h3 {
      font-weight: 600;
    }
  </style>
</head>

<body>

<!-- =================== JADWAL KONSELING =================== -->
<div class="container py-4">

  <div class="section-box mb-4">
    <h3 class="text-center mb-4">Jadwal Konseling</h3>

    <div class="row g-3">

      <!-- Card 1 -->
      <div class="col-md-4">
        <div class="kartu-konseling">
          <div class="d-flex justify-content-between">
            <span class="badge-status">Konseling Selesai</span>
            <span class="icon-waktu">🕒</span>
          </div>

          <h5 class="mt-3 mb-1">Rabu, 1 Oktober 2025</h5>
          <p class="mb-1">10.00 - 12.00 WIB</p>

          <p class="mb-0"><strong>Konselor:</strong> Ibu Rika, S.Pd.</p>
          <p class="mb-0"><strong>Status:</strong> Selesai</p>
        </div>
      </div>

      <!-- Duplicate card 2 -->
      <div class="col-md-4">
        <div class="kartu-konseling">
          <div class="d-flex justify-content-between">
            <span class="badge-status">Konseling Selesai</span>
            <span class="icon-waktu">🕒</span>
          </div>

          <h5 class="mt-3 mb-1">Rabu, 1 Oktober 2025</h5>
          <p class="mb-1">10.00 - 12.00 WIB</p>

          <p class="mb-0"><strong>Konselor:</strong> Ibu Rika, S.Pd.</p>
          <p class="mb-0"><strong>Status:</strong> Selesai</p>
        </div>
      </div>

      <!-- Duplicate card 3 -->
      <div class="col-md-4">
        <div class="kartu-konseling">
          <div class="d-flex justify-content-between">
            <span class="badge-status">Konseling Selesai</span>
            <span class="icon-waktu">🕒</span>
          </div>

          <h5 class="mt-3 mb-1">Rabu, 1 Oktober 2025</h5>
          <p class="mb-1">10.00 - 12.00 WIB</p>

          <p class="mb-0"><strong>Konselor:</strong> Ibu Rika, S.Pd.</p>
          <p class="mb-0"><strong>Status:</strong> Selesai</p>
        </div>
      </div>

    </div>

    <div class="text-end mt-4">
      <a href="#" class="btn btn-primary px-4">Ajukan Jadwal Konseling Baru</a>
    </div>
  </div>

  <!-- =================== RIWAYAT KONSELING =================== -->
  <div class="section-box">

    <h3 class="text-center mb-4">Riwayat Konseling</h3>

    <div class="row g-3">

      <!-- Riwayat 1 -->
      <div class="col-md-4">
        <div class="kartu-konseling">
          <div class="d-flex justify-content-between">
            <span class="badge-status">Konseling Selesai</span>
            <span class="icon-waktu">🕒</span>
          </div>

          <h5 class="mt-3 mb-1">Rabu, 1 Oktober 2025</h5>
          <p class="mb-1">10.00 - 12.00 WIB</p>

          <p class="mb-0"><strong>Konselor:</strong> Ibu Rika, S.Pd.</p>
          <p class="mb-0"><strong>Status:</strong> Selesai</p>
        </div>
      </div>

      <!-- Riwayat 2 -->
      <div class="col-md-4">
        <div class="kartu-konseling">
          <div class="d-flex justify-content-between">
            <span class="badge-status">Konseling Selesai</span>
            <span class="icon-waktu">🕒</span>
          </div>

          <h5 class="mt-3 mb-1">Rabu, 1 Oktober 2025</h5>
          <p class="mb-1">10.00 - 12.00 WIB</p>

          <p class="mb-0"><strong>Konselor:</strong> Ibu Rika, S.Pd.</p>
          <p class="mb-0"><strong>Status:</strong> Selesai</p>
        </div>
      </div>

      <!-- Riwayat 3 -->
      <div class="col-md-4">
        <div class="kartu-konseling">
          <div class="d-flex justify-content-between">
            <span class="badge-status">Konseling Selesai</span>
            <span class="icon-waktu">🕒</span>
          </div>

          <h5 class="mt-3 mb-1">Rabu, 1 Oktober 2025</h5>
          <p class="mb-1">10.00 - 12.00 WIB</p>

          <p class="mb-0"><strong>Konselor:</strong> Ibu Rika, S.Pd.</p>
          <p class="mb-0"><strong>Status:</strong> Selesai</p>
        </div>
      </div>

    </div>

    <div class="text-end mt-4">
      <a href="#" class="btn btn-primary px-4">Lihat Semua Riwayat Konseling</a>
    </div>

  </div>

</div>

</body>
</html>
