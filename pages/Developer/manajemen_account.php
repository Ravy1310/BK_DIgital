
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

  <table class="table table-bordered table-hover  table-striped border">
    <thead class="custom-table-header text-center">
      <tr>
        <th>No</th>
        <th>Nama</th>
        <th>No. Telp</th> 
        <th>Email</th>
        <th>Username</th>
        <th>Aksi</th>
     
    </thead>
    <tbody id="dataBody">
      <tr><td colspan="6" class="text-muted text-center">Belum ada data</td></tr>
    </tbody>
  </table>
</div>

<style>
  body {
    background-image: url('../../assets/image/background.jpg');
    /* background-color : #f5f5f5; */
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  }
table.table-bordered {
  border-collapse: separate;  /* Ubah dari 'collapse' ke 'separate' */
  border-spacing: 0;        /* Hilangkan jarak antar sel */
  border-radius: 5px;      /* Terapkan radius ke tabel */
  overflow: hidden;         /* Klip sudutnya */
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
</style>