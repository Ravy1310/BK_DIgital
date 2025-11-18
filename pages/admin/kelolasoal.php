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
  </style>
</head>

<body>

<div class="container my-5">
  <div class="card p-4">

    <h4 class="fw-bold mb-3">Kelola Tes BK</h4>
    <p class="text-muted">Ubah atau hapus jenis tes yang tersedia.</p>

    <!-- Tes Minat Belajar -->
    <div class="kelola-card mb-3 p-3 border rounded">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h5 class="mb-1">Tes Minat Belajar</h5>
          <small class="text-muted">Mengukur minat belajar siswa</small>
        </div>
        <div>
          <a href="kelola_soal.php?tes=Minat%20Belajar" class="btn btn-success btn-sm me-1">Edit</a>
          <button class="btn btn-merah btn-sm">Hapus</button>
        </div>
      </div>
    </div>

    <!-- Tes Kepribadian -->
    <div class="kelola-card mb-3 p-3 border rounded">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h5 class="mb-1">Tes Kepribadian</h5>
          <small class="text-muted">Mengukur karakter siswa</small>
        </div>
        <div>
          <a href="editsoal.php?tes=Kepribadian" class="btn btn-success btn-sm me-1">Edit</a>
          <button class="btn btn-merah btn-sm">Hapus</button>
        </div>
      </div>
    </div>
 <!-- Tombol kembali -->
      <div class="text-start mt-3">
        <button class="btn btn-merah btn-sm" onclick="loadContent('kelolaTes.php')">Kembali</button>
      </div>
    </div>
  </script>

</body>
</html>
