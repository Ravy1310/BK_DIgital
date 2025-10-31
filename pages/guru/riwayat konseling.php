<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Riwayat Laporan Konseling</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>

    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
      background-color: transparent;
    }

    body {
      background-image: url('background.jpg');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      font-family: 'Poppins', sans-serif;
      display: flex;
      justify-content: center;
      align-items: flex-start; /* ubah ke center kalau mau box di tengah layar */
      min-height: 100vh;
    }

    .header-section {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 30px;
      margin-bottom: 10px;
    }

    .btn-green {
      background-color: #00b050;
      color: white;
      font-weight: 500;
      border-radius: 8px;
      padding: 10px 20px;
      display: flex;
      align-items: center;
      gap: 10px;
      border: none;
    }

    .btn-green:hover {
      background-color: #019445;
      color: white;
    }

    .btn-green svg {
      width: 22px;
      height: 22px;
      fill: #ffffff;
    }

    .table-box {
      background-color: white;
      border: 2px solid #ced4da;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .table thead {
      background-color: #f2f3f5;

    }

    .table th, .table td {
      text-align: center;
      vertical-align: middle;
    }

    .action-link {
      color: #007bff;
      text-decoration: none;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 5px;
      cursor: pointer;
    }

    .action-link:hover {
      text-decoration: underline;
    }

    .icon-eye {
      width: 18px;
      height: 18px;
      fill: #007bff;
    }

    /* Modal styling */
    .modal-content {
      border-radius: 10px;
      border: 1.5px solid #ced4da;
      padding: 10px;
    }

    .modal-header {
      border-bottom: none;
      padding-bottom: 0;
    }

    .modal-title {
      font-weight: 600;
      font-size: 18px;
    }

    .info-box {
      background-color: #e8f6ef;
      border-radius: 8px;
      padding: 10px;
      margin-bottom: 10px;
    }

    .note-box {
      background-color: #e0e0e0;
      border-radius: 8px;
      padding: 10px;
    }

    .modal-footer .btn {
      border-radius: 8px;
    }
  </style>
</head>
<body>

  <div class="container">
    <!-- Header -->
    <div class="header-section">
      <h4 class="fw-bold">Riwayat Laporan Konseling</h4>
      <button class="btn btn-green" id="openLaporanBtn">
        <svg id="Layer_1" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
          <path d="M29.4,190.9c2.8,0,5-2.2,5-5v-52.3l30.2-16.2v42.9c0,2.8,2.2,5,5,5c2.8,0,5-2.2,5-5v-59.6l-50.1,26.9v58.3C24.4,188.6,26.6,190.9,29.4,190.9z"/>
          <path d="M89.6,153c2.8,0,5-2.2,5-5V59.6l30.2-16.2v143.8c0,2.8,2.2,5,5,5c2.8,0,5-2.2,5-5V26.7L84.6,53.6V148C84.6,150.7,86.8,153,89.6,153z"/>
          <path d="M149.8,185.7c2.8,0,5-2.2,5-5V85.4L185,69.2v86.3c0,2.8,2.2,5,5,5c2.8,0,5-2.2,5-5v-103l-50.1,26.9v101.3C144.8,183.5,147.1,185.7,149.8,185.7z"/>
        </svg>
        Buat Laporan Baru
      </button>
    </div>

    <!-- Box tabel -->
