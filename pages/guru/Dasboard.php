<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Monitoring</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
      background-color: transparent;
    }

    body {
      background-image: url('background.jpg');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      font-family: 'Poppins', sans-serif;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      min-height: 100vh;
    }

    .dashboard-title {
      font-weight: 700;
      font-size: 1.5rem;
      color: #000;
    }

    .card-stat {
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
      border: 1px solid #ddd;
      height: 90px;
      display: flex;
      justify-content: center;
      flex-direction: column;
      position: relative;
    }

    .card-stat h6 {
      font-size: 0.95rem;
      font-weight: 600;
      color: #444;
    }

    .card-stat h3 {
      font-weight: 700;
      margin: 0;
      color: #000;
    }

    .icon-box {
      position: absolute;
      top: 10px;
      right: 15px;
      font-size: 1.2rem;
    }

    .icon-green {
      color: #28a745;
    }

    .card-content {
      border-radius: 10px;
      border: 1px solid #ddd;
      background-color: #fff;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
      padding: 1.2rem;
    }

    .pengaduan-item {
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 10px 12px;
      margin-bottom: 10px;
    }

    .badge-status {
      font-size: 0.75rem;
      border-radius: 8px;
      padding: 4px 8px;
      font-weight: 500;
    }

    .badge-baru {
      background-color: #ffeb99;
      color: #000;
    }

    .badge-proses {
      background-color: #d0f0ff;
      color: #0d6efd;
    }

    .status-link {
      color: #28a745;
      text-decoration: none;
      font-weight: 500;
      font-size: 0.9rem;
    }

    .status-link:hover {
      text-decoration: underline;
    }

    .small-text {
      font-size: 0.85rem;
    }

    .fw-semibold {
      font-weight: 600;
    }
  </style>
</head>
<body>
  <div class="container py-4">

    <!-- Judul -->
    <h4 class="dashboard-title mb-4">Dashboard Monitoring</h4>

    <!-- Baris 1: Statistik -->
    <div class="row g-3 mb-4">
      <!-- Pengaduan Baru -->
      <div class="col-md-3">
        <div class="card-stat text-center">
          <div class="icon-box">
            <!-- SVG Ikon Hijau -->
            <svg class="feather feather-user icon-green" fill="none" height="24"
                stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                stroke-width="2" viewBox="0 0 24 24" width="24"
                xmlns="http://www.w3.org/2000/svg">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
          </div>
          <h6>Pengaduan Baru</h6>
          <h3>2</h3>
        </div>
      </div>

      <!-- Jadwal Hari Ini -->
      <div class="col-md-3">
        <div class="card-stat text-center">
          <div class="icon-box">
            <!-- SVG Kalender Hijau -->
            <svg id="Icons" viewBox="0 0 24 24" width="24" height="24" xmlns="http://www.w3.org/2000/svg">
              <defs>
                <style>
                  .cls-1 {
                    fill: #28a745; /* Warna hijau Bootstrap */
                  }
                </style>
              </defs>
              <path class="cls-1" d="M20,2H19V1a1,1,0,0,0-2,0V2H7V1A1,1,0,0,0,5,1V2H4A4,4,0,0,0,0,6V20a4,4,0,0,0,4,4H20a4,4,0,0,0,4-4V6A4,4,0,0,0,20,2Zm2,18a2,2,0,0,1-2,2H4a2,2,0,0,1-2-2V6A2,2,0,0,1,4,4H5V5A1,1,0,0,0,7,5V4H17V5a1,1,0,0,0,2,0V4h1a2,2,0,0,1,2,2Z"/>
              <path class="cls-1" d="M19,7H5A1,1,0,0,0,5,9H19a1,1,0,0,0,0-2Z"/>
              <path class="cls-1" d="M7,12H5a1,1,0,0,0,0,2H7a1,1,0,0,0,0-2Z"/>
              <path class="cls-1" d="M7,17H5a1,1,0,0,0,0,2H7a1,1,0,0,0,0-2Z"/>
              <path class="cls-1" d="M13,12H11a1,1,0,0,0,0,2h2a1,1,0,0,0,0-2Z"/>
              <path class="cls-1" d="M13,17H11a1,1,0,0,0,0,2h2a1,1,0,0,0,0-2Z"/>
              <path class="cls-1" d="M19,12H17a1,1,0,0,0,0,2h2a1,1,0,0,0,0-2Z"/>
              <path class="cls-1" d="M19,17H17a1,1,0,0,0,0,2h2a1,1,0,0,0,0-2Z"/>
            </svg>
          </div>
          <h6>Jadwal Hari Ini</h6>
          <h3>5</h3>
        </div>
      </div>

      <!-- Total Laporan -->
