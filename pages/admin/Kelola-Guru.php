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
    <!-- Statistik -->
<div class="row g-4 mb-4">
  <!-- Kartu Jumlah Guru -->
  <div class="col-lg-4 col-md-6">
    <div class="card shadow-sm">
      <div class="card-body d-flex align-items-center">
        <!-- Kotak ikon -->
        <div class="bg-primary text-white rounded  me-3 d-flex align-items-center justify-content-center" style="width:70px; height:70px;">
          <!-- SVG Ikon Guru (warna putih) -->
          <svg id="Icons_Teacher" viewBox="0 0 96 96" xmlns="http://www.w3.org/2000/svg" width="45" height="45">
            <path fill="white" d="M87.8 19L23.8 19C21.6 19 19.8 20.8 19.8 23L19.8 37.5C20.9 37.2 22.2 37 23.4 37C24.2 37 25 37.1 25.8 37.2L25.8 25L85.8 25L85.8 63L51.9 63L46.2 69L87.8 69C90 69 91.8 67.2 91.8 65L91.8 23C91.8 20.8 90 19 87.8 19Z"/>
            <path fill="white" d="M23.5 58C28.2 58 32 54.2 32 49.5C32 44.8 28.2 41 23.5 41C18.8 41 15 44.8 15 49.5C14.9 54.2 18.8 58 23.5 58Z"/>
            <path fill="white" d="M56.2 48.1C54.9 46.1 52.3 45.6 50.3 46.8C49.9 47 49.7 47.4 49.5 47.6L34.9 62.8C33.5 62.1 32 61.5 30.5 61C28.2 60.6 25.8 60.1 23.5 60.1C21.2 60.1 18.8 60.5 16.5 61.2C13.1 62.1 10.1 63.8 7.6 65.9C7 66.5 6.5 67.4 6.3 68.2L4.2 77L34.1 77L34.1 76.9L42.6 67L55.7 53.2C56.9 52 57.3 49.7 56.2 48.1Z"/>
          </svg>
        </div>

        <!-- Teks Statistik -->
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
      <div class="bg-success text-white rounded p-3 me-3 d-flex align-items-center justify-content-center" style="width:70px; height:70px;">
        <!-- SVG Ikon Akun Aktif (Centang Baru) -->
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="45" height="45">
          <style>.st0{fill:#41AD49;}</style>
          <g>
            <polygon class="st0" points="434.8,49 174.2,309.7 76.8,212.3 0,289.2 174.1,463.3 196.6,440.9 511.7,125.8 434.8,49"/>
          </g>
        </svg>
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
      <div class="bg-danger text-white rounded p-3 me-3 d-flex align-items-center justify-content-center" style="width:70px; height:70px;">
        <!-- SVG Ikon Akun Nonaktif (Baru) -->
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 114 114" width="45" height="45">
          <defs>
            <linearGradient id="def0" x1="0.5" x2="0.5" y1="0" y2="1">
              <stop offset="0" stop-color="#F27E5E"/>
              <stop offset="0.5" stop-color="#EB1C24"/>
              <stop offset="1" stop-color="#CE2229"/>
            </linearGradient>
          </defs>
          <g>
            <path d="M0,87.5347L30.964,56.564 0,25.6013 25.7973,0 56.7627,30.7707 87.7267,0 113.527,25.6013 82.5627,56.5693 113.527,87.5347 87.7267,113.329 56.7667,82.364 25.7973,113.333 0,87.5347z" fill="#990000"/>
            <path d="M111.641,87.5341L80.6768,56.5701 111.641,25.6021 87.7261,1.69014 56.7635,32.6555 25.7968,1.69014 1.8848,25.6021 32.8501,56.5648 1.8848,87.5341 25.7968,111.447 56.7675,80.4781 87.7261,111.443 111.641,87.5341z" fill="url(#def0)"/>
            <path d="M53.5507,42.1597C69.9773,36.9184,86.2987,35.0784,101.036,36.2077L111.64,25.6024 87.7267,1.6904 56.7627,32.6557 25.7973,1.6904 1.88534,25.6024 29.0347,52.7491C36.5187,48.5651,44.7387,44.9717,53.5507,42.1597z" fill="#FFFFFF" style="fill-opacity:0.1"/>
          </g>
        </svg>
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
