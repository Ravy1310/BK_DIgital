<?php
$base_dir = $_SERVER['DOCUMENT_ROOT'] . '/BK_DIGITAL/';
require_once $base_dir . 'includes/db_connection.php';
require_once $base_dir . 'includes/logAktivitas.php';

// AMBIL DATA TOTAL GURU DARI DATABASE
$total_guru = 0;

try {
    $query_guru = "SELECT COUNT(*) as total FROM guru";
    $stmt_guru = $pdo->prepare($query_guru);
    $stmt_guru->execute();
    $result_guru = $stmt_guru->fetch(PDO::FETCH_ASSOC);
    $total_guru = $result_guru['total'] ?? 0;
} catch (Exception $e) {
    $total_guru = 0;
}
$total_Siswa = 0;
try {
    $query_siswa = "SELECT COUNT(*) as total FROM siswa";
    $stmt_siswa = $pdo->prepare($query_siswa);
    $stmt_siswa->execute();
    $result_siswa = $stmt_siswa->fetch(PDO::FETCH_ASSOC);
    $total_Siswa = $result_siswa['total'] ?? 0;
} catch (Exception $e) {
    $total_Siswa = 0;
}
?>
<style>
    body {
        background: url('../../assets/image/background.jpg');
        background-size: cover;
        font-family: 'Poppins', sans-serif;
    }

    .log-item {
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        transition: transform 0.2s;
    }

    .log-item:hover {
        transform: translateY(-2px);
    }

    .empty-log {
        text-align: center;
        padding: 40px 20px;
        color: #6c757d;
    }

    .loading-spinner {
        text-align: center;
        padding: 20px;
    }

    .badge-action {
        font-size: 0.7rem;
    }

    .stat-card {
        transition: all 0.3s ease;
        height: 100%;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .filter-btn.active {
        font-weight: bold;
        transform: scale(1.05);
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .filter-btn[data-filter="create"].active {
        background-color: #198754;
        color: white;
        border-color: #198754;
    }

    .filter-btn[data-filter="edit"].active {
        background-color: #ffc107;
        color: black;
        border-color: #ffc107;
    }

    .filter-btn[data-filter="delete"].active {
        background-color: #dc3545;
        color: white;
        border-color: #dc3545;
    }

    .filter-btn[data-filter="all"].active {
        background-color: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }

    .stat-icon {
        width: 40px;
        height: 40px;
        margin-bottom: 10px;
    }

    .card-title {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 5px;
    }

    .card-value {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0;
    }
    
    @keyframes fadeUpSmooth {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .fade-container {
        animation: fadeUpSmooth 0.8s ease-out;
    }
</style>

<div class="d-flex pt-4 fade-container">
    <div class="content flex-grow-1 p-4">
        <h4 class="fw-bold">Selamat Datang <span class="text-primary"><?php echo $_SESSION['admin_name'] ?? 'SuperAdmin'; ?></span></h4>

        <!-- Kartu Statistik -->
        <div class="row mt-4">
            <!-- Total Siswa -->
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-0 stat-card">
                    <div class="card-body text-center">
                        <svg class="stat-icon" xmlns="http://www.w3.org/2000/svg" fill="green" viewBox="0 0 16 16">
                            <path d="M8 8a3 3 0 1 0-2.995-3.176A3 3 0 0 0 8 8z"/>
                            <path fill-rule="evenodd" d="M14 14s-1-1.5-6-1.5S2 14 2 14s1-4 6-4 6 4 6 4z"/>
                        </svg>
                        <h6 class="card-title">Total Siswa Aktif</h6>
                        <h5 class="card-value text-success"><?php echo $total_Siswa; ?></h5>
                    </div>
                </div>
            </div>

            <!-- Total Guru -->
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-0 stat-card">
                    <div class="card-body text-center">
                        <svg class="stat-icon" xmlns="http://www.w3.org/2000/svg" fill="blue" viewBox="0 0 16 16">
                            <path d="M8 7a3 3 0 1 0-2.995-3.176A3 3 0 0 0 8 7z"/>
                            <path fill-rule="evenodd" d="M14 14s-1-1.5-6-1.5S2 14 2 14s1-4 6-4 6 4 6 4z"/>
                            <path d="M13 3.5a.5.5 0 0 1 .5-.5h.793l.853-.854a.5.5 0 1 1 .708.708L15 3.707V4.5a.5.5 0 0 1-1 0V4H13.5a.5.5 0 0 1-.5-.5z"/>
                        </svg>
                        <h6 class="card-title">Total Guru Aktif</h6>
                        <h5 class="card-value text-primary"><?php echo $total_guru; ?></h5>
                    </div>
                </div>
            </div>

            <!-- Kasus Terbaru -->
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-0 stat-card">
                    <div class="card-body text-center">
                        <svg class="stat-icon" xmlns="http://www.w3.org/2000/svg" fill="orange" viewBox="0 0 16 16">
                            <path d="M4 1a2 2 0 0 0-2 2v10c0 .73.195 1.412.53 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h2z"/>
                            <path fill-rule="evenodd" d="M10 1a2 2 0 0 1 2 2v1h-1V3a1 1 0 0 0-1-1H6a1 1 0 0 0-1 1v1H4V3a2 2 0 0 1 2-2h4z"/>
                            <path d="M3 4h10v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4z"/>
                        </svg>
                        <h6 class="card-title">Kasus Terbaru</h6>
                        <h5 class="card-value text-warning">3</h5>
                    </div>
                </div>
            </div>

            <!-- Total Aktivitas Log -->
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-0 stat-card">
                    <div class="card-body text-center">
                        <svg class="stat-icon" xmlns="http://www.w3.org/2000/svg" fill="purple" viewBox="0 0 16 16">
                            <path d="M1 3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1H1zm7 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
                            <path d="M0 5a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V5zm3 0a2 2 0 0 1-2 2v4a2 2 0 0 1 2 2h10a2 2 0 0 1 2-2V7a2 2 0 0 1-2-2H3z"/>
                        </svg>
                        <h6 class="card-title">Total Aktivitas</h6>
                        <h5 class="card-value text-info" id="totalAktivitas">0</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kontrol Sederhana -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <button class="btn btn-outline-primary btn-sm me-2 filter-btn active" data-filter="all">
                            <i class="fas fa-list"></i> Semua
                        </button>
                        <button class="btn btn-outline-success btn-sm me-2 filter-btn" data-filter="create">
                            <i class="fas fa-plus"></i> Tambah
                        </button>
                        <button class="btn btn-outline-warning btn-sm me-2 filter-btn" data-filter="edit">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-outline-danger btn-sm me-2 filter-btn" data-filter="delete">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </div>
                    <div>
                        <button class="btn btn-primary btn-sm" id="refreshBtn">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Log Aktivitas -->
        <div class="mt-3 p-4 bg-white rounded shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">
                    <i class="fas fa-clipboard-list me-2"></i>Log Aktivitas
                </h5>
                <span class="badge bg-primary" id="logCount">0 aktivitas</span>
            </div>
            
            <div id="logContainer">
                <div class="loading-spinner">
                    <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                    <p class="mt-2 text-muted">Memuat log aktivitas...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Load JavaScript -->
<script src="../../includes/js/developer/log_aktivitas.js"></script>