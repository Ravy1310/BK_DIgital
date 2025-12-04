<?php

// File: manajemen jadwal konseling.php
session_start();

// Debug session (hanya untuk development)
error_log("Session data: " . print_r($_SESSION, true));

// Cek apakah user sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../../login.php');
    exit;
}

// Cek apakah user adalah guru
if ($_SESSION['admin_role'] !== 'user' || !isset($_SESSION['is_guru']) || $_SESSION['is_guru'] !== true) {
    echo "<script>
        alert('Akses ditolak. Hanya guru yang bisa mengakses halaman ini.');
        window.location.href = '../../dashboard.php';
    </script>";
    exit;
}

// Pastikan guru_id ada di session
if (!isset($_SESSION['guru_id']) || empty($_SESSION['guru_id'])) {
    // Coba ambil dari database berdasarkan user_id
   require_once __DIR__ . '/../../includes/db_connection.php';
;
    
    if (isset($_SESSION['admin_id'])) {
        $query = "SELECT id_guru FROM guru WHERE user_id = :user_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':user_id' => $_SESSION['admin_id']]);
        $guru = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($guru && isset($guru['id_guru'])) {
            $_SESSION['guru_id'] = $guru['id_guru'];
        } else {
            // Logout jika data guru tidak ditemukan
            session_destroy();
            header('Location: ../../login.php');
            exit;
        }
    }
}


// Include database connection
require_once __DIR__ . '/../../includes/db_connection.php';


// Inisialisasi variabel
$jadwalData = [];
$errorMessage = '';

// Ambil ID guru dari session
$id_guru = $_SESSION['guru_id'] ?? null;
if (!$id_guru) {
    $errorMessage = "❌ Data guru tidak ditemukan. Silakan login kembali.";
}

// Cek koneksi database
if (!$pdo) {
    $errorMessage = "❌ Koneksi database gagal.";
} else if ($id_guru) {
    try {
        // Query langsung tanpa controller class
        $query = "
            SELECT 
                jk.id_jadwal,
                jk.id_guru,
                jk.id_siswa,
                jk.Tanggal_Konseling,
                jk.Waktu_Konseling,
                jk.Status,
                jk.keterangan,
                jk.Topik_konseling,
                jk.created_at,
                IFNULL(s.nama, 'Siswa Tidak Diketahui') as nama,
                IFNULL(s.kelas, '-') as kelas,
                IFNULL(g.nama, 'Guru Belum Ditentukan') as nama_guru
            FROM jadwal_konseling jk
            LEFT JOIN siswa s ON jk.id_siswa = s.id_siswa
            LEFT JOIN guru g ON jk.id_guru = g.id_guru
            WHERE jk.Status IS NOT NULL
              AND jk.id_guru = :id_guru
            ORDER BY 
                CASE 
                    WHEN jk.Status = 'Menunggu' THEN 1
                    WHEN jk.Status = 'Jadwalkan Ulang' THEN 2
                    WHEN jk.Status = 'Disetujui' THEN 3
                    ELSE 4
                END,
                jk.Tanggal_Konseling DESC,
                jk.Waktu_Konseling DESC
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([':id_guru' => $id_guru]);
        $jadwalData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($jadwalData)) {
            $errorMessage = "📭 Tidak ada data jadwal konseling untuk Anda.";
        }
        
    } catch (Exception $e) {
        $errorMessage = "❌ Error: " . $e->getMessage();
    }
}

// Fungsi helper untuk menentukan kelas CSS berdasarkan status
function getStatusClass($status) {
    if (empty($status)) {
        return 'status-menunggu';
    }
    
    $status = strtolower(trim($status));
    
    if ($status === 'menunggu') {
        return 'status-menunggu';
    } elseif ($status === 'disetujui') {
        return 'status-disetujui';
    } elseif (strpos($status, 'jadwalkan') !== false || strpos($status, 'ulang') !== false) {
        return 'status-jadwalkan-ulang';
    } else {
        return 'status-menunggu';
    }
}

// Fungsi untuk format tanggal dan waktu
function formatDateTime($date, $time) {
    if (empty($date) || $date == '0000-00-00' || $date == '0000-00-00 00:00:00') {
        return '-';
    }
    
    try {
        $dateObj = new DateTime($date);
        $formattedDate = $dateObj->format('d M Y');
        
        if (!empty($time) && $time != '00:00:00') {
            $timeObj = new DateTime($time);
            $formattedTime = $timeObj->format('H.i');
            return $formattedDate . ' ' . $formattedTime;
        }
        
        return $formattedDate;
    } catch (Exception $e) {
        return '-';
    }
}

