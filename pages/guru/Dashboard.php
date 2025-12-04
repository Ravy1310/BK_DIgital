<?php
// Dashboard.php
session_start();

// SET TIMEZONE KE INDONESIA
date_default_timezone_set('Asia/Jakarta');

// Koneksi database
require_once __DIR__ . '/../../includes/db_connection.php';

// Ambil id_guru dari session (asumsi session sudah ada)
$id_guru = $_SESSION['guru_id'] ?? null;

// Debug timezone
echo "<!-- DEBUG: PHP Timezone = " . date_default_timezone_get() . " -->\n";
echo "<!-- DEBUG: Current PHP Time = " . date('H:i:s') . " -->\n";
echo "<!-- DEBUG: Current PHP Date = " . date('Y-m-d') . " -->\n";

// Fungsi untuk mendapatkan jumlah pengaduan baru
function getJumlahPengaduanBaru($pdo) {
    try {
        $sql = "SELECT COUNT(*) as total FROM pengaduan WHERE status = 'Menunggu'";
        $stmt = $pdo->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    } catch (PDOException $e) {
        return 0;
    }
}

// Fungsi untuk mendapatkan jumlah jadwal hari ini
function getJumlahJadwalHariIni($pdo, $id_guru = null) {
    try {
        $today = date('Y-m-d');
        $current_time = date('H:i:s');
        
        if ($id_guru) {
            $sql = "SELECT COUNT(*) as total FROM jadwal_konseling 
                    WHERE DATE(Tanggal_Konseling) = :tanggal 
                    AND id_guru = :id_guru
                    AND Waktu_Konseling > :waktu_sekarang
                    AND status IN ('Disetujui', 'pending')";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':tanggal' => $today,
                ':id_guru' => $id_guru,
                ':waktu_sekarang' => $current_time
            ]);
        } else {
            $sql = "SELECT COUNT(*) as total FROM jadwal_konseling 
                    WHERE DATE(Tanggal_Konseling) = :tanggal
                    AND Waktu_Konseling > :waktu_sekarang
                    AND status IN ('Disetujui', 'pending')";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':tanggal' => $today,
                ':waktu_sekarang' => $current_time
            ]);
        }
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    } catch (PDOException $e) {
        return 0;
    }
}

// Fungsi untuk mendapatkan total laporan
function getTotalLaporan($pdo) {
    try {
        $sql = "SELECT COUNT(*) as total FROM laporan_konseling";
        $stmt = $pdo->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    } catch (PDOException $e) {
        return 0;
    }
}

// Fungsi untuk mendapatkan jumlah siswa bimbingan
function getJumlahSiswaBimbingan($pdo, $id_guru = null) {
    try {
        if ($id_guru) {
            $sql = "SELECT COUNT(DISTINCT id_siswa) as total FROM jadwal_konseling 
                    WHERE status IN('selesai', 'Disetujui')
                    AND id_guru = :id_guru";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id_guru' => $id_guru]);
        } else {
            $sql = "SELECT COUNT(DISTINCT id_siswa) as total FROM jadwal_konseling 
                    WHERE status IN('selesai', 'Disetujui')";
            $stmt = $pdo->query($sql);
        }
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    } catch (PDOException $e) {
        return 0;
    }
}

