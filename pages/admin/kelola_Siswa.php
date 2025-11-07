<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Data Siswa - BK Digital</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-image: url('background.jpeg');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      padding: 10px;
    }

    
    h4 {
      font-weight: 700;
      color: #004AAD;
    }

    .card-info {
      border-radius: 12px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.15);
      padding: 20px;
      background: white;
      text-align: center;
      transition: transform 0.2s ease;
    }
    .card-info:hover { transform: scale(1.03); }
    .icon-card { width: 50px; height: 50px; object-fit: contain; }

    .table-container {
      background: white;
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      padding: 20px;
    }

    .btn-import {
      background-color: #38A169;
      color: white;
      border: none;
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 6px;
      padding: 6px 12px;
    }
    .btn-import:hover { background-color: #43a047; }

    .btn-tambah {
      background-color: #0050BC;
      color: white;
      border: none;
      font-weight: 500;
    }
    .btn-tambah:hover { background-color: #0069d9; }

    .icon-btn { width: 18px; height: 18px; object-fit: contain; }

    .search-container { display: flex; align-items: center; gap: 8px; }
    .search-box {
      width: 250px; background: white; border-radius: 50px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.15);
      border: 1px solid #ccc; padding: 6px 16px; font-size: 14px;
      outline: none; transition: 0.2s;
    }
    .search-box:focus {
      border-color: #38A169;
      box-shadow: 0 0 4px rgba(56,161,105,0.6);
    }

    .btn-cari {
      background-color: #38A169; border: none; border-radius: 50px;
      width: 42px; height: 32px; display: flex; align-items: center;
      justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2);
      transition: 0.2s;
    }
    .btn-cari:hover { background-color: #2F855A; }
    .btn-cari img { width: 18px; height: 18px; }

    table { font-size: 0.9rem; }

    .modal-content {
      border-radius: 10px;
      font-family: 'Poppins', sans-serif;
    }

    .modal-header-custom {
      background: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 2px solid #eee;
      padding: 16px 20px;
      border-top-left-radius: 10px;
      border-top-right-radius: 10px;
    }

    .modal-title-custom {
      font-weight: 600;
      color: #004AAD;
      margin: 0;
    }

    .btn-close-custom {
      background: none;
      border: none;
      font-size: 20px;
      color: #333;
      transition: 0.2s;
    }
    .btn-close-custom:hover { color: #d11a2a; }

    .btn-primary {
      background-color: #004AAD;
      border: none;
    }
    .btn-primary:hover { background-color: #003580; }
  </style>
</head>

<body>
  <div class="container">
    <!-- Kartu Statistik -->
    <div class="row g-4 mb-4">
      <div class="col-md-4 col-sm-6 col-12">
        <div class="card-info">
          <img src="gambar/jumlahsiswa2.svg" class="icon-card">
          <h6 class="text-muted mb-1">Jumlah Siswa Aktif</h6>
          <h4>1200</h4>
        </div>
      </div>
      <div class="col-md-4 col-sm-6 col-12">
        <div class="card-info">
          <img src="gambar/cowo.svg" class="icon-card">
          <h6 class="text-muted mb-1">Jumlah Laki-laki</h6>
          <h4>500</h4>
        </div>
      </div>
      <div class="col-md-4 col-sm-6 col-12">
        <div class="card-info">
          <img src="gambar/cewe.svg" class="icon-card">
          <h6 class="text-muted mb-1">Jumlah Perempuan</h6>
          <h4>400</h4>
        </div>
      </div>
    </div>
 
    <!-- Kelola Data -->
    <div class="table-container">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-2">Kelola Data Siswa</h6>
        <div class="d-flex flex-wrap align-items-center gap-2">
          <div class="search-container">
            <input type="text" id="searchBox" class="search-box" placeholder="Cari ID/Nama/Kelas siswa">
            <button class="btn-cari" id="btnCari"><img src="gambar/iconcari.svg" alt="Cari"></button>
          </div>

          <button class="btn btn-import btn-sm">
            <img src="gambar/icondata.svg" class="icon-btn"> Import Data
          </button>
          <button class="btn btn-tambah btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-lg"></i> Tambah Data
          </button>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered align-middle" id="tabelSiswa">
          <thead class="table-light text-center">
            <tr>
              <th>ID</th>
              <th>Nama</th>
              <th>Kelas</th>
              <th>Tahun Masuk</th>
              <th>Jenis Kelamin</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody class="text-center">
            <tr>
              <td>0001</td>
              <td>Rafi</td>
              <td>XI.12</td>
              <td>2024</td>
              <td>Laki-Laki</td>
              <td>
                <button class="btn btn-link p-0 text-primary edit-btn"><i class="bi bi-pencil-square"></i></button>
                <button class="btn btn-link p-0 text-danger delete-btn"><i class="bi bi-trash"></i></button>
              </td>
            </tr>
            <tr>
              <td>0002</td>
              <td>Zahra</td>
              <td>XI.11</td>
              <td>2024</td>
              <td>Perempuan</td>
              <td>
                <button class="btn btn-link p-0 text-primary edit-btn"><i class="bi bi-pencil-square"></i></button>
                <button class="btn btn-link p-0 text-danger delete-btn"><i class="bi bi-trash"></i></button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- MODAL TAMBAH / EDIT DATA -->
  <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">

        <div class="modal-header-custom">
          <h5 class="modal-title-custom" id="modalTitle">Tambah Data Siswa</h5>
          <button type="button" class="btn-close-custom" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="modal-body">
          <form id="formTambah">
            <input type="hidden" id="editIndex">
            <div class="mb-3">
              <label class="form-label">ID</label>
              <input type="text" class="form-control" id="idSiswa" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Nama</label>
              <input type="text" class="form-control" id="namaSiswa" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Kelas</label>
              <input type="text" class="form-control" id="kelasSiswa" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Tahun Masuk</label>
              <input type="number" class="form-control" id="tahunMasuk" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Jenis Kelamin</label><br>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="gender" value="Laki-Laki" required>
                <label class="form-check-label">Laki-Laki</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="gender" value="Perempuan">
                <label class="form-check-label">Perempuan</label>
              </div>
            </div>
            <div class="text-end">
              <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Data</button>
            </div>
          </form>
        </div>

      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    const formTambah = document.getElementById('formTambah');
    const tabel = document.getElementById('tabelSiswa').querySelector('tbody');
    const modalTambah = new bootstrap.Modal(document.getElementById('modalTambah'));
    const modalTitle = document.getElementById('modalTitle');
    let editRow = null;

    // === SIMPAN / EDIT DATA ===
    formTambah.addEventListener('submit', (e) => {
      e.preventDefault();

      const id = document.getElementById('idSiswa').value;
      const nama = document.getElementById('namaSiswa').value;
      const kelas = document.getElementById('kelasSiswa').value;
      const tahun = document.getElementById('tahunMasuk').value;
      const gender = document.querySelector('input[name="gender"]:checked').value;

      if (editRow) {
        editRow.children[0].textContent = id;
        editRow.children[1].textContent = nama;
        editRow.children[2].textContent = kelas;
        editRow.children[3].textContent = tahun;
        editRow.children[4].textContent = gender;
        editRow = null;
        modalTitle.textContent = "Tambah Data Siswa";
      } else {
        const row = tabel.insertRow();
        row.innerHTML = `
          <td>${id}</td>
          <td>${nama}</td>
          <td>${kelas}</td>
          <td>${tahun}</td>
          <td>${gender}</td>
          <td>
            <button class="btn btn-link p-0 text-primary edit-btn"><i class="bi bi-pencil-square"></i></button>
            <button class="btn btn-link p-0 text-danger delete-btn"><i class="bi bi-trash"></i></button>
          </td>
        `;
      }

      modalTambah.hide();
      formTambah.reset();
    });

    // === EDIT & HAPUS DATA ===
    tabel.addEventListener('click', (e) => {
      if (e.target.closest('.delete-btn')) {
        const row = e.target.closest('tr');
        if (confirm('Yakin ingin menghapus data ini?')) row.remove();
      }

      if (e.target.closest('.edit-btn')) {
        const row = e.target.closest('tr');
        editRow = row;
        modalTitle.textContent = "Edit Data Siswa";

        document.getElementById('idSiswa').value = row.children[0].textContent;
        document.getElementById('namaSiswa').value = row.children[1].textContent;
        document.getElementById('kelasSiswa').value = row.children[2].textContent;
        document.getElementById('tahunMasuk').value = row.children[3].textContent;
        document.querySelectorAll('input[name="gender"]').forEach(r => {
          r.checked = r.value === row.children[4].textContent;
        });

        modalTambah.show();
      }
    });

    // === FITUR CARI ===
    const btnCari = document.getElementById('btnCari');
    const searchBox = document.getElementById('searchBox');

    btnCari.addEventListener('click', () => {
      const keyword = searchBox.value.trim().toLowerCase();
      const rows = tabel.querySelectorAll('tr');
      rows.forEach(row => {
        const id = row.children[0].textContent.toLowerCase();
        const nama = row.children[1].textContent.toLowerCase();
        const kelas = row.children[2].textContent.toLowerCase();
        if (id.includes(keyword) || nama.includes(keyword) || kelas.includes(keyword) || keyword === '') {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    });

    // Enter key juga menjalankan pencarian
    searchBox.addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        btnCari.click();
      }
    });
  </script>
</body>
</html>
