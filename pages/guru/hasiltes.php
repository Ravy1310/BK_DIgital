<?php
// hasiltes.php
session_start();

// Koneksi database
require_once __DIR__ . '/../../includes/db_connection.php';

// Ambil data kelas dari database
$kelas_options = [];
$tes_options = [];

try {
    // Ambil data kelas
    $sql_kelas = "SELECT DISTINCT kelas FROM siswa WHERE kelas IS NOT NULL AND kelas != '' ORDER BY kelas";
    $stmt_kelas = $pdo->query($sql_kelas);
    $kelas_options = $stmt_kelas->fetchAll(PDO::FETCH_COLUMN, 0);
    
    // Ambil data jenis tes
    $sql_tes = "SELECT DISTINCT kategori_tes FROM tes WHERE kategori_tes IS NOT NULL AND kategori_tes != '' ORDER BY kategori_tes";
    $stmt_tes = $pdo->query($sql_tes);
    $tes_options = $stmt_tes->fetchAll(PDO::FETCH_COLUMN, 0);
    
} catch (PDOException $e) {
    $kelas_options = [];
    $tes_options = [];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BK Digital - Hasil Tes Siswa</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-blue: #2563eb;
            --light-blue: #eff6ff;
            --dark-blue: #1e40af;
        }

        body {
            background: url('../../assets/image/background.jpg');
            background-size: cover;
            background-attachment: fixed;
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            padding: 20px 0;
        }

        .main-container {
            padding: 30px;
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .page-title {
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 30px;
            font-size: 2.2rem;
            position: relative;
            padding-bottom: 15px;
        }

        .page-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 80px;
            height: 4px;
            background: var(--primary-blue);
            border-radius: 2px;
        }

        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border-left: 5px solid var(--primary-blue);
            transition: all 0.3s ease;
            height: 100%;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .stats-card .icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 10px;
        }

        .stats-card.blue .icon {
            background: var(--light-blue);
            color: var(--primary-blue);
        }

        .stats-card.green .icon {
            background: #d1fae5;
            color: #059669;
        }

        .filter-section {
            background: #f8fafc;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            border: 1px solid #e2e8f0;
        }

        .result-card {
            background: white;
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 12px;
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
        }

        .result-card:hover {
            border-color: var(--primary-blue);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.1);
        }

        .student-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--light-blue), #dbeafe);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: var(--primary-blue);
            font-weight: 700;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .student-info {
            display: flex;
            align-items: center;
        }

        .score-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .score-badge.ipa { background: #E3F2FD; color: #1976D2; }
        .score-badge.ips { background: #FFF9C4; color: #F57F17; }
        .score-badge.bahasa { background: #F3E5F5; color: #7B1FA2; }

        .btn-detail {
            background: var(--primary-blue);
            border: none;
            font-size: 0.9rem;
            font-weight: 600;
            border-radius: 8px;
            padding: 6px 15px;
        }

        .btn-detail:hover {
            background: var(--dark-blue);
        }

        .loading-spinner {
            display: none;
            text-align: center;
            padding: 40px;
        }

        .loading-spinner.active {
            display: block;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--primary-blue);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .empty-state {
            display: none;
            text-align: center;
            padding: 40px 20px;
            color: #6b7280;
        }

        .pagination .page-link {
            border-radius: 6px;
            margin: 0 3px;
        }
        .jawaban-container .card {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .jawaban-container .card-header {
        border-bottom: 1px solid #dee2e6;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    .jawaban-container .card-header:hover {
        background-color: #f8f9fa !important;
    }
    
    .jawaban-container .alert-success {
        background-color: #d1e7dd;
        border-color: #badbcc;
        color: #0f5132;
    }
    
    .jawaban-container .alert-danger {
        background-color: #f8d7da;
        border-color: #f5c2c7;
        color: #842029;
    }
    
    .jawaban-container .list-group-item-success {
        background-color: #d1e7dd;
        border-color: #badbcc;
    }
    
    .jawaban-container .list-group-item-danger {
        background-color: #f8d7da;
        border-color: #f5c2c7;
    }
    
    /* Style untuk ringkasan */
    .summary-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .summary-card h4 {
        font-weight: bold;
        margin-bottom: 5px;
    }
    
    .summary-card p {
        opacity: 0.9;
        margin-bottom: 0;
    }

    /* ===========================
   RESPONSIVE FIXES
   =========================== */

/* Container padding lebih kecil di layar kecil */
@media (max-width: 768px) {
    .main-container {
        padding: 20px;
        margin: 10px;
    }

    .page-title {
        font-size: 1.6rem;
        text-align: center;
    }

    .page-title::after {
        left: 50%;
        transform: translateX(-50%);
    }
}

/* Stats Card agar full width di mobile */
@media (max-width: 576px) {
    .stats-card {
        text-align: center;
    }
    .stats-card .icon {
        margin: 0 auto 10px auto;
    }
}

/* Filter section responsif */
@media (max-width: 768px) {
    .filter-section .row > div {
        width: 100%;
    }
}

/* Result card styling mobile */
@media (max-width: 600px) {
    .result-card {
        padding: 12px;
    }

    .student-info {
        flex-direction: row;
        align-items: center;
    }

    .student-avatar {
        width: 40px;
        height: 40px;
        font-size: 14px;
    }

    .btn-detail {
        width: 100%;
        margin-top: 10px;
    }
}

/* Modal responsif */
@media (max-width: 576px) {
    .modal-dialog {
        width: 95% !important;
        margin: auto;
    }

    .modal-body {
        padding: 10px;
    }
}

/* Summary card responsif */
@media (max-width: 576px) {
    .summary-card {
        padding: 15px;
        text-align: center;
    }
}

/* Pagination responsif */
@media (max-width: 480px) {
    .pagination .page-link {
        padding: 4px 8px;
        font-size: 13px;
        margin: 0 2px;
    }
}

    </style>
</head>
<body>
    <!-- Main Content -->
    <div class="main-container">
        <h2 class="page-title">Hasil Tes Siswa</h2>

        <!-- Stats Cards -->
        <div class="row g-3 mb-4" id="statsContainer">
            <div class="col-md-6">
                <div class="stats-card blue">
                    <div class="d-flex align-items-center">
                        <div class="icon"><i class="fas fa-users"></i></div>
                        <div class="ms-3">
                            <h3 class="mb-0 fw-bold" id="totalSiswa">0</h3>
                            <p class="text-muted mb-0 small">Total Siswa</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stats-card green">
                    <div class="d-flex align-items-center">
                        <div class="icon"><i class="fas fa-check-circle"></i></div>
                        <div class="ms-3">
                            <h3 class="mb-0 fw-bold" id="totalTes">0</h3>
                            <p class="text-muted mb-0 small">Siswa Mengerjakan Tes</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Cari Nama</label>
                    <input type="text" class="form-control" id="searchInput" placeholder="Nama siswa...">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Kelas</label>
                    <select class="form-select" id="filterKelas">
                        <option value="">Semua Kelas</option>
                        <?php foreach ($kelas_options as $kelas): ?>
                            <?php if (!empty($kelas)): ?>
                                <option value="<?php echo htmlspecialchars($kelas); ?>">
                                    <?php echo htmlspecialchars($kelas); ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Jenis Tes</label>
                    <select class="form-select" id="filterJenisTes">
                        <option value="">Semua Tes</option>
                        <?php foreach ($tes_options as $tes): ?>
                            <?php if (!empty($tes)): ?>
                                <option value="<?php echo htmlspecialchars($tes); ?>">
                                    <?php echo htmlspecialchars($tes); ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100" id="applyFilterBtn">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- Loading Spinner -->
        <div class="loading-spinner" id="loadingSpinner">
            <div class="spinner"></div>
            <p>Memuat data...</p>
        </div>

        <!-- Results List -->
        <div id="resultsList">
            <!-- Data akan diisi oleh AJAX -->
        </div>

        <!-- Pagination -->
        <nav class="mt-4" id="paginationContainer" style="display: none;">
            <ul class="pagination justify-content-center" id="pagination">
                <!-- Pagination akan diisi oleh AJAX -->
            </ul>
        </nav>

        <!-- Empty State -->
        <div class="empty-state" id="emptyState">
            <i class="fas fa-clipboard-list mb-3" style="font-size: 50px;"></i>
            <h5>Tidak ada hasil tes ditemukan</h5>
            <p class="text-muted">Coba ubah filter pencarian Anda</p>
        </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Hasil Tes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalBody">
                    <!-- Detail akan diisi oleh AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

</body>
</html>