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

// GET PARAMETER
$id_tes = isset($_GET['id_tes']) ? intval($_GET['id_tes']) : 0;

$base_dir = $_SERVER['DOCUMENT_ROOT'] . '/BK_DIGITAL/';
require_once $base_dir . 'includes/db_connection.php';

// CEK TES ADA
$stmt_tes = $pdo->prepare("SELECT kategori_tes, deskripsi_tes FROM tes WHERE id_tes = ?");
$stmt_tes->execute([$id_tes]);
$tesData = $stmt_tes->fetch(PDO::FETCH_ASSOC);

if (!$tesData) {
    die("Tes tidak ditemukan");
}

$tes = $tesData['kategori_tes']; 
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
       background-color: #710303 !important;
    }
  </style>
</head>

<body>

<div class="container my-3">
  <div class="card p-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="fw-bold">Kelola Soal: <?= htmlspecialchars($tes) ?></h4>

      <!-- BUTTON TAMBAH SOAL -->
      <a href="tambahsoal.php?id_tes=<?= $id_tes ?>" class="btn btn-primary px-4">
        + Tambah Soal Baru
      </a>
    </div>

    <p class="text-muted">Daftar soal untuk tes ini.</p>

    <?php
    // AMBIL SEMUA SOAL TES
    $stmt_soal = $pdo->prepare("SELECT * FROM soal_tes WHERE id_tes = ? ORDER BY id_soal ASC");
    $stmt_soal->execute([$id_tes]);
    $soal_list = $stmt_soal->fetchAll(PDO::FETCH_ASSOC);

    if (count($soal_list) === 0) {
        echo '<div class="alert alert-info">Belum ada soal pada tes ini.</div>';
    } else {
        $no = 1;
        foreach ($soal_list as $s):

            // AMBIL OPSI DARI TABEL opsi_jawaban
            $stmt_opsi = $pdo->prepare("SELECT * FROM opsi_jawaban WHERE id_soal = ? ORDER BY id_opsi ASC");
            $stmt_opsi->execute([$s['id_soal']]);
            $opsi_list = $stmt_opsi->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="kelola-card p-3 mb-3 border rounded bg-white">
      <p><strong><?= $no++ ?>.</strong> <?= htmlspecialchars($s['pertanyaan']) ?></p>

      <p>
      <?php
        if (count($opsi_list) > 0) {
    foreach ($opsi_list as $i => $o) {
        $huruf = chr(65 + $i); // A, B, C ...
        echo "<strong>$huruf.</strong> " . htmlspecialchars($o['opsi']) . "<br>";
    }
} else {
    echo "<em class='text-muted'>Belum ada opsi jawaban.</em>";
}
      ?>
      </p>

      <div class="text-end">
        <a href="ubahsoal.php?id_soal=<?= $s['id_soal'] ?>&id_tes=<?= $id_tes ?>" 
           class="btn btn-success px-3 me-1">Edit</a>

        <button class="btn btn-merah px-3"
        onclick="hapusSoal(<?= $s['id_soal'] ?>, <?= $id_tes ?>)">
    Hapus
</button>
      </div>
    </div>

    <?php
        endforeach;
    }
    ?>

    <div class="text-start mt-3">
        <button type="button" class="btn btn-merah px-4" onclick="window.history.back()">Kembali</button>
    </div>

  </div>
</div>

<script>
function hapusSoal(id_soal, id_tes) {
    if (!confirm("Yakin ingin menghapus soal ini?")) return;

    window.location.href =
        "../../includes/admin_control/HapusSoal_Controller.php?id_soal=" 
        + id_soal + "&id_tes=" + id_tes;
}
</script>

</body>
</html>
