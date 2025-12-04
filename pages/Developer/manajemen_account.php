<div class="container my-5 fade-container">
  <div class="mb-4">
    <h4 class="fw-bolder">Manajemen Akun</h4>
    <p class="text-muted fs-5">Buat akun admin</p>
  </div>

  <div class="card shadow-sm p-4 mb-5">
    <form id="accountForm">
      <h6 class="fw-bold mb-2">Nama Lengkap</h6>
      <div class="mb-3">
        <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama Lengkap" required>
      </div>
     <div class="mb-3">
        <input type="tel" class="form-control" id="no_telp" name="no_telp" placeholder="No. Telepon" required>
      </div>
      <div class="mb-3">
        <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
      </div>
      <div class="mb-3">
        <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
      </div>

      <h6 class="fw-bold mt-4 mb-2">Password</h6>
      <div class="mb-3">
        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
      </div>
      <div class="mb-3">
        <input type="password" class="form-control" id="konfirmasi" placeholder="Konfirmasi Password" required>
      </div>

      <p id="formMessage" class="mt-2"></p>

      <div class="text-end">
        <button type="submit" class="btn btn-success px-4">Buat akun</button>
      </div>
    </form>
  </div>

  <h5 class="fw-bold mb-3">Daftar akun admin</h5>
  <div class="input-group mb-3">
    <input type="text" id="cari" class="form-control" placeholder="Cari admin...">
    <button class="btn btn-light" type="button">
      <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="35" height="35" viewBox="0 0 128 128">
    <path d="M 52.349609 14.400391 C 42.624609 14.400391 32.9 18.1 25.5 25.5 C 10.7 40.3 10.7 64.399219 25.5 79.199219 C 32.9 86.599219 42.600391 90.300781 52.400391 90.300781 C 62.200391 90.300781 71.900781 86.599219 79.300781 79.199219 C 94.000781 64.399219 93.999219 40.3 79.199219 25.5 C 71.799219 18.1 62.074609 14.400391 52.349609 14.400391 z M 52.300781 20.300781 C 60.500781 20.300781 68.700391 23.399219 74.900391 29.699219 C 87.400391 42.199219 87.4 62.5 75 75 C 62.5 87.5 42.199219 87.5 29.699219 75 C 17.199219 62.5 17.199219 42.199219 29.699219 29.699219 C 35.899219 23.499219 44.100781 20.300781 52.300781 20.300781 z M 52.300781 26.300781 C 45.400781 26.300781 38.9 29 34 34 C 29.3 38.7 26.700391 44.800391 26.400391 51.400391 C 26.300391 53.100391 27.600781 54.4 29.300781 54.5 L 29.400391 54.5 C 31.000391 54.5 32.300391 53.199609 32.400391 51.599609 C 32.600391 46.499609 34.699219 41.799219 38.199219 38.199219 C 41.999219 34.399219 47.000781 32.300781 52.300781 32.300781 C 54.000781 32.300781 55.300781 31.000781 55.300781 29.300781 C 55.300781 27.600781 54.000781 26.300781 52.300781 26.300781 z M 35 64 A 3 3 0 0 0 32 67 A 3 3 0 0 0 35 70 A 3 3 0 0 0 38 67 A 3 3 0 0 0 35 64 z M 83.363281 80.5 C 82.600781 80.5 81.850781 80.800391 81.300781 81.400391 C 80.100781 82.600391 80.100781 84.499609 81.300781 85.599609 L 83.800781 88.099609 C 83.200781 89.299609 82.900391 90.6 82.900391 92 C 82.900391 94.4 83.8 96.700391 85.5 98.400391 L 98.300781 111 C 100.10078 112.8 102.39922 113.69922 104.69922 113.69922 C 106.99922 113.69922 109.29961 112.79961 111.09961 111.09961 C 114.59961 107.59961 114.59961 101.90039 111.09961 98.400391 L 98.300781 85.599609 C 96.600781 83.899609 94.300391 83 91.900391 83 C 90.500391 83 89.2 83.300391 88 83.900391 L 85.5 81.400391 C 84.9 80.800391 84.125781 80.5 83.363281 80.5 z M 91.900391 88.900391 C 92.700391 88.900391 93.5 89.200781 94 89.800781 L 106.69922 102.5 C 107.89922 103.7 107.89922 105.59922 106.69922 106.69922 C 105.49922 107.89922 103.6 107.89922 102.5 106.69922 L 89.800781 94.099609 C 89.200781 93.499609 88.900391 92.700391 88.900391 91.900391 C 88.900391 91.100391 89.200781 90.300781 89.800781 89.800781 C 90.400781 89.200781 91.100391 88.900391 91.900391 88.900391 z"></path>
