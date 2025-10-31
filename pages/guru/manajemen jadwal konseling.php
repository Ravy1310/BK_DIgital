<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manajemen Jadwal Konseling</title>
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

    h2 {
      font-weight: 700;
      margin-bottom: 25px;
      color: #000;
    }

    .search-box {
      position: relative;
      margin-bottom: 20px;
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .search-box input {
      border-radius: 10px;
      padding-left: 45px;
      border: 1px solid #ccc;
    }

    .search-box svg {
      position: absolute;
      top: 50%;
      left: 35px;
      transform: translateY(-50%);
      width: 18px;
      height: 18px;
      fill: gray;
    }

    th {
      background-color: #f1f1f1;
      text-align: center;
      vertical-align: middle;
    }

    td {
      vertical-align: middle;
      text-align: center;
    }

    .status-badge {
      background-color: #ffcf5c;
      color: #000;
      padding: 5px 10px;
      border-radius: 10px;
      font-size: 0.9rem;
      font-weight: 500;
    }

    .dropdown-menu {
      min-width: 120px;
      text-align: left;
    }

    .dropdown-item svg {
      width: 18px;
      height: 18px;
      margin-right: 8px;
      vertical-align: middle;
    }

    .dropdown-item.accept {
      color: green;
    }

    .dropdown-item.reject {
      color: black;
    }

    .table-wrapper {
      border: 1px solid #ddd;
      border-radius: 10px;
      overflow: hidden;
      background: #fff;
    }
  </style>
</head>
<body>

  <div class="container">
    <h2>Manajemen Jadwal Konseling</h2>

    <div class="search-box">
      <!-- SVG ICON -->
      <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
        <path d="M221.12,389.43A173.22,173.22,0,0,1,98.25,338.61c-67.75-67.75-67.75-178,0-245.74s178-67.75,245.74,0A173.69,173.69,0,0,1,221.12,389.43Zm0-317.39a143.37,143.37,0,0,0-101.66,42c-56,56.06-56,147.26,0,203.32A143.77,143.77,0,1,0,322.78,114.08h0A143.35,143.35,0,0,0,221.12,72Z"/>
        <path d="M221.12,332.16a116.42,116.42,0,1,1,82.36-34.06A116.1,116.1,0,0,1,221.12,332.16Zm0-202.86a86.44,86.44,0,1,0,61.15,25.29A86.22,86.22,0,0,0,221.12,129.3Z"/>
        <path d="M414.82,450.44a40.78,40.78,0,0,1-29-12L302.89,355.5a15,15,0,0,1,21.22-21.22L407,417.21a11,11,0,1,0,15.55-15.55l-82.93-82.93a15,15,0,1,1,21.22-21.22l82.93,82.93a41,41,0,0,1-29,70Z"/>
      </svg>

      <input type="text" id="searchInput" class="form-control" placeholder="Cari berdasarkan Nama, Kelas, Topik...">
    </div>

    <div class="table-wrapper">
      <table class="table table-bordered mb-0">
        <thead>
          <tr>
            <th>Nama Siswa</th>
            <th>Kelas</th>
            <th>Topik Bimbingan</th>
            <th>Tanggal & Jam</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody id="dataTable">
          <tr>
            <td>Selvi</td>
            <td>XI IPS 3</td>
            <td>Masalah Tidur</td>
            <td>5 Oktober 2025 pukul 11.36</td>
            <td><span class="status-badge">Menunggu</span></td>
            <td>
              <div class="dropdown">
                <button class="btn btn-light border rounded-pill" data-bs-toggle="dropdown" aria-expanded="false">⋯</button>
                <ul class="dropdown-menu">
                  <li>
                    <a class="dropdown-item accept" href="#">
                      <!-- SVG ICON TERIMA -->
                      <svg id="Icons" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                          <linearGradient gradientUnits="userSpaceOnUse" id="linear-gradient" x1="12" x2="12" y1="0.957" y2="22.957">
                            <stop offset="0" stop-color="#71ff7b"/>
                            <stop offset="1" stop-color="#27f42c"/>
                          </linearGradient>
                        </defs>
                        <circle fill="url(#linear-gradient)" cx="12" cy="12" r="11"/>
                        <path fill="#a4ffa6" d="M10,17a1,1,0,0,1-.707-.293l-3-3a1,1,0,0,1,1.414-1.414L10,14.586l6.293-6.293a1,1,0,0,1,1.414,1.414l-7,7A1,1,0,0,1,10,17Z"/>
                      </svg>
                      Terima
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item reject" href="#">
                      <!-- SVG ICON TOLAK -->
                      <svg fill="none" height="18" viewBox="0 0 24 24" width="18" xmlns="http://www.w3.org/2000/svg">
                        <path clip-rule="evenodd" d="M18.7071 5.29289C19.0976 5.68341 19.0976 6.31658 18.7071 6.7071L13.4142 12L18.7071 17.2929C19.0976 17.6834 19.0976 18.3166 18.7071 18.7071C18.3166 19.0976 17.6834 19.0976 17.2929 18.7071L12 13.4142L6.70711 18.7071C6.31658 19.0976 5.68342 19.0976 5.29289 18.7071C4.90237 18.3166 4.90237 17.6834 5.29289 17.2929L10.5858 12L5.2929 6.70711C4.90238 6.31658 4.90238 5.68342 5.2929 5.29289C5.68342 4.90237 6.31659 4.90237 6.70711 5.29289L12 10.5858L17.2929 5.29289C17.6834 4.90237 18.3166 4.90237 18.7071 5.29289Z" fill="black" fill-rule="evenodd"/>
                      </svg>
                      Tolak
                    </a>
                  </li>
                </ul>
              </div>
            </td>
          </tr>

          <!-- Baris kosong tambahan -->
          <tr><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td></tr>
          <tr><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td></tr>
          <tr><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Fungsi pencarian tabel
    document.getElementById('searchInput').addEventListener('keyup', function() {
      let filter = this.value.toLowerCase();
      let rows = document.querySelectorAll('#dataTable tr');
      rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
      });
    });
  </script>

</body>
</html>
