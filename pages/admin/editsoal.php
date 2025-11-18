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

// TERIMA PARAMETER TES
$tes = isset($_GET['tes']) ? $_GET['tes'] : "Nama Tes Tidak Ditemukan";
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Soal | <?= htmlspecialchars($tes) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: url('../../assets/image/background.jpg');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      padding: 40px 10px;
    }
    .card {
      border: none;
      border-radius: 16px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    }
    .btn-merah {
      background-color: #C60000 !important;
      color: #fff !important;
      border: none !important;
    }
    .btn-merah:hover {
       background-color: #710303ff !important;
      transform: translateY(-1px);
    }
  </style>
</head>

<body>

<div class="container my-3">
  <div class="card p-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="fw-bold">Kelola Soal: <?= htmlspecialchars($tes) ?></h4>
      <button class="btn btn-primary px-4">+ Tambah Soal Baru</button>
    </div>

    <p class="text-muted">Daftar soal untuk tes ini.</p>

    <!-- Contoh Soal Statis (nanti bisa dibuat dinamis dari DB) -->
    <div class="kelola-card p-3 mb-3 border rounded bg-white">
      <p><strong>1.</strong> Ketika mencoba mengingat petunjuk arah, cara apa yang paling efektif bagi anda?</p>
      <p>
        A. Membayangkan peta<br>
        B. Mengulang dengan suara keras<br>
        C. Bergerak atau menunjuk
      </p>
      <div class="text-end">
        <button class="btn btn-success px-3 me-1">Edit</button>
        <button class="btn btn-merah px-3">Hapus</button>
      </div>
    </div>
<div class="text-start mt-3">
        <button type="button" class="btn btn-merah px-4" onclick="window.loadContent('kelolasoal.php')">Kembali</button>
  </div>
    </div>
  </script>


</body>
</html>
