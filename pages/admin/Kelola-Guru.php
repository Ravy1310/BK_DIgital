<?php
// Simulasi data dari database
$jumlahGuru = 5;
$akunAktif = 3;
$akunNonaktif = 2;
$dataGuru = [
    ['id' => 1, 'nama' => 'Ahmad Fauzi', 'telepon' => '081234567890', 'alamat' => 'Jl. Merdeka No. 123', 'status' => 'Aktif'],
    ['id' => 2, 'nama' => 'Siti Rahayu', 'telepon' => '081234567891', 'alamat' => 'Jl. Sudirman No. 45', 'status' => 'Aktif'],
    ['id' => 3, 'nama' => 'Budi Santoso', 'telepon' => '081234567892', 'alamat' => 'Jl. Gatot Subroto No. 67', 'status' => 'Aktif'],
    ['id' => 4, 'nama' => 'Dewi Lestari', 'telepon' => '081234567893', 'alamat' => 'Jl. Thamrin No. 89', 'status' => 'Nonaktif'],
    ['id' => 5, 'nama' => 'Rudi Hermawan', 'telepon' => '081234567894', 'alamat' => 'Jl. Diponegoro No. 101', 'status' => 'Nonaktif']
];

// Filter data berdasarkan pencarian
$keyword = isset($_GET['cari']) ? $_GET['cari'] : '';
if (!empty($keyword)) {
    $dataGuru = array_filter($dataGuru, function($guru) use ($keyword) {
        return stripos($guru['nama'], $keyword) !== false || 
               stripos($guru['telepon'], $keyword) !== false;
    });
}
?>


