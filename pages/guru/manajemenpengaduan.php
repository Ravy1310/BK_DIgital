<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengaduan</title>
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
        
        /* Kontainer Utama - Menghilangkan margin negatif yang rumit */
        .main-card {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            padding: 24px;
            margin: 40px -20px 0 -30px;
        }

        /* Judul */
        h4 {
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 20px;
        }

        /* Search Bar */
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
        
        /* Gaya Tabel */
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        .table {
            margin-bottom: 0;
            width: 100%;
        }
        
        /* Header Tabel */
        .table thead th {
            font-weight: 600;
            color: #4b5563;
            background-color: #f9fafb;
            font-size: 0.85rem;
            padding: 12px 10px; /* Padding seragam */
        }
        
        /* Isi Tabel */
        .table tbody td {
            padding: 12px 10px; /* Padding seragam */
            font-size: 0.9rem;
            color: #374151;
        }

        .table-hover tbody tr:hover {
            background-color: #f3f4f6;
        }

        /* Tombol Status */
        .status-btn {
            border-radius: 9999px; /* Bentuk pil */
            padding: 4px 12px;
            font-size: 0.75rem;
            font-weight: 600;
            border: none;
            display: inline-block;
        }

        .status-process {
            background-color: #dbeafe; /* Biru muda */
            color: #1e40af; /* Biru tua */
        }
        .status-new {
            background-color: #fef3c7; /* Kuning muda */
            color: #92400e; /* Cokelat tua */
        }
        .status-done {
            background-color: #d1fae5; /* Hijau muda */
            color: #065f46; /* Hijau tua */
        }

        /* Link Aksi */
        .action-link {
            color: #3b82f6; /* Biru aksi */
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
        
        /* Modal Detail */
        #detailModal .modal-content {
            border-radius: 12px;
        }

        .detail-info i {
            font-size: 1.1rem;
        }

        /* Responsif */
        @media (min-width: 992px) {
            .main-card {
                /* Gunakan container Bootstrap, tidak perlu margin negatif */
                padding: 30px; 
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
                        <!-- Ikon Bootstrap untuk pencarian -->
                        <i class="bi bi-search text-muted me-2"></i>
                        <input type="text" id="searchInput" placeholder="Cari berdasarkan Subjek, Nama...">
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
                        <tr>
                            <td class="fw-medium text-start">Kesulitan dalam mengerjakan tugas kelompok</td>
                            <td class="text-center">Budi Santoso</td>
                            <td class="text-center">4 Oktober 2025 pukul 11:23</td>
                            <td class="text-center"><span class="status-btn status-process">Diproses</span></td>
                            <td class="text-center">
                                <a class="action-link" data-bs-toggle="modal" data-bs-target="#detailModal" 
                                   data-subject="Kesulitan dalam mengerjakan tugas kelompok" 
                                   data-reporter="Budi Santoso" 
                                   data-date="4 Oktober 2025 pukul 11:23" 
                                   data-status="Diproses"
                                   data-message="Saya merasa sulit bergaul dengan anggota kelompok saya.">
                                    <i class="bi bi-eye"></i> Lihat
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-medium text-start">Masalah penyesuaian di lingkungan sekolah baru</td>
                            <td class="text-center">Rina W.</td>
                            <td class="text-center">4 Oktober 2025 pukul 14:00</td>
                            <td class="text-center"><span class="status-btn status-new">Baru</span></td>
                            <td class="text-center">
                                <a class="action-link" data-bs-toggle="modal" data-bs-target="#detailModal" 
                                   data-subject="Masalah penyesuaian di lingkungan sekolah baru" 
                                   data-reporter="Rina W." 
                                   data-date="4 Oktober 2025 pukul 14:00" 
                                   data-status="Baru"
                                   data-message="Saya sering merasa sendirian dan kesulitan memulai percakapan dengan teman baru.">
                                    <i class="bi bi-eye"></i> Lihat
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-medium text-start">Tekanan belajar untuk masuk universitas favorit</td>
                            <td class="text-center">Anonim</td>
                            <td class="text-center">3 Oktober 2025 pukul 09:00</td>
                            <td class="text-center"><span class="status-btn status-done">Selesai</span></td>
                            <td class="text-center">
                                <a class="action-link" data-bs-toggle="modal" data-bs-target="#detailModal" 
                                   data-subject="Tekanan belajar untuk masuk universitas favorit" 
                                   data-reporter="Anonim" 
                                   data-date="3 Oktober 2025 pukul 09:00" 
                                   data-status="Selesai"
                                   data-message="Saya kesulitan tidur dan fokus karena merasa harus selalu belajar.">
                                    <i class="bi bi-eye"></i> Lihat
                                </a>
                            </td>
                        </tr>
                        <!-- Baris placeholder dihapus karena sudah ada data -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

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

                    <p class="fw-semibold mb-2">Pesan:</p>
                    <div class="border rounded p-3 mt-2 bg-light">
                        <p id="messageText" class="mb-0 small text-dark"></p>
                    </div>
                </div>

                <div class="modal-footer">
                    <!-- Tombol berubah berdasarkan status -->
                    <button id="actionButton" class="btn btn-primary"></button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fitur pencarian
        document.getElementById("searchInput").addEventListener("keyup", function() {
            let input = this.value.toLowerCase();
            let rows = document.querySelectorAll("#complaintTable tr");
            rows.forEach(row => {
                // Periksa di semua kolom selisih 
                let text = Array.from(row.querySelectorAll('td')).slice(0, 4).map(td => td.innerText).join(' ').toLowerCase();
                row.style.display = text.includes(input) ? "" : "none";
            });
        });

        // Logika Pengisian Modal
        const detailModal = document.getElementById('detailModal');
        detailModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const subject = button.getAttribute('data-subject');
            const reporter = button.getAttribute('data-reporter');
            const date = button.getAttribute('data-date');
            const message = button.getAttribute('data-message');
            const status = button.getAttribute('data-status');
            
            // Isi data
            document.getElementById('subjectText').innerText = subject;
            document.getElementById('reporterText').innerText = "Pelapor: " + reporter;
            document.getElementById('dateText').innerText = date;
            document.getElementById('messageText').innerText = message;
            
            // Isi status badge
            const statusBadge = document.getElementById('statusBadge');
            statusBadge.innerText = status;
            statusBadge.className = 'status-btn'; // Reset kelas
            
            const actionButton = document.getElementById('actionButton');
            
            // Logika Status dan Tombol Aksi
            if (status === 'Baru' || status === 'Diproses') {
                statusBadge.classList.add('status-process');
                actionButton.innerText = "Ubah ke Selesai";
                actionButton.className = 'btn btn-success';
                actionButton.onclick = function() {
                    alert('Logika untuk mengubah status ke Selesai akan ditambahkan di sini.');
                    // Tambahkan logika untuk update status di sini
                    // Anda bisa memanggil fungsi di sini
                };

            } else if (status === 'Selesai') {
                statusBadge.classList.add('status-done');
                actionButton.innerText = "Lihat Riwayat";
                actionButton.className = 'btn btn-info text-white';
                actionButton.onclick = function() {
                    alert('Logika untuk melihat riwayat akan ditambahkan di sini.');
                };
            }
        });
    </script>

</body>
</html>