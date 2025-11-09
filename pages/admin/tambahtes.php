<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Tes Baru | Tes BK Digital</title>
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

    /* Tombol Unduh CSV */
    .btn-csv {
      background-color: #00A651;
      color: white;
      font-weight: 60;
      border: none;
      border-radius: 40px;
      padding: 6px 16px;
      display: flex;
      align-items: center;
      margin-right: 765px;
      gap: 10px;
      line-height: 1;
      transition: background-color 0.3s ease;
    }
    .btn-csv:hover {
      background-color: #009444;
    }
    .btn-csv svg {
      width: 22px;   /* ikon diperbesar */
      height: 22px;
      fill: white;
    }
  </style>
</head>
<body>

  <div class="container my-4">
    <div class="card p-4">
      <h4 class="fw-bold">Tambah Tes Baru & Import Soal</h4>
      <p class="text-muted">Gunakan format CSV untuk menambah banyak soal.</p>

      <div class="card p-3 mb-3">
        <p><strong>Gunakan format CSV</strong> untuk mengimpor banyak soal sekaligus.</p>

        <!-- Tombol Unduh CSV -->
        <button class="btn-csv" onclick="alert('Template CSV berhasil diunduh!')">
          <!-- Ikon panah ke bawah -->
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
            <path d="M480 256c0 123.5-100.5 224-224 224S32 379.5 32 256 132.5 32 256 32s224 100.5 224 224zM256 128c-8.8 0-16 7.2-16 16v144l-41.6-41.6c-6.2-6.2-16.4-6.2-22.6 0s-6.2 16.4 0 22.6l64 64c6.2 6.2 16.4 6.2 22.6 0l64-64c6.2-6.2 6.2-16.4 0-22.6s-16.4-6.2-22.6 0L272 288V144c0-8.8-7.2-16-16-16z"/>
          </svg>
          Unduh Template Soal (CSV)
        </button>
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
        <a href="index.php" class="btn btn-merah px-4">Batal</a>
        <button class="btn btn-primary px-4" onclick="alert('Tes baru berhasil disimpan!')">Simpan</button>
      </div>
    </div>
  </div>

</body>
</html>
