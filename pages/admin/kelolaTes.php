<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tes BK Digital</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font: Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: url('../../assets/image/background.jpg');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      padding: 20px;
    }

    h4.fw-bold {
      font-weight: 700;
      color: #343a40;
    }

    .summary-card {
      border-radius: 10px;
      background-color: #fff;
      box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
      padding: 25px;
      text-align: center;
      font-weight: 600;
    }

    .summary-card h5 {
      font-weight: 700;
      color: #222;
    }

    .summary-card h3 {
      font-weight: 700;
      color: #000;
    }

    .action-btn {
      font-weight: 600;
      font-size: 16px;
      padding: 12px;
      border-radius: 10px;
      box-shadow: 0 3px 6px rgba(13,110,253,0.3);
    }

    .test-card {
      border-radius: 10px;
      background-color: #fff;
      box-shadow: 0 3px 8px rgba(0,0,0,0.1);
      padding: 15px;
      display: flex;
      align-items: center;
      transition: 0.2s;
    }

    .test-card:hover {
      background-color: #f3f6ff;
      transform: scale(1.01);
    }

    .test-card img {
      width: 60px;
      height: 60px;
      margin-right: 15px;
    }

    .icon-btn {
      width: 18px;
      height: 18px;
      margin-right: 6px;
      vertical-align: middle;
    }

    .btn-small {
      font-size: 14px;
      padding: 6px 12px;
      border-radius: 6px;
    }

    /* --- Modul Kelola Tes --- */
    .kelola-card {
      border: 1px solid #ccc;
      border-radius: 8px;
      background: #fff;
      box-shadow: 0 3px 6px rgba(0,0,0,0.1);
      padding: 15px;
      margin-bottom: 15px;
    }

    .kelola-card h5 {
      font-weight: 700;
      margin-bottom: 5px;
    }

    .kelola-card p {
      color: #555;
      font-size: 14px;
      margin-bottom: 10px;
    }

    /* Semua tombol merah */
    .btn-merah {
      background-color: #C60000 !important;
      color: #fff !important;
      border: none !important;
      transition: 0.2s;
    }

    .btn-merah:hover {
      background-color: #a00000 !important;
    }
  </style>
