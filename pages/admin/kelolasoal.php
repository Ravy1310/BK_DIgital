<?php
session_start();

// CEK LOGIN
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../../login.php");
    exit;
}

// CEK ROLE
if ($_SESSION['admin_role'] !== 'admin' && $_SESSION['admin_role'] !== 'superadmin') {
    header("Location: ../../login.php?error=unauthorized");
    exit;
}

// CEGAH CACHING
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Tes BK | Tes BK Digital</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: url('../../assets/image/background.jpg');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      min-height: 100vh;
      padding: 40px 10px;
    }
    .card {
      border: none;
      border-radius: 16px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .btn-merah {
      
      background-color: #C60000 !important;
      color: #fff !important;
      border: none !important;
      transition: 0.3s;
    }
    .btn-merah:hover {
      background-color: #a30000 !important;
    }
    .btn-primary {
      background-color: #0066cc;
      border: none;
    }
    .kelola-card {
      background-color: #fff;
      transition: 0.3s;
    }
    .kelola-card:hover {
      background-color: #f8f9fa;
      transform: scale(1.01);
    }
    h4.fw-bold {
      color: #222;
      border-left: 5px solid #0066cc;
      padding-left: 10px;
    }
  </style>
</head>
<body>

  <div class="container my-5">

    <!-- ====================== KELOLA TES ====================== -->
    <div id="kelolaTes" class="card p-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Kelola Tes BK</h4>
        
      </div>
      <p class="text-muted">Ubah atau hapus jenis tes yang tersedia.</p>

      <!-- Card Tes -->
      <div class="kelola-card mb-3 p-3 border rounded">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h5 class="mb-1">Tes Minat Belajar</h5>
            <small class="text-muted">Mengukur minat belajar siswa</small>
          </div>
          <div>
            <button class="btn btn-success btn-sm me-1" onclick="tampilkanKelolaSoal('Tes Minat Belajar')">Edit</button>
            <button class="btn btn-merah btn-sm" onclick="alert('Tes berhasil dihapus!')">Hapus</button>
          </div>
        </div>
      </div>

      <div class="kelola-card mb-3 p-3 border rounded">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h5 class="mb-1">Tes Kepribadian</h5>
            <small class="text-muted">Mengukur karakter dan tipe kepribadian siswa</small>
          </div>
          <div>
            <button class="btn btn-success btn-sm me-1" onclick="tampilkanKelolaSoal('Tes Kepribadian')">Edit</button>
            <button class="btn btn-merah btn-sm" onclick="alert('Tes berhasil dihapus!')">Hapus</button>
          </div>
        </div>
      </div>

      <!-- Tombol kembali -->
      <div class="text-start mt-3">
        <button class="btn btn-merah btn-sm" onclick="loadContent('kelolaTes.php')">Kembali</button>
      </div>
    </div>

    <!-- ====================== KELOLA SOAL ====================== -->
    <div id="kelolaSoal" class="card p-4" style="display: none;">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold" id="judulTes">Kelola Soal</h4>
        <button class="btn btn-primary btn-sm">+ Tambah Soal Baru</button>
      </div>
      <p class="text-muted">Daftar soal yang tersedia untuk tes ini.</p>

      <div class="kelola-card p-3 mb-3 border rounded">
        <p class="mb-2"><strong>1.</strong> Ketika mencoba mengingat petunjuk arah, cara apa yang paling efektif bagi anda?</p>
        <p class="mb-2">A. Membayangkan peta<br>B. Mengulang dengan suara keras<br>C. Bergerak atau menunjuk</p>
        <div class="text-end">
          <button class="btn btn-success btn-sm me-1">Edit</button>
          <button class="btn btn-merah btn-sm" onclick="alert('Soal dihapus!')">Hapus</button>
        </div>
      </div>

      <div class="text-start mt-3">
        <button class="btn btn-merah btn-lg" onclick="kembaliKeTes()">Kembali</button>
      </div>
    </div>

  </div>

  <script>
    function tampilkanKelolaSoal(namaTes) {
      document.getElementById('kelolaTes').style.display = 'none';
      document.getElementById('kelolaSoal').style.display = 'block';
      document.getElementById('judulTes').innerText = 'Kelola Soal: ' + namaTes;
    }

    function kembaliKeTes() {
      document.getElementById('kelolaTes').style.display = 'block';
      document.getElementById('kelolaSoal').style.display = 'none';
    }

    function loadContent(file) {
      if (typeof window.parent.loadContent === 'function') {
        window.parent.loadContent(file);
      } else {
        console.error('loadContent function not found in parent');
        window.location.href = file;
      }
    }
  </script>

</body>
</html>
