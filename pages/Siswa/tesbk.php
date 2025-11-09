<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BK Digital - Tes BK</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(to bottom right, #e8f1ff, #cfe4ff);
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 0;
      min-height: 100vh;
    }

    h3.section-title {
      color: #000;
      font-weight: 700;
      text-align: center;
      margin: 40px 0 25px;
    }

    .section-container {
      background-color: #dce7ff;
      border-radius: 20px;
      padding: 30px;
      margin: 0 auto 50px auto;
      width: 90%;
      max-width: 1100px;
      position: relative;
    }

    .test-card {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 6px 16px rgba(0,0,0,0.08);
      padding: 25px;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      transition: transform 0.25s ease, box-shadow 0.3s ease;
    }

    .test-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    .test-card h5 {
      font-weight: 600;
      color: #000;
      font-size: 1.05rem;
      margin-bottom: 6px;
    }

    .test-card p {
      color: #444;
      font-size: 0.93rem;
      margin-bottom: 8px;
    }

    .status {
      color: #004AAD;
      font-weight: 600;
      font-size: 0.85rem;
      margin-bottom: 8px;
    }

    .btn-utama {
      background-color: #004AAD;
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
      background-color: #003580;
    }

    /* Panah Navigasi */
    .carousel-control-prev-icon,
    .carousel-control-next-icon {
      background-color: #004AAD;
      border-radius: 50%;
      padding: 12px;
    }

    .carousel-control-prev,
    .carousel-control-next {
      width: 5%;
    }

    footer {
      text-align: center;
      padding: 25px 0;
      background: transparent;
      color: #555;
      font-size: 0.9rem;
    }
  </style>
</head>