</svg>
    </button>
  </div>



    <!-- Modal untuk Edit Admin -->
<div class="modal fade" id="editAdminModal" tabindex="-1" aria-labelledby="editAdminModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editAdminModalLabel">Edit Admin</h5>
        <button type="button" class="btn-close" id="ButtonBatal" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editAccountForm">
          <input type="hidden" id="edit_id_admin" name="id_admin">
          
          <div class="mb-3">
            <label for="edit_nama" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" id="edit_nama" name="nama" placeholder="Nama Lengkap" required>
          </div>
          
          <div class="mb-3">
            <label for="edit_no_telp" class="form-label">No. Telepon</label>
            <input type="tel" class="form-control" id="edit_no_telp" name="no_telp" placeholder="No. Telepon" required>
          </div>
          
          <div class="mb-3">
            <label for="edit_email" class="form-label">Email</label>
            <input type="email" class="form-control" id="edit_email" name="email" placeholder="Email" required>
          </div>
          
          <div class="mb-3">
            <label for="edit_username" class="form-label">Username</label>
            <input type="text" class="form-control" id="edit_username" name="username" placeholder="Username" required>
          </div>

          <h6 class="fw-bold mt-4 mb-2">Password</h6>
          <div class="mb-3">
            <input type="password" class="form-control" id="edit_password" name="password" placeholder="(Kosongkan jika tidak diubah)">
          </div>
          <div class="mb-3">
            <input type="password" class="form-control" id="edit_konfirmasi" placeholder="Konfirmasi Password">
          </div>

          <p id="editFormMessage" class="mt-2"></p>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="BatalUpdate">Batal</button>
        <button type="button" class="btn btn-warning" id="updateAdminBtn">Update Akun</button>
      </div>
    </div>
  </div>
</div>

<!-- Table responsive container -->
<div class="table-responsive">
  <table class="table table-bordered table-hover table-striped border">
    <thead class="custom-table-header text-center">
      <tr>
        <th>No</th>
        <th>Nama</th>
        <th>No. Telp</th> 
        <th>Email</th>
        <th>Username</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody id="dataBody">
      <tr><td colspan="6" class="text-muted text-center">Belum ada data</td></tr>
    </tbody>
  </table>
</div>
</div>

