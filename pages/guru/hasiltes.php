<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BK Digital - Hasil Tes Siswa</title>
    <!-- Memuat Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Memuat Font Awesome (Ikon) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Font modern -->
    <style>
        /* Font modern */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        
        :root {
            --primary-blue: #2563eb; /* Biru Primer yang konsisten */
            --light-blue: #eff6ff; /* Biru Muda */
            --dark-blue: #1e40af;
            --success-green: #059669;
            --warning-yellow: #f59e0b;
            --danger-red: #dc2626;
        }

        body {
            background: url('../../assets/image/background.jpg');
        background-size: cover;
        font-family: 'Poppins', sans-serif;
        }

        /* Container utama yang kini menjadi wadah putih besar */
        .main-container {
            padding: 30px 15px;
            max-width: 1200px;
            margin: 30px auto; /* Margin atas dan bawah untuk memisahkannya dari tepi */
            background-color: white; /* Diatur menjadi putih */
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05); /* Bayangan yang lebih menonjol */
        }
        
        /* Mengganti .main-content menjadi inner-content untuk padding internal */
        .inner-content {
            padding: 0; 
        }

        h2.page-title {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 25px;
            font-size: 2rem;
        }

        /* --- Stats Cards --- */
        .stats-card {
            background: #f9fafb; /* Sedikit abu-abu agar menonjol dari latar belakang container */
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
            border-left: 5px solid var(--primary-blue);
            transition: transform 0.2s, box-shadow 0.3s;
            height: 100%;
        }

        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }

        .stats-card .icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
            flex-shrink: 0;
        }

        .stats-card.blue .icon {
            background: var(--light-blue);
            color: var(--primary-blue);
        }

        .stats-card.green .icon {
            background: #d1fae5;
            color: var(--success-green);
        }

        /* --- Filter Section --- */
        .filter-section {
            background: #f9fafb; /* Sedikit abu-abu agar menonjol dari latar belakang container */
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid #e5e7eb;
        }
        
        .filter-section .form-select,
        .filter-section .form-control {
            border-radius: 8px;
        }
        
        .filter-section .btn-primary {
            background-color: var(--primary-blue);
            border: none;
            transition: background-color 0.2s;
            height: 42px; /* Sesuaikan dengan tinggi select */
        }
        .filter-section .btn-primary:hover {
            background-color: var(--dark-blue);
        }

        /* --- Result Card --- */
        .result-card {
            background: white; /* Tetap putih agar terlihat seperti baris di dalam wadah utama */
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 15px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
            border: 1px solid #e5e7eb;
            transition: border-color 0.2s;
        }

        .result-card:hover {
            border-color: var(--primary-blue);
        }

        .student-info {
            display: flex;
            align-items: center;
        }

        .student-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: var(--light-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: var(--primary-blue);
            margin-right: 15px;
            font-weight: bold;
            flex-shrink: 0;
        }
        
        .student-details h5 {
            font-size: 1rem;
            font-weight: 700;
        }

        .score-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
        }

        /* Warna Badge */
        .score-badge.ipa { background: #E3F2FD; color: #1976D2; }
        .score-badge.ips { background: #FFF9C4; color: #F57F17; }
        .score-badge.bahasa { background: #F3E5F5; color: #7B1FA2; }
        .score-badge.visual { background: #E8F5E9; color: var(--success-green); }
        .score-badge.auditori { background: #FFE0B2; color: #E65100; }
        .score-badge.kinestetik { background: #FFEBEE; color: var(--danger-red); }

        .btn-detail {
            background: var(--primary-blue);
            border: none;
            font-size: 0.9rem;
            font-weight: 600;
            border-radius: 8px;
        }
        .btn-detail:hover {
            background: var(--dark-blue);
        }
    </style>
</head>
<body>
    <!-- Main Content -->
    <div class="main-container">
        <div class="inner-content">
            <h2 class="page-title">Hasil Tes Siswa</h2>

            <!-- Stats Cards -->
            <div class="row g-4 mb-5">
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="stats-card blue">
                        <div class="d-flex align-items-center">
                            <div class="icon"><i class="fas fa-users"></i></div>
                            <div class="ms-3">
                                <h3 class="mb-0 fw-bold">128</h3>
                                <p class="text-muted mb-0 small">Total Siswa Mengikuti Tes</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="stats-card green">
                        <div class="d-flex align-items-center">
                            <div class="icon"><i class="fas fa-check-circle"></i></div>
                            <div class="ms-3">
                                <h3 class="mb-0 fw-bold">120</h3>
                                <p class="text-muted mb-0 small">Siswa Selesai Tes</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-12 col-lg-4">
                    <div class="stats-card yellow">
                        <div class="d-flex align-items-center">
                            <div class="icon" style="background: #FFF9C4; color: #F57F17;"><i class="fas fa-hourglass-half"></i></div>
                            <div class="ms-3">
                                <h3 class="mb-0 fw-bold">8</h3>
                                <p class="text-muted mb-0 small">Siswa Belum Menyelesaikan Tes</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3 col-lg-3">
                        <label class="form-label small fw-semibold">Cari Nama</label>
                        <input type="text" class="form-control" id="searchInput" placeholder="Nama siswa...">
                    </div>
                    <div class="col-md-3 col-lg-3">
                        <label class="form-label small fw-semibold">Kelas</label>
                        <select class="form-select" id="filterKelas">
                            <option value="">Semua Kelas</option>
                            <option value="X IPA 1">Kelas X IPA 1</option>
                            <option value="X IPA 2">Kelas X IPA 2</option>
                            <option value="XI IPA 1">Kelas XI IPA 1</option>
                            <option value="XII IPS 3">Kelas XII IPS 3</option>
                        </select>
                    </div>
                    <div class="col-md-4 col-lg-4">
                        <label class="form-label small fw-semibold">Jenis Tes</label>
                        <select class="form-select" id="filterJenisTes">
                            <option value="">Semua Tes</option>
                            <option value="Penjurusan">Tes Penjurusan</option>
                            <option value="Minat Bakat">Tes Minat Bakat</option>
                            <option value="Gaya Belajar">Tes Gaya Belajar</option>
                            <option value="Kepribadian">Tes Kepribadian</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-lg-2">
                        <button class="btn btn-primary w-100" id="applyFilterBtn">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                    </div>
                </div>
            </div>

            <!-- Results List -->
            <div id="resultsList">
                <!-- Data akan diisi oleh JavaScript -->

                <!-- Contoh Data 1 (Penjurusan IPA) -->
                <div class="result-card" data-kelas="X IPA 1" data-tes="Penjurusan">
                    <div class="row align-items-center g-2">
                        <div class="col-12 col-md-6 col-lg-5">
                            <div class="student-info">
                                <div class="student-avatar">BS</div>
                                <div class="student-details">
                                    <h5 class="mb-0">Budi Santoso (X IPA 1)</h5>
                                    <small class="text-muted">Tes Penjurusan - 4 Oktober 2025</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 col-lg-4 text-start text-md-center">
                            <span class="score-badge ipa">Rekomendasi IPA</span>
                        </div>
                        <div class="col-6 col-md-3 col-lg-3 text-end">
                            <button class="btn btn-sm btn-detail btn-primary">
                                <i class="fas fa-eye me-1"></i> Detail
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Contoh Data 2 (Penjurusan IPS) -->
                <div class="result-card" data-kelas="X IPA 1" data-tes="Penjurusan">
                    <div class="row align-items-center g-2">
                        <div class="col-12 col-md-6 col-lg-5">
                            <div class="student-info">
                                <div class="student-avatar">SA</div>
                                <div class="student-details">
                                    <h5 class="mb-0">Siti Aminah (X IPA 1)</h5>
                                    <small class="text-muted">Tes Penjurusan - 4 Oktober 2025</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 col-lg-4 text-start text-md-center">
                            <span class="score-badge ips">Rekomendasi IPS</span>
                        </div>
                        <div class="col-6 col-md-3 col-lg-3 text-end">
                            <button class="btn btn-sm btn-detail btn-primary">
                                <i class="fas fa-eye me-1"></i> Detail
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Contoh Data 3 (Gaya Belajar Visual) -->
                <div class="result-card" data-kelas="X IPA 2" data-tes="Gaya Belajar">
                    <div class="row align-items-center g-2">
                        <div class="col-12 col-md-6 col-lg-5">
                            <div class="student-info">
                                <div class="student-avatar">AP</div>
                                <div class="student-details">
                                    <h5 class="mb-0">Ahmad Permana (X IPA 2)</h5>
                                    <small class="text-muted">Tes Gaya Belajar - 3 Oktober 2025</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 col-lg-4 text-start text-md-center">
                            <span class="score-badge visual">Gaya Belajar Visual</span>
                        </div>
                        <div class="col-6 col-md-3 col-lg-3 text-end">
                            <button class="btn btn-sm btn-detail btn-primary">
                                <i class="fas fa-eye me-1"></i> Detail
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Contoh Data 4 (Gaya Belajar Auditori) -->
                <div class="result-card" data-kelas="X IPA 1" data-tes="Gaya Belajar">
                    <div class="row align-items-center g-2">
                        <div class="col-12 col-md-6 col-lg-5">
                            <div class="student-info">
                                <div class="student-avatar">DP</div>
                                <div class="student-details">
                                    <h5 class="mb-0">Dewi Puspita (X IPA 1)</h5>
                                    <small class="text-muted">Tes Gaya Belajar - 3 Oktober 2025</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 col-lg-4 text-start text-md-center">
                            <span class="score-badge auditori">Gaya Belajar Auditori</span>
                        </div>
                        <div class="col-6 col-md-3 col-lg-3 text-end">
                            <button class="btn btn-sm btn-detail btn-primary">
                                <i class="fas fa-eye me-1"></i> Detail
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Contoh Data 5 (Penjurusan Bahasa) -->
                <div class="result-card" data-kelas="X IPA 2" data-tes="Penjurusan">
                    <div class="row align-items-center g-2">
                        <div class="col-12 col-md-6 col-lg-5">
                            <div class="student-info">
                                <div class="student-avatar">RH</div>
                                <div class="student-details">
                                    <h5 class="mb-0">Rudi Hartono (X IPA 2)</h5>
                                    <small class="text-muted">Tes Penjurusan - 2 Oktober 2025</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 col-lg-4 text-start text-md-center">
                            <span class="score-badge bahasa">Rekomendasi Bahasa</span>
                        </div>
                        <div class="col-6 col-md-3 col-lg-3 text-end">
                            <button class="btn btn-sm btn-detail btn-primary">
                                <i class="fas fa-eye me-1"></i> Detail
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Contoh Data 6 (Tes Minat Bakat) -->
                <div class="result-card" data-kelas="XI IPA 1" data-tes="Minat Bakat">
                    <div class="row align-items-center g-2">
                        <div class="col-12 col-md-6 col-lg-5">
                            <div class="student-info">
                                <div class="student-avatar">DS</div>
                                <div class="student-details">
                                    <h5 class="mb-0">Doni Setiawan (XI IPA 1)</h5>
                                    <small class="text-muted">Tes Minat Bakat - 1 Oktober 2025</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 col-lg-4 text-start text-md-center">
                            <span class="score-badge kinestetik">Minat Teknik</span>
                        </div>
                        <div class="col-6 col-md-3 col-lg-3 text-end">
                            <button class="btn btn-sm btn-detail btn-primary">
                                <i class="fas fa-eye me-1"></i> Detail
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Pagination -->
            <nav class="mt-5">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" aria-label="Previous">Previous</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#" aria-label="Next">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const resultsList = document.getElementById('resultsList');
            const searchInput = document.getElementById('searchInput');
            const filterKelas = document.getElementById('filterKelas');
            const filterJenisTes = document.getElementById('filterJenisTes');
            const applyFilterBtn = document.getElementById('applyFilterBtn');
            const resultCards = resultsList.querySelectorAll('.result-card');

            const applyFilters = () => {
                const search = searchInput.value.toLowerCase();
                const selectedKelas = filterKelas.value;
                const selectedTes = filterJenisTes.value;

                resultCards.forEach(card => {
                    const studentName = card.querySelector('h5').textContent.toLowerCase();
                    const cardKelas = card.getAttribute('data-kelas');
                    const cardTes = card.getAttribute('data-tes');

                    const matchesSearch = studentName.includes(search);
                    const matchesKelas = !selectedKelas || cardKelas === selectedKelas;
                    const matchesTes = !selectedTes || cardTes === selectedTes;

                    if (matchesSearch && matchesKelas && matchesTes) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            };

            // Terapkan filter saat tombol diklik
            applyFilterBtn.addEventListener('click', applyFilters);

            // Terapkan filter saat nama diketik (Debounced untuk kinerja yang lebih baik)
            let debounceTimeout;
            searchInput.addEventListener('input', () => {
                clearTimeout(debounceTimeout);
                debounceTimeout = setTimeout(applyFilters, 300);
            });
            
            // Terapkan filter saat dropdown berubah
            filterKelas.addEventListener('change', applyFilters);
            filterJenisTes.addEventListener('change', applyFilters);

            // Terapkan filter awal saat halaman dimuat
            applyFilters();
        });
    </script>
</body>
</html>