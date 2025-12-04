<?php
session_start();
// CEK APAKAH SUDAH LOGIN
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../../login.php");
    exit;
}

// CEK ROLE (admin atau superadmin bisa akses)
if ($_SESSION['admin_role'] !== 'admin' && $_SESSION['admin_role'] !== 'superadmin') {
    header("Location: ../../login.php?error=unauthorized");
    exit;
}

$base_dir = $_SERVER['DOCUMENT_ROOT'] . '/BK_DIGITAL/';
require_once $base_dir . 'includes/db_connection.php';

// Ambil data dari database
$jumlah_siswa = 0;
$jumlah_guru = 0;
$jumlah_tes = 0;
$tes_terpopuler = [];

try {
    // Jumlah Siswa dari tabel siswa
    $query_siswa = "SELECT COUNT(*) as total FROM siswa";
    $stmt_siswa = $pdo->prepare($query_siswa);
    $stmt_siswa->execute();
    $result_siswa = $stmt_siswa->fetch(PDO::FETCH_ASSOC);
    $jumlah_siswa = $result_siswa['total'] ?? 0;

    // Jumlah Guru dari tabel guru
    $query_guru = "SELECT COUNT(*) as total FROM guru";
    $stmt_guru = $pdo->prepare($query_guru);
    $stmt_guru->execute();
    $result_guru = $stmt_guru->fetch(PDO::FETCH_ASSOC);
    $jumlah_guru = $result_guru['total'] ?? 0;

    // Jumlah Tes dari tabel tes
    $query_tes = "SELECT COUNT(*) as total FROM tes";
    $stmt_tes = $pdo->prepare($query_tes);
    $stmt_tes->execute();
    $result_tes = $stmt_tes->fetch(PDO::FETCH_ASSOC);
    $jumlah_tes = $result_tes['total'] ?? 0;

    // Tes Terpopuler - HANYA 6 YANG PALING BANYAK DIKERJAKAN
    // Cek apakah tabel hasil_tes ada
    $query_check_hasil_tes = "SHOW TABLES LIKE 'hasil_tes'";
    $stmt_check = $pdo->prepare($query_check_hasil_tes);
    $stmt_check->execute();
    $hasil_tes_exists = $stmt_check->rowCount() > 0;

    if ($hasil_tes_exists) {
        // Cek apakah ada data pengerjaan tes
        $query_check_data = "SELECT COUNT(DISTINCT id_tes) as total_tes FROM hasil_tes";
        $stmt_check_data = $pdo->prepare($query_check_data);
        $stmt_check_data->execute();
        $hasil_data = $stmt_check_data->fetch(PDO::FETCH_ASSOC);
        $jumlah_tes_dikerjakan = $hasil_data['total_tes'] ?? 0;
        
        if ($jumlah_tes_dikerjakan > 0) {
            // Ambil 6 tes dengan jumlah pengerjaan TERBANYAK
            $query_populer = "
                SELECT 
                    t.kategori_tes,
                    COUNT(ht.id_hasil) as jumlah_pengerjaan
                FROM tes t 
                INNER JOIN hasil_tes ht ON t.id_tes = ht.id_tes 
                GROUP BY t.id_tes, t.kategori_tes 
                ORDER BY jumlah_pengerjaan DESC 
                LIMIT 6
            ";
        } else {
            // Jika belum ada yang dikerjakan, ambil 6 tes acak untuk placeholder
            $query_populer = "
                SELECT 
                    kategori_tes,
                    0 as jumlah_pengerjaan
                FROM tes 
                LIMIT 6
            ";
        }
        
        $stmt_populer = $pdo->prepare($query_populer);
        $stmt_populer->execute();
        $tes_terpopuler = $stmt_populer->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Jika tabel hasil_tes tidak ada, ambil semua tes (maks 6)
        $query_all_tes = "SELECT kategori_tes FROM tes LIMIT 6";
        $stmt_all_tes = $pdo->prepare($query_all_tes);
        $stmt_all_tes->execute();
        $all_tes = $stmt_all_tes->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($all_tes as $tes) {
            $tes_terpopuler[] = [
                'kategori_tes' => $tes['kategori_tes'],
                'jumlah_pengerjaan' => 0
            ];
        }
    }

} catch (Exception $e) {
    // Jika terjadi error, gunakan nilai default
    error_log("Error fetching dashboard data: " . $e->getMessage());
    $jumlah_siswa = 0;
    $jumlah_guru = 0;
    $jumlah_tes = 0;
    $tes_terpopuler = [];
}

