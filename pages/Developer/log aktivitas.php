<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard SuperAdmin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
  <style>
    body {
      background: url('../../assets/image/background.jpg');
      background-size: cover;
      font-family: 'Poppins', sans-serif;
    }

    .log-item {
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    svg {
      width: 35px;
      height: 35px;
    }
  </style>
</head>
<body>
  <div class="d-flex pt-4">
    <!-- Konten Utama -->
    <div class="content flex-grow-1 p-4">
      <h4 class="fw-bold">Selamat Datang <span class="text-primary">superAdmin</span></h4>

      <!-- Kartu Statistik -->
      <div class="row mt-4">
        <!-- Total Siswa -->
        <div class="col-md-3">
          <div class="card shadow-sm border-0">
            <div class="card-body text-center">
              <!-- SVG Siswa -->
              <svg xmlns="http://www.w3.org/2000/svg" fill="green" viewBox="0 0 16 16">
                <path d="M8 8a3 3 0 1 0-2.995-3.176A3 3 0 0 0 8 8z"/>
                <path fill-rule="evenodd" d="M14 14s-1-1.5-6-1.5S2 14 2 14s1-4 6-4 6 4 6 4z"/>
              </svg>
              <h6 class="mt-2">Total Siswa Aktif</h6>
              <h5 class="fw-bold">22</h5>
            </div>
          </div>
        </div>

        <!-- Total Guru -->
        <div class="col-md-3">
          <div class="card shadow-sm border-0">
            <div class="card-body text-center">
              <!-- SVG Guru -->
              <svg xmlns="http://www.w3.org/2000/svg" fill="blue" viewBox="0 0 16 16">
                <path d="M8 7a3 3 0 1 0-2.995-3.176A3 3 0 0 0 8 7z"/>
                <path fill-rule="evenodd" d="M14 14s-1-1.5-6-1.5S2 14 2 14s1-4 6-4 6 4 6 4z"/>
                <path d="M13 3.5a.5.5 0 0 1 .5-.5h.793l.853-.854a.5.5 0 1 1 .708.708L15 3.707V4.5a.5.5 0 0 1-1 0V4H13.5a.5.5 0 0 1-.5-.5z"/>
              </svg>
              <h6 class="mt-2">Total Guru Aktif</h6>
              <h5 class="fw-bold">22</h5>
            </div>
          </div>
        </div>

        <!-- Kasus Terbaru -->
        <div class="col-md-6">
          <div class="card shadow-sm border-0">
            <div class="card-body d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center">
                <!-- SVG Kasus -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="deepskyblue" viewBox="0 0 16 16" class="me-2">
                  <path d="M4 1a2 2 0 0 0-2 2v10c0 .73.195 1.412.53 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h2z"/>
                  <path fill-rule="evenodd" d="M10 1a2 2 0 0 1 2 2v1h-1V3a1 1 0 0 0-1-1H6a1 1 0 0 0-1 1v1H4V3a2 2 0 0 1 2-2h4z"/>
                  <path d="M3 4h10v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4z"/>
                </svg>
                <span class="fw-semibold">Kasus Terbaru</span>
              </div>
              <span class="badge bg-secondary fs-6">3</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Log Aktivitas -->
      <div class="mt-5 p-4 bg-white rounded shadow-sm">
        <h5 class="fw-bold mb-3">Log Aktivitas</h5>
        <div id="logContainer">
          <div class="card log-item mb-3 border-success-subtle">
            <div class="card-body bg-success-subtle rounded">
              <strong>Admin 1 menambahkan tes</strong>
              <p class="text-muted small mb-0">2 menit lalu</p>
            </div>
          </div>

          <div class="card log-item mb-3 border-success-subtle">
            <div class="card-body bg-success-subtle rounded">
              <strong>Admin 2 menghapus data guru</strong>
              <p class="text-muted small mb-0">5 menit lalu</p>
            </div>
          </div>

          <div class="card log-item mb-3 border-success-subtle">
            <div class="card-body bg-success-subtle rounded">
              <strong>Admin 1 memperbarui kasus</strong>
              <p class="text-muted small mb-0">10 menit lalu</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