<style>
  body {
    background-image: url('../../assets/image/background.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
  }
  
  table.table-bordered {
    border-collapse: separate;
    border-spacing: 0;
    border-radius: 5px;
    overflow: hidden;
    min-width: 600px;
  }
  
  .table-bordered,
  .table-bordered th,
  .table-bordered td {
    border-color: #373738ff !important;
  }
  
  .table thead.custom-table-header th {
    background-color: #6D7AE0 !important;
    color: #ffffff;
  }
  
  @keyframes fadeUpSmooth {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
  }
  
  .fade-container {
    animation: fadeUpSmooth 0.8s ease-out;
  }
  
  /* Responsive adjustments untuk mobile dan tablet saja */
  @media (max-width: 992px) {
    .container {
      padding-left: 15px;
      padding-right: 15px;
    }
    
    .card {
      padding: 1.5rem !important;
    }
    
    h4 {
      font-size: 1.5rem;
    }
    
    h5 {
      font-size: 1.25rem;
    }
    
    .modal-dialog {
      max-width: 95%;
      margin: 0.5rem auto;
    }
    
    /* Input search group */
    .input-group {
      flex-wrap: nowrap;
    }
    
    .input-group input {
      font-size: 16px; /* Mencegah zoom di iOS */
    }
    
    .input-group .btn-light svg {
      width: 28px;
      height: 28px;
    }
  }
  
  @media (max-width: 768px) {
    .card {
      padding: 1rem !important;
    }
    
    .btn {
      padding: 0.5rem 1rem;
      font-size: 0.9rem;
    }
    
    .modal-content {
      padding: 0.75rem;
    }
    
    h4 {
      font-size: 1.35rem;
    }
    
    h5 {
      font-size: 1.15rem;
    }
    
    /* Tabel: sembunyikan kolom No. Telp dan Email pada layar kecil */
    .table thead th:nth-child(3), /* No. Telp */
    .table thead th:nth-child(4) { /* Email */
      display: none;
    }
    
    .table tbody td:nth-child(3), /* No. Telp */
    .table tbody td:nth-child(4) { /* Email */
      display: none;
    }
  }
  
  @media (max-width: 576px) {
    /* Table menjadi horizontal scroll pada mobile */
    .table-responsive {
      border: 1px solid #dee2e6;
      border-radius: 5px;
    }
    
    /* Tampilkan kembali header dan kolom pada desktop */
    @media (min-width: 769px) {
      .table thead th:nth-child(3),
      .table thead th:nth-child(4),
      .table tbody td:nth-child(3),
      .table tbody td:nth-child(4) {
        display: table-cell;
      }
    }
    
    /* Tombol responsif */
    .text-end .btn {
      width: 100%;
      margin-top: 0.5rem;
    }
    
    /* Form inputs */
    .form-control {
      font-size: 16px; /* Mencegah zoom di iOS */
    }
    
    /* Modal adjustments */
    .modal-dialog {
      margin: 0.25rem;
    }
    
    .modal-body {
      padding: 0.5rem;
    }
    
    .modal-footer {
      flex-direction: column;
      gap: 0.5rem;
    }
    
    .modal-footer .btn {
      width: 100%;
      margin: 0;
    }
    
    /* Search button icon */
    .input-group .btn-light {
      padding: 0.375rem 0.75rem;
    }
    
    .input-group .btn-light svg {
      width: 24px;
      height: 24px;
    }
  }
  
  /* Untuk layar sangat kecil (smartphone portrait) */
  @media (max-width: 360px) {
    .container {
      padding-left: 10px;
      padding-right: 10px;
    }
    
    .card {
      padding: 0.75rem !important;
    }
    
    h4 {
      font-size: 1.25rem;
    }
    
    h5 {
      font-size: 1.1rem;
    }
    
    h6 {
      font-size: 0.95rem;
    }
    
    .input-group input {
      font-size: 14px;
    }
    
    .btn {
      padding: 0.375rem 0.75rem;
      font-size: 0.85rem;
    }
  }
  
  /* Desktop tetap seperti semula - di atas 992px */
  @media (min-width: 992px) {
    .container {
      max-width: 960px;
    }
    
    /* Pastikan layout desktop tetap sama */
    .card {
      margin-bottom: 2rem;
    }
    
    .table-responsive {
      overflow-x: visible;
    }
    
    table.table-bordered {
      min-width: auto;
      width: 100%;
    }
    
    /* Pastikan semua kolom ditampilkan di desktop */
    .table thead th,
    .table tbody td {
      display: table-cell !important;
    }
  }
  
  /* Pastikan semua kolom ditampilkan di layar medium ke atas */
  @media (min-width: 769px) {
    .table thead th,
    .table tbody td {
      display: table-cell !important;
    }
  }
</style>

<script>
  // JavaScript untuk menangani tampilan tabel responsif
  document.addEventListener('DOMContentLoaded', function() {
    // Fungsi untuk menyesuaikan tampilan tabel
    function adjustTableDisplay() {
      const table = document.querySelector('table');
      if (!table) return;
      
      const headers = table.querySelectorAll('thead th');
      const rows = table.querySelectorAll('tbody tr');
      
      // Reset semua display
      headers.forEach(header => {
        header.style.display = '';
      });
      
      rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        cells.forEach(cell => {
          cell.style.display = '';
        });
      });
      
      // Pada layar kecil (≤768px), sembunyikan kolom 3 dan 4
      if (window.innerWidth <= 768) {
        // Kolom 3: No. Telp (indeks 2 karena mulai dari 0)
        if (headers[2]) headers[2].style.display = 'none';
        if (headers[3]) headers[3].style.display = 'none';
        
        rows.forEach(row => {
          const cells = row.querySelectorAll('td');
          if (cells[2]) cells[2].style.display = 'none';
          if (cells[3]) cells[3].style.display = 'none';
        });
      }
    }
    
    // Jalankan saat load dan resize
    adjustTableDisplay();
    window.addEventListener('resize', adjustTableDisplay);
    
    // Prevent form zoom on iOS
    document.querySelectorAll('input, select, textarea').forEach(el => {
      el.addEventListener('focus', function() {
        if (window.innerWidth <= 768) {
          setTimeout(() => {
            this.style.fontSize = '16px';
          }, 100);
        }
      });
    });
  });
</script>