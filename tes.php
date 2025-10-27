<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manajemen Akun</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
<style>
body {
 background-image: url('background.jpg');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  font-family: 'Poppins', sans-serif;
}

/* Biar isi kolom Aksi rata tengah */
td:last-child {
  text-align: center;
  vertical-align: middle;
}

/* Ukuran tombol kecil */
.btn-edit, .btn-hapus {
  background: none;
  border: none;
  padding: 4px;
  margin: 0 2px;
  cursor: pointer;
  width: 28px;
  height: 28px;
}

/* Ukuran ikon di dalam tombol */
.btn-edit img,
.btn-hapus img {
  width: 16px;
  height: 16px;
}
.card {
  border-radius: 10px;
}

.table {
  border-radius: 10px;
  overflow: hidden;
}

.table thead.custom-table-header th {
  background-color: #6D7AE0 !important; /* Biru Bootstrap */
  color: #ffffff; /* Warna teks putih agar kontras */
}
h5 {
  font-weight: 600;
}

.btn-success {
  border-radius: 8px;
}

</style>
</head>
<body class="bg-light">

  

  <div class="container my-5">
    <div class=" mb-4">
      <h3 class="fw-bold">Manajemen Akun</h3>
      <p class="text-muted fs-5">Buat akun superadmin</p>
    </div>

    <!-- Form Buat Akun -->
    <div class="card shadow-sm p-4 mb-5">
      <form id="formAkun">
        <h5 class="fw-bold mb-3">Nama Lengkap</h5>
        <div class="mb-3">
          <input type="text" class="form-control" id="nama" placeholder="Nama Lengkap" required>
        </div>
        <div class="mb-3">
          <input type="email" class="form-control" id="email" placeholder="Email" required>
        </div>
        <div class="mb-3">
          <input type="text" class="form-control" id="username" placeholder="Username" required>
        </div>

        <h5 class="fw-bold mt-4 mb-3">Password</h5>
        <div class="mb-3">
          <input type="password" class="form-control" id="password" placeholder="Password" required>
        </div>
        <div class="mb-3">
          <input type="password" class="form-control" id="konfirmasi" placeholder="Konfirmasi Password" required>
        </div>

        <div class="text-end">
          <button type="submit" class="btn btn-success px-4">Buat akun</button>
        </div>
      </form>
    </div>

    <!-- Daftar Akun -->
    <h5 class="fw-bold mb-3">Daftar akun admin</h5>
    <div class="input-group mb-3">
      <input type="text" id="cari" class="form-control" placeholder="Cari admin...">
      <button class="btn btn-light" type="button">
        <svg xmlns="http://www .w3.org/2000/svg" x="0px" y="0px" width="35" height="35" viewBox="0 0 128 128">
    <path d="M 52.349609 14.400391 C 42.624609 14.400391 32.9 18.1 25.5 25.5 C 10.7 40.3 10.7 64.399219 25.5 79.199219 C 32.9 86.599219 42.600391 90.300781 52.400391 90.300781 C 62.200391 90.300781 71.900781 86.599219 79.300781 79.199219 C 94.000781 64.399219 93.999219 40.3 79.199219 25.5 C 71.799219 18.1 62.074609 14.400391 52.349609 14.400391 z M 52.300781 20.300781 C 60.500781 20.300781 68.700391 23.399219 74.900391 29.699219 C 87.400391 42.199219 87.4 62.5 75 75 C 62.5 87.5 42.199219 87.5 29.699219 75 C 17.199219 62.5 17.199219 42.199219 29.699219 29.699219 C 35.899219 23.499219 44.100781 20.300781 52.300781 20.300781 z M 52.300781 26.300781 C 45.400781 26.300781 38.9 29 34 34 C 29.3 38.7 26.700391 44.800391 26.400391 51.400391 C 26.300391 53.100391 27.600781 54.4 29.300781 54.5 L 29.400391 54.5 C 31.000391 54.5 32.300391 53.199609 32.400391 51.599609 C 32.600391 46.499609 34.699219 41.799219 38.199219 38.199219 C 41.999219 34.399219 47.000781 32.300781 52.300781 32.300781 C 54.000781 32.300781 55.300781 31.000781 55.300781 29.300781 C 55.300781 27.600781 54.000781 26.300781 52.300781 26.300781 z M 35 64 A 3 3 0 0 0 32 67 A 3 3 0 0 0 35 70 A 3 3 0 0 0 38 67 A 3 3 0 0 0 35 64 z M 83.363281 80.5 C 82.600781 80.5 81.850781 80.800391 81.300781 81.400391 C 80.100781 82.600391 80.100781 84.499609 81.300781 85.599609 L 83.800781 88.099609 C 83.200781 89.299609 82.900391 90.6 82.900391 92 C 82.900391 94.4 83.8 96.700391 85.5 98.400391 L 98.300781 111 C 100.10078 112.8 102.39922 113.69922 104.69922 113.69922 C 106.99922 113.69922 109.29961 112.79961 111.09961 111.09961 C 114.59961 107.59961 114.59961 101.90039 111.09961 98.400391 L 98.300781 85.599609 C 96.600781 83.899609 94.300391 83 91.900391 83 C 90.500391 83 89.2 83.300391 88 83.900391 L 85.5 81.400391 C 84.9 80.800391 84.125781 80.5 83.363281 80.5 z M 91.900391 88.900391 C 92.700391 88.900391 93.5 89.200781 94 89.800781 L 106.69922 102.5 C 107.89922 103.7 107.89922 105.59922 106.69922 106.69922 C 105.49922 107.89922 103.6 107.89922 102.5 106.69922 L 89.800781 94.099609 C 89.200781 93.499609 88.900391 92.700391 88.900391 91.900391 C 88.900391 91.100391 89.200781 90.300781 89.800781 89.800781 C 90.400781 89.200781 91.100391 88.900391 91.900391 88.900391 z"></path>
</svg>
      </button>
    </div>

   <table class="table table-bordered table-hover">
  <thead class="custom-table-header text-center">
    <tr>
      <th>No</th>
      <th>Nama</th>
      <th>Email</th>
      <th>Username</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody id="tabelAdmin">
    <td>01</td>
    <td>selvi</td>
    <td>selvi21@gmail.com</td>
    <td>selvii</td>
    <td>
      <button class="btn-edit" id="btnEdit"><img src="edit.svg" alt="Edit"></button>
      <button class="btn-hapus" id="btnHapus"><img src="hapus.svg" alt="Hapus"></button>
    <tr><td colspan="5" class="text-muted">Belum ada data</td></tr>
  </tbody>
</table>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.js"></script>
    <script>
 document.getElementById("formAkun").addEventListener("submit", function (e) {
  e.preventDefault();

  const nama = document.getElementById("nama").value;
  const email = document.getElementById("email").value;
  const username = document.getElementById("username").value;
  const password = document.getElementById("password").value;
  const konfirmasi = document.getElementById("konfirmasi").value;

  if (password !== konfirmasi) {
    alert("Password dan konfirmasi tidak sama!");
    return;
  }

  const tabel = document.getElementById("tabelAdmin");
  const barisBaru = document.createElement("tr");
  barisBaru.innerHTML = `
    <td>${nama}</td>
    <td>${email}</td>
    <td>${username}</td>
    <td>
      <button class="btn btn-sm btn-danger">Hapus</button>
    </td>
  `;

  tabel.appendChild(barisBaru);
  this.reset();
});

// Hapus data admin
document.getElementById("tabelAdmin").addEventListener("click", function (e) {
  if (e.target.classList.contains("btn-danger")) {
    e.target.closest("tr").remove();
  }
});
    </script>
</body>
</html>