<body>
  <div class="container py-5">

    <!-- ===== BAGIAN TEST BK ===== -->
    <h3 class="section-title">Test BK</h3>
    <div class="section-container">
      <div id="carouselTesBK" class="carousel slide" data-bs-interval="false">
        <div class="carousel-inner">

          <!-- Slide 1 -->
          <div class="carousel-item active">
            <div class="row g-4 justify-content-center">
              <div class="col-md-5 col-lg-4 d-flex">
                <div class="test-card flex-fill">
                  <div>
                    <h5>Test Minat Belajar</h5>
                    <p>Untuk Mengetahui Minat Belajar Siswa</p>
                    <div class="status">Belum Dikerjakan</div>
                    <p class="text-muted small mb-3">Total Soal: 3 Soal</p>
                  </div>
                  <button class="btn btn-utama">Mulai Test</button>
                </div>
              </div>
              <div class="col-md-5 col-lg-4 d-flex">
                <div class="test-card flex-fill">
                  <div>
                    <h5>Test Gaya Belajar</h5>
                    <p>Untuk Mengenali Gaya Belajar yang Cocok</p>
                    <div class="status">Belum Dikerjakan</div>
                    <p class="text-muted small mb-3">Total Soal: 4 Soal</p>
                  </div>
                  <button class="btn btn-utama">Mulai Test</button>
                </div>
              </div>
            </div>
          </div>

          <!-- Slide 2 -->
          <div class="carousel-item">
            <div class="row g-4 justify-content-center">
              <div class="col-md-5 col-lg-4 d-flex">
                <div class="test-card flex-fill">
                  <div>
                    <h5>Test Kepribadian</h5>
                    <p>Untuk Mengenal Kepribadian Diri Siswa</p>
                    <div class="status">Belum Dikerjakan</div>
                    <p class="text-muted small mb-3">Total Soal: 5 Soal</p>
                  </div>
                  <button class="btn btn-utama">Mulai Test</button>
                </div>
              </div>
              <div class="col-md-5 col-lg-4 d-flex">
                <div class="test-card flex-fill">
                  <div>
                    <h5>Test Penjurusan</h5>
                    <p>Mengetahui Jurusan yang Sesuai</p>
                    <div class="status">Belum Dikerjakan</div>
                    <p class="text-muted small mb-3">Total Soal: 8 Soal</p>
                  </div>
                  <button class="btn btn-utama">Mulai Test</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Panah Navigasi -->
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselTesBK" data-bs-slide="prev" id="prevTesBK">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselTesBK" data-bs-slide="next" id="nextTesBK">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </button>
      </div>
    </div>

    <!-- ===== BAGIAN RIWAYAT ===== -->
    <h3 class="section-title">Riwayat</h3>
    <div class="section-container">
      <div id="carouselRiwayat" class="carousel slide" data-bs-interval="false">
        <div class="carousel-inner">

          <!-- Slide 1 -->
          <div class="carousel-item active">
            <div class="row g-4 justify-content-center">
              <div class="col-md-4 col-lg-3 d-flex">
                <div class="test-card flex-fill">
                  <div>
                    <h5>Test Penjurusan</h5>
                    <p>Mengetahui Penjurusan Siswa</p>
                    <div class="status text-dark">1 Tahun Lalu</div>
                    <p class="text-muted small mb-3">Total Soal: 8 Soal</p>
                  </div>
                  <button class="btn btn-utama">Lihat Hasil</button>
                </div>
              </div>
              <div class="col-md-4 col-lg-3 d-flex">
                <div class="test-card flex-fill">
                  <div>
                    <h5>Test Kepribadian</h5>
                    <p>Untuk Mengenali Kepribadian Diri</p>
                    <div class="status text-dark">6 Bulan Lalu</div>
                    <p class="text-muted small mb-3">Total Soal: 7 Soal</p>
                  </div>
                  <button class="btn btn-utama">Lihat Hasil</button>
                </div>
              </div>
            </div>
          </div>

          <!-- Slide 2 -->
          <div class="carousel-item">
            <div class="row g-4 justify-content-center">
              <div class="col-md-4 col-lg-3 d-flex">
                <div class="test-card flex-fill">
                  <div>
                    <h5>Test Sosial Emosional</h5>
                    <p>Untuk Mengenali Kesehatan Emosional</p>
                    <div class="status text-dark">2 Minggu Lalu</div>
                    <p class="text-muted small mb-3">Total Soal: 6 Soal</p>
                  </div>
                  <button class="btn btn-utama">Lihat Hasil</button>
                </div>
              </div>
              <div class="col-md-4 col-lg-3 d-flex">
                <div class="test-card flex-fill">
                  <div>
                    <h5>Test Kepemimpinan</h5>
                    <p>Mengetahui Potensi Kepemimpinan</p>
                    <div class="status text-dark">1 Bulan Lalu</div>
                    <p class="text-muted small mb-3">Total Soal: 5 Soal</p>
                  </div>
                  <button class="btn btn-utama">Lihat Hasil</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Panah Navigasi -->
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselRiwayat" data-bs-slide="prev" id="prevRiwayat">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselRiwayat" data-bs-slide="next" id="nextRiwayat">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </button>
      </div>
    </div>
  </div>

  <footer>© 2025 BK Digital — SMA Al Islam Krian</footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Fungsi untuk sembunyikan panah kiri saat di slide pertama
    function handleCarouselControls(carouselId, prevId) {
      const carousel = document.querySelector(carouselId);
      const prevButton = document.querySelector(prevId);

      carousel.addEventListener('slid.bs.carousel', function () {
        const activeIndex = [...carousel.querySelectorAll('.carousel-item')]
          .findIndex(item => item.classList.contains('active'));

        if (activeIndex === 0) {
          prevButton.style.display = 'none';
        } else {
          prevButton.style.display = 'block';
        }
      });

      // Awal: sembunyikan panah kiri
      prevButton.style.display = 'none';
    }

    handleCarouselControls('#carouselTesBK', '#prevTesBK');
    handleCarouselControls('#carouselRiwayat', '#prevRiwayat');
  </script>
</body>
</html>