</head>
<body>

  <!-- ===== DASHBOARD ===== -->
  <div class="container" id="kelolaTes">
      <h4 class="fw-bold">Tes BK Digital</h4>
      <p class="text-muted mb-4">Ringkasan dan daftar tes BK yang tersedia.</p>

      <!-- Ringkasan -->
      <div class="row text-center mb-3">
        <div class="col-md-6 mb-3">
          <div class="summary-card">
            <h5>Total Soal</h5>
            <h3>500</h3>
          </div>
        </div>
        <div class="col-md-6 mb-3">
          <div class="summary-card">
            <h5>Jenis Tes</h5>
            <h3>10</h3>
          </div>
        </div>
      </div>

      <!-- Tombol -->
      <div class="row text-center mb-4">
        <div class="col-md-6 mb-3">
          <button class="btn btn-primary w-100 action-btn" onclick="tampilKelolaTesBK()">Kelola Tes BK</button>
        </div>
        <div class="col-md-6 mb-3">
          <button class="btn btn-primary w-100 action-btn" onclick="tampilTambahTes()">
            <i class="bi bi-plus-lg"></i> Tambah Tes Baru
          </button>
        </div>
      </div>

      <!-- Tes contoh -->
      <div class="test-card mb-2">
        <img src="https://cdn-icons-png.flaticon.com/512/4140/4140048.png" alt="icon">
        <div>
          <h5>Tes Minat Belajar</h5>
          <p>Mengukur minat belajar siswa<br>50 soal</p>
        </div>
      </div>
  </div>

  <!-- ===== TAMBAH TES ===== -->
  <div class="container my-4" id="tambahTes" style="display:none;">
    <div class="card p-4">
      <h4 class="fw-bold">Tambah Tes Baru & Import Soal</h4>
      <p class="text-muted">Gunakan format CSV untuk menambah banyak soal.</p>

      <div class="card p-3 mb-3">
        <p><strong>Gunakan format CSV</strong> untuk mengimpor banyak soal sekaligus.</p>
        <div class="text-start">
          <button class="btn btn-success btn-small" onclick="alert('Template CSV berhasil diunduh!')">
            <img src="gambar/iconcsv.svg" class="icon-btn" alt="icon"> Unduh Template Soal (CSV)
          </button>
        </div>
      </div>

      <div class="mb-3">
        <label class="fw-bold">Nama Tes Baru</label>
        <input type="text" class="form-control" placeholder="Masukkan nama tes baru">
      </div>

      <div class="mb-3">
        <label class="fw-bold">Deskripsi Tes</label>
        <textarea class="form-control" rows="3" placeholder="Masukkan deskripsi tes"></textarea>
      </div>

      <div class="mb-3">
        <label class="fw-bold">Unggah File Soal (CSV)</label>
        <input type="file" class="form-control">
      </div>

      <div class="d-flex justify-content-between">
        <button class="btn btn-merah px-4" onclick="kembali()">Batal</button>
        <button class="btn btn-primary px-4" onclick="alert('Tes baru berhasil disimpan!')">Simpan</button>
      </div>
    </div>
  </div>

  <!-- ===== KELOLA TES BK ===== -->
  <div class="container my-4" id="modulKelolaTes" style="display:none;">
    <div class="card p-4">
      <h4 class="fw-bold">Kelola Tes BK</h4>
      <p class="text-muted">Ubah atau hapus jenis tes yang sudah ada.</p>

      <div id="daftarTes">
        <div class="kelola-card">
          <h5>Tes Minat Belajar</h5>
          <p>Mengukur minat belajar siswa</p>
          <button class="btn btn-success btn-sm" onclick="tampilKelolaSoal()"><i class="bi bi-pencil"></i> Edit</button>
          <button class="btn btn-merah btn-sm" onclick="hapusItem(this)"><i class="bi bi-trash"></i> Hapus</button>
        </div>

        <div class="kelola-card">
          <h5>Tes Kepribadian</h5>
          <p>Mengukur karakter dan tipe kepribadian siswa</p>
          <button class="btn btn-success btn-sm" onclick="tampilKelolaSoal()"><i class="bi bi-pencil"></i> Edit</button>
          <button class="btn btn-merah btn-sm" onclick="hapusItem(this)"><i class="bi bi-trash"></i> Hapus</button>
        </div>
      </div>

      <div class="text-start mt-3">
        <button class="btn btn-merah btn-sm px-3" onclick="kembali()">Kembali</button>
      </div>
    </div>
  </div>

  <!-- ===== KELOLA SOAL ===== -->
  <div class="container my-4" id="modulKelolaSoal" style="display:none;">
    <div class="card p-4">
      <h4 class="fw-bold">Kelola Soal : Tes Minat Belajar</h4>
      <p class="text-muted">Daftar soal yang tersedia untuk tes ini.</p>

      <div id="daftarSoal" class="border rounded p-3 mb-3">
        <div class="mb-3 soal">
          <p><strong>1.</strong> Ketika mencoba mengingat petunjuk arah, cara apa yang paling efektif bagi anda?</p>
          <p>A. Membayangkan peta<br>B. Mengulang dengan suara keras<br>C. Bergerak atau menunjuk</p>
          <button class="btn btn-success btn-sm"><i class="bi bi-pencil"></i> Edit</button>
          <button class="btn btn-merah btn-sm" onclick="hapusItem(this)"><i class="bi bi-trash"></i> Hapus</button>
        </div>
      </div>

      <div class="d-flex justify-content-between align-items-center mt-3">
        <button class="btn btn-merah btn-sm px-3" onclick="kembali()">Kembali</button>
        <button class="btn btn-primary btn-sm px-3" onclick="alert('Fitur tambah soal sedang dikembangkan!')"><i class="bi bi-plus-lg"></i> Tambah soal baru</button>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.js"></script>
  <script>
    function tampilTambahTes() {
      tampilHalaman("tambahTes");
    }

    function tampilKelolaTesBK() {
      tampilHalaman("modulKelolaTes");
    }

    function tampilKelolaSoal() {
      tampilHalaman("modulKelolaSoal");
    }

    function kembali() {
      tampilHalaman("kelolaTes");
    }

    // Fungsi umum untuk tampil/menyembunyikan halaman
    function tampilHalaman(id) {
      const semua = ["kelolaTes", "tambahTes", "modulKelolaTes", "modulKelolaSoal"];
      semua.forEach(el => document.getElementById(el).style.display = "none");
      document.getElementById(id).style.display = "block";
    }

    // Fungsi hapus umum
    function hapusItem(btn) {
      const card = btn.closest(".kelola-card, .soal");
      if (card && confirm("Yakin ingin menghapus item ini?")) {
        card.remove();
        alert("Item berhasil dihapus!");
      }
    }
  </script>

</body>
</html>
