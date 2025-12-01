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
        background-attachment: fixed;
        font-family: 'Poppins', sans-serif;
        margin: 0;
        padding: 0;
        min-height: 100vh;
    }

    .d-flex.pt-4 {
        margin-left: 0;
        padding-left: 0;
        padding-right: 0;
        width: 100%;
    }

    .content {
        width: 100%;
        padding: 15px !important;
    }

    /* Styling untuk log item dengan scroll horizontal */
    .log-item {
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        transition: transform 0.2s;
        margin-bottom: 10px;
        border: 1px solid #e9ecef;
        background: white;
        overflow: hidden;
    }

    .log-item .log-content {
        padding: 12px 15px;
        word-wrap: break-word;
        overflow-wrap: break-word;
        white-space: normal;
        overflow: visible;
    }

    .log-item .log-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 8px;
        flex-wrap: wrap;
        gap: 8px;
    }

    .log-item .log-title {
        font-weight: 600;
        color: #495057;
        font-size: 0.95rem;
        flex: 1;
        min-width: 0;
    }

    .log-item .log-title span {
        display: inline-block;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .log-item .log-details {
        color: #6c757d;
        font-size: 0.85rem;
        line-height: 1.4;
        margin-bottom: 5px;
        padding: 8px 10px;
        background-color: #f8f9fa;
        border-radius: 6px;
        border: 1px solid #e9ecef;
        overflow-x: auto;
        overflow-y: hidden;
        white-space: pre-wrap;
        max-height: 120px;
        position: relative;
    }

    /* Custom scrollbar untuk log details */
    .log-item .log-details::-webkit-scrollbar {
        height: 6px;
        width: 6px;
    }
    
    .log-item .log-details::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .log-item .log-details::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }
    
    .log-item .log-details::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .log-item .log-details pre {
        margin: 0;
        font-family: 'Courier New', Courier, monospace;
        font-size: 0.8rem;
        white-space: pre-wrap;
        word-break: break-word;
        min-width: min-content;
    }

    .log-item .log-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.8rem;
        color: #868e96;
        padding-top: 8px;
        border-top: 1px solid #f1f3f5;
        flex-wrap: wrap;
        gap: 10px;
        overflow: visible;
    }

    .log-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    /* Styling khusus untuk konten JSON yang panjang */
    .json-content {
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
        font-size: 0.8rem;
        line-height: 1.3;
    }

    .json-key {
        color: #0366d6;
        font-weight: 600;
    }

    .json-string {
        color: #22863a;
    }

    .json-number {
        color: #005cc5;
    }

    .json-boolean {
        color: #d73a49;
    }

    .json-null {
        color: #6f42c1;
    }

    /* Indikator scroll horizontal */
    .scroll-indicator {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        font-size: 0.7rem;
        background: rgba(255, 255, 255, 0.8);
        padding: 2px 6px;
        border-radius: 4px;
        display: none;
    }

    .log-details:hover .scroll-indicator {
        display: block;
    }

    .empty-log {
        text-align: center;
        padding: 40px 20px;
        color: #6c757d;
        background: white;
        border-radius: 10px;
        border: 1px dashed #dee2e6;
    }

    .loading-spinner {
        text-align: center;
        padding: 40px 20px;
        background: white;
        border-radius: 10px;
        border: 1px solid #e9ecef;
    }

    .badge-action {
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 6px;
        font-weight: 600;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .stat-card {
        transition: all 0.3s ease;
        height: 100%;
        min-height: 140px;
        border-radius: 15px !important;
        border: none;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important;
    }

    .filter-btn {
        border-radius: 8px;
        padding: 8px 16px;
        font-size: 0.85rem;
    }

    .filter-btn.active {
        font-weight: 600;
        transform: scale(1.05);
        box-shadow: 0 3px 8px rgba(0,0,0,0.2);
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
        width: 50px;
        height: 50px;
        margin-bottom: 15px;
        opacity: 0.8;
    }

    .card-title {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 5px;
        font-weight: 500;
    }

    .card-value {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 0;
    }
    
    @keyframes fadeUpSmooth {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .fade-container {
        animation: fadeUpSmooth 0.8s ease-out;
        width: 100%;
    }

    /* Log Aktivitas Container */
    .bg-white.rounded.shadow-sm {
        border-radius: 15px !important;
        border: none;
        padding: 20px !important;
        margin-top: 25px;
        overflow: visible;
    }

    h4.fw-bold {
        font-size: 1.8rem;
        margin-bottom: 25px;
    }

    h5.fw-bold {
        font-size: 1.3rem;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .content {
            padding: 10px !important;
        }
        
        .d-flex.pt-4 {
            padding-left: 0;
            padding-right: 0;
        }
        
        .col-md-3 {
            margin-bottom: 15px;
        }
        
        .card-value {
            font-size: 1.6rem;
        }
        
        .stat-icon {
            width: 40px;
            height: 40px;
            margin-bottom: 10px;
        }
        
        .filter-btn {
            padding: 6px 12px;
            font-size: 0.8rem;
            margin-bottom: 5px;
        }
        
        .bg-white.rounded.shadow-sm {
            padding: 15px !important;
        }
        
        h4.fw-bold {
            font-size: 1.5rem;
            padding-left: 5px;
        }
        
        .log-item .log-content {
            padding: 10px 12px;
        }
        
        .log-item .log-title {
            font-size: 0.9rem;
        }
        
        .log-item .log-details {
            font-size: 0.82rem;
            max-height: 100px;
        }
        
        .log-item .log-details pre {
            font-size: 0.78rem;
        }
        
        .scroll-indicator {
            font-size: 0.65rem;
            padding: 1px 4px;
        }
    }

    @media (max-width: 576px) {
        .content {
            padding: 8px !important;
        }
        
        h4.fw-bold {
            font-size: 1.4rem;
            padding-left: 5px;
        }
        
        .card-value {
            font-size: 1.4rem;
        }
        
        .col-md-3 {
            flex: 0 0 50%;
            max-width: 50%;
            padding-left: 5px;
            padding-right: 5px;
        }
        
        .filter-btn {
            padding: 5px 10px;
            font-size: 0.75rem;
        }
        
        .bg-white.rounded.shadow-sm {
            padding: 12px !important;
            margin-left: 5px;
            margin-right: 5px;
        }
        
        .row.g-3 {
            margin-left: -5px;
            margin-right: -5px;
        }
        
        .log-item .log-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 5px;
        }
        
        .log-item .log-title {
            width: 100%;
        }
        
        .log-item .log-meta {
            flex-direction: column;
            align-items: flex-start;
            gap: 5px;
        }
        
        .log-item .log-details {
            max-height: 90px;
            font-size: 0.8rem;
        }
        
        .log-item .log-details pre {
            font-size: 0.76rem;
        }
    }

    /* Grid system adjustments - lebih mepet */
    .row {
        margin-left: -5px;
        margin-right: -5px;
    }
    
    .row > [class*="col-"] {
        padding-left: 5px;
        padding-right: 5px;
    }

    /* Kontainer log tanpa batasan tinggi */
    #logContainer {
        max-height: none;
        overflow-y: visible;
        padding-right: 0;
    }

    /* Scroll keseluruhan page */
    .content {
        min-height: 100vh;
        overflow-y: auto;
    }

    /* Statistik cards lebih mepet */
    .row.g-3.mb-4 {
        margin-bottom: 1.5rem !important;
    }

    /* Kontrol filter lebih mepet */
    .d-flex.flex-wrap.justify-content-between.align-items-center.gap-2 {
        gap: 5px !important;
    }

    /* Tambahan untuk memastikan konten tidak meluber */
    .text-truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .log-timestamp {
        font-family: 'Courier New', monospace;
        background: #f8f9fa;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.75rem;
        flex-shrink: 0;
    }

    /* Hover effect untuk log details */
    .log-details:hover {
        background-color: #f1f3f5;
        transition: background-color 0.2s;
    }

    /* Tooltip untuk log details yang panjang */
    .log-details[title] {
        cursor: help;
    }

    /* Hover effect untuk scroll indicator */
    .log-details:hover .scroll-indicator {
        animation: bounce 1s infinite;
    }

    @keyframes bounce {
        0%, 100% { transform: translateY(-50%) translateX(0); }
        50% { transform: translateY(-50%) translateX(3px); }
    }
