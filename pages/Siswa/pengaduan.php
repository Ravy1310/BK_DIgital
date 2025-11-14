<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Buat Pengaduan | BK Digital</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: url('../../assets/image/background.jpg');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }

    .form-container {
      background: #ffffff;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.15);
      padding: 40px 45px;
      width: 100%;
      max-width: 750px;
    }

    .form-title {
      font-weight: 700;
      font-size: 1.6rem;
      margin-bottom: 25px;
      color: #000;
      text-align: center;
    }

    .form-label {
      font-weight: 500;
      color: #333;
      margin-bottom: 6px;
    }

    select, textarea, input {
      border-radius: 8px;
      font-size: 15px;
      padding: 10px 14px;
    }

    .btn-danger {
      background-color: #e63946;
      border: none;
      font-weight: 500;
      padding: 8px 25px;
      border-radius: 8px;
    }

    .btn-danger:hover {
      background-color: #c82333;
    }

    .btn-primary {
      background-color: #004AAD;
      border: none;
      font-weight: 500;
      padding: 8px 25px;
      border-radius: 8px;
    }

    .btn-primary:hover {
      background-color: #003580;
    }

    textarea {
      resize: vertical;
    }
  </style>
</head>

<body>

  <!-- Form Pengaduan -->
  <div class="form-container">
    <h5 class="form-title">Buat Pengaduan</h5>

    <form id="formPengaduan">
      <div class="mb-3">
        <label for="jenisLaporan" class="form-label">Jenis Laporan</label>
        <select id="jenisLaporan" class="form-select" required>
          <option selected disabled>Pilih jenis laporan</option>
          <option>Masalah Akademik</option>
          <option>Masalah Non-Akademik</option>
          <option>Lainnya</option>
        </select>
      </div>

      <div class="mb-3">
        <label for="jenisKejadian" class="form-label">Jenis Kejadian</label>
        <select id="jenisKejadian" class="form-select" required>
          <option selected disabled>Pilih jenis kejadian</option>
          <option>Bully</option>
          <option>Kekerasan Fisik</option>
          <option>Kekerasan Verbal</option>
          <option>Lainnya</option>
        </select>
      </div>

      <div class="mb-3">
        <label for="penjelasan" class="form-label">Penjelasan</label>
        <textarea id="penjelasan" class="form-control" rows="5" placeholder="Tuliskan penjelasan Anda..." required></textarea>
      </div>

      <div class="d-flex justify-content-between mt-4">
        <button type="reset" class="btn btn-danger">Batal</button>
        <button type="submit" class="btn btn-primary">Kirim</button>
      </div>
    </form>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Jalankan aksi submit biasa (tanpa verifikasi NIS)
    document.getElementById('formPengaduan').addEventListener('submit', function(e) {
      e.preventDefault();
      alert("Pengaduan berhasil dikirim!");
    });
  </script>

</body>
</html>
