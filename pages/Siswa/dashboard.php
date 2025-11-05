<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Beranda BK Digital - SMA Al-Islam Krian</title>
  
  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f3f3f3;
      margin: 0;
      padding: 0;
    }

    /* === HEADER SECTION === */
    .header-section {
      position: relative;
      overflow: hidden;
      border-radius: 15px;
      margin: 20px auto 0;
      max-width: 1200px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    }

    .header-section img {
      width: 100%;
      height: 320px;
      object-fit: cover;
      border-radius: 15px;
      display: block;
    }

    /* Overlay tulisan di atas gambar */
    .header-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.45);
      color: white;
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      text-align: center;
      padding: 20px;
      border-radius: 15px;
    }

    .header-overlay h2 {
      font-weight: 700;
      font-size: 1.9rem;
      line-height: 1.5;
    }

    /* === WAVE SHAPE === */
    .wave-shape {
      position: relative;
      top: -5px;
      z-index: 5;
    }

    .wave-shape svg {
      display: block;
      width: 100%;
      height: 80px;
    }

    /* === KONTEN SECTION === */
    .content-section {
      max-width: 1200px;
      margin: 0 auto 40px;
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      padding: 25px;
    }

    .content-section h6 {
      font-weight: 600;
      margin-bottom: 10px;
    }

    textarea {
      width: 100%;
      height: 200px;
      border-radius: 10px;
      border: 1px solid #ccc;
      resize: none;
      padding: 10px;
      font-size: 0.95rem;
      background-color: #f9f9f9;
    }

    /* === RESPONSIVE === */
    @media (max-width: 768px) {
      .header-section img {
        height: 220px;
      }

      .header-overlay h2 {
        font-size: 1.3rem;
      }

      .content-section {
        margin: 10px;
        padding: 20px;
      }
    }
  </style>
</head>

<body>

  <!-- === HEADER === -->
  <div class="header-section">
    <!-- ✅ Gambar dengan fallback otomatis -->
    <img 
      src="assets/image/sekolah.jpg" 
      onerror="this.onerror=null;this.src='https://via.placeholder.com/1200x320?text=Gambar+tidak+ditemukan';" 
      alt="Gedung SMA Al-Islam Krian"
    >
    
    <div class="header-overlay">
      <h2>Selamat Datang di Layanan<br>Bimbingan dan Konseling SMALISKA</h2>
    </div>

    <!-- === WAVE === -->
    <div class="wave-shape">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 160">
        <path fill="#f3f3f3" d="M0,96L60,80C120,64,240,32,360,42.7C480,53,600,107,720,122.7C840,139,960,117,1080,106.7C1200,96,1320,96,1380,96L1440,96L1440,160L1380,160C1320,160,1200,160,1080,160C960,160,840,160,720,160C600,160,480,160,360,160C240,160,120,160,60,160L0,160Z"></path>
      </svg>
    </div>
  </div>

  <!-- === KONTEN BAWAH === -->
  <div class="content-section">
    <h6>Profil BK Smaliska</h6>
    <textarea readonly placeholder="Deskripsi singkat tentang profil BK SMA Al-Islam Krian..."></textarea>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
 