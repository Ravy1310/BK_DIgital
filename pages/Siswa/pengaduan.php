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

    /* Form kedua (verifikasi NIS) disembunyikan dulu */
    #verifikasiForm {
      display: none;
    }

    /* Gaya layout verifikasi */
    .verif-container {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 20px;
    }

    .verif-left {
      flex: 1;
      text-align: center;
    }

    .verif-right {
      flex: 1;
    }

    .verif-left img {
      width: 120px;
      margin-bottom: 15px;
    }

    .verif-title {
      font-weight: 700;
      font-size: 1.3rem;
      color: #004AAD;
    }
  </style>
</head>

<body>

  <!-- Form Pengaduan -->
  <div class="form-container" id="pengaduanForm">
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

  <!-- Form Verifikasi NIS -->
  <div class="form-container" id="verifikasiForm">
    <div class="verif-container">
      <div class="verif-left">
       <img src="data:image/svg+xml;utf8,<?xml version='1.0'?><svg xmlns='http://www.w3.org/2000/svg' width='48' height='48' viewBox='0 0 48 48'><path fill='%23002E6E' d='M24 2l-18 8v12c0 11.11 7.67 21.47 18 24 10.33-2.53 18-12.89 18-24v-12l-18-8zm0 21.98h14c-1.06 8.24-6.55 15.58-14 17.87v-17.85h-14v-11.4l14-6.22v17.6z'/><path fill='none' d='M0 0h48v48h-48z'/></svg>" alt="ikon biru">
        <h5 class="mt-2">Verifikasi NIS</h5>
        <p>Anda Akan Melakukan:<br><strong>(pengajuan pengaduan)</strong></p>
      </div>

      <div class="verif-right">
        <h5 class="verif-title text-center mb-3">MASUKKAN NOMOR INDUK SISWA (NIS)</h5>
        <p class="text-center">NIS diperlukan untuk menyimpan data aduan anda agar tercatat dengan benar</p>
        <input type="text" class="form-control mb-3" placeholder="Nomor Induk Siswa (NIS)" required>
        <button class="btn btn-primary w-100">Ajukan Sekarang</button>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Script untuk ganti tampilan -->
  <script>
    document.getElementById('formPengaduan').addEventListener('submit', function(e) {
      e.preventDefault(); // cegah reload
      document.getElementById('pengaduanForm').style.display = 'none';
      document.getElementById('verifikasiForm').style.display = 'block';
    });
  </script>

</body>
</html>