// Fungsi untuk mendapatkan pengaduan terbaru
function getPengaduanTerbaru($pdo) {
    try {
        $sql = "SELECT p.*, s.nama as nama_siswa 
                FROM pengaduan p 
                LEFT JOIN siswa s ON p.id_siswa = s.id_siswa 
                ORDER BY p.tanggal_pengaduan DESC 
                LIMIT 3";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

// Fungsi untuk mendapatkan jadwal mendatang
function getJadwalMendatang($pdo, $id_guru = null) {
    try {
        $current_time = date('H:i:s');
        $today = date('Y-m-d');
        
        echo "<!-- DEBUG: Current time = $current_time, Today = $today -->\n";
        
        if ($id_guru) {
            $sql = "SELECT j.*, s.nama as nama_siswa, s.kelas, 
                           j.Tanggal_Konseling as tanggal,
                           j.Waktu_Konseling as waktu
                    FROM jadwal_konseling j 
                    JOIN siswa s ON j.id_siswa = s.id_siswa 
                    WHERE j.status IN ('Disetujui', 'pending')
                    AND j.id_guru = :id_guru
                    AND (
                        j.Tanggal_Konseling > :today
                        OR (
                            j.Tanggal_Konseling = :today 
                            AND j.Waktu_Konseling > :current_time
                        )
                    )
                    ORDER BY j.Tanggal_Konseling ASC, j.Waktu_Konseling ASC
                    LIMIT 3";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id_guru' => $id_guru,
                ':today' => $today,
                ':current_time' => $current_time
            ]);
        } else {
            $sql = "SELECT j.*, s.nama as nama_siswa, s.kelas,
                           j.Tanggal_Konseling as tanggal,
                           j.Waktu_Konseling as waktu
                    FROM jadwal_konseling j 
                    JOIN siswa s ON j.id_siswa = s.id_siswa 
                    WHERE j.status IN ('Disetujui', 'pending')
                    AND (
                        j.Tanggal_Konseling > :today
                        OR (
                            j.Tanggal_Konseling = :today 
                            AND j.Waktu_Konseling > :current_time
                        )
                    )
                    ORDER BY j.Tanggal_Konseling ASC, j.Waktu_Konseling ASC
                    LIMIT 3";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':today' => $today,
                ':current_time' => $current_time
            ]);
        }
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<!-- DEBUG: Found " . count($results) . " upcoming schedules -->\n";
        
        return $results;
        
    } catch (PDOException $e) {
        echo "<!-- ERROR getJadwalMendatang: " . $e->getMessage() . " -->\n";
        return [];
    }
}

// Fungsi untuk mendapatkan jumlah pengaduan baru (untuk badge)
function getJumlahPengaduanBaruCount($pdo) {
    try {
        $sql = "SELECT COUNT(*) as total FROM pengaduan WHERE status = 'Menunggu'";
        $stmt = $pdo->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    } catch (PDOException $e) {
        return 0;
    }
}

// Ambil data dari database dengan parameter id_guru
$jumlah_pengaduan_baru = getJumlahPengaduanBaru($pdo);
$jumlah_jadwal_hari_ini = getJumlahJadwalHariIni($pdo, $id_guru);
$total_laporan = getTotalLaporan($pdo);
$jumlah_siswa_bimbingan = getJumlahSiswaBimbingan($pdo, $id_guru);
$pengaduan_terbaru = getPengaduanTerbaru($pdo);
$jadwal_mendatang = getJadwalMendatang($pdo, $id_guru);
$jumlah_pengaduan_baru_badge = getJumlahPengaduanBaruCount($pdo);

// Fungsi untuk format tanggal dan waktu jadwal
function formatJadwalDateTime($tanggal, $waktu = null) {
    $months = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    // Parse tanggal
    $timestamp_tanggal = strtotime($tanggal);
    $day = date('j', $timestamp_tanggal);
    $month = $months[date('n', $timestamp_tanggal)];
    $year = date('Y', $timestamp_tanggal);
    
    // Parse waktu jika ada
    $time_formatted = '';
    if ($waktu) {
        $time_formatted = date('H.i', strtotime($waktu));
    }
    
    // Cek apakah hari ini atau besok
    $today = date('Y-m-d');
    $tomorrow = date('Y-m-d', strtotime('+1 day'));
    
    if ($tanggal == $today) {
        return $waktu ? "Hari ini pukul $time_formatted" : "Hari ini";
    } elseif ($tanggal == $tomorrow) {
        return $waktu ? "Besok pukul $time_formatted" : "Besok";
    } else {
        return $waktu ? "$day $month $year, pukul $time_formatted" : "$day $month $year";
    }
}

