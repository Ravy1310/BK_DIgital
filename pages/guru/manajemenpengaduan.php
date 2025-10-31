<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manajemen Pengaduan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
      background-color: transparent;
    }

    body {
      background-image: url('background.jpg');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      font-family: 'Poppins', sans-serif;
      display: flex;
      justify-content: center;
      align-items: flex-start; /* ubah ke center kalau mau box di tengah layar */
      min-height: 100vh;
    }

    .container-box {
      background-color: #fff;
      border-radius: 10px;
      padding: 25px;
      margin-top: 40px;
      margin-bottom: 0 !important;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    h4 {
      font-weight: 600;
      margin-bottom: 20px;
    }

    .search-bar {
      border: 1px solid #dcdcdc;
      border-radius: 8px;
      padding: 10px 15px;
      display: flex;
      align-items: center;
      background-color: #fff;
      margin-bottom: 20px;
      gap: 10px;
    }

    .search-bar input {
      border: none;
      outline: none;
      width: 100%;
    }

    .status-btn {
      border-radius: 20px;
      padding: 5px 12px;
      font-size: 0.85rem;
      border: none;
    }

    .status-process {
      background-color: #007bff;
      color: #fff;
    }

    .action-link {
      color: #28a745;
      text-decoration: none;
      font-weight: 500;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      cursor: pointer;
    }

    .action-link svg {
      width: 16px;
      height: 16px;
      fill: #28a745;
      transition: transform 0.2s ease;
    }

    .action-link:hover svg {
      transform: scale(1.2);
    }

    .action-link:hover {
      text-decoration: underline;
    }

    @media (max-width: 768px) {
      .container-box {
        padding: 15px;
      }
      .table {
        font-size: 0.9rem;
      }
    }
  </style>
</head>
<body>

  <div class="container container-box">
    <h4>Manajemen Pengaduan</h4>

    <!-- Search bar -->
    <div class="search-bar">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="18">
        <path d="M344.5,298c15-23.6,23.8-51.6,23.8-81.7c0-84.1-68.1-152.3-152.1-152.3
        C132.1,64,64,132.2,64,216.3c0,84.1,68.1,152.3,152.1,152.3c30.5,0,58.9-9,82.7-24.4l6.9-4.8L414.3,448l33.7-34.3L339.5,305.1
        L344.5,298z M301.4,131.2c22.7,22.7,35.2,52.9,35.2,85c0,32.1-12.5,62.3-35.2,85c-22.7,22.7-52.9,35.2-85,35.2
        c-32.1,0-62.3-12.5-85-35.2c-22.7-22.7-35.2-52.9-35.2-85c0-32.1,12.5-62.3,35.2-85c22.7-22.7,52.9-35.2,85-35.2
        C248.5,96,278.7,108.5,301.4,131.2z"/>
      </svg>

      <input type="text" id="searchInput" placeholder="Cari berdasarkan Subjek, Nama...">
    </div>

    <!-- Table -->
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-light text-center">
          <tr>
            <th>Subjek</th>
            <th>Pelapor</th>
            <th>Tanggal</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody id="complaintTable">
          <tr>
            <td>Kesulitan dalam mengerjakan tugas kelompok</td>
            <td>Budi Santoso</td>
            <td>4 Oktober 2025 pukul 11:23</td>
            <td class="text-center"><span class="status-btn status-process">Diproses</span></td>
            <td class="text-center">
              <a class="action-link" data-bs-toggle="modal" data-bs-target="#detailModal" 
                 data-subject="Kesulitan dalam mengerjakan tugas kelompok" 
                 data-reporter="Budi Santoso" 
                 data-date="4 Oktober 2025 pukul 11:23" 
                 data-message="Saya merasa sulit bergaul dengan anggota kelompok saya.">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                  <path d="M12,1C5.9,1,1,5.9,1,12s4.9,11,11,11s11-4.9,11-11S18.1,1,12,1z 
                  M17,14h-3v3c0,1.1-0.9,2-2,2s-2-0.9-2-2v-3H7c-1.1,0-2-0.9-2-2
                  c0-1.1,0.9-2,2-2h3V7c0-1.1,0.9-2,2-2s2,0.9,2,2v3h3
                  c1.1,0,2,0.9,2,2C19,13.1,18.1,14,17,14z"/>
                </svg>
                Lihat detail
              </a>
            </td>
          </tr>

          <tr><td colspan="5" class="text-center text-muted">-</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-semibold" id="detailModalLabel">Detail Pengaduan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <h6 id="subjectText" class="fw-bold"></h6>

        <div class="d-flex flex-column gap-1 text-muted mb-2">

          <div class="d-flex align-items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24"
              viewBox="0 0 24 24" width="20" height="20" fill="#6c757d">
              <circle cx="12" cy="8" r="4" />
              <path d="M12,14c-6.1,0-8,4-8,4v2h16v-2C20,18,18.1,14,12,14z" />
            </svg>
            <span id="reporterText"></span>
          </div>

          <div class="d-flex align-items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"
              width="18" height="18" fill="#6c757d">
              <path
                d="M14,0h-2v1H4V0H2v1H0v3v1v10v1h1h15v-1V5V4V1h-2V0z M15,5v10H1V5H15z" />
              <rect height="3" width="3" x="11" y="6" />
              <rect height="3" width="3" x="11" y="10" />
              <rect height="3" width="3" x="7" y="10" />
              <rect height="3" width="3" x="3" y="10" />
              <rect height="3" width="3" x="7" y="6" />
              <rect height="3" width="3" x="3" y="6" />
            </svg>
            <span id="dateText"></span>
          </div>
        </div>

        <div class="border rounded p-3 mt-2 bg-light">
          <p id="messageText" class="mb-0"></p>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-success">Ubah ke selesai</button>
      </div>
    </div>
  </div>
</div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Fitur pencarian
    document.getElementById("searchInput").addEventListener("keyup", function() {
      let input = this.value.toLowerCase();
      let rows = document.querySelectorAll("#complaintTable tr");
      rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(input) ? "" : "none";
      });
    });

    // Isi modal dengan data dari atribut
    const detailModal = document.getElementById('detailModal');
    detailModal.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      document.getElementById('subjectText').innerText = button.getAttribute('data-subject');
      document.getElementById('reporterText').innerText = "Pelapor: " + button.getAttribute('data-reporter');
      document.getElementById('dateText').innerText = button.getAttribute('data-date');
      document.getElementById('messageText').innerText = button.getAttribute('data-message');
    });
  </script>

</body>
</html>
