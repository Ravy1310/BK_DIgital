<?php
session_start();
require_once "../../includes/db_connection.php"; // koneksi PDO

$error = "";

// Jika ada notifikasi sukses dari pengaduan.php
$success = isset($_GET["success"]) ? true : false;

// ===========================
// FITUR BARU: PENGADUAN ANONIM
// ===========================
// Jika URL: verifikasi_pengaduan.php?anonim=1
// Maka langsung masuk pengaduan anonim tanpa verifikasi ID
if (isset($_GET["anonim"]) && $_GET["anonim"] == "1") {

    // Tidak menyimpan ID siswa
    $_SESSION["verified_siswa_id"] = null;

    // Langsung menuju form pengaduan anonim
    header("Location: riwayat_aduan.php?idsiswa=0&anonim=1");
    exit;
}

// ===========================
// PROSES VERIFIKASI ID
// ===========================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id_siswa = trim($_POST["id_siswa"]);

    // Cek ID siswa di database
    $stmt = $pdo->prepare("SELECT id_siswa FROM siswa WHERE id_siswa = ? LIMIT 1");
    $stmt->execute([$id_siswa]);
    $siswa = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($siswa) {
        // Simpan ID ke session
        $_SESSION["verified_siswa_id"] = $siswa["id_siswa"];

        // Lanjut ke form pengaduan teridentifikasi
        header("Location: riwayat_aduan.php?idsiswa=" . $siswa["id_siswa"] . "&anonim=0");
        exit;
    } else {
        $error = "ID tidak ditemukan. Pastikan data benar.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi ID Siswa - BK Digital</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #f5f7fb;
            font-family: 'Poppins', sans-serif;
            animation: fadeIn 0.8s ease-in-out;
        }
        @keyframes fadeIn {
            from {opacity: 0;}
            to {opacity: 1;}
        }
        .shield {
            width: 200px;
        }
        .card-custom {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            animation: slideUp 0.8s ease;
        }
        @keyframes slideUp {
            from {transform: translateY(30px); opacity: 0;}
            to {transform: translateY(0); opacity: 1;}
        }
        h2 {
            font-weight: 700;
            color: #003893;
        }
        .btn-custom {
            background-color: #003893;
            color: white;
            padding: 10px 25px;
            border-radius: 6px;
        }
        .btn-custom:hover {
            background-color: #002d73;
        }
    </style>
</head>

<body>

<div class="container py-5">

    <!-- NOTIFIKASI SUKSES -->
    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <strong>Berhasil!</strong> Pengaduan Anda telah terkirim dan dicatat.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center align-items-center">

        <!-- KIRI -->
        <div class="col-md-5 text-center">
            <img class="shield"
                 src="data:image/svg+xml;utf8,<?xml version='1.0'?><svg xmlns='http://www.w3.org/2000/svg' width='48' height='48' viewBox='0 0 48 48'><path fill='%23002E6E' d='M24 2l-18 8v12c0 11.11 7.67 21.47 18 24 10.33-2.53 18-12.89 18-24v-12l-18-8zm0 21.98h14c-1.06 8.24-6.55 15.58-14 17.87v-17.85h-14v-11.4l14-6.22v17.6z'/><path fill='none' d='M0 0h48v48h-48z'/></svg>"
                 alt="ikon biru">

            <h4 class="fw-bold">Verifikasi ID Siswa</h4>
            <p class="mt-4">
                Anda Akan Melakukan:<br>
                <span class="fw-bold">(Pengaduan)</span>
            </p>
        </div>

        <!-- KANAN -->
        <div class="col-md-6">
            <div class="card-custom">
                <h2 class="text-center">MASUKKAN<br>ID Siswa Anda</h2>

                <p class="text-center mt-2 mb-4">
                    ID diperlukan untuk menyimpan data aduan anda agar tercatat dengan benar
                </p>

                <!-- ERROR -->
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger text-center"><?= $error ?></div>
                <?php endif; ?>

                <!-- FORM VERIFIKASI -->
                <form action="" method="POST">
                    <input type="text" name="id_siswa" class="form-control mb-3" placeholder="ID Siswa" required>

                    <div class="text-center">
                        <button type="submit" class="btn btn-custom">Buat Aduan Sekarang</button>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
