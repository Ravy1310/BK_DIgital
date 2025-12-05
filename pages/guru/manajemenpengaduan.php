<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengaduan</title>
    
    <!-- HANYA CSS saja, TIDAK perlu Bootstrap JS di sini -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        /* CSS tetap sama seperti sebelumnya */
        body {
            background: url('../../assets/image/background.jpg');
            background-size: cover;
            font-family: 'Poppins', sans-serif;
        }
        
        .main-card {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            padding: 24px;
            margin: 40px -20px 0 -30px;
        }

        h4 {
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 20px;
        }

        .search-bar {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 8px 15px;
            display: flex;
            align-items: center;
            background-color: #fff;
            margin-bottom: 20px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .search-bar input {
            border: none;
            outline: none;
            width: 100%;
            font-size: 0.95rem;
        }
        
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        .table {
            margin-bottom: 0;
            width: 100%;
        }
        
        .table thead th {
            font-weight: 600;
            color: #4b5563;
            background-color: #f9fafb;
            font-size: 0.85rem;
            padding: 12px 10px;
        }
        
        .table tbody td {
            padding: 12px 10px;
            font-size: 0.9rem;
            color: #374151;
        }

        .table-hover tbody tr:hover {
            background-color: #f3f4f6;
        }

        .status-btn {
            border-radius: 9999px;
            padding: 4px 12px;
            font-size: 0.75rem;
            font-weight: 600;
            border: none;
            display: inline-block;
        }

        .status-process {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .status-new {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-done {
            background-color: #d1fae5;
            color: #065f46;
        }

        .action-link {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            font-size: 0.85rem;
        }
        .action-link:hover {
            text-decoration: underline;
            color: #1d4ed8;
        }
        
        #detailModal .modal-content {
            border-radius: 12px;
        }

        .detail-info i {
            font-size: 1.1rem;
        }
        
        @media (min-width: 992px) {
            .main-card {
                padding: 30px; 
            }
        }
        /* Tambahkan di manajemenpengaduan.php atau di CSS global */
.loading-spinner {
    display: inline-block;
    width: 1rem;
    height: 1rem;
    border: 2px solid currentColor;
    border-right-color: transparent;
    border-radius: 50%;
    animation: spinner-rotate .75s linear infinite;
}

@keyframes spinner-rotate {
    to {
        transform: rotate(360deg);
    }
}

.btn:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}

/* Di file CSS manajemenpengaduan.php atau global.css */
.status-new {
    background-color: #ffedd5 !important;  /* Orange muda untuk Menunggu */
    color: #9a3412 !important;
    border: 1px solid #fdba74;
}

.status-process {
    background-color: #dbeafe !important;  /* Biru muda untuk Diproses */
    color: #1e40af !important;
    border: 1px solid #93c5fd;
}

.status-done {
    background-color: #d1fae5 !important;  /* Hijau muda untuk Selesai */
    color: #065f46 !important;
    border: 1px solid #6ee7b7;
}

/* Tombol aksi cepat */
.action-quick {
    color: #198754 !important; /* Hijau untuk Selesai */
    text-decoration: none;
    cursor: pointer;
    margin-left: 5px;
}

.action-quick:hover {
    text-decoration: underline;
    color: #146c43 !important;
}

/* ===============================
   RESPONSIVE FIXES - TANPA UBAH
   KODE YANG SUDAH ADA
   =============================== */

/* Perbaikan tampilan mobile */
@media (max-width: 768px) {
    .main-card {
        margin: 20px 0 0 0 !important;
        padding: 18px !important;
        width: 100% !important;
        border-radius: 10px;
    }

    h4 {
        font-size: 1.3rem;
        text-align: center;
    }

    body {
        padding: 10px;
        background-size: cover;
        background-position: center;
    }
}

/* Search bar responsif */
@media (max-width: 576px) {
    .search-bar {
        padding: 6px 12px;
    }
    .search-bar input {
        font-size: 0.9rem;
    }
}

/* Tabel responsif */
@media (max-width: 768px) {
    .table-responsive {
        overflow-x: auto;
        border-radius: 6px;
    }

    .table thead th {
        font-size: 0.75rem !important;
        padding: 8px 6px !important;
        white-space: nowrap;
    }

    .table tbody td {
        font-size: 0.8rem !important;
        padding: 8px 6px !important;
        white-space: nowrap;
    }

    .status-btn {
        font-size: 0.65rem !important;
        padding: 3px 8px !important;
    }
}

/* Kolom aksi agar tidak rusak */
@media (max-width: 480px) {
    td:last-child, th:last-child {
        width: 60px !important;
    }
}

/* Dropdown & modal */
@media (max-width: 576px) {
    .modal-dialog {
        max-width: 92% !important;
        margin: auto;
    }

    .modal-body {
        padding: 12px !important;
    }

    #detailModal h6 {
        font-size: 1rem;
    }

    .detail-info i {
        font-size: 1rem;
    }
}