<div class="col-md-3">
  <div class="card-stat text-center">
    <div class="icon-box">
      <!-- SVG Ikon Biru -->
      <svg width="28" height="28" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
        <g fill="#007BFF">
          <path d="M32,17c-8.271,0-15,6.729-15,15s6.729,15,15,15s15-6.729,15-15S40.271,17,32,17z M32,45c-3.675,0-6.992-1.54-9.359-4
            h18.718C38.992,43.46,35.675,45,32,45z M30.503,30.581C29.562,30.033,29,29.068,29,28v-2c0-0.838,0.355-1.645,0.974-2.212
            c0.626-0.575,1.438-0.851,2.298-0.776C33.801,23.145,35,24.548,35,26.208V28c0,1.068-0.562,2.033-1.503,2.581L33,30.87v2.884
            l4.84,1.383C39.112,35.5,40,36.678,40,38v1H24v-1c0-1.322,0.888-2.5,2.16-2.863L31,33.754V30.87L30.503,30.581z M42,40.295V38
            c0-2.21-1.484-4.179-3.61-4.786L35,32.246v-0.266c1.246-0.939,2-2.416,2-3.979v-1.792c0-2.688-2.001-4.966-4.556-5.188
            c-1.401-0.125-2.793,0.35-3.822,1.294C27.591,23.259,27,24.603,27,26v2c0,1.563,0.754,3.041,2,3.979v0.266l-3.39,0.969
            C23.484,33.821,22,35.79,22,38v2.295c-1.872-2.253-3-5.144-3-8.295c0-7.168,5.832-13,13-13s13,5.832,13,13
            C45,35.151,43.872,38.042,42,40.295z"/>
          <path d="M54,7h-9.184C44.402,5.839,43.302,5,42,5h-3.383C37.321,2.529,34.799,1,32,1c-2.799,0-5.321,1.528-6.617,4H22
            c-1.302,0-2.402,0.839-2.816,2H10c-1.654,0-3,1.346-3,3v50c0,1.654,1.346,3,3,3h44c1.654,0,3-1.346,3-3V10C57,8.346,55.654,7,54,7z
             M51,49h-8v8H13V13h6v2h26v-2h6V49z M49.586,51L45,55.586V51H49.586z M21,8c0-0.551,0.449-1,1-1h4.618l0.487-0.975
            C28.039,4.159,29.914,3,32,3s3.961,1.159,4.895,3.024L37.382,7H42c0.551,0,1,0.449,1,1v5H21V8z M55,60c0,0.551-0.449,1-1,1H10
            c-0.551,0-1-0.449-1-1V10c0-0.551,0.449-1,1-1h9v2h-8v48h33.414L53,50.414V11h-8V9h9c0.551,0,1,0.449,1,1V60z"/>
          <rect height="2" width="2" x="39" y="49"/>
          <rect height="2" width="22" x="15" y="49"/>
          <rect height="2" width="22" x="15" y="53"/>
          <rect height="2" width="2" x="39" y="53"/>
          <path d="M32,5c-1.654,0-3,1.346-3,3s1.346,3,3,3s3-1.346,3-3S33.654,5,32,5z M32,9c-0.551,0-1-0.449-1-1s0.449-1,1-1
            s1,0.449,1,1S32.551,9,32,9z"/>
          <rect height="2" width="2" x="24" y="9"/>
          <rect height="2" width="2" x="38" y="9"/>
        </g>
      </svg>
    </div>
    <h6>Total Laporan</h6>
    <h3>2</h3>
  </div>
</div>


    <!-- Siswa Bimbingan -->