<div class="table-box">
  <div class="table-responsive">
    <table class="table table-bordered align-middle">
      <thead class="table-secondary">
        <tr>
          <th>Nama Siswa</th>
          <th>Tanggal Sesi</th>
          <th>Topik</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody id="laporanTableBody">
        <!-- Baris pertama berisi -->
        <tr>
          <td>Selvi Fitra</td>
          <td>2 Oktober 2025 pukul 11.34</td>
          <td>Orientasi karir dan pilihan studi</td>
          <td>
            <a class="action-link" data-bs-toggle="modal" data-bs-target="#detailModal">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon-eye" viewBox="0 0 24 24">
                <path d="M12 5c-7.633 0-11 7-11 7s3.367 7 11 7 11-7 11-7-3.367-7-11-7zm0 12c-2.755 0-5-2.245-5-5s2.245-5 5-5 5 2.245 5 5-2.245 5-5 5zm0-8c-1.662 0-3 1.338-3 3s1.338 3 3 3 3-1.338 3-3-1.338-3-3-3z"/>
              </svg>
              Detail
            </a>
          </td>
        </tr>

        <!-- 19 baris kosong -->
        <tr><td colspan="4" style="height: 40px;"></td></tr>
        <tr><td colspan="4" style="height: 40px;"></td></tr>
        <tr><td colspan="4" style="height: 40px;"></td></tr>
        <tr><td colspan="4" style="height: 40px;"></td></tr>
        <tr><td colspan="4" style="height: 40px;"></td></tr>
        <tr><td colspan="4" style="height: 40px;"></td></tr>
      </tbody>

  <!-- Modal Buat Laporan -->
  <div class="modal fade" id="laporanModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content p-3">
        <div class="modal-header">
          <h5 class="modal-title text-center w-100 fw-semibold">Buat Laporan Konseling</h5>
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
            <div class="modal-footer justify-content-center">
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
      <div class="modal-content p-3">
        <div class="modal-header">
          <h5 class="modal-title text-center w-100 fw-semibold">Laporan Konseling</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body text-center">
          <h6 class="fw-semibold text-primary" id="detailNama">Selvi Fitra</h6>
          <p class="mb-1 text-muted" id="detailTanggal">Tanggal Sesi: 2 Oktober 2025 pukul 11.34</p>
          <p class="mb-3 text-muted" id="detailTopik">Topik: Orientasi karir dan pilihan studi</p>

          <h6 class="text-start fw-semibold">Hasil Pertemuan & Solusi:</h6>
          <div class="info-box text-start" id="detailHasil">Saya menunjukan minat yang kuat di bidang desain grafis</div>

          <h6 class="text-start fw-semibold">Catatan Tambahan:</h6>
          <div class="note-box text-start" id="detailCatatan">Tindak lanjut: Jadwal pertemuan tripartit</div>

          <p class="text-muted small mt-2 text-end">Dicatat oleh : Guru BK</p>

          <div class="modal-footer justify-content-center mt-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.getElementById("openLaporanBtn").addEventListener("click", function() {
      const modal = new bootstrap.Modal(document.getElementById("laporanModal"));
      modal.show();
    });

    // Simpan laporan baru ke tabel
    document.getElementById("formLaporanBaru").addEventListener("submit", function(e) {
      e.preventDefault();

      const nama = document.getElementById("namaSiswa").value;
      const tanggal = new Date(document.getElementById("tanggalSesi").value).toLocaleString("id-ID", {
        day: "numeric", month: "long", year: "numeric", hour: "2-digit", minute: "2-digit"
      });
      const topik = document.getElementById("topik").value;
      const hasil = document.getElementById("hasilPertemuan").value;
      const catatan = document.getElementById("catatanTambahan").value;

      const tbody = document.getElementById("laporanTableBody");
      const newRow = document.createElement("tr");
      newRow.innerHTML = `
        <td>${nama}</td>
        <td>${tanggal}</td>
        <td>${topik}</td>
        <td>
          <a class="action-link" onclick="showDetail('${nama}','${tanggal}','${topik}','${hasil}','${catatan}')">
            <svg xmlns='http://www.w3.org/2000/svg' class='icon-eye' viewBox='0 0 24 24'>
              <path d='M12 5c-7.633 0-11 7-11 7s3.367 7 11 7 11-7 11-7-3.367-7-11-7zm0 12c-2.755 0-5-2.245-5-5s2.245-5 5-5 5 2.245 5 5-2.245 5-5 5zm0-8c-1.662 0-3 1.338-3 3s1.338 3 3 3 3-1.338 3-3-1.338-3-3-3z'/>
            </svg>
            Detail
          </a>
        </td>
      `;
      tbody.appendChild(newRow);

      document.getElementById("formLaporanBaru").reset();
      const modal = bootstrap.Modal.getInstance(document.getElementById("laporanModal"));
      modal.hide();
    });

    function showDetail(nama, tanggal, topik, hasil, catatan) {
      document.getElementById("detailNama").innerText = nama;
      document.getElementById("detailTanggal").innerText = "Tanggal Sesi: " + tanggal;
      document.getElementById("detailTopik").innerText = "📋 Topik: " + topik;
      document.getElementById("detailHasil").innerText = hasil || "-";
      document.getElementById("detailCatatan").innerText = catatan || "-";

      const modal = new bootstrap.Modal(document.getElementById("detailModal"));
      modal.show();
    }
  </script>
</body>
</html>
