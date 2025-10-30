<?php
$jumlahGuru = 0;
$akunAktif = 0;
$akunNonaktif = 0;
$dataGuru = [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Data Guru</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <style>
    body {
      background-image: url('background.jpeg');
      background-repeat: no-repeat;
      background-position: center center;
      background-attachment: fixed;
      background-size: cover;
      display: flex;
      flex-direction: column;
    }
    .content-wrapper {
      flex: 1;
      display: flex;
      flex-direction: column;
      padding: 2rem;
    }
    .card {
      background-color: rgba(255, 255, 255, 0.93);
      backdrop-filter: blur(6px);
      border: none;
    }
    .card-header {
      background-color: rgba(255, 255, 255, 0.9);
    }
    .table {
      width: 100%;
    }

    /* Warna khusus untuk card Jumlah Akun Aktif */
    .bg-success {
      background-color: #DBF4D6 !important;
      color: #000 !important;
    }

    /* Warna khusus untuk card Jumlah Akun Nonaktif */
    .bg-danger {
      background-color: #F4D6D6 !important;
      color: #000 !important;
    }

    /* Tombol Cari (oval hijau seperti gambar) */
    .btn-cari {
      background-color: #4CAF50;
      border: none;
      border-radius: 50px;
      padding: 5px 14px;
      box-shadow: 0 3px 5px rgba(0, 0, 0, 0.2);
      transition: 0.3s;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .btn-cari:hover {
      background-color: #43a047;
    }

    .btn-cari i {
      color: white;
      font-size: 1rem;
    }
  </style>
</head>
<body>

  <div class="content-wrapper container-fluid">

    <!-- Statistik -->
    <div class="row g-4 mb-4">
      <div class="col-lg-4 col-md-6">
        <div class="card shadow-sm">
          <div class="card-body d-flex align-items-center">
            <div class="bg-primary text-white rounded p-3 me-3">
              <img src="gambar/jumlahguru2.svg" alt="Cari">
            </div>
            <div>
              <h6 class="mb-0">Jumlah Guru</h6>
              <small class="text-muted">Aktif</small>
              <h4 class="fw-bold mt-2" id="jumlahGuru"><?= $jumlahGuru ?></h4>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="card shadow-sm">
          <div class="card-body d-flex align-items-center">
            <div class="bg-success text-white rounded p-3 me-3">
              <img src="gambar/aktif.svg" alt="Cari">
            </div>
            <div>
              <h6 class="mb-0">Jumlah Akun</h6>
              <small class="text-muted">Aktif</small>
              <h4 class="fw-bold mt-2" id="akunAktif"><?= $akunAktif ?></h4>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="card shadow-sm">
          <div class="card-body d-flex align-items-center">
            <div class="bg-danger text-white rounded p-3 me-3">
              <img src="gambar/nonaktif.svg" alt="Cari">
            </div>
            <div>
              <h6 class="mb-0">Jumlah Akun</h6>
              <small class="text-muted">Nonaktif</small>
              <h4 class="fw-bold mt-2" id="akunNonaktif"><?= $akunNonaktif ?></h4>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Kelola Data Guru -->
    <div class="card shadow-sm flex-grow-1">
      <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
        <h6 class="fw-bold mb-2 mb-md-0">Kelola Data Guru</h6>

        <div class="d-flex align-items-center mb-2 mb-md-0">
          <form method="GET" class="d-flex">
            <input type="text" name="cari" class="form-control form-control-sm me-2" placeholder="Cari Nama Guru" style="width:200px;">
            <button class="btn-cari btn-sm" type="submit"><i class="bi bi-search"></i></button>
          </form>
        </div>

        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambahGuruModal">
          <i class="bi bi-plus-lg"></i> Tambah Data
        </button>
      </div>

      <div class="table-responsive">
        <table class="table table-striped align-middle mb-0" id="tabelGuru">
          <thead class="table-light">
            <tr>
              <th>No.</th>
              <th>Nama</th>
              <th>No. Telepon</th>
              <th>Alamat</th>
              <th>Status Akun</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="bodyGuru">
            <tr>
              <td colspan="6" class="text-center text-muted py-4">Belum ada data</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal Tambah/Edit Data Guru -->
  <div class="modal fade" id="tambahGuruModal" tabindex="-1" aria-labelledby="tambahGuruModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold" id="tambahGuruModalLabel">Tambah Data Guru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
          <form id="formGuru">
            <input type="hidden" id="editIndex" value="">
            <div class="mb-3">
              <label class="form-label">Nama</label>
              <input type="text" id="nama" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">No. Telepon</label>
              <input type="text" id="telepon" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Alamat</label>
              <textarea id="alamat" class="form-control" rows="2" required></textarea>
            </div>
            <div class="d-flex justify-content-end">
              <button type="submit" class="btn btn-primary" id="btnSimpan">
                <i class="bi bi-save"></i> Simpan Data
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    let dataGuru = [];
    const bodyGuru = document.getElementById('bodyGuru');
    const formGuru = document.getElementById('formGuru');
    const jumlahGuruEl = document.getElementById('jumlahGuru');
    const akunAktifEl = document.getElementById('akunAktif');
    const akunNonaktifEl = document.getElementById('akunNonaktif');
    const modalEl = document.getElementById('tambahGuruModal');
    const modal = new bootstrap.Modal(modalEl);

    // === Simpan data pakai tombol Enter ===
    formGuru.addEventListener('keydown', function(e) {
      if (e.key === "Enter") {
        e.preventDefault();
        document.getElementById('btnSimpan').click();
      }
    });

    formGuru.addEventListener('submit', function(e) {
      e.preventDefault();

      const nama = document.getElementById('nama').value.trim();
      const telepon = document.getElementById('telepon').value.trim();
      const alamat = document.getElementById('alamat').value.trim();
      const editIndex = document.getElementById('editIndex').value;

      if (!nama || !telepon || !alamat) {
        alert('Semua field wajib diisi!');
        return;
      }

      if (editIndex === "") {
        dataGuru.push({ nama, telepon, alamat, status: 'Aktif' });
      } else {
        dataGuru[editIndex] = { nama, telepon, alamat, status: 'Aktif' };
      }

      renderTable();
      formGuru.reset();
      document.getElementById('editIndex').value = "";
      document.getElementById('tambahGuruModalLabel').textContent = "Tambah Data Guru";
      modal.hide();
    });

    function renderTable() {
      bodyGuru.innerHTML = "";
      if (dataGuru.length === 0) {
        bodyGuru.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">Belum ada data</td></tr>`;
        jumlahGuruEl.textContent = 0;
        akunAktifEl.textContent = 0;
        akunNonaktifEl.textContent = 0;
        return;
      }

      dataGuru.forEach((guru, index) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${index + 1}</td>
          <td>${guru.nama}</td>
          <td>${guru.telepon}</td>
          <td>${guru.alamat}</td>
          <td>${guru.status}</td>
          <td>
            <button class="btn btn-warning btn-sm" onclick="editGuru(${index})"><i class="bi bi-pencil"></i></button>
            <button class="btn btn-danger btn-sm" onclick="hapusGuru(${index})"><i class="bi bi-trash"></i></button>
          </td>
        `;
        bodyGuru.appendChild(tr);
      });

      jumlahGuruEl.textContent = dataGuru.length;
      akunAktifEl.textContent = dataGuru.length; // anggap semua aktif
      akunNonaktifEl.textContent = 0;
    }

    function hapusGuru(index) {
      if (confirm('Yakin ingin menghapus data ini?')) {
        dataGuru.splice(index, 1);
        renderTable();
      }
    }

    function editGuru(index) {
      const guru = dataGuru[index];
      document.getElementById('nama').value = guru.nama;
      document.getElementById('telepon').value = guru.telepon;
      document.getElementById('alamat').value = guru.alamat;
      document.getElementById('editIndex').value = index;
      document.getElementById('tambahGuruModalLabel').textContent = "Edit Data Guru";
      modal.show();
    }
  </script>
</body>
</html>
