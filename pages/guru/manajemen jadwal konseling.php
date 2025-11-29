<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Jadwal Konseling</title>
    <!-- Memuat Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Memuat Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* Font modern */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        
        body {
             background: url('../../assets/image/background.jpg');
        background-size: cover;
        font-family: 'Poppins', sans-serif;
        }
        
        /* Kontainer Utama */
        .main-card {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            padding: 24px;
           margin: 40px -20px 0 -30px;
        }

        /* Judul */
        h2 {
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 20px;
            font-size: 1.75rem;
        }

        /* Search Bar */
        .search-box {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 8px 15px;
            display: flex;
            align-items: center;
            background-color: #fff;
            margin-bottom: 20px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .search-box input {
            border: none;
            outline: none;
            width: 100%;
            font-size: 0.95rem;
        }
        
        /* Gaya Tabel */
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        .table-tight {
            width: 100%;
            margin-bottom: 0;
        }
        
        /* Header Tabel */
        .table-tight thead th {
            font-weight: 600;
            color: #4b5563;
            background-color: #f9fafb;
            font-size: 0.85rem;
            padding: 12px 10px;
            border: none; /* Hilangkan border ganda */
        }
        
        /* Isi Tabel */
        .table-tight tbody td {
            padding: 12px 10px;
            font-size: 0.9rem;
            color: #374151;
            border-top: 1px solid #f3f4f6; /* Garis pemisah antar baris yang lembut */
        }

        .table-tight tbody tr:hover {
            background-color: #f9fafb;
        }

        /* Tombol Status */
        .status-badge {
            border-radius: 9999px; /* Bentuk pil */
            padding: 4px 12px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }

        .status-menunggu {
            background-color: #fef3c7; /* Kuning muda */
            color: #92400e; /* Cokelat tua */
        }
        .status-diterima {
            background-color: #d1fae5; /* Hijau muda */
            color: #065f46; /* Hijau tua */
        }
        .status-ditolak {
            background-color: #fee2e2; /* Merah muda */
            color: #991b1b; /* Merah tua */
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
        }
        
        .btn-dot:hover {
            background-color: #f3f4f6;
        }

        .dropdown-menu {
            min-width: 130px;
            padding: 6px 0;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .dropdown-item {
            font-size: 0.85rem;
            padding: 8px 12px;
        }
        
        .dropdown-item i {
            width: 16px;
            height: 16px;
            margin-right: 8px;
        }

    </style>
</head>
<body>

    <div class="container-fluid">
        <div class="main-card">
            <h2>Manajemen Jadwal Konseling</h2>

            <!-- Search bar -->
            <div class="row">
                <div class="col-12 col-md-6 mb-3">
                    <div class="search-box">
                        <!-- Ikon Bootstrap untuk pencarian -->
                        <i class="bi bi-search text-muted me-2"></i>
                        <input type="text" id="searchInput" class="form-control" placeholder="Cari berdasarkan Nama, Kelas, Topik...">
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-tight align-middle">
                    <thead class="text-center">
                        <tr>
                            <th scope="col">Nama Siswa</th>
                            <th scope="col">Kelas</th>
                            <th scope="col">Topik Bimbingan</th>
                            <th scope="col">Tanggal & Jam</th>
                            <th scope="col">Status</th>
                            <th scope="col" style="width: 80px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="dataTable">
                        <!-- Data Baris 1: Menunggu -->
                        <tr data-status="Menunggu">
                            <td>Selvi</td>
                            <td>XI IPS 3</td>
                            <td class="text-start">Masalah Tidur</td>
                            <td>5 Okt 2025 11.36</td>
                            <td><span class="status-badge status-menunggu">Menunggu</span></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-dot dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item text-success" href="#" data-action="accept">
                                                <i class="bi bi-check-circle"></i> Terima
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-danger" href="#" data-action="reject">
                                                <i class="bi bi-x-circle"></i> Tolak
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>

                        <!-- Data Baris 2: Diterima -->
                        <tr data-status="Diterima">
                            <td>Budi Santoso</td>
                            <td>X IPA 1</td>
                            <td class="text-start">Kesulitan mengatur waktu belajar</td>
                            <td>4 Okt 2025 09.00</td>
                            <td><span class="status-badge status-diterima">Diterima</span></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-dot dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item text-secondary" href="#" data-action="detail">
                                                <i class="bi bi-info-circle"></i> Detail
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>

                        <!-- Data Baris 3: Ditolak -->
                        <tr data-status="Ditolak">
                            <td>Alex Dion</td>
                            <td>XII Bahasa</td>
                            <td class="text-start">Kecemasan pasca ujian</td>
                            <td>3 Okt 2025 14.30</td>
                            <td><span class="status-badge status-ditolak">Ditolak</span></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-dot dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item text-secondary" href="#" data-action="detail">
                                                <i class="bi bi-info-circle"></i> Detail
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>

                        <!-- Baris Kosong (diisi ulang jika perlu) -->
                        <tr class="text-muted text-center"><td colspan="6">-</td></tr>
                        <tr class="text-muted text-center"><td colspan="6">-</td></tr>
                        <tr class="text-muted text-center"><td colspan="6">-</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Fungsi pencarian tabel
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('#dataTable tr');
            rows.forEach(row => {
                // Jangan sembunyikan baris placeholder kosong
                if (row.classList.contains('text-muted')) {
                    return;
                }
                
                let text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
        
        // Logika Aksi (Simulasi)
        document.getElementById('dataTable').addEventListener('click', function(event) {
            const target = event.target.closest('.dropdown-item');
            if (!target) return;
            
            const action = target.getAttribute('data-action');
            const row = target.closest('tr');
            const studentName = row.querySelector('td:first-child').innerText;

            if (action === 'accept') {
                alert(`Permintaan konseling dari ${studentName} diterima!`);
                // Di sini Anda akan menambahkan logika untuk update status di database
            } else if (action === 'reject') {
                alert(`Permintaan konseling dari ${studentName} ditolak.`);
                // Di sini Anda akan menambahkan logika untuk update status di database
            } else if (action === 'detail') {
                 alert(`Menampilkan detail jadwal untuk ${studentName}.`);
                 // Di sini Anda akan menampilkan modal detail (jika ada)
            }
        });
    </script>

</body>
</html>