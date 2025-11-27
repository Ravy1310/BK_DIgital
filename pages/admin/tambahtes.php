<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Tes Baru | Tes BK Digital</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

  <style>
   body {
      font-family: 'Poppins', sans-serif;
      background: url('../../assets/image/background.jpg');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      min-height: 100vh;
      padding-top: 10px;
    }

    .card {
      border: none;
      border-radius: 16px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .btn-merah {
      background-color: #C60000 !important;
      color: #fff !important;
      border: none !important;
      transition: 0.3s;
    }
    .btn-merah:hover {
      background-color: #a30000 !important;
    }

    .btn-csv {
      background-color: #00A651;
      color: white;
      font-weight: 500;
      border: none;
      border-radius: 30px;
      padding: 8px 20px;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      line-height: 1;
      transition: all 0.3s ease;
    }
    .btn-csv:hover {
      background-color: #009444;
      transform: translateY(-2px);
    }
    .btn-csv svg {
      width: 20px;
      height: 20px;
      fill: white;
    }

    h4.fw-bold {
      color: #222;
      border-left: 5px solid #0066cc;
      padding-left: 10px;
    }

    label.fw-bold {
      color: #333;
    }

    .form-control {
      border-radius: 10px;
      border: 1px solid #ccc;
      transition: all 0.3s;
    }

    .form-control:focus {
      border-color: #0066cc;
      box-shadow: 0 0 0 0.15rem rgba(0, 102, 204, 0.25);
    }

    .card-import {
      background-color: #f8f9fa;
      border-radius: 12px;
      border: 1px dashed #00A651;
      text-align: center;
      transition: 0.3s;
    }

    .card-import:hover {
      background-color: #f3fdf6;
    }

    /* Loading indicator */
    .loading {
      display: inline-block;
      width: 16px;
      height: 16px;
      border: 2px solid #f3f3f3;
      border-top: 2px solid #3498db;
      border-radius: 50%;
      animation: spin 1s linear infinite;
      vertical-align: middle;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    /* Field error styling */
    .is-invalid {
      border-color: #dc3545 !important;
      box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }

    .field-error {
      font-size: 0.875em;
    }

    /* File info styling */
    .file-info {
      border-radius: 8px;
      border-left: 4px solid #0dcaf0;
    }

    /* Custom alert */
    .custom-alert {
      position: fixed;
      top: 100px;
      right: 20px;
      z-index: 1050;
      min-width: 350px;
      max-width: 500px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      border-radius: 10px;
      border: none;
      animation: slideInRight 0.3s ease;
    }

    @keyframes slideInRight {
      from {
        transform: translateX(100%);
        opacity: 0;
      }
      to {
        transform: translateX(0);
        opacity: 1;
      }
    } 
    .nama-tes-feedback {
      font-size: 0.875em;
      margin-top: 5px;
    }
    
    .nama-tes-valid {
      color: #198754;
    }
    
    .nama-tes-invalid {
      color: #dc3545;
    }
    
    .nama-tes-checking {
      color: #6c757d;
    }
    
    /* Style untuk suggestion */
    .suggestion-box {
      background-color: #f8f9fa;
      border: 1px solid #dee2e6;
      border-radius: 5px;
      padding: 10px;
      margin-top: 10px;
      font-size: 0.875em;
    }
  /* CARI bagian CSS dan TAMBAHKAN: */

/* Loading state untuk button - PASTIKAN ADA */
.btn-loading .button-text {
    display: none !important;
}

.btn-loading .loading-spinner {
    display: inline !important;
}

.btn-loading {
    pointer-events: none !important;
    opacity: 0.7 !important;
}

/* Disabled state */
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
  </style>
</head>
<body>

  <div class="container my-5">
    <div class="card p-4">
      <h4 class="fw-bold mb-3">Tambah Tes Baru & Import Soal</h4>
      <p class="text-muted mb-4">Gunakan format CSV untuk menambah banyak soal dengan cepat dan mudah.</p>

      <!-- CARD IMPOR CSV -->
      <div class="card-import p-4 mb-4">
        <p class="mb-3"><strong>Gunakan format CSV</strong> untuk mengimpor banyak soal sekaligus.</p>
        <button class="btn-csv" id="downloadTemplateBtn">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
            <path d="M480 256c0 123.5-100.5 224-224 224S32 379.5 32 256 132.5 32 256 32s224 100.5 224 224zM256 128c-8.8 0-16 7.2-16 16v144l-41.6-41.6c-6.2-6.2-16.4-6.2-22.6 0s-6.2 16.4 0 22.6l64 64c6.2 6.2 16.4 6.2 22.6 0l64-64c6.2-6.2 6.2-16.4 0-22.6s-16.4-6.2-22.6 0L272 288V144c0-8.8-7.2-16-16-16z"/>
          </svg>
          Unduh Template Soal (CSV)
        </button>
      </div>

      <!-- FORM TAMBAH TES -->
      <!-- Dalam file tambahtes.php, pastikan form seperti ini: -->

<!-- FORM TAMBAH TES -->
<form id="formTambahTes" enctype="multipart/form-data">
    <!-- INPUT TES -->
    <div class="mb-3">
        <label class="fw-bold">Nama Tes Baru</label>
        <input type="text" class="form-control" name="nama_tes" id="namaTesInput" 
               placeholder="Masukkan nama tes baru" required>
        <!-- <div id="namaTesFeedback" class="nama-tes-feedback"></div> -->
        <div id="suggestionBox" class="suggestion-box" style="display: none;">
            <strong>💡 Suggestion:</strong> 
            Coba gunakan nama yang lebih spesifik atau tambahkan tahun/tanggal.
            <div id="suggestedNames" class="mt-2"></div>
        </div>
    </div>

    <div class="mb-3">
        <label class="fw-bold">Deskripsi Tes</label>
        <textarea class="form-control" name="deskripsi_tes" rows="3" placeholder="Masukkan deskripsi tes" required></textarea>
    </div>

   <div class="mb-4">
    <label class="fw-bold">Unggah File Soal (CSV)</label>
    <input type="file" class="form-control" name="csv_file" accept=".csv" id="csvFileInput">
    <!-- PASTIKAN ELEMEN INI ADA -->
    <div id="fileValidation" class="file-validation"></div>
    <small class="text-muted">Format file harus CSV dengan struktur kolom sesuai template. Maksimal 2MB.</small>
</div>

   <!-- TOMBOL AKSI -->
<div class="d-flex justify-content-between align-items-center">
    <button type="button" class="btn btn-merah px-4"  onclick="window.loadContent('kelolaTes.php')">Batal</button>
    <button type="submit" class="btn btn-primary px-4" id="submitBtn">
    <span class="button-text">Simpan</span>
    <span class="loading-spinner" style="display: none;">
        <span class="loading me-2"></span> Menyimpan...
    </span>
</button>
</div>
</form>
    </div>
  </div>


</body>
</html>