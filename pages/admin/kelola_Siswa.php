<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Data Siswa - BK Digital</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: url('../../assets/image/background.jpg');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      padding: 40px;
    }

    h4 {
      font-weight: 700;
      color: #004AAD;
    }

    .card-info {
      border-radius: 12px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.15);
      padding: 20px;
      background: white;
      text-align: center;
      transition: transform 0.2s ease;
    }
    .card-info:hover { transform: scale(1.03); }
    .icon-card { width: 50px; height: 50px; object-fit: contain; }

    .table-container {
      background: white;
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      padding: 20px;
    }

    .btn-import {
      background-color: #38A169;
      color: white;
      border: none;
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 6px;
      padding: 6px 12px;
    }
    .btn-import:hover { background-color: #43a047; }

    .btn-tambah {
      background-color: #0050BC;
      color: white;
      border: none;
      font-weight: 500;
    }
    .btn-tambah:hover { background-color: #0069d9; }

    .icon-btn { width: 18px; height: 18px; object-fit: contain; }

    .search-container { display: flex; align-items: center; gap: 8px; }
    .search-box {
      width: 250px; background: white; border-radius: 50px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.15);
      border: 1px solid #ccc; padding: 6px 16px; font-size: 14px;
      outline: none; transition: 0.2s;
    }
    .search-box:focus {
      border-color: #38A169;
      box-shadow: 0 0 4px rgba(56,161,105,0.6);
    }

    .btn-cari {
      background-color: #38A169; border: none; border-radius: 50px;
      width: 42px; height: 32px; display: flex; align-items: center;
      justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2);
      transition: 0.2s;
    }
    .btn-cari:hover { background-color: #2F855A; }
    .btn-cari img { width: 18px; height: 18px; }

    table { font-size: 0.9rem; }

    .modal-content {
      border-radius: 10px;
      font-family: 'Poppins', sans-serif;
    }

    .modal-header-custom {
      background: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 2px solid #eee;
      padding: 16px 20px;
      border-top-left-radius: 10px;
      border-top-right-radius: 10px;
    }

    .modal-title-custom {
      font-weight: 600;
      color: #004AAD;
      margin: 0;
    }

    .btn-close-custom {
      background: none;
      border: none;
      font-size: 20px;
      color: #333;
      transition: 0.2s;
    }
    .btn-close-custom:hover { color: #d11a2a; }

    .btn-primary {
      background-color: #004AAD;
      border: none;
    }
    .btn-primary:hover { background-color: #003580; }
  </style>
</head>

<body>
  <div class="container">
    <h4 class="mb-4">BK Digital</h4>

    <!-- Kartu Statistik -->
    <div class="row g-4 mb-5">
      <div class="col-md-4">
        <div class="card-info text-center">
          <div class="d-flex justify-content-center align-items-center mx-auto rounded-rectangle" style="background-color:#28a745; width:60px; height:60px; border-top-left-radius: 10px;
      border-top-right-radius: 10px; border-bottom-left-radius: 10px;
      border-bottom-right-radius: 10px;">
            <svg viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg" width="30" height="30">
              <rect fill="none" height="256" width="256"/>
              <line fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="16" x1="32" x2="32" y1="64" y2="144"/>
              <path d="M54.2,216a88.1,88.1,0,0,1,147.6,0" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/>
              <polygon fill="none" points="224 64 128 96 32 64 128 32 224 64" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/>
              <path d="M169.3,82.2a56,56,0,1,1-82.6,0" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/>
            </svg>
          </div>
          <h6 class="mt-2">Jumlah Siswa Aktif</h6>
          <h4>4000</h4>
        </div>
      </div>
      <div class="col-md-4 col-sm-6 col-12">
  <div class="card-info text-center">
    <div class="d-flex justify-content-center align-items-center mx-auto rounded-rectangle" 
         style="background-color:#DBF4D6; width:60px; height:60px; border-radius:10px;">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="50" height="50" fill="#00000">
        <path d="M285.35,268.6a4.93,4.93,0,0,0-6.36-2.78,65.54,65.54,0,0,1-47.77,0,4.9,4.9,0,1,0-3.57,9.13,75.24,75.24,0,0,0,54.92,0A4.9,4.9,0,0,0,285.35,268.6Z"/>
        <path d="M231.56,236.3V214.88a4.91,4.91,0,0,0-9.81,0V236.3a4.91,4.91,0,0,0,9.81,0Z"/>
        <path d="M280.78,241.2a4.9,4.9,0,0,0,4.9-4.9V214.88a4.91,4.91,0,0,0-9.81,0V236.3A4.91,4.91,0,0,0,280.78,241.2Z"/>
        <path d="M302.05,325.65c26.15-7.69,44.86-33.1,44.86-64.12v-11.1a4.78,4.78,0,0,0,2.09-1.06,75.1,75.1,0,0,0,25.76-56.61v-30.4a75.2,75.2,0,0,0-75.1-75.1H229.29C162.57,87.26,151.51,71,151.5,71a4.9,4.9,0,0,0-9.23,1.52c-16.86,120.28,13.49,150.14,21,155.58v33.48c0,31,18.59,56.4,44.6,64.11l-41.07,19.43a51.28,51.28,0,0,0-29.24,46.16v47.62a4.91,4.91,0,0,0,9.81,0V403.47l29.47,19.47v15.91a4.9,4.9,0,1,0,9.8,0V420.3a4.94,4.94,0,0,0-2.19-4.1L147.4,391.71v-.48A41.46,41.46,0,0,1,171,353.93L194.09,343c3.6,13.29,16.53,42.13,61,42.13,44.34,0,57.33-28.66,61-42l22.8,10.78a41.44,41.44,0,0,1,23.63,37.3v.17L325,416.2a4.93,4.93,0,0,0-2.2,4.1v18.55a4.91,4.91,0,0,0,9.81,0V422.94l29.95-19.78v35.69a4.91,4.91,0,0,0,9.81,0V391.23a51.27,51.27,0,0,0-29.25-46.16l-28.22-13.35-.05,0ZM150.77,82.91c10,6.31,31.88,14.16,78.52,14.16h70.37A65.37,65.37,0,0,1,365,162.36v30.4a65.26,65.26,0,0,1-18,45V144.57a4.9,4.9,0,0,0-7.29-4.28c-79.17,44.25-169.06,17.44-170,17.16a4.9,4.9,0,0,0-6.35,4.69v51.77C154.43,201.45,140.2,167.45,150.77,82.91ZM255.11,375.34c-40.76,0-50.05-27.1-52.06-36.56l23.65-11.19c.09,0,.16-.1.24-.14a4.33,4.33,0,0,0,.51-.31,3.37,3.37,0,0,0,.35-.29,3.7,3.7,0,0,0,.36-.33,3.79,3.79,0,0,0,.31-.37c.09-.12.19-.24.27-.37a3.55,3.55,0,0,0,.23-.43c.07-.13.14-.27.2-.41s.1-.32.15-.48.08-.28.1-.43a3.68,3.68,0,0,0,.06-.57c0-.1,0-.2,0-.3s0-.06,0-.09a5.11,5.11,0,0,0-.05-.55c0-.15,0-.29-.06-.44a2.84,2.84,0,0,0-.13-.38,5.27,5.27,0,0,0-.19-.56.26.26,0,0,1,0-.08c0-.09-.1-.16-.15-.25a3.58,3.58,0,0,0-.3-.49,2.85,2.85,0,0,0-.31-.38c-.1-.11-.19-.22-.3-.32a3.26,3.26,0,0,0-.4-.34l-.34-.25c-.14-.09-.29-.16-.44-.24l-.41-.19a4,4,0,0,0-.46-.14c-.15,0-.3-.09-.46-.12a4.23,4.23,0,0,0-.5,0c-.12,0-.24,0-.36,0-28.88,0-51.5-24.91-51.5-56.72v-93c21.73,5.39,94.67,19.26,164-15.82v108.8c0,31.81-22.74,56.72-51.79,56.72-.16,0-.32,0-.48.05l-.34,0a4.25,4.25,0,0,0-.59.15l-.34.1-.49.23-.37.2c-.13.09-.26.18-.38.28s-.26.2-.38.31-.21.22-.31.34-.21.24-.3.37a4.94,4.94,0,0,0-.3.49c0,.09-.11.16-.15.25s0,.06,0,.08c-.08.18-.13.37-.19.55s-.09.26-.12.39a3.35,3.35,0,0,0-.07.44,5.11,5.11,0,0,0-.05.55s0,.06,0,.09,0,.2,0,.3a3.68,3.68,0,0,0,.06.57,3,3,0,0,0,.11.43,4.34,4.34,0,0,0,.14.48c.06.14.13.28.2.41a3.55,3.55,0,0,0,.23.43c.08.13.18.25.27.37a3.79,3.79,0,0,0,.31.37,3.7,3.7,0,0,0,.36.33,3.37,3.37,0,0,0,.35.29,4.33,4.33,0,0,0,.51.31c.08,0,.15.1.24.14l23.93,11.32C305.16,348.48,295.93,375.34,255.11,375.34Z"/>
      </svg>
    </div>
    <h6 class="mt-2 text-muted mb-1">Jumlah Laki-laki</h6>
    <h4>500</h4>
  </div>
</div>
       <div class="col-md-4 col-sm-6 col-12">
  <div class="card-info text-center">
    <div class="d-flex justify-content-center align-items-center mx-auto rounded-rectangle" 
         style="background-color:#F4D6D6; width:60px; height:60px; border-radius:10px;">
          <?xml version="1.0"?><svg data-name="Layer 1" id="Layer_1" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><title/><path d="M286.25,275.7a4.92,4.92,0,0,0-6.37-2.79,65.56,65.56,0,0,1-47.76,0,4.91,4.91,0,0,0-3.58,9.14,75.37,75.37,0,0,0,54.92,0A4.91,4.91,0,0,0,286.25,275.7Z"/><path d="M396.72,327.83l-13.57-76.95V188.3a127.15,127.15,0,0,0-254.3,0l.08,61.73-13.65,77.8a4.83,4.83,0,0,0,1,4,4.92,4.92,0,0,0,3.71,1.78l84.55,1.26-36.71,17.32a51.26,51.26,0,0,0-29.23,46.16v47.62a4.91,4.91,0,0,0,9.81,0V398.32c0-.32,0-.64,0-1L178.14,417v29a4.91,4.91,0,0,0,9.81,0V414.31a4.92,4.92,0,0,0-2.2-4.09l-35.61-23.53A41.39,41.39,0,0,1,172.06,361L195,350.2c3.65,13.34,16.64,42,61,42s57.34-28.67,61-42L339.94,361a41.46,41.46,0,0,1,21.92,25.67l-35.63,23.53a4.94,4.94,0,0,0-2.19,4.09v31.63a4.91,4.91,0,0,0,9.81,0V417l29.67-19.59c0,.32,0,.64,0,1v47.62a4.9,4.9,0,1,0,9.8,0V398.32a51.31,51.31,0,0,0-29.23-46.17l-39.38-18.57H391.9a4.9,4.9,0,0,0,4.82-5.75ZM256,382.4c-40.56,0-50-26.84-52-36.42l27.27-12.86c.12-.06.23-.15.35-.22l.37-.22a5.19,5.19,0,0,0,.45-.37,3,3,0,0,0,.26-.24,5.32,5.32,0,0,0,.38-.45,3.21,3.21,0,0,0,.21-.3,4.67,4.67,0,0,0,.27-.49,4,4,0,0,0,.17-.36c.06-.17.11-.34.16-.52s.07-.26.1-.39.05-.39.06-.58,0-.2,0-.3,0-.06,0-.09a3.31,3.31,0,0,0-.06-.54,3.47,3.47,0,0,0-.06-.45c0-.13-.08-.25-.12-.38a5.31,5.31,0,0,0-.2-.56s0-.05,0-.08-.1-.15-.15-.24a3.86,3.86,0,0,0-.31-.51,2.78,2.78,0,0,0-.26-.32,4.49,4.49,0,0,0-.36-.39,4.15,4.15,0,0,0-.32-.27,5,5,0,0,0-.44-.32,3.65,3.65,0,0,0-.34-.18,4.59,4.59,0,0,0-.5-.24c-.13-.05-.26-.08-.4-.12a4.07,4.07,0,0,0-.5-.13c-.18,0-.36,0-.54-.06s-.21,0-.32,0A55.21,55.21,0,0,1,174,268.63V180.44c16.88,13.13,57.78,41.16,104.16,45.48V247.1a4.91,4.91,0,1,0,9.81,0V226.46A112.11,112.11,0,0,0,338,216v52.6a55.21,55.21,0,0,1-55.15,55.14,3.41,3.41,0,0,0-.46.05c-.12,0-.24,0-.35,0a4.55,4.55,0,0,0-.66.17l-.24.08a4.39,4.39,0,0,0-.62.29l-.24.13a5.88,5.88,0,0,0-.51.36l-.26.22q-.21.21-.39.42l-.25.3a5.64,5.64,0,0,0-.31.53c-.05.08-.1.15-.14.23a.26.26,0,0,0,0,.08,5.31,5.31,0,0,0-.2.56c0,.13-.09.25-.12.38s0,.3-.06.45a5,5,0,0,0-.06.54s0,.06,0,.09,0,.19,0,.3,0,.38.06.58.07.26.1.39.1.35.16.52a4,4,0,0,0,.17.36c.09.17.17.33.27.49l.21.3a5.32,5.32,0,0,0,.38.45,3,3,0,0,0,.26.24,5.19,5.19,0,0,0,.45.37l.37.22c.12.07.23.16.35.22L308,346C306.05,355.55,296.82,382.4,256,382.4Zm61.11-58.63a65,65,0,0,0,30.7-55.14V208a4.91,4.91,0,0,0-7.3-4.28c-77.93,43.51-167.21-36.48-168.1-37.29a4.91,4.91,0,0,0-8.22,3.62v98.57a65,65,0,0,0,32.62,56.29l-70.88-1.06,12.73-73V188.3a117.34,117.34,0,0,1,234.68,0l.08,63.42,12.64,72Z"/><path d="M228.92,252a4.91,4.91,0,0,0,4.91-4.91V225.68a4.91,4.91,0,0,0-9.81,0V247.1A4.91,4.91,0,0,0,228.92,252Z"/></svg>
          </div>
    <h6 class="mt-2 text-muted mb-1">Jumlah Perempuan</h6>
    <h4>400</h4>
        </div>
      </div>
    </div>
 
    <!-- Kelola Data -->
    <div class="table-container">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-2">Kelola Data Siswa</h6>
        <div class="d-flex flex-wrap align-items-center gap-2">
          <div class="search-container">
            <input type="text" id="searchBox" class="search-box" placeholder="Cari ID/Nama/Kelas siswa">
            <button class="btn-cari" id="btnCari"><?xml version="1.0"?><svg id="Layer_1" style="enable-background:new 0 0 64 64;" version="1.1" viewBox="0 0 64 64" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g><g><path d="M26.402,48.286C14.325,48.286,4.5,38.461,4.5,26.384S14.325,4.482,26.402,4.482    c12.077,0,21.902,9.825,21.902,21.902S38.479,48.286,26.402,48.286z M26.402,10.012c-9.028,0-16.372,7.345-16.372,16.372    s7.345,16.372,16.372,16.372s16.372-7.345,16.372-16.372S35.43,10.012,26.402,10.012z" style="fill:#ffffff;"/></g><g><path d="M36.36,29.149c-1.527,0-2.765-1.238-2.765-2.765c0-2.431-1.215-4.681-3.25-6.018    c-1.171-0.769-2.534-1.176-3.943-1.176c-1.527,0-2.765-1.238-2.765-2.765s1.238-2.765,2.765-2.765    c2.491,0,4.904,0.721,6.979,2.084c3.597,2.363,5.744,6.34,5.744,10.639C39.125,27.911,37.887,29.149,36.36,29.149z" style="fill:#ffffff;"/></g><g><path d="M51.018,59.518c-2.266,0-4.396-0.882-5.998-2.484L33.296,45.31    c-0.622-0.622-0.911-1.504-0.778-2.374c0.133-0.87,0.673-1.625,1.453-2.032c2.975-1.553,5.371-3.943,6.929-6.91    c0.409-0.778,1.163-1.316,2.032-1.448c0.869-0.132,1.749,0.157,2.371,0.778l11.713,11.712c1.602,1.602,2.484,3.732,2.484,5.998    s-0.882,4.396-2.484,5.998C55.414,58.636,53.283,59.518,51.018,59.518z M39.642,43.836l9.288,9.287    c0.558,0.558,1.299,0.865,2.088,0.865c0.789,0,1.53-0.307,2.088-0.865c1.151-1.151,1.151-3.025,0-4.176l-9.282-9.282    C42.625,41.24,41.221,42.641,39.642,43.836z" style="fill:#ffffff;"/></g></g></svg></button>
          </div>

          <button class="btn btn-import btn-sm">
  <!-- SVG putih kecil -->
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="18" height="18" style="fill:white;">
    <polygon points="28 19 14.83 19 17.41 16.41 16 15 11 20 16 25 17.41 23.59 14.83 21 28 21 28 19"/>
    <path d="M24,14V10a1,1,0,0,0-.29-.71l-7-7A1,1,0,0,0,16,2H6A2,2,0,0,0,4,4V28a2,2,0,0,0,2,2H22a2,2,0,0,0,2-2V26H22v2H6V4h8v6a2,2,0,0,0,2,2h6v2Zm-8-4V4.41L21.59,10Z"/>
  </svg>
  Import Data
</button>
          <button class="btn btn-tambah btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-lg"></i> Tambah Data
          </button>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered align-middle" id="tabelSiswa">
          <thead class="table-light text-center">
            <tr>
              <th>ID</th>
              <th>Nama</th>
              <th>Kelas</th>
              <th>Tahun Masuk</th>
              <th>Jenis Kelamin</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody class="text-center">
            <tr>
              <td>0001</td>
              <td>Rafi</td>
              <td>XI.12</td>
              <td>2024</td>
              <td>Laki-Laki</td>
              <td>
                <button class="btn btn-link p-0 text-primary edit-btn"><i class="bi bi-pencil-square"></i></button>
                <button class="btn btn-link p-0 text-danger delete-btn"><i class="bi bi-trash"></i></button>
              </td>
            </tr>
            <tr>
              <td>0002</td>
              <td>Zahra</td>
              <td>XI.11</td>
              <td>2024</td>
              <td>Perempuan</td>
              <td>
                <button class="btn btn-link p-0 text-primary edit-btn"><i class="bi bi-pencil-square"></i></button>
                <button class="btn btn-link p-0 text-danger delete-btn"><i class="bi bi-trash"></i></button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- MODAL TAMBAH / EDIT DATA -->
  <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">

        <div class="modal-header-custom">
          <h5 class="modal-title-custom" id="modalTitle">Tambah Data Siswa</h5>
          <button type="button" class="btn-close-custom" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="modal-body">
          <form id="formTambah">
            <input type="hidden" id="editIndex">
            <div class="mb-3">
              <label class="form-label">ID</label>
              <input type="text" class="form-control" id="idSiswa" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Nama</label>
              <input type="text" class="form-control" id="namaSiswa" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Kelas</label>
              <input type="text" class="form-control" id="kelasSiswa" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Tahun Masuk</label>
              <input type="number" class="form-control" id="tahunMasuk" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Jenis Kelamin</label><br>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="gender" value="Laki-Laki" required>
                <label class="form-check-label">Laki-Laki</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="gender" value="Perempuan">
                <label class="form-check-label">Perempuan</label>
              </div>
            </div>
            <div class="text-end">
              <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Data</button>
            </div>
          </form>
        </div>

      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    const formTambah = document.getElementById('formTambah');
    const tabel = document.getElementById('tabelSiswa').querySelector('tbody');
    const modalTambah = new bootstrap.Modal(document.getElementById('modalTambah'));
    const modalTitle = document.getElementById('modalTitle');
    let editRow = null;

    // === SIMPAN / EDIT DATA ===
    formTambah.addEventListener('submit', (e) => {
      e.preventDefault();

      const id = document.getElementById('idSiswa').value;
      const nama = document.getElementById('namaSiswa').value;
      const kelas = document.getElementById('kelasSiswa').value;
      const tahun = document.getElementById('tahunMasuk').value;
      const gender = document.querySelector('input[name="gender"]:checked').value;

      if (editRow) {
        editRow.children[0].textContent = id;
        editRow.children[1].textContent = nama;
        editRow.children[2].textContent = kelas;
        editRow.children[3].textContent = tahun;
        editRow.children[4].textContent = gender;
        editRow = null;
        modalTitle.textContent = "Tambah Data Siswa";
      } else {
        const row = tabel.insertRow();
        row.innerHTML = `
          <td>${id}</td>
          <td>${nama}</td>
          <td>${kelas}</td>
          <td>${tahun}</td>
          <td>${gender}</td>
          <td>
            <button class="btn btn-link p-0 text-primary edit-btn"><i class="bi bi-pencil-square"></i></button>
            <button class="btn btn-link p-0 text-danger delete-btn"><i class="bi bi-trash"></i></button>
          </td>
        `;
      }

      modalTambah.hide();
      formTambah.reset();
    });

    // === EDIT & HAPUS DATA ===
    tabel.addEventListener('click', (e) => {
      if (e.target.closest('.delete-btn')) {
        const row = e.target.closest('tr');
        if (confirm('Yakin ingin menghapus data ini?')) row.remove();
      }

      if (e.target.closest('.edit-btn')) {
        const row = e.target.closest('tr');
        editRow = row;
        modalTitle.textContent = "Edit Data Siswa";

        document.getElementById('idSiswa').value = row.children[0].textContent;
        document.getElementById('namaSiswa').value = row.children[1].textContent;
        document.getElementById('kelasSiswa').value = row.children[2].textContent;
        document.getElementById('tahunMasuk').value = row.children[3].textContent;
        document.querySelectorAll('input[name="gender"]').forEach(r => {
          r.checked = r.value === row.children[4].textContent;
        });

        modalTambah.show();
      }
    });

    // === FITUR CARI ===
    const btnCari = document.getElementById('btnCari');
    const searchBox = document.getElementById('searchBox');

    btnCari.addEventListener('click', () => {
      const keyword = searchBox.value.trim().toLowerCase();
      const rows = tabel.querySelectorAll('tr');
      rows.forEach(row => {
        const id = row.children[0].textContent.toLowerCase();
        const nama = row.children[1].textContent.toLowerCase();
        const kelas = row.children[2].textContent.toLowerCase();
        if (id.includes(keyword) || nama.includes(keyword) || kelas.includes(keyword) || keyword === '') {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    });

    // Enter key juga menjalankan pencarian
    searchBox.addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        btnCari.click();
      }
    });
  </script>
</body>
</html>
