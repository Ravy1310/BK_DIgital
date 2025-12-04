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
        padding-top: 40px;
    }

    h4 {
        font-weight: 700;
        color: #004AAD;
    }

    h6 {
        font-size: 0.9rem;
    }

    .card-info {
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.15);
        padding: 20px;
        background: white;
        text-align: center;
        transition: transform 0.2s ease;
        height: 100%;
        min-height: 160px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    .card-info:hover { 
        transform: scale(1.03); 
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .icon-card { 
        width: 50px; 
        height: 50px; 
        object-fit: contain; 
    }

    .table-container {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        padding: 20px;
        margin-top: 20px;
    }

    /* === ANIMASI BUTTON IMPORT === */
    .btn-import {
        background-color: #38A169;
        color: white;
        border: none;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 6px;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    
    .btn-import:hover { 
        background-color: #2F855A;
        transform: translateY(-1px);
        color: white !important;
    }

    /* === ANIMASI BUTTON TAMBAH === */
    .btn-tambah {
        background-color: #0050BC;
        color: white;
        border: none;
        font-weight: 500;
        border-radius: 6px;
        padding: 6px 12px;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    
    .btn-tambah:hover { 
        background-color: #003580;
        transform: translateY(-1px);
        color: white !important;
    }

    /* === ANIMASI BUTTON CARI === */
    .btn-cari {
        background-color: #38A169; 
        border: none; 
        border-radius: 50px;
        width: 42px; 
        height: 32px; 
        display: flex; 
        align-items: center;
        justify-content: center; 
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        transition: all 0.2s ease;
        cursor: pointer;
        flex-shrink: 0;
    }
    
    .btn-cari:hover { 
        background-color: #2F855A;
        transform: scale(1.05);
    }

    /* === ANIMASI BUTTON EDIT === */
    .edit-btn {
        transition: all 0.2s ease;
        border-radius: 4px;
        padding: 4px 8px !important;
    }
    
    .edit-btn:hover {
        background-color: rgba(0, 123, 255, 0.1) !important;
        transform: scale(1.1);
    }

    /* === ANIMASI BUTTON DELETE === */
    .delete-btn {
        transition: all 0.2s ease;
        border-radius: 4px;
        padding: 4px 8px !important;
    }
    
    .delete-btn:hover {
        background-color: rgba(220, 53, 69, 0.1) !important;
        transform: scale(1.1);
    }

    /* === ANIMASI BUTTON SIMPAN === */
    .btn-primary {
        background-color: #004AAD;
        border: none;
        border-radius: 6px;
        padding: 8px 20px;
        transition: all 0.2s ease;
    }
    
    .btn-primary:hover { 
        background-color: #003580;
        transform: translateY(-1px);
    }

    /* === ANIMASI BUTTON CLOSE MODAL === */
    .btn-close-custom {
        background: none;
        border: none;
        font-size: 20px;
        color: #333;
        transition: all 0.2s ease;
        border-radius: 4px;
        padding: 4px 8px;
    }
    
    .btn-close-custom:hover { 
        color: #d11a2a;
        background-color: rgba(209, 26, 42, 0.1);
    }

    .icon-btn { 
        width: 18px; 
        height: 18px; 
        object-fit: contain; 
    }

    .search-container { 
        display: flex; 
        align-items: center; 
        gap: 8px; 
        flex: 1;
        max-width: 300px;
    }
    
    .search-box {
        width: 100%; 
        background: white; 
        border-radius: 50px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.15);
        border: 1px solid #ccc; 
        padding: 8px 16px; 
        font-size: 14px;
        outline: none; 
        transition: 0.2s;
    }
    
    .search-box:focus {
        border-color: #38A169;
        box-shadow: 0 0 4px rgba(56,161,105,0.6);
    }

    table { 
        font-size: 0.85rem; 
    }

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
        font-size: 1.1rem;
    }

    .file-upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .file-upload-area:hover {
        border-color: #38A169;
        background-color: #f8f9fa;
    }

    .file-upload-area.dragover {
        border-color: #38A169;
        background-color: #e8f5e8;
    }

    /* === RESPONSIVE DESIGN === */

    /* Tablet dan Desktop - tetap seperti semula */
    @media (min-width: 768px) {
        .header-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
        }
        
        .search-container {
            max-width: 250px;
        }
        
        .btn-import, .btn-tambah {
            font-size: 0.9rem;
        }
    }

    /* Mobile Devices (≤ 767px) */
    @media (max-width: 767.98px) {
        body {
            padding-top: 20px;
            background-attachment: scroll;
            background-size: cover;
        }
        
        .container {
            padding-left: 15px;
            padding-right: 15px;
        }
        
        /* Kartu statistik responsif */
        .row.g-4 {
            margin-left: -8px;
            margin-right: -8px;
        }
        
        .row.g-4 > [class*="col-"] {
            padding-left: 8px;
            padding-right: 8px;
            margin-bottom: 16px;
        }
        
        .card-info {
            padding: 15px 10px;
            min-height: 140px;
        }
        
        .card-info h6 {
            font-size: 0.8rem;
            margin-bottom: 5px;
        }
        
        .card-info h4 {
            font-size: 1.2rem;
        }
        
        .rounded-rectangle {
            width: 45px !important;
            height: 45px !important;
        }
        
        .rounded-rectangle svg {
            width: 25px !important;
            height: 25px !important;
        }
        
        /* Table container */
        .table-container {
            padding: 15px;
            margin-top: 10px;
        }
        
        /* Header dengan judul dan tombol */
        .table-container > .d-flex {
            flex-direction: column;
            align-items: stretch !important;
            gap: 15px;
        }
        
        .table-container h6 {
            font-size: 1rem;
            text-align: center;
            margin-bottom: 0;
        }
        
        /* Kontainer aksi (search dan tombol) */
        .header-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: 100%;
        }
        
        .search-container {
            order: 1;
            max-width: 100%;
            margin-bottom: 5px;
        }
        
        .search-box {
            padding: 10px 16px;
            font-size: 14px;
            width: 100%;
        }
        
        .btn-cari {
            width: 45px;
            height: 45px;
        }
        
        /* Tombol import dan tambah */
        .header-actions .d-flex {
            order: 2;
            display: flex;
            gap: 10px;
            justify-content: space-between;
            width: 100%;
        }
        
        .btn-import, .btn-tambah {
            flex: 1;
            justify-content: center;
            padding: 8px 12px;
            font-size: 0.85rem;
            min-width: 0;
        }
        
        .btn-import svg, .btn-tambah i {
            margin-right: 5px;
        }
        
        /* Table responsif */
        .table-responsive {
            margin: 0 -15px;
            padding: 0 15px;
            overflow-x: auto;
        }
        
        #tabelSiswa {
            min-width: 600px;
        }
        
        #tabelSiswa th,
        #tabelSiswa td {
            padding: 8px 6px;
            font-size: 0.8rem;
        }
        
        /* Modal responsif */
        .modal-dialog {
            margin: 10px;
            max-width: calc(100% - 20px);
        }
        
        .modal-header-custom {
            padding: 12px 15px;
        }
        
        .modal-title-custom {
            font-size: 1rem;
        }
        
        .modal-body {
            padding: 15px;
        }
        
        .form-control {
            padding: 8px 12px;
            font-size: 14px;
        }
        
        .form-label {
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        
        .file-upload-area {
            padding: 1.5rem 1rem;
        }
        
        .file-upload-area p {
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        
        .file-upload-area small {
            font-size: 0.8rem;
        }
        
        .alert-info ul {
            padding-left: 20px;
            margin-bottom: 10px;
        }
        
        .alert-info li {
            font-size: 0.85rem;
        }
        
        /* Tombol di modal */
        .text-end .btn {
            width: 100%;
            margin-bottom: 5px;
        }
        
        .text-end .btn-secondary {
            margin-right: 0 !important;
        }
    }

    /* Very small mobile devices (≤ 375px) */
    @media (max-width: 375px) {
        .card-info {
            padding: 12px 8px;
            min-height: 130px;
        }
        
        .card-info h6 {
            font-size: 0.75rem;
        }
        
        .card-info h4 {
            font-size: 1rem;
        }
        
        .rounded-rectangle {
            width: 40px !important;
            height: 40px !important;
        }
        
        .rounded-rectangle svg {
            width: 22px !important;
            height: 22px !important;
        }
        
        .table-container {
            padding: 12px;
        }
        
        .btn-import, .btn-tambah {
            font-size: 0.8rem;
            padding: 7px 10px;
        }
        
        .btn-import svg {
            width: 16px;
            height: 16px;
        }
        
        .search-box {
            padding: 8px 14px;
            font-size: 13px;
        }
        
        #tabelSiswa th,
        #tabelSiswa td {
            padding: 6px 4px;
            font-size: 0.75rem;
        }
        
        .modal-dialog {
            margin: 5px;
            max-width: calc(100% - 10px);
        }
    }

    /* Landscape mode untuk mobile */
    @media (max-height: 500px) and (orientation: landscape) {
        body {
            padding-top: 10px;
        }
        
        .container {
            padding-left: 10px;
            padding-right: 10px;
        }
        
        .row.g-4 {
            margin-bottom: 10px;
        }
        
        .row.g-4 > [class*="col-"] {
            margin-bottom: 10px;
        }
        
        .card-info {
            padding: 10px 8px;
            min-height: 120px;
        }
        
        .table-container {
            padding: 12px;
            margin-top: 5px;
        }
        
        .header-actions {
            flex-direction: row;
            align-items: center;
        }
        
        .search-container {
            flex: 1;
            max-width: 200px;
        }
        
        .header-actions .d-flex {
            flex: 0 0 auto;
        }
    }
