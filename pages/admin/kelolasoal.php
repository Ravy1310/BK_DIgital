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
                      GROUP BY t.id_tes";
    $stmt_tes_list = $pdo->prepare($query_tes_list);
    $stmt_tes_list->execute();
    $tes_list = $stmt_tes_list->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($tes_list) > 0) {
        foreach ($tes_list as $tes) {
?>
    <div class="kelola-card mb-3 p-3 border rounded">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1"><?php echo htmlspecialchars($tes['kategori_tes']); ?></h5>
                <small class="text-muted"><?php echo htmlspecialchars($tes['deskripsi_tes']); ?></small>
                <br>
               
            </div>
            <div>
                <button type="button" class="btn btn-success px-3 btn-sm me-1 action-btn" data-edit-tes="<?php echo $tes['id_tes']; ?>">Edit</button>
                <button class="btn btn-merah btn-sm px-3" data-hapus-tes="<?php echo $tes['id_tes']; ?>">Hapus</button>
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
 <!-- Tombol kembali -->
      <div class="text-start mt-3">
        <button type="button" class="btn btn-merah px-4" onclick="loadContent('kelolaTes.php')">Kembali</button>
        
      </div>
    </div>
  </script>

</body>
</html>
