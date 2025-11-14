<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi NIS - BK Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #f5f7fb;
        }
        .shield {
            width: 200px;
        }
        .card-custom {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        h2 {
            font-weight: 700;
            color: #003893;
        }
        .btn-custom {
            background-color: #003893;
            color: white;
            padding: 10px 25px;
            border-radius: 6px;
        }
        .btn-custom:hover {
            background-color: #002d73;
        }
    </style>
</head>

<body>

<div class="container py-5">
    <div class="row justify-content-center align-items-center">

        <!-- Kiri -->
        <div class="col-md-5 text-center">
            <img class="shield" src="data:image/svg+xml;utf8,<?xml version='1.0'?><svg xmlns='http://www.w3.org/2000/svg' width='48' height='48' viewBox='0 0 48 48'><path fill='%23002E6E' d='M24 2l-18 8v12c0 11.11 7.67 21.47 18 24 10.33-2.53 18-12.89 18-24v-12l-18-8zm0 21.98h14c-1.06 8.24-6.55 15.58-14 17.87v-17.85h-14v-11.4l14-6.22v17.6z'/><path fill='none' d='M0 0h48v48h-48z'/></svg>"
     alt="ikon biru">
            <h4 class="fw-bold">Verifikasi NIS</h4>
            <p class="mt-4">
                Anda Akan Melakukan:<br>
                <span class="fw-bold">(Test Minat Belajar)</span>
            </p>
        </div>

        <!-- Kanan -->
        <div class="col-md-6">
            <div class="card-custom">
                <h2 class="text-center">MASUKKAN<br>NOMER INDUK SISWA (NIS)</h2>

                <p class="text-center mt-2 mb-4">
                    NIS diperlukan untuk menyimpan data tes anda agar tercatat dengan benar
                </p>

                <input type="text" class="form-control mb-3" placeholder="Nomor Induk Siswa (NIS)">
                <div class="text-center">
                    <button class="btn btn-custom">Mulai Test Sekarang</button>
                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>