// Jika tidak ada data tes terpopuler sama sekali, buat data dummy
if (empty($tes_terpopuler)) {
    $tes_terpopuler = [
        ['kategori_tes' => 'Tes Minat Belajar', 'jumlah_pengerjaan' => 0],
        ['kategori_tes' => 'Tes Kokologi', 'jumlah_pengerjaan' => 0],
        ['kategori_tes' => 'Tes Sosialisasi', 'jumlah_pengerjaan' => 0],
        ['kategori_tes' => 'Tes Percaya Diri', 'jumlah_pengerjaan' => 0],
        ['kategori_tes' => 'Tes Kedisiplinan', 'jumlah_pengerjaan' => 0],
        ['kategori_tes' => 'Tes Konsentrasi', 'jumlah_pengerjaan' => 0]
    ];
}

// Pastikan hanya menampilkan maksimal 6 tes
if (count($tes_terpopuler) > 6) {
    $tes_terpopuler = array_slice($tes_terpopuler, 0, 6);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      background: url('../../assets/image/background.jpg');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      font-family: 'Poppins', sans-serif;
      min-height: 100vh;
      margin-top: 15px;
    }
    .card-stat {
      border-radius: 15px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      padding: 1.5rem;
      text-align: center;
      background-color: #fff;
      transition: transform 0.2s;
    }
    .card-stat:hover {
      transform: scale(1.03);
    }
    .card-test {
      border-radius: 10px;
      border: 1px solid #ddd;
      background-color: #fff;
      padding: 1rem;
      text-align: center;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
      transition: transform 0.2s;
    }
    .card-test:hover {
      transform: scale(1.02);
    }
    h5, h6 {
      font-weight: 600;
    }
    .text-muted {
      font-size: 0.9rem;
    }
  </style>
</head>

