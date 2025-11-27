<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Monitoring BK</title>
    <!-- Memuat Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Memuat Bootstrap Icons (Pengganti Lucide untuk Ikon) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* Font modern */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        
        body {
           background: url('../../assets/image/background.jpg');
        background-size: cover;
        font-family: 'Poppins', sans-serif;
        }

        .dashboard-container {
            max-width: 1200px; /* Lebar maksimum seperti max-w-6xl */
            margin: 0 -55px;
        }

        /* --- Kartu Statistik (Metrics Card) --- */
        .card-stat {
            border-radius: 1rem; /* Sudut lebih membulat (rounded-2xl) */
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); /* Shadow XL */
            background-color: #ffffff;
            padding: 1.25rem;
            border: 1px solid #f0f0f0; /* Border tipis */
            transition: all 0.3s ease;
        }

        .card-stat:hover {
             box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); /* Shadow 2XL pada hover */
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
            font-weight: 800; /* Font Extrabold */
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
            border-radius: 1rem; /* Sudut lebih membulat */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.06); /* Shadow sedang */
            border: 1px solid #f0f0f0;
            height: 100%; /* Penting untuk simetri ketinggian kolom */
        }

        /* --- Item Pengaduan --- */
        .pengaduan-item {
            padding: 1rem;
            margin-bottom: 0.75rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            transition: background-color 0.15s ease;
            text-decoration: none;
            color: inherit;
        }

        .pengaduan-item:hover {
            background-color: #f9fafb;
            border-color: #d1d5db;
        }
        
        /* --- Badge Status Kustom --- */
        .badge-status {
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 9999px;
            font-weight: 600;
            line-height: 1; /* Agar teks center */
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

        /* --- Item Jadwal --- */
        .jadwal-item {
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid;
            border-radius: 0.5rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .jadwal-item.diterima {
            background-color: #eef2ff; /* indigo-50 */
            border-color: #6366f1; /* indigo-500 */
        }

        .jadwal-item.menunggu {
            background-color: #fef2f2; /* red-50 */
            border-color: #ef4444; /* red-500 */
        }

        .text-keterangan {
            font-size: 0.8rem;
            color: #6b7280; /* abu-abu */
        }

    </style>
</head>
<body>
    <div class="dashboard-container p-4 p-md-5">

        <!-- Judul Dashboard -->
        <h1 class="h3 font-weight-bold text-gray-800 mb-5">Dashboard Monitoring Bimbingan Konseling</h1>

        <!-- Baris 1: Statistik (4 Kolom Simetris) -->
        <div class="row g-4 mb-5">
            
            <!-- Card: Pengaduan Baru -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card-stat">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-sm text-muted mb-0">Pengaduan Baru</p>
                            <h2 class="text-3xl-bold text-dark mt-1">2</h2>
                        </div>
                        <!-- Ikon: Bell (BI) -->
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
                            <h2 class="text-3xl-bold text-dark mt-1">5</h2>
                        </div>
                        <!-- Ikon: Calendar (BI) -->
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
                            <h2 class="text-3xl-bold text-dark mt-1">2</h2>
                        </div>
                        <!-- Ikon: File Text (BI) -->
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
                            <h2 class="text-3xl-bold text-dark mt-1">2</h2>
                        </div>
                        <!-- Ikon: Users (BI) -->
                        <div class="card-stat-icon-box icon-green">
                            <i class="bi bi-people fs-fill-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Baris 2: Pengaduan Terbaru & Jadwal Mendatang -->
        <div class="row g-4">
            
            <!-- Kolom Kiri: Pengaduan Terbaru (Lebar 7) -->
            <div class="col-lg-7">
                <div class="card-content">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="h5 font-weight-bold mb-0">Pengaduan Terbaru</h2>
                        <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-1 rounded-pill fw-semibold">1 Baru</span>
                    </div>

                    <!-- Item Pengaduan 1 -->
                    <a href="#" class="pengaduan-item d-block">
                        <div class="small fw-semibold text-dark">Kesulitan dalam mengerjakan tugas kelompok</div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <small class="text-keterangan">Alex Dion</small>
                            <span class="badge-status badge-baru">BARU</span>
                        </div>
                    </a>

                    <!-- Item Pengaduan 2 -->
                    <a href="#" class="pengaduan-item d-block">
                        <div class="small fw-semibold text-dark">Masalah penyesuaian di lingkungan sekolah baru</div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <small class="text-keterangan">Anonim</small>
                            <span class="badge-status badge-proses">Di Proses</span>
                        </div>
                    </a>

                    <!-- Item Pengaduan 3 -->
                    <a href="#" class="pengaduan-item d-block">
                        <div class="small fw-semibold text-dark">Tekanan belajar untuk masuk universitas favorit</div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <small class="text-keterangan">Fitri H.</small>
                            <span class="badge-status badge-selesai">Selesai</span>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Kolom Kanan: Jadwal Mendatang (Lebar 5) -->
            <div class="col-lg-5">
                <div class="card-content">
                    <h2 class="h5 font-weight-bold mb-4">Jadwal Mendatang</h2>

                    <!-- Item Jadwal 1 -->
                    <div class="jadwal-item diterima">
                        <p class="mb-1 fw-semibold text-dark">Budi Santoso (X IPA 1)</p>
                        <small class="text-keterangan d-block">4 Oktober 2025 pukul 09.17</small>
                        <span class="small fw-bold text-indigo-700 mt-2 d-block">DITERIMA</span>
                    </div>

                    <!-- Item Jadwal 2 -->
                    <div class="jadwal-item menunggu">
                        <p class="mb-1 fw-semibold text-dark">Rina W. (XI IPS 2)</p>
                        <small class="text-keterangan d-block">4 Oktober 2025 pukul 14.00</small>
                        <span class="small fw-bold text-danger mt-2 d-block">Menunggu Konfirmasi</span>
                    </div>

                    <a href="#" class="mt-3 d-inline-block small fw-semibold text-primary text-decoration-none hover-underline">
                        Lihat semua jadwal &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>