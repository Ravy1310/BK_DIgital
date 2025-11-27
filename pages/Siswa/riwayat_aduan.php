<?php
include 'header.php';
session_start();
require_once "../../includes/db_connection.php"; 

$id_siswa = $_GET["idsiswa"] ?? 0;
$anonim   = $_GET["anonim"] ?? 0;

// ==========================
// VALIDASI MODE
// ==========================
if ($anonim == 1) {
    $nama_siswa = "Pengguna Anonim";
} else {
    if (empty($id_siswa) || $id_siswa == 0) {
        $nama_siswa = "ID Tidak Valid";
    } else {
        $stmt = $pdo->prepare("SELECT nama FROM siswa WHERE id_siswa = ? LIMIT 1");
        $stmt->execute([$id_siswa]);
        $data_siswa = $stmt->fetch(PDO::FETCH_ASSOC);
        $nama_siswa = $data_siswa ? $data_siswa["nama"] : "ID Tidak Ditemukan";
    }
}

// ==========================
// AMBIL SEMUA RIWAYAT (TERMASUK ANONIM)
// ==========================

$stmt2 = $pdo->prepare("
    SELECT * FROM pengaduan 
    WHERE id_siswa = ? OR id_siswa IS NULL OR id_siswa = 0
    ORDER BY tanggal_pengaduan DESC
");
$stmt2->execute([$id_siswa]);
$riwayat = $stmt2->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pengaduan - BK Digital</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: url('../../assets/image/background.jpg');
            background-size: cover;
            background-position: center;
            font-family: 'Poppins', sans-serif;
        }

        .main-wrapper {
            background: white;
            padding: 45px;
            border-radius: 24px;
            box-shadow: 0 6px 16px rgba(0,0,0,0.1);
            margin-top: 40px;
        }

        .judul-section {
            text-align:center;
            font-size: 32px;
            font-weight:700;
            color:#003893;
        }

        .pengaduan-card {
            background:white;
            border-radius:16px;
            box-shadow:0 4px 10px rgba(0,0,0,0.08);
            overflow:hidden;
            transition:0.25s;
            border:1px solid #d7e0ef;
        }

        .pengaduan-card:hover {
            transform: translateY(-5px);
            box-shadow:0 8px 18px rgba(0,0,0,0.15);
        }

        .card-header-custom {
            background:#0050BC;
            padding:12px 18px;
            color:white;
            font-size:16px;
            font-weight:600;
        }

        .card-body-custom {
            padding:18px;
            font-size:15px;
        }

        .footer-info {
            padding:12px 18px;
            background:#f5f8ff;
            font-size:14px;
            color:#555;
            display:flex;
            align-items:center;
            gap:6px;
        }

        .btn-primary {
            background:#003893;
            border:none;
            font-weight:600;
        }
    </style>
</head>

<body>

<div class="container">

    <div class="main-wrapper">

        <h2 class="judul-section mb-4">Riwayat Pengaduan</h2>

        <p><strong>Siswa:</strong> <?= htmlspecialchars($nama_siswa) ?></p>
        <p><strong>Mode:</strong> <?= $anonim == 1 ? "Anonim" : "Teridentifikasi" ?></p>

        <a href="pengaduan.php?idsiswa=<?= $id_siswa ?>&anonim=<?= $anonim ?>" 
           class="btn btn-primary mb-4">
            + Buat Pengaduan Baru
        </a>

        <h4 class="mb-3">Riwayat Pengaduan</h4>
        <hr>

        <?php if (empty($riwayat)): ?>
            <p class="text-muted">Belum ada pengaduan sebelumnya.</p>

        <?php else: ?>

        <div class="row g-4">

        <?php foreach ($riwayat as $r): ?>

            <?php  
            // Handling status warna
            $status = strtolower($r["status_pengaduan"] ?? "menunggu");
            $warna = [
                "menunggu" => "bg-secondary",
                "diproses" => "bg-warning text-dark",
                "selesai"  => "bg-success",
            ];
            ?>

            <div class="col-md-4">
                <div class="pengaduan-card">

                    <div class="card-header-custom d-flex justify-content-between">
                        <span>Pengaduan</span>
                        <span class="badge <?= $warna[$status] ?? 'bg-secondary' ?>">
                            <?= htmlspecialchars($r["status_pengaduan"]) ?>
                        </span>
                    </div>

                    <div class="card-body-custom">
                        <?= nl2br(htmlspecialchars($r["isi_aduan"])) ?>
                    </div>

                    <div class="footer-info">
                        <i class="bi bi-calendar-event"></i>
                        <?= $r["tanggal_pengaduan"] ?>
                    </div>

                </div>
            </div>

        <?php endforeach; ?>

        </div>

        <?php endif; ?>

    </div>

</div>

</body>
</html>
