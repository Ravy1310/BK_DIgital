<?php
include 'header.php';
session_start();
require_once "../../includes/db_connection.php"; 

$id_siswa = $_GET["idsiswa"] ?? 0;
$anonim   = $_GET["anonim"] ?? 0;

// ==========================
// VALIDASI MODE
// ==========================
if ($anonim == 1) {
    $nama_siswa = "Pengguna Anonim";
} else {
    if (empty($id_siswa) || $id_siswa == 0) {
        $nama_siswa = "ID Tidak Valid";
    } else {
        $stmt = $pdo->prepare("SELECT nama FROM siswa WHERE id_siswa = ? LIMIT 1");
        $stmt->execute([$id_siswa]);
        $data_siswa = $stmt->fetch(PDO::FETCH_ASSOC);
        $nama_siswa = $data_siswa ? $data_siswa["nama"] : "ID Tidak Ditemukan";
    }
}

// ==========================
// AMBIL SEMUA RIWAYAT (TERMASUK ANONIM)
// ==========================
$stmt2 = $pdo->prepare("
    SELECT * FROM pengaduan 
    WHERE id_siswa = ? OR id_siswa IS NULL OR id_siswa = 0
    ORDER BY tanggal_pengaduan DESC
");
$stmt2->execute([$id_siswa]);
$riwayat = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Riwayat Pengaduan - BK Digital</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
body {
    background: url('../../assets/image/background.jpg') center/cover no-repeat;
    font-family: 'Poppins', sans-serif;
}
.main-wrapper {
    background: white;
    padding: 45px;
    border-radius: 24px;
    box-shadow: 0 6px 16px rgba(0,0,0,0.1);
    margin-top: 40px;
}
.judul-section {
    text-align:center;
    font-size: 32px;
    font-weight:700;
    color:#003893;
}
.pengaduan-card {
    background:white;
    border-radius:16px;
    box-shadow:0 4px 10px rgba(0,0,0,0.08);
    overflow:hidden;
    transition:0.25s;
    border:1px solid #d7e0ef;
}
.pengaduan-card:hover {
    transform: translateY(-5px);
    box-shadow:0 8px 18px rgba(0,0,0,0.15);
}
.card-header-custom {
    background:#0050BC;
    padding:12px 18px;
    color:white;
    font-size:16px;
    font-weight:600;
}
.card-body-custom {
    padding:18px;
    font-size:15px;
}
.footer-info {
    padding:12px 18px;
    background:#f5f8ff;
    font-size:14px;
    color:#555;
    display:flex;
    align-items:center;
    gap:6px;
}
.btn-primary {
    background:#003893;
    border:none;
    font-weight:600;
}
</style>
</head>

<body>
<div class="container">
    <div class="main-wrapper">

        <h2 class="judul-section mb-4">Riwayat Pengaduan</h2>

        <p><strong>Siswa:</strong> <?= htmlspecialchars($nama_siswa) ?></p>
        <p><strong>Mode:</strong> <?= $anonim == 1 ? "Anonim" : "Teridentifikasi" ?></p>

        <!-- Button untuk buka modal pengaduan -->
        <button class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#pengaduanModal">
            + Buat Pengaduan Baru
        </button>

        <h4 class="mb-3">Riwayat Pengaduan</h4>
        <hr>

        <?php if (empty($riwayat)): ?>
            <p class="text-muted">Belum ada pengaduan sebelumnya.</p>
        <?php else: ?>
            <div class="row g-4">
            <?php foreach ($riwayat as $r): ?>
                <?php  
                $status = strtolower($r["status_pengaduan"] ?? "menunggu");
                $warna = [
                    "menunggu" => "bg-secondary",
                    "diproses" => "bg-warning text-dark",
                    "selesai"  => "bg-success",
                ];
                ?>
                <div class="col-md-4">
                    <div class="pengaduan-card">
                        <div class="card-header-custom d-flex justify-content-between">
                            <span>Pengaduan</span>
                            <span class="badge <?= $warna[$status] ?? 'bg-secondary' ?>">
                                <?= htmlspecialchars($r["status_pengaduan"]) ?>
                            </span>
                        </div>
                        <div class="card-body-custom">
                            <?= nl2br(htmlspecialchars($r["isi_aduan"])) ?>
                        </div>
                        <div class="footer-info">
                            <i class="bi bi-calendar-event"></i>
                            <?= $r["tanggal_pengaduan"] ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ================= MODAL PENGADUAN ================= -->
<div class="modal fade" id="pengaduanModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Buat Pengaduan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formPengaduan" method="POST" action="../../includes/pengaduan_controller.php">
        <div class="modal-body p-3">
            <input type="hidden" name="id_siswa" value="<?= $anonim ? 0 : $id_siswa ?>">
            <input type="hidden" id="anonimInput" name="anonim" value="<?= $anonim ?>">

            <div class="mb-2">
                <label class="form-label">Jenis Laporan</label>
                <select id="jenisAduan" name="jenis_laporan" class="form-select form-select-sm" required>
                    <option disabled selected>Pilih jenis laporan</option>
                    <option value="Anonim" <?= $anonim?'selected':'' ?>>Anonim</option>
                    <option value="Teridentifikasi" <?= !$anonim?'selected':'' ?>>Teridentifikasi</option>
                </select>
            </div>

            <div class="mb-2">
                <label class="form-label">Jenis Kejadian</label>
                <select name="jenis_kejadian" class="form-select form-select-sm" required>
                    <option disabled selected>Pilih jenis kejadian</option>
                    <option value="Bully">Bully</option>
                    <option value="Kekerasan Fisik">Kekerasan Fisik</option>
                    <option value="Kekerasan Verbal">Kekerasan Verbal</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>

            <div class="mb-2">
                <label class="form-label">Penjelasan</label>
                <textarea name="penjelasan" class="form-control form-control-sm" rows="4" required></textarea>
            </div>
        </div>
        <div class="modal-footer p-2">
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
            <button id="confirmSendBtn" type="button" class="btn btn-primary btn-sm">Kirim</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById("jenisAduan").addEventListener("change", e => {
    document.getElementById("anonimInput").value = e.target.value === "Anonim" ? 1 : 0;
});
document.getElementById("confirmSendBtn").onclick = ()=> {
    const f = document.getElementById("formPengaduan");
    if(!f.checkValidity()){
        alert("Semua field wajib diisi!");
        return;
    }
    f.submit();
};
</script>
</body>
</html>
