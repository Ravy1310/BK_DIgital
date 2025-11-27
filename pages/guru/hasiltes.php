<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BK Digital - Hasil Tes Siswa</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #0052CC;
            --light-blue: #E8F2FF;
            --dark-blue: #003D99;
            --success-green: #00C853;
            --warning-yellow: #FFD600;
            --danger-red: #D32F2F;
        }

        body {
            background: url('../../assets/image/background.jpg') center/cover no-repeat;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-content {
            padding: 20px 30px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 20px 30px;
            margin: -20px -30px 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h2 {
            margin: 0;
            color: var(--dark-blue);
            font-weight: 600;
        }

        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-left: 4px solid var(--primary-blue);
            transition: transform 0.2s;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .stats-card .icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .stats-card.blue .icon {
            background: var(--light-blue);
            color: var(--primary-blue);
        }

        .stats-card.green .icon {
            background: #E8F5E9;
            color: var(--success-green);
        }

        .stats-card.yellow .icon {
            background: #FFF9C4;
            color: #F57F17;
        }

        .result-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s;
        }

        .result-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }

        .student-info {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .student-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--light-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: var(--primary-blue);
            margin-right: 15px;
            font-weight: bold;
        }

        .score-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }

        .score-badge.ipa {
            background: #E3F2FD;
            color: #1976D2;
        }

        .score-badge.ips {
            background: #FFF9C4;
            color: #F57F17;
        }

        .score-badge.bahasa {
            background: #F3E5F5;
            color: #7B1FA2;
        }

        .score-badge.visual {
            background: #E8F5E9;
            color: var(--success-green);
        }

        .score-badge.auditori {
            background: #FFE0B2;
            color: #E65100;
        }

        .score-badge.kinestetik {
            background: #FFEBEE;
            color: var(--danger-red);
        }

        .btn-primary {
            background: var(--primary-blue);
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
        }

        .btn-primary:hover {
            background: var(--dark-blue);
        }

        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .progress-custom {
            height: 8px;
            border-radius: 10px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--light-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-blue);
        }
    </style>
</head>
<body>
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <h2>Hasil Tes Siswa</h2>
            <div class="user-profile">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <div style="font-weight: 600; font-size: 14px;">Guru BK</div>
                    <div style="font-size: 12px; color: #666;">guru@bkdigital.id</div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="stats-card blue">
                    <div class="icon"><i class="fas fa-users"></i></div>
                    <h3 class="mb-0">128</h3>
                    <p class="text-muted mb-0">Total Siswa Mengikuti Tes</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stats-card green">
                    <div class="icon"><i class="fas fa-check-circle"></i></div>
                    <h3 class="mb-0">120</h3>
                    <p class="text-muted mb-0">Siswa Selesai Tes</p>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="row align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Kelas</label>
                    <select class="form-select">
                        <option>Semua Kelas</option>
                        <option>Kelas X IPA 1</option>
                        <option>Kelas X IPA 2</option>
                        <option>Kelas XI IPA 1</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jenis Tes</label>
                    <select class="form-select">
                        <option>Semua Tes</option>
                        <option>Tes Penjurusan</option>
                        <option>Tes Minat Bakat</option>
                        <option>Tes Gaya Belajar</option>
                        <option>Tes Kepribadian</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- Results List -->
        <div class="result-card">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <div class="student-info">
                        <div class="student-avatar">BS</div>
                        <div>
                            <h5 class="mb-0">Budi Santoso (X IPA 1)</h5>
                            <small class="text-muted">Tes Penjurusan - 4 Oktober 2025</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <span class="score-badge ipa">IPA</span>
                </div>
                <div class="col-md-2 text-end">
                    <button class="btn btn-sm btn-primary">
                        <i class="fas fa-eye me-1"></i> Detail
                    </button>
                </div>
            </div>
        </div>

        <div class="result-card">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <div class="student-info">
                        <div class="student-avatar">SA</div>
                        <div>
                            <h5 class="mb-0">Siti Aminah (X IPA 1)</h5>
                            <small class="text-muted">Tes Penjurusan - 4 Oktober 2025</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <span class="score-badge ips">IPS</span>
                </div>
                <div class="col-md-2 text-end">
                    <button class="btn btn-sm btn-primary">
                        <i class="fas fa-eye me-1"></i> Detail
                    </button>
                </div>
            </div>
        </div>

        <div class="result-card">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <div class="student-info">
                        <div class="student-avatar">AP</div>
                        <div>
                            <h5 class="mb-0">Ahmad Permana (X IPA 2)</h5>
                            <small class="text-muted">Tes Gaya Belajar - 3 Oktober 2025</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <span class="score-badge visual">Visual</span>
                </div>
                <div class="col-md-2 text-end">
                    <button class="btn btn-sm btn-primary">
                        <i class="fas fa-eye me-1"></i> Detail
                    </button>
                </div>
            </div>
        </div>

        <div class="result-card">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <div class="student-info">
                        <div class="student-avatar">DP</div>
                        <div>
                            <h5 class="mb-0">Dewi Puspita (X IPA 1)</h5>
                            <small class="text-muted">Tes Gaya Belajar - 3 Oktober 2025</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <span class="score-badge auditori">Auditori</span>
                </div>
                <div class="col-md-2 text-end">
                    <button class="btn btn-sm btn-primary">
                        <i class="fas fa-eye me-1"></i> Detail
                    </button>
                </div>
            </div>
        </div>

        <div class="result-card">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <div class="student-info">
                        <div class="student-avatar">RH</div>
                        <div>
                            <h5 class="mb-0">Rudi Hartono (X IPA 2)</h5>
                            <small class="text-muted">Tes Penjurusan - 2 Oktober 2025</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <span class="score-badge bahasa">Bahasa</span>
                </div>
                <div class="col-md-2 text-end">
                    <button class="btn btn-sm btn-primary">
                        <i class="fas fa-eye me-1"></i> Detail
                    </button>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item disabled">
                    <a class="page-link" href="#">Previous</a>
                </li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                    <a class="page-link" href="#">Next</a>
                </li>
            </ul>
        </nav>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>