// Fungsi untuk menormalisasi status untuk display
function displayStatus($status) {
    if (empty($status)) {
        return 'Menunggu';
    }
    
    $status = trim($status);
    $lowerStatus = strtolower($status);
    
    if ($lowerStatus === 'menunggu') {
        return 'Menunggu';
    } elseif ($lowerStatus === 'disetujui') {
        return 'Disetujui';
    } elseif (strpos($lowerStatus, 'jadwalkan') !== false || strpos($lowerStatus, 'ulang') !== false) {
        return 'Jadwalkan Ulang';
    } else {
        return ucfirst($status);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Jadwal Konseling</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
   
    
    <style>
        body {
            background: url('../../assets/image/background.jpg');
            background-size: cover;
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            padding: 20px;
            margin-top: 10px;
            margin-left: -50px;
        }
        
        .main-card {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            padding: 24px;
            margin: 20px ;
            margin-left: -10px;
            max-width: 105%; /* DIUBAH: dari 100% menjadi 95% */
            width: 105%; /* DIUBAH: tambahkan width 95% */
        }

        h2 {
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 20px;
            font-size: 1.75rem;
            padding-bottom: 15px;
            border-bottom: 2px solid #e5e7eb;
        }

        .search-box {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 8px 15px;
            display: flex;
            align-items: center;
            background-color: #fff;
            margin-bottom: 20px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .search-box:focus-within {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .search-box input {
            border: none;
            outline: none;
            width: 100%;
            font-size: 0.95rem;
            background: transparent;
        }
        
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            margin-top: 20px;
            min-height: 350px; /* DIUBAH: tambahkan tinggi minimal untuk 5 row */
        }

        .table-tight {
            width: 100%;
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .table-tight thead th {
            font-weight: 600;
            color: #4b5563;
            background-color: #f9fafb;
            font-size: 0.85rem;
            padding: 12px 10px;
            border: none;
            text-align: center;
            vertical-align: middle;
        }
        
        .table-tight tbody td {
            padding: 12px 10px;
            font-size: 0.9rem;
            color: #374151;
            border-top: 1px solid #f3f4f6;
            vertical-align: middle;
        }

        .table-tight tbody tr:hover {
            background-color: #f9fafb;
        }

        /* Menambahkan row kosong dengan border-bottom untuk mempertahankan tinggi */
        .table-tight tbody {
            min-height: 290px; /* Tinggi untuk 5 row (58px x 5 = 290px) */
        }

        /* Tombol Status */
        .status-badge {
            border-radius: 9999px;
            padding: 4px 12px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
            text-transform: capitalize;
        }

        .status-menunggu {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }
        
        .status-disetujui {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        
        .status-jadwalkan-ulang {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        /* Tombol Aksi Dropdown */
        .btn-dot {
            padding: 4px 10px;
            font-size: 1rem;
            line-height: 1;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            background: white;
            color: #4b5563;
            transition: all 0.2s ease;
        }
        
        .btn-dot:hover {
            background-color: #f3f4f6;
            border-color: #d1d5db;
        }

        .dropdown-menu {
            min-width: 140px;
            padding: 6px 0;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
        }

        .dropdown-item {
            font-size: 0.85rem;
            padding: 8px 12px;
            display: flex;
            align-items: center;
            transition: all 0.2s ease;
        }
        
        .dropdown-item:hover {
            background-color: #f3f4f6;
        }
        
        .dropdown-item i {
            width: 16px;
            height: 16px;
            margin-right: 8px;
        }
        
        /* Alert error */
        .alert-danger {
            border-radius: 8px;
            border: 1px solid #fecaca;
            background-color: #fef2f2;
        }
        
        /* Modal styles */
        .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 1.5rem;
            border-radius: 12px 12px 0 0;
        }
        
        .modal-footer {
            border-top: 1px solid #e9ecef;
            padding: 1rem 1.5rem;
            border-radius: 0 0 12px 12px;
        }
        
        .no-data {
            text-align: center;
            padding: 50px 20px;
            color: #6b7280;
        }
        
        .no-data i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #9ca3af;
        }
        
        .badge-info {
            background-color: #dbeafe;
            color: #1e40af;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 600;
        }

    </style>
</head>
<body>

    <!-- Modal Jadwalkan Ulang -->
    <div class="modal fade" id="rescheduleModal" tabindex="-1" aria-labelledby="rescheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rescheduleModalLabel">Jadwalkan Ulang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="rescheduleForm">
                    <div class="modal-body">
                        <input type="hidden" id="rescheduleId" name="id_jadwal">
                        <p>
                            Anda akan mengubah status permintaan konseling dari 
                            <strong id="studentName"></strong> menjadi <span class="badge bg-warning">Jadwalkan Ulang</span>.
                        </p>
                        <p class="text-muted">
                            <small>
                                <i class="bi bi-info-circle"></i> 
                                Siswa akan menerima notifikasi untuk mengatur ulang jadwal sesuai ketersediaan mereka.
                            </small>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">Ya, Jadwalkan Ulang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Jadwal Konseling</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detailContent">
                    <!-- Detail akan diisi via AJAX -->
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat detail...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="main-card">
            <h2>Manajemen Jadwal Konseling</h2>
            
            <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Error:</strong> <?php echo htmlspecialchars($errorMessage); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($jadwalData)): ?>
            <div class="row">
                <div class="col-12 col-md-6 mb-3">
                    <div class="search-box">
                        <i class="bi bi-search text-muted me-2"></i>
                        <input type="text" id="searchInput" class="form-control" placeholder="Cari berdasarkan Nama, Kelas, Topik...">
                    </div>
                </div>
                <div class="col-12 col-md-6 mb-3 text-end">
                    <span class="badge-info">Total: <?php echo count($jadwalData); ?> jadwal</span>
                </div>
            </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-tight align-middle">
                    <thead class="text-center">
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Nama Siswa</th>
                            <th scope="col">Kelas</th>
                            <th scope="col">Topik Bimbingan</th>
                            <th scope="col">Tanggal & Jam</th>
                            <th scope="col">Status</th>
                            <th scope="col" style="width: 80px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="dataTable">
                        <?php if (empty($jadwalData)): ?>
                            <!-- Hanya tampilkan pesan tidak ada data -->
                            <tr>
                                <td colspan="7">
                                    <div class="no-data">
                                        <i class="bi bi-calendar-x"></i>
                                        <h5>Tidak ada data jadwal konseling</h5>
                                        <p class="text-muted">Belum ada permintaan konseling yang diajukan untuk Anda</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; ?>
                            <?php foreach ($jadwalData as $jadwal): ?>
                            <?php 
                                $status = $jadwal['Status'] ?? 'Menunggu';
                                $statusClass = getStatusClass($status);
                                $displayStatus = displayStatus($status);
                                $datetime = formatDateTime($jadwal['Tanggal_Konseling'], $jadwal['Waktu_Konseling']);
                            ?>
                            <tr data-id="<?php echo $jadwal['id_jadwal']; ?>" data-status="<?php echo htmlspecialchars($status); ?>">
                                <td class="text-center"><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($jadwal['nama'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($jadwal['kelas'] ?? '-'); ?></td>
                                <td class="text-start"><?php echo htmlspecialchars($jadwal['Topik_konseling'] ?? '-'); ?></td>
                                <td><?php echo $datetime; ?></td>
                                <td class="text-center">
                                    <span class="status-badge <?php echo $statusClass; ?>" id="status-<?php echo $jadwal['id_jadwal']; ?>">
                                        <?php echo htmlspecialchars($displayStatus); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-dot dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <?php if (strtolower($status) === 'menunggu'): ?>
                                            <li>
                                                <a class="dropdown-item text-success setujui-btn" href="#" data-id="<?php echo $jadwal['id_jadwal']; ?>" data-name="<?php echo htmlspecialchars($jadwal['nama'] ?? '-'); ?>">
                                                    <i class="bi bi-check-circle"></i> Setujui
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item text-warning reschedule-btn" href="#" data-id="<?php echo $jadwal['id_jadwal']; ?>" data-name="<?php echo htmlspecialchars($jadwal['nama'] ?? '-'); ?>">
                                                    <i class="bi bi-calendar-event"></i> Jadwalkan Ulang
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item text-info detail-btn" href="#" data-id="<?php echo $jadwal['id_jadwal']; ?>">
                                                    <i class="bi bi-info-circle"></i> Detail
                                                </a>
                                            </li>
                                            <?php else: ?>
                                            <!-- Untuk status selain Menunggu, hanya tampilkan Detail -->
                                            <li>
                                                <a class="dropdown-item text-info detail-btn" href="#" data-id="<?php echo $jadwal['id_jadwal']; ?>">
                                                    <i class="bi bi-info-circle"></i> Detail
                                                </a>
                                            </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="js/JadwalKonseling.js"></script>

    <!-- Panggil fungsi inisialisasi -->
    <script>
        // Pastikan semua library sudah dimuat
        window.addEventListener('load', function() {
            console.log('Page fully loaded');
            if (typeof initManajemenJadwalKonseling === 'function') {
                console.log('Calling init function');
                initManajemenJadwalKonseling();
            } else {
                console.error('initManajemenJadwalKonseling function not found');
            }
        });
    </script>

</body>
</html>