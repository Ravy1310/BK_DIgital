<?php
session_start();
require_once __DIR__ . "/../../includes/db_connection.php"; // memuat $pdo

// Default pesan error
$message = "";

// Ambil error message dari session jika ada
if (isset($_SESSION['error_id'])) {
    $message = $_SESSION['error_id'];
    unset($_SESSION['error_id']); // hapus setelah ditampilkan
}

// PROSES KETIKA TOMBOL SUBMIT DIKLIK
if (isset($_POST['cek_id'])) {

    if (!$pdo) {
        die("Koneksi database gagal.");
    }

    $id = trim($_POST['id_siswa']);

    // Query PDO benar
    $stmt = $pdo->prepare("SELECT id_siswa, nama FROM siswa WHERE id_siswa = ?");
    $stmt->execute([$id]);

    $data = $stmt->fetch();

    if ($data) {
        // Set session siswa
        $_SESSION['siswa_logged_in'] = true;
        $_SESSION['siswa_id'] = $data['id_siswa'];
        $_SESSION['siswa_nama'] = $data['nama'];

        header("Location: jadwaltemu.php");
        exit;

    } else {

        $_SESSION['error_id'] = "❌ ID Siswa tidak ditemukan. Periksa kembali.";
        header("Location: verifikasi_jadwal.php");
        exit;
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
            background-color: #ffff;
            font-family: 'Poppins', sans-serif;
        }
        .shield {
            width: 200px;
        }
        .card-custom {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
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
        .error-box {
            background: #ffe1e1;
            color: #b10000;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 10px;
            font-size: 14px;
            text-align: center;
        }

        /* ============================= */
        /*        ANIMASI TAMBAHAN       */
        /* ============================= */

        .fade-in {
            animation: fadeIn 0.8s ease-out forwards;
            opacity: 0;
        }

        .slide-left {
            animation: slideLeft 0.8s ease-out forwards;
            opacity: 0;
        }

        .slide-right {
            animation: slideRight 0.8s ease-out forwards;
            opacity: 0;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.96); }
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes slideLeft {
            from { opacity: 0; transform: translateX(-40px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @keyframes slideRight {
            from { opacity: 0; transform: translateX(40px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* Tambahan animasi hover tombol */
        .btn-custom {
            transition: 0.3s;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
    </style>
</head>

<body>

<div class="container py-5">
    <div class="row justify-content-center align-items-center">

        <!-- Kiri (DITAMBAH SLIDE-LEFT) -->
        <div class="col-md-5 text-center slide-left">
            <img class="shield" src="data:image/svg+xml;utf8,<?xml version='1.0'?><svg xmlns='http://www.w3.org/2000/svg' width='48' height='48' viewBox='0 0 48 48'><path fill='%23002E6E' d='M24 2l-18 8v12c0 11.11 7.67 21.47 18 24 10.33-2.53 18-12.89 18-24v-12l-18-8zm0 21.98h14c-1.06 8.24-6.55 15.58-14 17.87v-17.85h-14v-11.4l14-6.22v17.6z'/><path fill='none' d='M0 0h48v48h-48z'/></svg>"
     alt="ikon biru">

            <h4 class="fw-bold">Verifikasi ID Siswa</h4>
            <p class="mt-4">
                Anda Akan Melakukan:<br>
                <span class="fw-bold">(Pengajuan Jadwal Konsultasi)</span>
            </p>
        </div>

        <!-- Kanan (DITAMBAH SLIDE-RIGHT) -->
        <div class="col-md-6 slide-right">
            <div class="card-custom fade-in">

                <?php if (!empty($message)): ?>
                <div class="error-box">
                    <?= $message ?>
                </div>
                <?php endif; ?>

                <h2 class="text-center">MASUKKAN<br>ID SISWA ANDA</h2>

                <p class="text-center mt-2 mb-4">
                    ID digunakan untuk memvalidasi data sebelum melakukan pengajuan jadwal konsultasi.
                </p>

                <form action="verifikasi_jadwal.php" method="POST">
                    <input type="text" name="id_siswa" class="form-control mb-3" placeholder="Masukkan ID Siswa" required>
                    <div class="text-center">
                        <button type="submit" name="cek_id" class="btn btn-custom">
                            Masuk Menu Pengajuan Konseling
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>

</body>
</html>