/* Fix badge & text panjang agar tidak overflow */
@media (max-width: 480px) {
    .badge {
        font-size: 0.65rem !important;
        padding: 4px 6px !important;
    }

    #messageText {
        font-size: 0.8rem;
    }
}

    </style>
</head>
<body>

    <div class="container-fluid">
        <div class="main-card">
            <h4>Manajemen Pengaduan</h4>

            <!-- Search bar -->
            <div class="row">
                <div class="col-12 col-md-6 mb-3">
                    <div class="search-bar">
                        <i class="bi bi-search text-muted me-2"></i>
                        <input type="text" id="searchInput" placeholder="Cari berdasarkan Subjek, Nama, Deskripsi...">
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="text-center">
                        <tr>
                            <th scope="col" class="text-start">Subjek</th>
                            <th scope="col">Pelapor</th>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Status</th>
                            <th scope="col" style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="complaintTable">
                        <?php
                        require_once '../../includes/guru_control/PengaduanController.php';
                        
                        try {
                            $controller = new PengaduanController();
                            echo $controller->initManajemenPengaduan();
                        } catch (Exception $e) {
                            echo '<tr><td colspan="5" class="text-center py-4 text-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="detailModalLabel">Detail Pengaduan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6 id="subjectText" class="fw-bold mb-3"></h6>
                
                <div class="mb-3">
                    <span class="badge bg-secondary" id="jenisKejadianBadge"></span>
                </div>

                <div class="d-flex flex-column gap-2 text-muted detail-info">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-person-circle text-secondary"></i>
                        <span id="reporterText" class="small"></span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-calendar-event text-secondary"></i>
                        <span id="dateText" class="small"></span>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-info-circle text-secondary"></i>
                        <span class="small me-2">Status: </span>
                        <span id="statusBadge" class="status-btn"></span>
                    </div>
                </div>

                <p class="fw-semibold mb-2">Deskripsi:</p>
                <div class="border rounded p-3 mt-2 bg-light">
                    <p id="messageText" class="mb-0 small text-dark"></p>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" id="actionButton" class="btn btn-primary" data-id-pengaduan="">Ubah Status</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

    <!-- HAPUS Bootstrap JS dari sini -->
    
    <!-- Script sederhana untuk handle modal -->
    <script>
        console.log('manajemenpengaduan.php dimuat');
        
        // Fungsi untuk test modal (debugging)
        window.testModal = function() {
            const modalElement = document.getElementById('detailModal');
            if (modalElement && typeof bootstrap !== 'undefined') {
                const modal = new bootstrap.Modal(modalElement);
                
                // Isi data dummy
                document.getElementById('subjectText').textContent = 'TEST: Pengaduan Bullying';
                document.getElementById('reporterText').textContent = 'Andi Pratama (XII IPA 1)';
                document.getElementById('dateText').textContent = '15 Desember 2024 pukul 14:30';
                document.getElementById('messageText').textContent = 'Ini adalah deskripsi pengaduan untuk testing.';
                document.getElementById('statusBadge').textContent = 'Baru';
                document.getElementById('statusBadge').className = 'status-btn status-new';
                document.getElementById('jenisKejadianBadge').textContent = 'Bullying';
                document.getElementById('jenisKejadianBadge').className = 'badge bg-secondary';
                
                modal.show();
                console.log('Modal test ditampilkan');
            } else {
                console.error('Bootstrap tidak tersedia atau modal tidak ditemukan');
            }
        };
        
        // Cek jika Bootstrap tersedia
        if (typeof bootstrap !== 'undefined') {
            console.log('Bootstrap tersedia di manajemenpengaduan');
        } else {
            console.error('Bootstrap TIDAK tersedia di manajemenpengaduan!');
        }
    </script>
</body>
</html>