</style>
</head>

<body>
  <div class="container">

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
          <h6 class="mt-2 text-muted mb-1">Jumlah Siswa</h6>
          <h4></h4>
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
    <h4></h4>
  </div>
</div>
       <div class="col-md-4 col-sm-6 col-12">
  <div class="card-info text-center">
    <div class="d-flex justify-content-center align-items-center mx-auto rounded-rectangle" 
         style="background-color:#F4D6D6; width:60px; height:60px; border-radius:10px;">
          <?xml version="1.0"?><svg data-name="Layer 1" id="Layer_1" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><title/><path d="M286.25,275.7a4.92,4.92,0,0,0-6.37-2.79,65.56,65.56,0,0,1-47.76,0,4.91,4.91,0,0,0-3.58,9.14,75.37,75.37,0,0,0,54.92,0A4.91,4.91,0,0,0,286.25,275.7Z"/><path d="M396.72,327.83l-13.57-76.95V188.3a127.15,127.15,0,0,0-254.3,0l.08,61.73-13.65,77.8a4.83,4.83,0,0,0,1,4,4.92,4.92,0,0,0,3.71,1.78l84.55,1.26-36.71,17.32a51.26,51.26,0,0,0-29.23,46.16v47.62a4.91,4.91,0,0,0,9.81,0V398.32c0-.32,0-.64,0-1L178.14,417v29a4.91,4.91,0,0,0,9.81,0V414.31a4.92,4.92,0,0,0-2.2-4.09l-35.61-23.53A41.39,41.39,0,0,1,172.06,361L195,350.2c3.65,13.34,16.64,42,61,42s57.34-28.67,61-42L339.94,361a41.46,41.46,0,0,1,21.92,25.67l-35.63,23.53a4.94,4.94,0,0,0-2.19,4.09v31.63a4.91,4.91,0,0,0,9.81,0V417l29.67-19.59c0,.32,0,.64,0,1v47.62a4.9,4.9,0,1,0,9.8,0V398.32a51.31,51.31,0,0,0-29.23-46.17l-39.38-18.57H391.9a4.9,4.9,0,0,0,4.82-5.75ZM256,382.4c-40.56,0-50-26.84-52-36.42l27.27-12.86c.12-.06.23-.15.35-.22l.37-.22a5.19,5.19,0,0,0,.45-.37,3,3,0,0,0,.26-.24,5.32,5.32,0,0,0,.38-.45,3.21,3.21,0,0,0,.21-.3,4.67,4.67,0,0,0,.27-.49,4,4,0,0,0,.17-.36c.06-.17.11-.34.16-.52s.07-.26.1-.39.05-.39.06-.58,0-.2,0-.3,0-.06,0-.09a3.31,3.31,0,0,0-.06-.54,3.47,3.47,0,0,0-.06-.45c0-.13-.08-.25-.12-.38a5.31,5.31,0,0,0-.2-.56s0-.05,0-.08-.1-.15-.15-.24a3.86,3.86,0,0,0-.31-.51,2.78,2.78,0,0,0-.26-.32,4.49,4.49,0,0,0-.36-.39,4.15,4.15,0,0,0-.32-.27,5,5,0,0,0-.44-.32,3.65,3.65,0,0,0-.34-.18,4.59,4.59,0,0,0-.5-.24c-.13-.05-.26-.08-.4-.12a4.07,4.07,0,0,0-.5-.13c-.18,0-.36,0-.54-.06s-.21,0-.32,0A55.21,55.21,0,0,1,174,268.63V180.44c16.88,13.13,57.78,41.16,104.16,45.48V247.1a4.91,4.91,0,1,0,9.81,0V226.46A112.11,112.11,0,0,0,338,216v52.6a55.21,55.21,0,0,1-55.15,55.14,3.41,3.41,0,0,0-.46.05c-.12,0-.24,0-.35,0a4.55,4.55,0,0,0-.66.17l-.24.08a4.39,4.39,0,0,0-.62.29l-.24.13a5.88,5.88,0,0,0-.51.36l-.26.22q-.21.21-.39.42l-.25.3a5.64,5.64,0,0,0-.31.53c-.05.08-.1.15-.14.23a.26.26,0,0,0,0,.08,5.31,5.31,0,0,0-.2.56c0,.13-.09.25-.12.38s0,.3-.06.45a5,5,0,0,0-.06.54s0,.06,0,.09,0,.19,0,.3,0,.38.06.58.07.26.1.39.1.35.16.52a4,4,0,0,0,.17.36c.09.17.17.33.27.49l.21.3a5.32,5.32,0,0,0,.38.45,3,3,0,0,0,.26.24,5.19,5.19,0,0,0,.45.37l.37.22c.12.07.23.16.35.22L308,346C306.05,355.55,296.82,382.4,256,382.4Zm61.11-58.63a65,65,0,0,0,30.7-55.14V208a4.91,4.91,0,0,0-7.3-4.28c-77.93,43.51-167.21-36.48-168.1-37.29a4.91,4.91,0,0,0-8.22,3.62v98.57a65,65,0,0,0,32.62,56.29l-70.88-1.06,12.73-73V188.3a117.34,117.34,0,0,1,234.68,0l.08,63.42,12.64,72Z"/><path d="M228.92,252a4.91,4.91,0,0,0,4.91-4.91V225.68a4.91,4.91,0,0,0-9.81,0V247.1A4.91,4.91,0,0,0,228.92,252Z"/></svg>
          </div>
    <h6 class="mt-2 text-muted mb-1">Jumlah Perempuan</h6>
    <h4></h4>
        </div>
      </div>
    </div>
 
    <!-- Kelola Data -->
    <div class="table-container">


