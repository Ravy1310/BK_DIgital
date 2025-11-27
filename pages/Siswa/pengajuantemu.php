<?php
session_start();
require_once __DIR__ . "/../../includes/db_connection.php";

// CEK LOGIN SISWA
if (!isset($_SESSION['siswa_logged_in'])) {
    header("Location: verifikasi_jadwal.php");
    exit;
}

$id_siswa = $_SESSION['siswa_id'];
$nama_siswa = $_SESSION['siswa_nama'];

$success_message = ""; // Untuk menampung notifikasi sukses

// PROSES SIMPAN DATA
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nama = $_POST['nama'];
    $tanggal = $_POST['tanggal'];
    $topik = $_POST['topik'];
    $jam = $_POST['jam'];
    $id_guru = $_POST['id_guru'];

    $stmt = $pdo->prepare("
        INSERT INTO jadwal_konseling 
        (id_siswa, nama, tanggal, topik, jam, id_guru, status)
        VALUES (?, ?, ?, ?, ?, ?, 'menunggu')
    ");

    $stmt->execute([
        $id_siswa,
        $nama,
        $tanggal,
        $topik,
        $jam,
        $id_guru
    ]);

    // Munculkan notifikasi
    $success_message = "Pengajuan jadwal berhasil dikirim!";

    // Redirect otomatis
    echo "
    <script>
        setTimeout(function() {
            window.location.href = 'jadwaltemu.php';
        }, 2000);
    </script>
    ";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ajukan Jadwal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: url('../../assets/image/background.jpg');
      background-size: cover;
      background-position: center;
      min-height: 100vh;
    }
    .card-custom {
      background: rgba(255, 255, 255, 0.9);
      border-radius: 12px;
      padding: 30px;
      box-shadow: 0 5px 18px rgba(0,0,0,0.08);
    }

    /* Fade-in untuk card */
.card-custom {
    animation: fadeInUp 0.8s ease;
}

/* Animasi alert success */
.alert-success {
    animation: popIn 0.6s ease;
}

/* Animasi tombol saat hover */
button:hover, .btn:hover {
    transform: scale(1.03);
    transition: 0.2s ease;
}

/* Keyframes */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(25px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes popIn {
    0% {
        opacity: 0;
        transform: scale(0.8);
    }
    70% {
        opacity: 1;
        transform: scale(1.05);
    }
    100% {
        transform: scale(1);
    }
}

  </style>
</head>

<body>

  <div class="container py-4">

    <!-- Jika sukses tampilkan alert -->
    <?php if ($success_message): ?>
        <div class="alert alert-success text-center fw-bold">
            <?= $success_message ?>
            <br>
            <small>Anda akan dialihkan ke menu jadwal...</small>
        </div>
    <?php endif; ?>

    <div class="card-custom mx-auto" style="max-width: 700px;">
      <h4 class="fw-bold mb-4">Ajukan Jadwal</h4>

      <form method="POST">

        <!-- Nama -->
        <div class="mb-3">
          <label class="form-label">Nama</label>
          <input type="text" class="form-control" name="nama" value="<?= $nama_siswa ?>" readonly>
        </div>

        <!-- Tanggal -->
        <div class="mb-3">
          <label class="form-label">Tanggal Bimbingan</label>
          <input type="date" class="form-control" name="tanggal" required>
        </div>

        <!-- Topik -->
        <div class="mb-3">
          <label class="form-label">Topik Konseling</label>
          <select class="form-select" name="topik" required>
            <option selected disabled>Pilih topik konseling</option>
            <option>Masalah Akademik</option>
            <option>Masalah Pergaulan</option>
            <option>Masalah Keluarga</option>
            <option>Perencanaan Karir</option>
            <option>Kesehatan Mental</option>
            <option>Lainnya</option>
          </select>
        </div>

        <!-- Jam -->
        <div class="mb-3">
          <label class="form-label">Jam</label>
          <select class="form-select" name="jam" required>
            <option selected disabled>Pilih jam</option>
            <option>07:00</option>
            <option>08:00</option>
            <option>09:00</option>
            <option>10:00</option>
            <option>11:00</option>
          </select>
        </div>

        <!-- Guru BK -->
        <div class="mb-4">
          <label class="form-label">Guru BK</label>
          <select class="form-select" name="id_guru" required>
            <option selected disabled>Pilih guru BK</option>
            <option value="6">Budi Santoso</option>
            <option value="7">Siti Rahayu</option>
            <option value="16">Rafi Isnanto</option>
          </select>
        </div>

        <div class="d-flex justify-content-between">
          <a href="jadwaltemu.php" class="btn btn-danger">Batal</a>
          <button type="submit" class="btn btn-primary">Kirim</button>
        </div>

      </form>
    </div>

  </div>

</body>
</html>