// Fungsi untuk format tanggal
function formatTanggal($date) {
    $months = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $timestamp = strtotime($date);
    $day = date('j', $timestamp);
    $month = $months[date('n', $timestamp)];
    $year = date('Y', $timestamp);
    $time = date('H.i', $timestamp);
    
    return "$day $month $year pukul $time";
}

// Fungsi untuk menentukan warna badge berdasarkan status
function getBadgeClass($status) {
    switch(strtolower($status)) {
        case 'Menunggu':
            return 'badge-baru';
        case 'proses':
        case 'Diproses':
            return 'badge-proses';
        case 'selesai':
            return 'badge-selesai';
        case 'DiSETUJUI':
        case 'Disetujui':
            return 'badge-diterima';
        case 'ditolak':
            return 'badge-ditolak';
        default:
            return 'badge-proses';
    }
}

// Fungsi untuk menentukan label status
function getStatusLabel($status) {
    switch(strtolower($status)) {
        case 'baru':
        case 'menunggu':
            return 'MENUNGGU';
        case 'proses':
        case 'diproses':
            return 'DIPROSES';
        case 'selesai':
            return 'SELESAI';
        case 'Disetujui':
            return 'DISETUJUI';
        case 'ditolak':
            return 'DITOLAK';
        default:
            return strtoupper($status);
    }
}