<div class="col-md-3">
  <div class="card-stat text-center">
    <div class="icon-box">
      <!-- Ikon SVG kecil warna hijau -->
      <svg width="25" height="25" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
        <path fill="#28a745" d="M322.47,97.68A36.64,36.64,0,1,0,285.83,61,36.68,36.68,0,0,0,322.47,97.68Zm0-63.07A26.44,26.44,0,1,1,296,61,26.46,26.46,0,0,1,322.47,34.61Z"/>
        <path fill="#28a745" d="M189.53,97.68A36.64,36.64,0,1,0,152.9,61,36.68,36.68,0,0,0,189.53,97.68Zm0-63.07A26.44,26.44,0,1,1,163.1,61,26.46,26.46,0,0,1,189.53,34.61Z"/>
        <path fill="#28a745" d="M473.76,221.74H460.29a71.69,71.69,0,0,0-49.13-62.91,40.63,40.63,0,1,0-44.46,0,71.9,71.9,0,0,0-44.23,41.53,71.91,71.91,0,0,0-44.24-41.53,40.64,40.64,0,1,0-44.46,0,71.91,71.91,0,0,0-44.24,41.53,71.9,71.9,0,0,0-44.23-41.53,40.64,40.64,0,1,0-44.46,0,71.69,71.69,0,0,0-49.13,62.91H38.24a5.1,5.1,0,0,0,0,10.2h319a51,51,0,0,0,2.16,83.15,88,88,0,0,0-43.88,22.28l-47.94,44.85-56.13-54.87c-10.75-10.51-27.66-10.59-38.87.08a27.75,27.75,0,0,0-.25,39.89l63.87,61.29a46,46,0,0,0,62.24,1.09l28.87-25.84,2,78.76a5.1,5.1,0,0,0,5.1,5h.13a5.1,5.1,0,0,0,5-5.23l-2.28-89.78a5.1,5.1,0,0,0-8.5-3.67l-37.1,33.19a35.75,35.75,0,0,1-48.37-.85L179.46,360a17.4,17.4,0,0,1-5.23-12.64,17.87,17.87,0,0,1,30.12-12.72L264,392.93a5.1,5.1,0,0,0,7,.08l51.5-48.19a77.82,77.82,0,0,1,53.36-21.07h27.34a57.15,57.15,0,0,1,57.09,57.09V482.49a5.1,5.1,0,1,0,10.2,0V380.84a67.4,67.4,0,0,0-54-66,50.95,50.95,0,0,0,1.87-82.94h55.35a5.1,5.1,0,1,0,0-10.2Z"/>
      </svg>
    </div>
    <h6>Siswa Bimbingan</h6>
    <h3>2</h3>
  </div>
</div>


    <!-- Baris 2: Pengaduan Terbaru & Jadwal Mendatang -->
    <div class="row g-4">
      <!-- Kolom kiri -->
      <div class="col-md-7">
        <div class="card-content">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-semibold mb-0">Pengaduan Terbaru</h5>
            <span class="text-danger small fw-semibold">1 Baru</span>
          </div>

          <div class="pengaduan-item">
            <div class="fw-semibold small-text">Kesulitan dalam mengerjakan tugas kelompok</div>
            <div class="d-flex justify-content-between align-items-center mt-1">
              <small>Alex Dion</small>
              <span class="badge-status badge-baru">BARU</span>
            </div>
          </div>

          <div class="pengaduan-item">
            <div class="fw-semibold small-text">Kesulitan dalam mengerjakan tugas kelompok</div>
            <div class="d-flex justify-content-between align-items-center mt-1">
              <small>Anonim</small>
              <span class="badge-status badge-proses">Di Proses</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Kolom kanan -->
      <div class="col-md-5">
        <div class="card-content">
          <h5 class="fw-semibold mb-3">Jadwal Mendatang</h5>

          <div class="border rounded p-3 mb-3">
            <p class="mb-1 fw-semibold">Budi Santoso (X IPA 1)</p>
            <small>4 Oktober 2025 pukul 09.17</small><br>
            <span class="text-primary fw-semibold">DITERIMA</span>
          </div>

          <a href="#" class="status-link">Lihat semua jadwal →</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap Icon -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
