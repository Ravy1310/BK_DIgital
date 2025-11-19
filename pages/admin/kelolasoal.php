<?php
session_start();

// CEK LOGIN
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../../login.php");
    exit;
}

// CEK ROLE
if ($_SESSION['admin_role'] !== 'admin' && $_SESSION['admin_role'] !== 'superadmin') {
    header("Location: ../../login.php?error=unauthorized");
    exit;
}

// Sesuaikan base_dir jika perlu; ini tidak wajib jika kamu sudah include db_connection di sini.
$base_dir = $_SERVER['DOCUMENT_ROOT'] . '/BK_DIGITAL/';
require_once $base_dir . 'includes/db_connection.php';

// CEGAH CACHING
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Tes BK | Tes BK Digital</title>
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
      padding-top: 30px;
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
    }
    .btn-merah:hover {
       background-color: #710303ff !important;
      transform: translateY(-1px);
    }
    .btn-primary {
      background-color: #0066cc;
      border: none;
    }
    .kelola-card {
      background-color: #fff;
      transition: 0.3s;
    }
    .kelola-card:hover {
      background-color: #f8f9fa;
      transform: scale(1.01);
    }
  </style>
</head>

<body>

<div class="container my-3">
  <div class="card p-4">

    <h4 class="fw-bold mb-3">Kelola Tes BK</h4>
    <p class="text-muted">Ubah atau hapus jenis tes yang tersedia.</p>

   <?php
try {
    // Query untuk mengambil data tes beserta jumlah soal
    $query_tes_list = "SELECT t.*, COUNT(s.id_soal) as jumlah_soal 
                      FROM tes t 
                      LEFT JOIN soal_tes s ON t.id_tes = s.id_tes 
                      GROUP BY t.id_tes
                      ORDER BY t.kategori_tes ASC";
    $stmt_tes_list = $pdo->prepare($query_tes_list);
    $stmt_tes_list->execute();
    $tes_list = $stmt_tes_list->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($tes_list) > 0) {
        foreach ($tes_list as $tes) {
            // pastikan index ada
            $id_tes = (int) $tes['id_tes'];
            $kategori = htmlspecialchars($tes['kategori_tes']);
            $deskripsi = htmlspecialchars($tes['deskripsi_tes'] ?? '');
            $jumlah = (int) $tes['jumlah_soal'];
?>
    <div class="kelola-card mb-3 p-3 border rounded">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1"><?= $kategori ?></h5>
                <small class="text-muted"><?= $deskripsi ?></small>
                <div class="small text-muted">Jumlah soal: <?= $jumlah ?></div>
            </div>
            <div>
                <!-- Tombol membuka Kelola Soal (edit soal) -->
                <button type="button"
                        class="btn btn-success px-3 btn-sm me-1"
                        onclick="window.location.href='editsoal.php?id_tes=<?= $id_tes ?>&tes=<?= urlencode($kategori) ?>'">
                   Edit
                </button>

                <!-- Tombol hapus -->
                <button class="btn btn-merah btn-sm px-3"
                        onclick="hapusTes(<?= $id_tes ?>, this)">
                    Hapus
                </button>
            </div>
        </div>
    </div>
<?php
        }
    } else {
        echo '<div class="alert alert-info">Belum ada tes yang tersedia.</div>';
    }
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Gagal memuat data tes: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>
    <!-- Tombol kembali (opsional) -->
    <div class="text-start mt-3">
      <button type="button" class="btn btn-secondary px-4" onclick="window.history.back()">Kembali</button>
    </div>

  </div>
</div>

<!-- SCRIPT: fungsi hapus ajax -->
<script>
/**
 * hapusTes(id, btn)
 * id: integer id_tes
 * btn: elemen tombol (dipakai untuk men-disable)
 */
function hapusTes(id, btn) {
    if (!confirm("Yakin ingin menghapus tes ini beserta semua soalnya? Tindakan ini tidak bisa dibatalkan.")) {
        return;
    }

    // disable tombol sementara
    if (btn) { btn.disabled = true; btn.textContent = 'Menghapus...'; }

    fetch('../../includes/admin_control/HapusTes_Controller.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id_tes=' + encodeURIComponent(id)
    })
    .then(response => response.json())
    .then(json => {
        if (json.success) {
            alert(json.message || 'Tes berhasil dihapus.');
            // reload halaman utk update daftar
            window.location.reload();
        } else {
            alert(json.message || 'Gagal menghapus tes.');
            if (btn) { btn.disabled = false; btn.textContent = 'Hapus'; }
        }
    })
    .catch(err => {
        console.error(err);
        alert('Terjadi kesalahan saat menghapus.');
        if (btn) { btn.disabled = false; btn.textContent = 'Hapus'; }
    });
}
</script>

</body>
</html>