</style>
<div class="d-flex pt-4 fade-container">
    <div class="content p-3">
        <h4 class="fw-bold mb-4 px-2">Selamat Datang <span class="text-primary"><?php echo $_SESSION['admin_name'] ?? 'SuperAdmin'; ?></span></h4>

        <!-- Kartu Statistik -->
        <div class="row g-2 mb-4">
            <!-- Total Siswa -->
            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12 px-2">
                <div class="card shadow border-0 stat-card">
                    <div class="card-body text-center p-3">
                        <svg class="stat-icon" xmlns="http://www.w3.org/2000/svg" fill="green" viewBox="0 0 16 16">
                            <path d="M8 8a3 3 0 1 0-2.995-3.176A3 3 0 0 0 8 8z"/>
                            <path fill-rule="evenodd" d="M14 14s-1-1.5-6-1.5S2 14 2 14s1-4 6-4 6 4 6 4z"/>
                        </svg>
                        <h6 class="card-title">Total Siswa</h6>
                        <h5 class="card-value text-success"><?php echo $total_Siswa; ?></h5>
                    </div>
                </div>
            </div>

            <!-- Total Guru -->
            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12 px-2">
                <div class="card shadow border-0 stat-card">
                    <div class="card-body text-center p-3">
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
            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12 px-2">
                <div class="card shadow border-0 stat-card">
                    <div class="card-body text-center p-3">
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
            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12 px-2">
                <div class="card shadow border-0 stat-card">
                    <div class="card-body text-center p-3">
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
        <div class="row mb-4 px-2">
            <div class="col-12 px-0">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-1">
                    <div class="d-flex flex-wrap gap-1">
                        <button class="btn btn-outline-primary filter-btn active" data-filter="all">
                            <i class="fas fa-list me-1"></i> Semua
                        </button>
                        <button class="btn btn-outline-success filter-btn" data-filter="create">
                            <i class="fas fa-plus me-1"></i> Tambah
                        </button>
                        <button class="btn btn-outline-warning filter-btn" data-filter="edit">
                            <i class="fas fa-edit me-1"></i> Edit
                        </button>
                        <button class="btn btn-outline-danger filter-btn" data-filter="delete">
                            <i class="fas fa-trash me-1"></i> Hapus
                        </button>
                    </div>
                    <div>
                        <button class="btn btn-primary" id="refreshBtn">
                            <i class="fas fa-sync-alt me-1"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Log Aktivitas -->
        <div class="mt-4 p-3 bg-white rounded shadow">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 px-2">
                <h5 class="fw-bold mb-2 mb-md-0">
                    <i class="fas fa-clipboard-list me-2"></i>Log Aktivitas
                </h5>
                <span class="badge bg-primary fs-6 px-3 py-2" id="logCount">0 aktivitas</span>
            </div>
            
            <div id="logContainer" class="px-1">
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