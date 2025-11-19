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

$id_soal = isset($_GET['id_soal']) ? intval($_GET['id_soal']) : 0;
$id_tes  = isset($_GET['id_tes']) ? intval($_GET['id_tes']) : 0;

if ($id_soal <= 0) {
    die("ID Soal tidak valid");
}

// DB
$base_dir = $_SERVER['DOCUMENT_ROOT'] . '/BK_DIGITAL/';
require_once $base_dir . 'includes/db_connection.php';

// AMBIL DATA SOAL
$stmt = $pdo->prepare("SELECT * FROM soal_tes WHERE id_soal = ? LIMIT 1");
$stmt->execute([$id_soal]);
$soal = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$soal) {
    die("Soal tidak ditemukan");
}

// AMBIL OPSI (DINAMIS)
$stmt_opsi = $pdo->prepare("SELECT * FROM opsi_jawaban WHERE id_soal = ? ORDER BY id_opsi ASC");
$stmt_opsi->execute([$id_soal]);
$opsi_list = $stmt_opsi->fetchAll(PDO::FETCH_ASSOC);

// Tentukan jawaban benar dari bobot tertinggi
$jawaban_benar = "";
if (!empty($opsi_list)) {
    $bobot = array_column($opsi_list, 'bobot');
    $maxIndex = array_keys($bobot, max($bobot))[0];
    $jawaban_benar = chr(65 + $maxIndex); // 0=A, 1=B, dst
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Soal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

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
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
  </style>
</head>

<body>

<div class="container my-4">
  <div class="card p-4">

    <h4 class="fw-bold mb-3">Edit Soal</h4>
    <p class="text-muted">Perbarui pertanyaan & pilihan jawaban (otomatis membaca jumlah OPSI).</p>

    <form action="../../includes/admin_control/UbahSoal_Controller.php" method="POST">

        <input type="hidden" name="id_soal" value="<?= $id_soal ?>">
        <input type="hidden" name="id_tes" value="<?= $id_tes ?>">

        <div class="mb-3">
            <label class="form-label">Pertanyaan</label>
            <textarea class="form-control" name="pertanyaan" rows="3" required><?= htmlspecialchars($soal['pertanyaan']) ?></textarea>
        </div>

        <label class="form-label fw-semibold">Pilihan Jawaban:</label>

        <?php 
        $label = "A";
        foreach ($opsi_list as $index => $opsi): 
        ?>
            <div class="mb-3">
                <label class="form-label">Opsi <?= $label ?></label>
                <input type="text" class="form-control" 
                       name="opsi_<?= strtolower($label) ?>" 
                       value="<?= htmlspecialchars($opsi['opsi']) ?>" required>
            </div>
        <?php 
            $label = chr(ord($label) + 1);
        endforeach;
        ?>

        <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-secondary px-4" onclick="window.history.back()">Kembali</button>
            <button type="submit" class="btn btn-success px-4">Simpan Perubahan</button>
        </div>

    </form>

  </div>
</div>

</body>
</html>