<body>
  <div class="container my-3">
    <h5 class="fw-bold pt-3">Statistik Sekilas</h5>
    <p class="text-muted">Ringkasan data utama</p>

    <!-- Statistik Sekilas -->
    <div class="row g-4 mb-5">
      <!-- Jumlah Siswa -->
      <div class="col-md-4">
        <div class="card-stat">
          <div class="d-flex justify-content-center align-items-center mx-auto rounded-circle" style="background-color:#28a745; width:60px; height:60px;">
            <svg viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg" width="30" height="30">
              <rect fill="none" height="256" width="256"/>
              <line fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="16" x1="32" x2="32" y1="64" y2="144"/>
              <path d="M54.2,216a88.1,88.1,0,0,1,147.6,0" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/>
              <polygon fill="none" points="224 64 128 96 32 64 128 32 224 64" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/>
              <path d="M169.3,82.2a56,56,0,1,1-82.6,0" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/>
            </svg>
          </div>
          <h6 class="mt-2">Jumlah Siswa</h6>
          <h4><?php echo $jumlah_siswa; ?></h4>
        </div>
      </div>

      <!-- Jumlah Guru -->
      <div class="col-md-4">
        <div class="card-stat">
          <div class="d-flex justify-content-center align-items-center mx-auto rounded-circle" style="background-color:#007bff; width:60px; height:60px;">
            <svg id="Icons_Teacher" viewBox="0 0 96 96" xmlns="http://www.w3.org/2000/svg" width="35" height="35">
              <path fill="white" d="M87.8 19L23.8 19C21.6 19 19.8 20.8 19.8 23L19.8 37.5C20.9 37.2 22.2 37 23.4 37C24.2 37 25 37.1 25.8 37.2L25.8 25L85.8 25L85.8 63L51.9 63L46.2 69L87.8 69C90 69 91.8 67.2 91.8 65L91.8 23C91.8 20.8 90 19 87.8 19Z"/>
              <path fill="white" d="M23.5 58C28.2 58 32 54.2 32 49.5C32 44.8 28.2 41 23.5 41C18.8 41 15 44.8 15 49.5C14.9 54.2 18.8 58 23.5 58Z"/>
              <path fill="white" d="M56.2 48.1C54.9 46.1 52.3 45.6 50.3 46.8C49.9 47 49.7 47.4 49.5 47.6L34.9 62.8C33.5 62.1 32 61.5 30.5 61C28.2 60.6 25.8 60.1 23.5 60.1C21.2 60.1 18.8 60.5 16.5 61.2C13.1 62.1 10.1 63.8 7.6 65.9C7 66.5 6.5 67.4 6.3 68.2L4.2 77L34.1 77L34.1 76.9L42.6 67L55.7 53.2C56.9 52 57.3 49.7 56.2 48.1Z"/>
            </svg>
          </div>
          <h6 class="mt-2">Jumlah Guru</h6>
          <h4><?php echo $jumlah_guru; ?></h4>
        </div>
      </div>

      <!-- Jumlah Tes -->
      <div class="col-md-4">
        <div class="card-stat">
          <div class="d-flex justify-content-center align-items-center mx-auto rounded-circle" style="background-color:#f7931e; width:60px; height:60px;">
            <svg viewBox="0 0 371.43 512" xmlns="http://www.w3.org/2000/svg" width="32" height="32">
              <defs>
                <style>
                  .cls-1{fill:none;stroke:white;stroke-linecap:round;stroke-linejoin:round;stroke-width:20px;}
                </style>
              </defs>
              <g data-name="Layer 2" id="Layer_2">
                <g data-name="E423_Comparison_pros_and_cons_test" id="E423_Comparison_pros_and_cons_test">
                  <line class="cls-1" x1="78.76" x2="142.93" y1="106.26" y2="106.26"/>
                  <line class="cls-1" x1="207.11" x2="271.28" y1="106.26" y2="106.26"/>
                  <line class="cls-1" x1="89.45" x2="292.67" y1="181.13" y2="181.13"/>
                  <line class="cls-1" x1="89.45" x2="292.67" y1="245.3" y2="245.3"/>
                  <line class="cls-1" x1="89.45" x2="292.67" y1="309.48" y2="309.48"/>
                  <line class="cls-1" x1="89.45" x2="292.67" y1="373.65" y2="373.65"/>
                  <line class="cls-1" x1="89.45" x2="292.67" y1="437.83" y2="437.83"/>
                  <line class="cls-1" x1="110.84" x2="110.84" y1="74.17" y2="138.35"/>
                  <path class="cls-1" d="M331.31,502H40.12A30.13,30.13,0,0,1,10,471.88V40.12A30.13,30.13,0,0,1,40.12,10H301.18l60.25,60.24V471.88A30.13,30.13,0,0,1,331.31,502Z"/>
                </g>
              </g>
            </svg>
          </div>
          <h6 class="mt-2">Jumlah Tes</h6>
          <h4><?php echo $jumlah_tes; ?></h4>
        </div>
      </div>
    </div>

    <!-- Tes Terpopuler -->
    <h5 class="fw-bold">Tes Terpopuler</h5>
    <p class="text-muted">Tes paling banyak yang dikerjakan siswa</p>

    <div class="row g-3">
      <?php 
      // Hitung berapa kolom yang dibutuhkan (maks 6, dalam 3 kolom per baris)
      $col_class = 'col-md-4'; // Default 3 per baris
      if (count($tes_terpopuler) <= 3) {
          $col_class = 'col-md-4'; // 3 per baris
      } else {
          $col_class = 'col-md-4'; // 3 per baris, akan ada 2 baris
      }
      
      foreach ($tes_terpopuler as $tes): ?>
        <div class="<?php echo $col_class; ?>">
          <div class="card-test">
            <span class="fw-bold"><?php echo htmlspecialchars($tes['kategori_tes']); ?></span><br>
            <small class="text-muted">
              Dikerjakan oleh <?php echo $tes['jumlah_pengerjaan']; ?> siswa
            </small>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>