<?php
// Jika siswa sudah diverifikasi → akan masuk dengan parameter ?idsiswa=
$verified_id = isset($_GET['idsiswa']) ? $_GET['idsiswa'] : null;
?>
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
  </style>
</head>

<body>

  <div class="form-container">
    <h5 class="form-title">Buat Pengaduan</h5>

    <!-- FORM -->
    <form id="formPengaduan" action="../../includes/pengaduan_controller.php" method="POST">

      <!-- Jika sudah diverifikasi, kirim id siswa -->
      <?php if ($verified_id): ?>
        <input type="hidden" name="id_siswa" value="<?= $verified_id ?>">
      <?php endif; ?>

      <div class="mb-3">
        <label class="form-label">Jenis Laporan</label>
        <select name="jenis_laporan" class="form-select" required>
          <option selected disabled>Pilih jenis laporan</option>
          <option>Anonim</option>
          <option>Teridentifikasi</option>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Jenis Kejadian</label>
        <select name="jenis_kejadian" class="form-select" required>
          <option selected disabled>Pilih jenis kejadian</option>
          <option>Bully</option>
          <option>Kekerasan Fisik</option>
          <option>Kekerasan Verbal</option>
          <option>Lainnya</option>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Penjelasan</label>
        <textarea name="penjelasan" class="form-control" rows="5" placeholder="Tuliskan penjelasan Anda..." required></textarea>
      </div>

      <div class="d-flex justify-content-between mt-4">
        <button type="reset" class="btn btn-danger">Batal</button>
        <button type="submit" class="btn btn-primary">Kirim</button>
      </div>
    </form>

  </div>

  <!-- SCRIPT VERIFIKASI -->
  <script>
    document.getElementById("formPengaduan").addEventListener("submit", function(event) {

        const jenis = document.querySelector("select[name='jenis_laporan']").value;
        const alreadyVerified = window.location.search.includes("idsiswa");

        // Jika pilih teridentifikasi tetapi belum diverifikasi, arahkan ke halaman verifikasi
        if (jenis === "Teridentifikasi" && !alreadyVerified) {
            event.preventDefault();
            window.location.href = "verifikasi_pengaduan.php";
        }

        // Jika sudah diverifikasi, proses lanjut kirim ke controller
    });
  </script>

</body>
</html>
