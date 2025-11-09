<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Tes BK | Tes BK Digital</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: url('../../assets/image/background.jpg');
      padding: 20px;
    }
    .btn-merah {
      background-color: #C60000 !important;
      color: #fff !important;
      border: none !important;
    }
  </style>
</head>
<body>

  <div class="container my-4">
    
    <!-- ====================== Halaman KELOLA TES ====================== -->
    <div id="kelolaTes" class="card p-4">
      <h4 class="fw-bold">Kelola Tes BK</h4>
      <p class="text-muted">Ubah atau hapus jenis tes yang sudah ada.</p>

      <div class="kelola-card mb-3 p-3 border rounded">
        <h5>Tes Minat Belajar</h5>
        <p>Mengukur minat belajar siswa</p>
        <button class="btn btn-success btn-sm" onclick="tampilkanKelolaSoal('Tes Minat Belajar')">Edit</button>
        <button class="btn btn-merah btn-sm" onclick="alert('Tes berhasil dihapus!')">Hapus</button>
      </div>

      <div class="kelola-card mb-3 p-3 border rounded">
        <h5>Tes Kepribadian</h5>
        <p>Mengukur karakter dan tipe kepribadian siswa</p>
        <button class="btn btn-success btn-sm" onclick="tampilkanKelolaSoal('Tes Kepribadian')">Edit</button>
        <button class="btn btn-merah btn-sm" onclick="alert('Tes berhasil dihapus!')">Hapus</button>
      </div>

      <!-- Tombol kembali -->
      <div class="d-flex justify-content-start mt-3">
        <a href="index.php" class="btn btn-merah btn-sm">Kembali</a>
      </div>
    </div>

    <!-- ====================== Halaman KELOLA SOAL ====================== -->
    <div id="kelolaSoal" class="card p-4" style="display: none;">
      <h4 class="fw-bold" id="judulTes">Kelola Soal</h4>
      <p class="text-muted">Daftar soal yang tersedia untuk tes ini.</p>

      <div class="border rounded p-3 mb-3">
        <p><strong>1.</strong> Ketika mencoba mengingat petunjuk arah, cara apa yang paling efektif bagi anda?</p>
        <p>A. Membayangkan peta<br>B. Mengulang dengan suara keras<br>C. Bergerak atau menunjuk</p>
        <button class="btn btn-success btn-sm">Edit</button>
        <button class="btn btn-merah btn-sm" onclick="alert('Soal dihapus!')">Hapus</button>
      </div>

      <div class="d-flex justify-content-between align-items-center mt-3">
        <button class="btn btn-merah btn-sm" onclick="kembaliKeTes()">Kembali</button>
        <button class="btn btn-primary btn-sm">Tambah Soal Baru</button>
      </div>
    </div>

  </div>

  <script>
    // Menampilkan halaman Kelola Soal
    function tampilkanKelolaSoal(namaTes) {
      document.getElementById('kelolaTes').style.display = 'none';
      document.getElementById('kelolaSoal').style.display = 'block';
      document.getElementById('judulTes').innerText = 'Kelola Soal: ' + namaTes;
    }

    // Kembali ke halaman Kelola Tes
    function kembaliKeTes() {
      document.getElementById('kelolaTes').style.display = 'block';
      document.getElementById('kelolaSoal').style.display = 'none';
    }
  </script>

</body>
</html>