// Fungsi untuk menentukan warna jadwal
function getJadwalColor($status) {
    switch(strtolower($status)) {
        case 'Disetujui':
            return 'diterima';
        case 'pending':
        case 'menunggu':
            return 'menunggu';
        default:
            return 'menunggu';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Monitoring BK</title>
    <!-- Memuat Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Memuat Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Memuat Font Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Font modern */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        
        body {
           background: url('../../assets/image/background.jpg');
           background-size: cover;
           background-attachment: fixed;
           font-family: 'Poppins', sans-serif;
           min-height: 100vh;
           padding: 20px 0;
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        /* --- Kartu Statistik (Metrics Card) --- */
        .card-stat {
            border-radius: 1rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            background-color: #ffffff;
            padding: 1.25rem;
            border: 1px solid #f0f0f0;
            transition: all 0.3s ease;
            height: 100%;
        }

        .card-stat:hover {
             box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
             transform: translateY(-5px);
        }
        
        .card-stat-icon-box {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-shrink: 0;
        }

        .text-3xl-bold {
            font-size: 2rem;
            font-weight: 800;
        }

        /* Warna Ikon */
        .icon-yellow { background-color: #fffbe6; color: #b45309; }
        .icon-indigo { background-color: #eef2ff; color: #4338ca; }
        .icon-blue { background-color: #eff6ff; color: #2563eb; }
        .icon-green { background-color: #ecfdf5; color: #059669; }

        /* --- Kartu Konten (Content Cards) --- */
        .card-content {
            background-color: #ffffff;
            padding: 1.5rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.06);
            border: 1px solid #f0f0f0;
            height: 100%;
        }

        /* --- Item Pengaduan --- */
        .pengaduan-item {
            padding: 1rem;
            margin-bottom: 0.75rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            display: block;
            background: white;
        }

        .pengaduan-item:hover {
            background-color: #f9fafb;
            border-color: #d1d5db;
            text-decoration: none;
            color: inherit;
            transform: translateY(-2px);
        }
        
        /* --- Badge Status Kustom --- */
        .badge-status {
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 9999px;
            font-weight: 600;
            line-height: 1;
        }
        .badge-baru {
            background-color: #fef3c7;
            color: #92400e;
        }
        .badge-proses {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .badge-selesai {
            background-color: #d1fae5;
            color: #065f46;
        }
        .badge-diterima {
            background-color: #dcfce7;
            color: #166534;
        }
        .badge-ditolak {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* --- Item Jadwal --- */
        .jadwal-item {
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid;
            border-radius: 0.5rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .jadwal-item:hover {
            transform: translateX(5px);
        }

        .jadwal-item.diterima {
            background-color: #eef2ff;
            border-color: #6366f1;
        }

        .jadwal-item.menunggu {
            background-color: #fef2f2;
            border-color: #ef4444;
        }

        .jadwal-item.pending {
            background-color: #fffbeb;
            border-color: #f59e0b;
        }

        .text-keterangan {
            font-size: 0.8rem;
            color: #6b7280;
        }

        .dashboard-title {
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 30px;
            font-size: 2.2rem;
            position: relative;
            padding-bottom: 15px;
        }

        .dashboard-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 80px;
            height: 4px;
            background: #2563eb;
            border-radius: 2px;
        }

        .no-data {
            text-align: center;
            padding: 30px;
            color: #6b7280;
        }

        .no-data i {
            font-size: 50px;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        /* Styling untuk info waktu jadwal */
        .jadwal-time-info {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 5px;
        }
        
        .jadwal-time-info i {
            color: #4b5563;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">

        <!-- Judul Dashboard -->
        <h1 class="dashboard-title">Dashboard 
            
        </h1>

        <!-- Baris 1: Statistik (4 Kolom Simetris) -->
        <div class="row g-4 mb-5">
            
            <!-- Card: Pengaduan Baru -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card-stat">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-sm text-muted mb-0">Pengaduan Baru</p>
                            <h2 class="text-3xl-bold text-dark mt-1"><?php echo $jumlah_pengaduan_baru; ?></h2>
                        </div>
                        <!-- Ikon: Bell -->
                        <div class="card-stat-icon-box icon-yellow">
                             <i class="bi bi-bell fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card: Jadwal Hari Ini -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card-stat">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-sm text-muted mb-0">Jadwal Hari Ini</p>
                            <h2 class="text-3xl-bold text-dark mt-1"><?php echo $jumlah_jadwal_hari_ini; ?></h2>
                            
                        </div>
                        <!-- Ikon: Calendar -->
                        <div class="card-stat-icon-box icon-indigo">
                            <i class="bi bi-calendar-check fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card: Total Laporan -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card-stat">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-sm text-muted mb-0">Total Laporan</p>
                            <h2 class="text-3xl-bold text-dark mt-1"><?php echo $total_laporan; ?></h2>
                        </div>
                        <!-- Ikon: File Text -->
                        <div class="card-stat-icon-box icon-blue">
                             <i class="bi bi-file-text fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card: Siswa Bimbingan -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card-stat">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-sm text-muted mb-0">Siswa Bimbingan</p>
                            <h2 class="text-3xl-bold text-dark mt-1"><?php echo $jumlah_siswa_bimbingan; ?></h2>
                            
                        </div>
                        <!-- Ikon: Users -->
                        <div class="card-stat-icon-box icon-green">
                            <i class="bi bi-people fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Baris 2: Pengaduan Terbaru & Jadwal Mendatang -->
        <div class="row g-4">
            
            <!-- Kolom Kiri: Pengaduan Terbaru -->
            <div class="col-lg-7">
                <div class="card-content">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="h5 font-weight-bold mb-0">Pengaduan Terbaru</h2>
                        <?php if ($jumlah_pengaduan_baru_badge > 0): ?>
                            <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-1 rounded-pill fw-semibold">
                                <?php echo $jumlah_pengaduan_baru_badge; ?> Baru
                            </span>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($pengaduan_terbaru)): ?>
                        <?php foreach ($pengaduan_terbaru as $pengaduan): ?>
                            <a href="#" class="pengaduan-item">
                                <div class="small fw-semibold text-dark">
                                    <?php echo htmlspecialchars($pengaduan['deskripsi'] ?? 'Tidak ada deskripsi'); ?>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="text-keterangan">
                                        <?php 
                                        if (!empty($pengaduan['nama_siswa'])) {
                                            echo htmlspecialchars($pengaduan['nama_siswa']);
                                        } else if (!empty($pengaduan['anonim']) && $pengaduan['anonim'] == 1) {
                                            echo 'Anonim';
                                        } else {
                                            echo 'Tidak diketahui';
                                        }
                                        ?>
                                    </small>
                                    <span class="badge-status <?php echo getBadgeClass($pengaduan['status'] ?? 'Menunggu'); ?>">
                                        <?php echo getStatusLabel($pengaduan['status'] ?? 'Menunggu'); ?>
                                    </span>
                                </div>
                                <?php if (!empty($pengaduan['tanggal_pengaduan'])): ?>
                                    <small class="text-keterangan d-block mt-1">
                                        <?php echo formatTanggal($pengaduan['tanggal_pengaduan']); ?>
                                    </small>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-data">
                            <i class="bi bi-inbox"></i>
                            <h5>Tidak ada pengaduan</h5>
                            <p class="text-muted">Belum ada pengaduan dari siswa</p>
                        </div>
                    <?php endif; ?>

                    
                </div>
            </div>

            <!-- Kolom Kanan: Jadwal Mendatang -->
            <div class="col-lg-5">
                <div class="card-content">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="h5 font-weight-bold mb-0">Jadwal Mendatang</h2>
                       
                    </div>

                    <?php if (!empty($jadwal_mendatang)): ?>
                        <?php foreach ($jadwal_mendatang as $jadwal): ?>
                            <div class="jadwal-item <?php echo getJadwalColor($jadwal['status'] ?? 'pending'); ?>">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <p class="mb-1 fw-semibold text-dark">
                                            <?php 
                                            echo htmlspecialchars($jadwal['nama_siswa'] ?? 'Tidak diketahui');
                                            if (!empty($jadwal['kelas'])) {
                                                echo ' (' . htmlspecialchars($jadwal['kelas']) . ')';
                                            }
                                            ?>
                                        </p>
                                        
                                        <!-- Informasi Tanggal dan Waktu -->
                                        <?php if (!empty($jadwal['tanggal']) && !empty($jadwal['waktu'])): ?>
                                            <div class="jadwal-time-info">
                                                <i class="bi bi-calendar"></i>
                                                <small class="text-keterangan">
                                                    <?php echo formatJadwalDateTime($jadwal['tanggal'], $jadwal['waktu']); ?>
                                                </small>
                                            </div>
                                        <?php elseif (!empty($jadwal['Tanggal_Konseling']) && !empty($jadwal['Waktu_Konseling'])): ?>
                                            <div class="jadwal-time-info">
                                                <i class="bi bi-calendar"></i>
                                                <small class="text-keterangan">
                                                    <?php echo formatJadwalDateTime($jadwal['Tanggal_Konseling'], $jadwal['Waktu_Konseling']); ?>
                                                </small>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($jadwal['topik'])): ?>
                                            <div class="jadwal-time-info mt-1">
                                                <i class="bi bi-chat-dots"></i>
                                                <small class="text-keterangan">
                                                    Topik: <?php echo htmlspecialchars($jadwal['topik']); ?>
                                                </small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="text-end">
                                        <span class="badge-status <?php echo getBadgeClass($jadwal['status'] ?? 'pending'); ?>">
                                            <?php echo getStatusLabel($jadwal['status'] ?? 'pending'); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-data">
                            <i class="bi bi-calendar-x"></i>
                            <h5>Tidak ada jadwal</h5>
                            <p class="text-muted">
                                <?php if ($id_guru): ?>
                                    Anda tidak memiliki jadwal konseling mendatang
                                <?php else: ?>
                                    Belum ada jadwal konseling mendatang
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    
                </div>
            </div>
        </div>
        
    </div>

  
</body>
</html>