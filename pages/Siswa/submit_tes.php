<?php 
require_once __DIR__ . "/../../includes/db_connection.php";

$id_tes   = $_POST['id_tes'] ?? 0;
$id_siswa = $_POST['id_siswa'] ?? 0;
$jawaban  = $_POST['jawaban'] ?? "";

// Ambil detail tes
$stmt = $pdo->prepare("SELECT * FROM tes WHERE id_tes = ?");
$stmt->execute([$id_tes]);
$tes = $stmt->fetch();

if (!$tes) {
    die("Tes tidak ditemukan.");
}

// Simpan jawaban ke database
$save = $pdo->prepare("
    INSERT INTO hasil_tes (id_tes, id_siswa, jawaban, tanggal_submit) 
    VALUES (?, ?, ?, NOW())
");
$save->execute([$id_tes, $id_siswa, $jawaban]);

// Nilai dummy (70–100)
$nilai_dummy = rand(70, 100);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tes Tersubmit</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            background: #e9f0fa;
            font-family: 'Poppins', sans-serif;
            padding: 40px 20px;
        }
        .page-title {
            background: #003893;
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 30px;
        }
        .page-title h2 {
            margin: 0;
            font-weight: 700;
        }
        .section-box {
            background: white;
            padding: 20px;
            border-radius: 12px;
        }

        /* ===== SCORE BOX (KIRI & KECIL) ===== */
        .score-box {
            background: #003893;
            color: white;
            padding: 10px 14px;
            border-radius: 6px;
            text-align: left;
            width: 200px;              /* ukuran kecil */
            margin: 10px 0 20px 0;     /* berada di kiri */
        }
        .score-box h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
        }

        .btn-primary-custom {
            background-color: #003893;
            border: none;
            padding: 10px 22px;
            border-radius: 7px;
            font-weight: 600;
            color: white; 
        }
        .btn-primary-custom:hover {
            background-color: #002d73;
        }
        ul li strong {
            color: #003893;
        }
    </style>
</head>

<body>

<div class="container">

    <div class="page-title">
        <h2>Tes Berhasil Dikirim</h2>
        <p style="margin-top:5px; font-size:14px;">Terima kasih telah mengerjakan tes ini</p>
    </div>

    <div class="section-box">

        <h4 class="mb-3">Detail Pengisian Kamu</h4>

        <ul class="list-group mb-4">
            <li class="list-group-item">
                <strong>Kategori Tes:</strong> <?= htmlspecialchars($tes['kategori_tes']) ?>
            </li>

            <li class="list-group-item">
                <strong>Deskripsi:</strong> <?= htmlspecialchars($tes['deskripsi_tes']) ?>
            </li>

            <li class="list-group-item">
                <strong>ID Siswa:</strong> <?= htmlspecialchars($id_siswa) ?>
            </li>

            <li class="list-group-item">
                <strong>Jawaban:</strong><br>
                <?= nl2br(htmlspecialchars($jawaban)) ?>
            </li>
        </ul>

        <!-- SCORE BOX KECIL DAN DI KIRI -->
        <div class="score-box">
            <h3>Nilai Kamu: <?= $nilai_dummy ?></h3>
        </div>

        <div class="text-center mt-3">
            <a href="tesbk.php" class="btn btn-primary-custom">
                Kembali ke Daftar Tes
            </a>
        </div>

    </div>

</div>

</body>
</html>
