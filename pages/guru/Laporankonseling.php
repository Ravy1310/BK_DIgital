<?php
// File: Laporankonseling.php
session_start();

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

// Include database connection
$base_dir = $_SERVER['DOCUMENT_ROOT'] . '/BK_DIGITAL/';
require_once $base_dir . 'includes/db_connection.php';

// Ambil ID guru dari session
$id_guru = $_SESSION['guru_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Laporan Konseling</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        body {
            background: url('../../assets/image/background.jpg');
            background-size: cover;
            background-attachment: fixed;
           font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            padding: 20px;
        }

        /* Kontainer Utama */
        .main-card {
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            padding: 30px;
            margin: 20px ;
            margin-left: -50px;
            max-width: 110%; /* DIUBAH: dari 100% menjadi 95% */
            width: 110%; /* DIUBAH: tambahkan width 95% */
        }

        /* Header */
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eaeaea;
        }

        h4 {
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        h4 i {
            color: #10b981;
        }

        /* Tombol Utama */
        .btn-green {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            font-weight: 600;
            border-radius: 10px;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        .btn-green:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
            color: white;
        }

        /* Search Box */
        .search-box {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            background-color: #ffffff;
            margin-bottom: 25px;
            transition: all 0.3s ease;
        }

        .search-box:focus-within {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .search-box input {
            border: none;
            outline: none;
            width: 100%;
            font-size: 1rem;
            background: transparent;
            color: #4a5568;
        }

        .search-box i {
            color: #a0aec0;
            font-size: 1.1rem;
        }

        /* Tabel */
        .table-container {
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            margin-top: 20px;
            background: white;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background-color: #f8fafc;
            color: #4a5568;
            font-weight: 600;
            padding: 16px 20px;
            border-bottom: 2px solid #e2e8f0;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table tbody td {
            padding: 16px 20px;
            color: #2d3748;
            border-bottom: 1px solid #edf2f7;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background-color: #f7fafc;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Action Buttons */
        .action-btn {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s ease;
        }

        .btn-view {
            background-color: #ebf8ff;
            color: #3182ce;
            border: 1px solid #bee3f8;
        }

        .btn-view:hover {
            background-color: #bee3f8;
            color: #2c5282;
        }

        /* Modal Styling */
        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 20px 30px;
            border-radius: 15px 15px 0 0;
        }

        .modal-title {
            color: #2c3e50;
            font-weight: 700;
            font-size: 1.5rem;
        }

        .modal-body {
            padding: 30px;
        }

        .modal-footer {
            border-top: 1px solid #e2e8f0;
            padding: 20px 30px;
            border-radius: 0 0 15px 15px;
        }

        /* Jadwal Card in Modal */
        .jadwal-card {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .jadwal-card:hover {
            border-color: #10b981;
            background-color: #f0fdf4;
            transform: translateY(-2px);
        }

        .jadwal-card.active {
            border-color: #10b981;
            background-color: #f0fdf4;
        }

        /* Info Boxes */
        .info-box {
            background-color: #f0fdf4;
            border: 1px solid #d1fae5;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .note-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
        }

        /* Loading State */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #10b981;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* No Data Message */
        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: #718096;
        }

        .no-data i {
            font-size: 4rem;
            color: #cbd5e0;
            margin-bottom: 20px;
        }

        .no-data h5 {
            color: #4a5568;
            margin-bottom: 10px;
        }

        /* Badge */
        .badge-info {
            background-color: #e6f7ff;
            color: #1890ff;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 500;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-card {
                padding: 20px;
                margin: 10px;
            }
            
            .header-section {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .table-responsive {
                font-size: 0.9rem;
            }
            
            .modal-dialog {
                margin: 10px;
            }
        }
    </style>
</head>
<body>

    <div class="container-fluid">
        <div class="main-card">
            <!-- Header -->
            <div class="header-section">
                <h4>
                    <i class="bi bi-file-earmark-text"></i>
                    Riwayat Laporan Konseling
                </h4>
                <button class="btn btn-green" id="openLaporanBtn">
                    <i class="bi bi-plus-circle"></i>
                    Buat Laporan Baru
                </button>
            </div>

            <!-- Search Box -->
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" placeholder="Cari berdasarkan nama siswa, kelas, atau topik...">
            </div>

            <!-- Info -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="badge-info" id="totalInfo">Memuat data...</span>
            </div>

            <!-- Tabel -->
            <div class="table-container">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr class="text-center">
                                <th class="text-start">Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Tanggal Sesi</th>
                                <th class="text-start">Topik</th>
                                <th style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="laporanTableBody">
                            <!-- Data akan diisi oleh JavaScript -->
                            <tr id="loadingRow">
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-center">
                                        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-3 text-muted">Memuat data laporan...</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Pilih Jadwal -->
    <div class="modal fade" id="selectJadwalModal" tabindex="-1" aria-labelledby="selectJadwalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="selectJadwalModalLabel">
                        <i class="bi bi-calendar-check me-2"></i>
                        Pilih Jadwal Konseling
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="search-box">
                            <i class="bi bi-search"></i>
                            <input type="text" id="searchJadwalInput" placeholder="Cari nama siswa atau kelas...">
                        </div>
                    </div>
                    
                    <div id="jadwalListContainer">
                        <div id="jadwalList" style="max-height: 400px; overflow-y: auto;">
                            <!-- Daftar jadwal akan diisi di sini -->
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-3 text-muted">Memuat daftar jadwal...</p>
                            </div>
                        </div>
                        
                        <div id="noJadwalMessage" class="text-center py-5" style="display: none;">
                            <i class="bi bi-calendar-x" style="font-size: 4rem; color: #cbd5e0;"></i>
                            <h5 class="mt-3 text-muted">Tidak ada jadwal tersedia</h5>
                            <p class="text-muted">Semua jadwal yang sudah disetujui telah memiliki laporan.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Buat Laporan -->
    <div class="modal fade" id="laporanModal" tabindex="-1" aria-labelledby="laporanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="laporanModalLabel">
                        <i class="bi bi-file-earmark-plus me-2"></i>
                        Buat Laporan Konseling
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formLaporanBaru">
                    <div class="modal-body">
                        <input type="hidden" id="selectedJadwalId" name="id_jadwal">
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="info-box">
                                    <h6 class="fw-bold mb-2">Informasi Jadwal</h6>
                                    <p class="mb-1"><strong>Siswa:</strong> <span id="selectedSiswaInfo" class="text-primary">-</span></p>
                                    <p class="mb-1"><strong>Kelas:</strong> <span id="selectedKelasInfo">-</span></p>
                                    <p class="mb-1"><strong>Tanggal:</strong> <span id="selectedTanggalInfo">-</span></p>
                                    <p class="mb-0"><strong>Topik:</strong> <span id="selectedTopikInfo">-</span></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tanggalDibuat" class="form-label fw-semibold">Tanggal Pembuatan Laporan</label>
                                    <input type="datetime-local" class="form-control" id="tanggalDibuat" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="hasilPertemuan" class="form-label fw-semibold">
                                <i class="bi bi-chat-left-text me-1"></i>
                                Hasil Pertemuan & Solusi
                            </label>
                            <textarea class="form-control" id="hasilPertemuan" rows="5" 
                                      placeholder="Tuliskan hasil pertemuan, solusi yang diberikan, perkembangan siswa, dan hasil yang dicapai..."
                                      required></textarea>
                            <div class="form-text">Minimal 10 karakter. Jelaskan dengan detail dan jelas.</div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="catatanTambahan" class="form-label fw-semibold">
                                <i class="bi bi-sticky me-1"></i>
                                Catatan Tambahan
                            </label>
                            <textarea class="form-control" id="catatanTambahan" rows="3" 
                                      placeholder="Catatan khusus, rencana tindak lanjut, saran, atau hal penting lainnya..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-green px-4">
                            <span id="submitBtnText">
                                <i class="bi bi-save me-1"></i> Simpan Laporan
                            </span>
                            <span id="submitLoading" class="loading-spinner ms-2" style="display: none;"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Detail Laporan -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">
                        <i class="bi bi-eye me-2"></i>
                        Detail Laporan Konseling
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="detailLoading" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Memuat detail laporan...</p>
                    </div>
                    
                    <div id="detailContent" style="display: none;">
                        <div class="text-center mb-4">
                            <h5 class="fw-bold text-primary" id="detailNama"></h5>
                            <p class="mb-2">
                                <span class="badge bg-light text-dark" id="detailKelas"></span>
                            </p>
                            <p class="text-muted small mb-0" id="detailTanggalSesi"></p>
                            <p class="text-muted small" id="detailTopik"></p>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3 text-success">
                                <i class="bi bi-check-circle me-1"></i> Hasil Pertemuan & Solusi
                            </h6>
                            <div class="info-box" id="detailHasil"></div>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3 text-info">
                                <i class="bi bi-info-circle me-1"></i> Catatan Tambahan
                            </h6>
                            <div class="note-box" id="detailCatatan"></div>
                        </div>
                        
                        <div class="row mt-4 pt-3 border-top">
                            <div class="col-md-6">
                                <p class="text-muted small mb-1">Tanggal Pembuatan Laporan:</p>
                                <p class="fw-semibold" id="detailTanggalLaporan"></p>
                            </div>
                            <div class="col-md-6 text-end">
                                <p class="text-muted small mb-1">Dilaporkan oleh:</p>
                                <p class="fw-semibold" id="detailGuru"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->

  
</body>
</html>