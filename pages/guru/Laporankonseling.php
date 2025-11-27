<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Laporan Konseling</title>
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

        /* Kontainer Utama & Tabel Box */
        .main-card {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            padding: 24px;
           margin: 40px -20px 0 -30px;
        }

        /* Header */
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        h4 {
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0;
            font-size: 1.75rem;
        }

        /* Tombol Utama Hijau */
        .btn-green {
            background-color: #10b981; /* Warna hijau yang lebih modern (Emerald) */
            color: white;
            font-weight: 600;
            border-radius: 8px;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            border: none;
            transition: background-color 0.2s;
        }

        .btn-green:hover {
            background-color: #059669;
            color: white;
        }

        .btn-green i {
            font-size: 1.2rem;
        }

        /* Gaya Tabel */
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        .table thead th {
            font-weight: 600;
            color: #4b5563;
            background-color: #f9fafb;
            font-size: 0.85rem;
            padding: 12px 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .table tbody td {
            padding: 12px 10px;
            font-size: 0.9rem;
            color: #374151;
            vertical-align: middle;
        }

        .table-hover tbody tr:hover {
            background-color: #f9fafb;
        }

        /* Link Aksi */
        .action-link {
            color: #3b82f6; /* Biru aksi */
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            cursor: pointer;
            font-size: 0.85rem;
        }
        .action-link:hover {
            text-decoration: underline;
            color: #1d4ed8;
        }

        .icon-eye {
            width: 18px;
            height: 18px;
            color: #3b82f6;
        }

        /* Modal styling */
        .modal-content {
            border-radius: 12px;
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .info-box {
            background-color: #f0fdf4; /* Hijau sangat muda */
            border: 1px solid #dcfce7;
            color: #047857;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }

        .note-box {
            background-color: #f3f4f6; /* Abu-abu muda */
            border: 1px solid #e5e7eb;
            color: #4b5563;
            border-radius: 8px;
            padding: 12px;
            font-size: 0.9rem;
        }

        .modal-footer .btn {
            border-radius: 8px;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="main-card">
            <!-- Header -->
            <div class="header-section">
                <h4>Riwayat Laporan Konseling</h4>
                <button class="btn btn-green" id="openLaporanBtn">
                    <!-- Menggunakan Bootstrap Icon untuk konsistensi -->
                    <i class="bi bi-file-earmark-plus"></i>
                    Buat Laporan Baru
                </button>
            </div>

            <!-- Box tabel -->
            <div class="table-box">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="text-center">
                            <tr>
                                <th scope="col" class="text-start">Nama Siswa</th>
                                <th scope="col">Tanggal Sesi</th>
                                <th scope="col" class="text-start">Topik</th>
                                <th scope="col" style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="laporanTableBody">
                            <!-- Baris pertama berisi data statis untuk contoh -->
                            <tr data-nama="Selvi Fitra" 
                                data-tanggal="2 Oktober 2025 pukul 11.34"
                                data-topik="Orientasi karir dan pilihan studi"
                                data-hasil="Setelah berdiskusi, Selvi menunjukkan minat yang kuat di bidang desain grafis dan setuju untuk mengikuti tes minat bakat online. Kami menyusun rencana untuk mencari beasiswa seni."
                                data-catatan="Tindak lanjut: Jadwal pertemuan tripartit dengan orang tua minggu depan untuk membahas langkah selanjutnya.">
                                <td class="text-start">Selvi Fitra</td>
                                <td>2 Oktober 2025 pukul 11.34</td>
                                <td class="text-start">Orientasi karir dan pilihan studi</td>
                                <td>
                                    <a class="action-link" data-bs-toggle="modal" data-bs-target="#detailModal" onclick="prepareDetail(this)">
                                        <i class="bi bi-eye icon-eye"></i>
                                        Detail
                                    </a>
                                </td>
                            </tr>

                            <!-- Baris Kosong (menggunakan kelas Bootstrap untuk tinggi) -->
                            <tr class="text-center text-muted"><td colspan="4" style="height: 30px;">-</td></tr>
                            <tr class="text-center text-muted"><td colspan="4" style="height: 30px;">-</td></tr>
                            <tr class="text-center text-muted"><td colspan="4" style="height: 30px;">-</td></tr>
                            <tr class="text-center text-muted"><td colspan="4" style="height: 30px;">-</td></tr>
                            <tr class="text-center text-muted"><td colspan="4" style="height: 30px;">-</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal Buat Laporan -->
    <div class="modal fade" id="laporanModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-center w-100 fw-bold">Buat Laporan Konseling</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form id="formLaporanBaru">
                        <div class="mb-3">
                            <label for="namaSiswa" class="form-label fw-semibold">Nama Siswa</label>
                            <input type="text" class="form-control" id="namaSiswa" placeholder="Masukkan nama siswa" required>
                        </div>
                        <div class="mb-3">
                            <label for="tanggalSesi" class="form-label fw-semibold">Tanggal Sesi</label>
                            <input type="datetime-local" class="form-control" id="tanggalSesi" required>
                        </div>
                        <div class="mb-3">
                            <label for="topik" class="form-label fw-semibold">Topik</label>
                            <input type="text" class="form-control" id="topik" placeholder="Masukkan topik pembahasan" required>
                        </div>
                        <div class="mb-3">
                            <label for="hasilPertemuan" class="form-label fw-semibold">Hasil Pertemuan & Solusi</label>
                            <textarea class="form-control" id="hasilPertemuan" rows="3" placeholder="Tuliskan hasil dan solusi..." required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="catatanTambahan" class="form-label fw-semibold">Catatan Tambahan</label>
                            <textarea class="form-control" id="catatanTambahan" rows="2" placeholder="Catatan tambahan jika ada..."></textarea>
                        </div>
                        <div class="modal-footer justify-content-center border-top-0">
                            <button type="submit" class="btn btn-green px-4">Simpan Laporan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Laporan -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title text-center w-100 fw-bold">Laporan Konseling</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body text-center">
                    <h6 class="fw-bold text-primary" id="detailNama"></h6>
                    <p class="mb-1 text-muted small" id="detailTanggal"></p>
                    <p class="mb-3 text-muted small" id="detailTopik"></p>

                    <h6 class="text-start fw-semibold small mb-2">Hasil Pertemuan & Solusi:</h6>
                    <div class="info-box text-start" id="detailHasil"></div>

                    <h6 class="text-start fw-semibold small mb-2">Catatan Tambahan:</h6>
                    <div class="note-box text-start" id="detailCatatan"></div>

                    <p class="text-muted small mt-3 text-end">Dicatat oleh : Guru BK</p>
                </div>
                
                <div class="modal-footer justify-content-center border-top-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Membuka Modal Buat Laporan
        document.getElementById("openLaporanBtn").addEventListener("click", function() {
            const modal = new bootstrap.Modal(document.getElementById("laporanModal"));
            modal.show();
        });
        
        // Fungsi untuk mempersiapkan data ke Modal Detail
        function prepareDetail(element) {
            const row = element.closest('tr');
            const nama = row.getAttribute('data-nama');
            const tanggal = row.getAttribute('data-tanggal');
            const topik = row.getAttribute('data-topik');
            const hasil = row.getAttribute('data-hasil');
            const catatan = row.getAttribute('data-catatan');

            document.getElementById("detailNama").innerText = nama;
            document.getElementById("detailTanggal").innerText = "Tanggal Sesi: " + tanggal;
            document.getElementById("detailTopik").innerText = "Topik: " + topik;
            document.getElementById("detailHasil").innerText = hasil || "-";
            document.getElementById("detailCatatan").innerText = catatan || "Tidak ada catatan tambahan.";
        }


        // Simpan laporan baru ke tabel (Logika Simulasi)
        document.getElementById("formLaporanBaru").addEventListener("submit", function(e) {
            e.preventDefault();

            const nama = document.getElementById("namaSiswa").value;
            const tanggalInput = document.getElementById("tanggalSesi").value;
            
            // Format tanggal Indonesia
            const tanggalObj = new Date(tanggalInput);
            const tanggal = tanggalObj.toLocaleString("id-ID", {
                day: "numeric", month: "long", year: "numeric", hour: "2-digit", minute: "2-digit"
            });
            
            const topik = document.getElementById("topik").value;
            const hasil = document.getElementById("hasilPertemuan").value;
            const catatan = document.getElementById("catatanTambahan").value;
            
            // Membuat data untuk row baru
            const newRowHtml = `
                <td class="text-start">${nama}</td>
                <td>${tanggal}</td>
                <td class="text-start">${topik}</td>
                <td>
                    <a class="action-link" data-bs-toggle="modal" data-bs-target="#detailModal" onclick="prepareDetail(this)">
                        <i class="bi bi-eye icon-eye"></i> Detail
                    </a>
                </td>
            `;

            const tbody = document.getElementById("laporanTableBody");
            const newRow = tbody.insertRow(0); // Tambah di atas
            newRow.innerHTML = newRowHtml;
            
            // Set atribut data untuk detail modal
            newRow.setAttribute('data-nama', nama);
            newRow.setAttribute('data-tanggal', tanggal);
            newRow.setAttribute('data-topik', topik);
            newRow.setAttribute('data-hasil', hasil);
            newRow.setAttribute('data-catatan', catatan);

            document.getElementById("formLaporanBaru").reset();
            const modal = bootstrap.Modal.getInstance(document.getElementById("laporanModal"));
            modal.hide();
        });

        // Mengatur fungsi prepareDetail agar bisa dipanggil langsung dari onclick pada baris yang sudah ada
        document.querySelector('#laporanTableBody tr:first-child .action-link').setAttribute('onclick', 'prepareDetail(this)');
    </script>
</body>
</html>