<!-- Menjadi: -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <h6 class="fw-bold mb-2">Kelola Data Siswa</h6>
    <div class="header-actions">
        <div class="search-container">
            <input type="text" id="searchBox" class="search-box" placeholder="Cari ID/Nama/Kelas siswa">
            <button class="btn-cari" id="btnCari"><i class="bi bi-search"></i></button>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <button class="btn btn-import btn-sm" data-bs-toggle="modal" data-bs-target="#modalImport">
               <i class="bi bi-file-arrow-up"></i>
                Import Data
            </button>
            <button class="btn btn-tambah btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bi bi-plus-lg"></i> Tambah Data
            </button>
        </div>
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
            <input type="hidden" id="idSiswaLama">
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

  <!-- MODAL IMPORT DATA -->
  <div class="modal fade" id="modalImport" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">

        <div class="modal-header-custom">
          <h5 class="modal-title-custom">Import Data Siswa</h5>
          <button type="button" class="btn-close-custom" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="modal-body">
          <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Petunjuk Import:</strong>
            <ul class="mb-0 mt-2">
              <li>Download template terlebih dahulu</li>
              <li>Hanya file CSV yang didukung</li>
              <li>Maksimal ukuran file: 2MB</li>
              <li>Pastikan format kolom sesuai template</li>
            </ul>
          </div>

          <form id="formImport" enctype="multipart/form-data">
            <div class="mb-3">
              <label class="form-label">Download Template</label>
              <div>
                <button type="button" class="btn btn-outline-success btn-sm" id="btnDownloadTemplate">
                  <i class="bi bi-download me-2"></i>Download Template CSV
                </button>
              </div>
            </div>

            <div class="mb-3">
              <label for="fileImport" class="form-label">Pilih File CSV</label>
              <div class="file-upload-area" id="fileUploadArea">
                <i class="bi bi-cloud-arrow-up fs-1 text-muted mb-3"></i>
                <p class="mb-2">Klik untuk memilih file atau drag & drop file CSV di sini</p>
                <small class="text-muted">Format yang didukung: CSV (Comma Separated Values)</small>
                <input type="file" class="form-control d-none" id="fileImport" accept=".csv" required>
              </div>
              <div id="fileInfo" class="mt-2"></div>
            </div>

            <div class="mb-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="overwriteData">
                <label class="form-check-label" for="overwriteData">
                  Timpa data yang sudah ada (ID yang sama akan diupdate)
                </label>
              </div>
            </div>

            <div class="progress mb-3" id="uploadProgress" style="display: none; height: 20px;">
              <div class="progress-bar progress-bar-striped progress-bar-animated" 
                   role="progressbar" style="width: 0%"></div>
            </div>

            <div class="text-end">
              <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-success" id="btnSubmitImport">
                <i class="bi bi-upload me-2"></i>Import Data
              </button>
            </div>
          </form>
        </div>

      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../../includes/js/admin/Kelola_Siswa.js"></script>
</body>
</html>