<head>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: url('../../assets/image/background.jpg');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      margin-top: 50px !important;
    }

    h4 {
      font-weight: 700;
      color: #004AAD;
    }

    .stat-card {
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      padding: 20px;
      background: white;
      transition: all 0.2s ease;
      border: 1px solid transparent;
    }

    .stat-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      border-color: rgba(0, 74, 173, 0.1);
    }

    .stat-icon {
      width: 60px;
      height: 60px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 15px;
      transition: transform 0.2s ease;
    }

    .stat-card:hover .stat-icon {
      transform: scale(1.05);
    }

    .table-container {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      padding: 24px;
      margin-top: 20px;
    }

   

    .btn-tambah {
      background-color: #0050BC;
      color: white;
      border: none;
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 6px;
      padding: 8px 16px;
      border-radius: 8px;
      font-size: 14px;
      transition: all 0.2s ease;
    }

    .btn-tambah:hover {
      background-color: #003580;
      color: white;
      transform: translateY(-1px);
    }

    .search-container { 
      display: flex; 
      align-items: center; 
      gap: 8px; 
    }

    .search-box {
      width: 280px; 
      background: white; 
      border-radius: 25px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      border: 1px solid #e2e8f0; 
      padding: 10px 20px; 
      font-size: 14px;
      outline: none; 
      transition: all 0.2s ease;
    }

    .search-box:focus {
      border-color: #38A169;
      box-shadow: 0 0 0 2px rgba(56, 161, 105, 0.1);
    }

    .btn-cari {
      background-color: #38A169; 
      border: none; 
      border-radius: 50%;
      width: 44px; 
      height: 44px; 
      display: flex; 
      align-items: center;
      justify-content: center; 
      box-shadow: 0 2px 8px rgba(0,0,0,0.15);
      transition: all 0.2s ease;
      color: white;
    }

    .btn-cari:hover { 
      background-color: #2F855A; 
      transform: scale(1.05);
    }

    table { 
      font-size: 0.9rem; 
      margin-bottom: 0;
    }

    .table th {
      background: #f8f9fa;
      font-weight: 600;
      color: #2d3748;
      border-bottom: 2px solid #e2e8f0;
      padding: 12px 8px;
    }

    .table tbody tr {
      transition: background-color 0.2s ease;
      border-bottom: 1px solid #f1f5f9;
    }

    .table tbody tr:hover {
      background-color: #f8fafc;
    }

    .modal-content {
      border-radius: 12px;
      font-family: 'Poppins', sans-serif;
      border: none;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    .modal-header-custom {
      background: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 2px solid #eee;
      padding: 20px 24px;
      border-top-left-radius: 12px;
      border-top-right-radius: 12px;
    }

    .modal-title-custom {
      font-weight: 600;
      color: #004AAD;
      margin: 0;
      font-size: 1.3rem;
    }

    .btn-close-custom {
      background: none;
      border: none;
      font-size: 18px;
      color: #666;
      transition: color 0.2s ease;
      padding: 0;
      width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 6px;
    }

    .btn-close-custom:hover { 
      color: #d11a2a;
      background: #f8f9fa;
    }

    .btn-primary {
      background-color: #004AAD;
      border: none;
      padding: 10px 24px;
      border-radius: 8px;
      font-weight: 500;
      transition: all 0.2s ease;
    }

    .btn-primary:hover {
      background-color: #003580;
      transform: translateY(-1px);
    }

    .status-badge {
      padding: 6px 16px;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 500;
    }

    .status-aktif {
      background-color: #d1fae5;
      color: #065f46;
    }

    .status-nonaktif {
      background-color: #fee2e2;
      color: #991b1b;
    }

    .action-buttons {
      display: flex;
      gap: 6px;
      justify-content: center;
    }

    .btn-action {
      padding: 6px 10px;
      border: none;
      background: none;
      border-radius: 6px;
      transition: all 0.2s ease;
    }

    .btn-edit {
      color: #004AAD;
    }
    .btn-edit:hover {
      background-color: #e3f2fd;
    }

    .btn-delete {
      color: #dc3545;
    }
    .btn-delete:hover {
      background-color: #fde8e8;
    }

    .btn-status {
      color: #38A169;
    }
    .btn-status:hover {
      background-color: #e8f5e8;
    }
  </style>
</head>

<body>
  <div class="container">
    <!-- Statistik -->
    <div class="row g-4 mb-4">
      <!-- Jumlah Guru -->
      <div class="col-lg-4 col-md-6">
        <div class="card stat-card">
          <div class="d-flex align-items-center">
            <div class="stat-icon bg-primary text-white">
              <i class="bi bi-person-video3 fs-2"></i>
            </div>
            <div>
              <h6 class="mb-0 text-muted">Jumlah Guru</h6>
              <h4 class="fw-bold mt-1" id="jumlahGuru"><?= $jumlahGuru ?></h4>
            </div>
          </div>
        </div>
      </div>

      <!-- Akun Aktif -->
      <div class="col-lg-4 col-md-6">
        <div class="card stat-card">
          <div class="d-flex align-items-center">
            <div class="stat-icon bg-success text-white">
              <i class="bi bi-check-circle-fill fs-2"></i>
            </div>
            <div>
              <h6 class="mb-0 text-muted">Akun Aktif</h6>
              <h4 class="fw-bold mt-1" id="akunAktif"><?= $akunAktif ?></h4>
            </div>
          </div>
        </div>
      </div>

      <!-- Akun Nonaktif -->
      <div class="col-lg-4 col-md-6">
        <div class="card stat-card">
          <div class="d-flex align-items-center">
            <div class="stat-icon bg-danger text-white">
              <i class="bi bi-x-circle-fill fs-2"></i>
            </div>
            <div>
              <h6 class="mb-0 text-muted">Akun Nonaktif</h6>
              <h4 class="fw-bold mt-1" id="akunNonaktif"><?= $akunNonaktif ?></h4>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Kelola Data -->
    <div class="table-container">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h6 class="fw-bold mb-2" style=" font-size: 1.1rem;">Kelola Data Guru</h6>
        <div class="d-flex flex-wrap align-items-center gap-2">
          <div class="search-container">
            <input type="text" id="searchBox" class="search-box" placeholder="Cari Nama/Telepon Guru" value="<?= htmlspecialchars($keyword) ?>">
            <button class="btn-cari" id="btnCari"><i class="bi bi-search"></i></button>
          </div>

        
          <button class="btn btn-tambah btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-lg"></i> Tambah Data
          </button>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered align-middle" id="tabelGuru">
          <thead class="table-light text-center">
            <tr>
              <th>No</th>
              <th>Nama</th>
              <th>No. Telepon</th>
              <th>Alamat</th>
              <th>Status Akun</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody class="text-center">
            <?php if (empty($dataGuru)): ?>
              <tr>
                <td colspan="6" class="text-center py-5">
                  <div class="text-muted">
                    <i class="bi bi-person-x fs-1 d-block mb-2"></i>
                    <span>Belum ada data guru</span>
                  </div>
                </td>
              </tr>
            <?php else: ?>
              <?php $no = 1; ?>
              <?php foreach ($dataGuru as $guru): ?>
                <tr>
                  <td class="fw-medium"><?= $no++ ?></td>
                  <td class="fw-medium"><?= htmlspecialchars($guru['nama']) ?></td>
                  <td><?= htmlspecialchars($guru['telepon']) ?></td>
                  <td><?= htmlspecialchars($guru['alamat']) ?></td>
                  <td>
                    <span class="status-badge <?= $guru['status'] == 'Aktif' ? 'status-aktif' : 'status-nonaktif' ?>">
                      <?= $guru['status'] ?>
                    </span>
                  </td>
                  <td>
                    <div class="action-buttons">
                      <button class="btn-action btn-edit edit-btn" data-id="<?= $guru['id'] ?>">
                        <i class="bi bi-pencil-square"></i>
                      </button>
                      <button class="btn-action btn-delete delete-btn" data-id="<?= $guru['id'] ?>">
                        <i class="bi bi-trash"></i>
                      </button>
                      <button class="btn-action btn-status status-btn" data-id="<?= $guru['id'] ?>" data-status="<?= $guru['status'] ?>">
                        <i class="bi bi-<?= $guru['status'] == 'Aktif' ? 'x' : 'check' ?>-circle"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
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
          <h5 class="modal-title-custom" id="modalTitle">Tambah Data Guru</h5>
          <button type="button" class="btn-close-custom" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="modal-body">
          <form id="formTambah" method="POST" action="simpan_guru.php">
            <input type="hidden" id="editId" name="editId">
            <div class="mb-3">
              <label class="form-label">Nama Lengkap</label>
              <input type="text" class="form-control" id="namaGuru" name="nama" required>
            </div>
            <div class="mb-3">
              <label class="form-label">No. Telepon</label>
              <input type="text" class="form-control" id="teleponGuru" name="telepon" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Alamat</label>
              <textarea class="form-control" id="alamatGuru" name="alamat" rows="3" required></textarea>
            </div>
            <div class="text-end">
              <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
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
    const modalTambah = new bootstrap.Modal(document.getElementById('modalTambah'));
    const modalTitle = document.getElementById('modalTitle');

    // Data guru (dalam implementasi nyata, ini akan dari database)
    const guruData = {
      1: { nama: 'Ahmad Fauzi', telepon: '081234567890', alamat: 'Jl. Merdeka No. 123' },
      2: { nama: 'Siti Rahayu', telepon: '081234567891', alamat: 'Jl. Sudirman No. 45' },
      3: { nama: 'Budi Santoso', telepon: '081234567892', alamat: 'Jl. Gatot Subroto No. 67' },
      4: { nama: 'Dewi Lestari', telepon: '081234567893', alamat: 'Jl. Thamrin No. 89' },
      5: { nama: 'Rudi Hermawan', telepon: '081234567894', alamat: 'Jl. Diponegoro No. 101' }
    };

    // === EDIT DATA ===
    document.querySelectorAll('.edit-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        if (guruData[id]) {
          document.getElementById('editId').value = id;
          document.getElementById('namaGuru').value = guruData[id].nama;
          document.getElementById('teleponGuru').value = guruData[id].telepon;
          document.getElementById('alamatGuru').value = guruData[id].alamat;
          modalTitle.textContent = "Edit Data Guru";
          modalTambah.show();
        }
      });
    });

    // === HAPUS DATA ===
    document.querySelectorAll('.delete-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        if (confirm('Yakin ingin menghapus data ini?')) {
          // Dalam implementasi nyata, ini akan mengirim permintaan ke server
          alert('Data guru dengan ID ' + id + ' akan dihapus');
        }
      });
    });

    // === UBAH STATUS ===
    document.querySelectorAll('.status-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const statusSekarang = this.getAttribute('data-status');
        const statusBaru = statusSekarang === 'Aktif' ? 'Nonaktif' : 'Aktif';
        
        if (confirm(`Ubah status akun menjadi ${statusBaru}?`)) {
          // Dalam implementasi nyata, ini akan mengirim permintaan ke server
          alert(`Status guru dengan ID ${id} diubah menjadi ${statusBaru}`);
        }
      });
    });

    // Reset form saat modal ditutup
    document.getElementById('modalTambah').addEventListener('hidden.bs.modal', function() {
      formTambah.reset();
      document.getElementById('editId').value = '';
      modalTitle.textContent = "Tambah Data Guru";
    });

    // === FITUR CARI ===
    const btnCari = document.getElementById('btnCari');
    const searchBox = document.getElementById('searchBox');

    btnCari.addEventListener('click', () => {
      const keyword = searchBox.value.trim().toLowerCase();
      const rows = document.querySelectorAll('#tabelGuru tbody tr');
      
      rows.forEach(row => {
        if (row.cells.length > 1) {
          const nama = row.cells[1].textContent.toLowerCase();
          const telepon = row.cells[2].textContent.toLowerCase();
          
          if (nama.includes(keyword) || telepon.includes(keyword) || keyword === '') {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
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