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
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Tes BK | Tes BK Digital</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
      border-radius: 8px;
      padding: 8px 20px;
      transition: all 0.3s ease;
    }
    .btn-merah:hover {
      background-color: #710303 !important;
      transform: translateY(-2px);
    }
    .btn-success {
      border-radius: 8px;
      padding: 8px 20px;
      transition: all 0.3s ease;
    }
    .btn-success:hover {
      transform: translateY(-2px);
    }
    .btn-warning {
      border-radius: 8px;
      padding: 8px 20px;
      transition: all 0.3s ease;
    }
    .btn-warning:hover {
      transform: translateY(-2px);
    }
    .kelola-card {
      background-color: #fff;
      transition: 0.3s;
    }
    .kelola-card:hover {
      background-color: #f8f9fa;
      transform: scale(1.01);
    }
    .btn-loading {
      position: relative;
      color: transparent !important;
    }
    .btn-loading::after {
      content: '';
      position: absolute;
      width: 16px;
      height: 16px;
      top: 50%;
      left: 50%;
      margin-left: -8px;
      margin-top: -8px;
      border: 2px solid #ffffff;
      border-radius: 50%;
      border-right-color: transparent;
      animation: spin 1s linear infinite;
    }
    .status-badge {
      font-size: 0.75rem;
      padding: 4px 8px;
      border-radius: 12px;
    }
    .status-aktif {
      background-color: #d4edda;
      color: #155724;
    }
    .status-nonaktif {
      background-color: #f8d7da;
      color: #721c24;
    }
    @keyframes spin {
      to { transform: rotate(360deg); }
    }
  </style>
</head>

<body>

<div class="container my-3">
  <div class="card p-4">

    <h4 class="fw-bold mb-3">Kelola Tes BK</h4>
    <p class="text-muted">Ubah, hapus, atau aktifkan/nonaktifkan jenis tes yang tersedia.</p>

   <?php
try {
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
            $id_tes = (int) $tes['id_tes'];
            $kategori = htmlspecialchars($tes['kategori_tes']);
            $deskripsi = htmlspecialchars($tes['deskripsi_tes'] ?? '');
            $jumlah = (int) $tes['jumlah_soal'];
            $status = $tes['status'];
            $status_text = $status === 'aktif' ? 'Aktif' : 'Nonaktif';
            $status_class = $status === 'aktif' ? 'status-aktif' : 'status-nonaktif';
?>
    <div class="kelola-card mb-3 p-3 border rounded" id="tes-card-<?= $id_tes ?>">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <div class="d-flex align-items-center mb-1">
                    <h5 class="mb-0 me-2"><?= $kategori ?></h5>
                    <span class="status-badge <?= $status_class ?>"><?= $status_text ?></span>
                </div>
                <small class="text-muted"><?= $deskripsi ?></small>
                <div class="small text-muted">Jumlah soal: <?= $jumlah ?></div>
            </div>
            <div class="d-flex gap-2">
                <!-- Tombol Aktif/Nonaktif -->
                <?php if ($status === 'aktif'): ?>
                    <button class="btn btn-warning px-3 btn-toggle-status" 
                            data-tes-id="<?= $id_tes ?>"
                            data-action="nonaktif">
                        <i class="fas fa-pause me-1"></i>Nonaktifkan
                    </button>
                <?php else: ?>
                    <button class="btn btn-success px-3 btn-toggle-status" 
                            data-tes-id="<?= $id_tes ?>"
                            data-action="aktif">
                        <i class="fas fa-play me-1"></i>Aktifkan
                    </button>
                <?php endif; ?>

                <!-- Tombol Edit -->
                <button class="btn btn-success px-3 btn-edit-tes" 
                       data-tes-id="<?= $id_tes ?>">
                    <i class="fas fa-edit me-1"></i>Edit
                </button>

                <!-- Tombol Hapus -->
                <button class="btn btn-merah px-3 btn-hapus-tes" 
                        data-tes-id="<?= $id_tes ?>"
                        data-tes-name="<?= htmlspecialchars($kategori) ?>">
                    <i class="fas fa-trash me-1"></i>Hapus
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
    <!-- Tombol kembali -->
    <div class="text-start mt-3">
      <button type="button" class="btn btn-merah px-4" id="btn-kembali">Kembali</button>
    </div>

  </div>
</div>
<script>
// Hanya sediakan fungsi hapusTes saja
window.hapusTes = function(id, btn) {
    const tesName = btn.getAttribute('data-tes-name') || 'Tes';
    
    console.log('🗑️ Menghapus tes ID:', id);
    
    // Tampilkan loading state
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menghapus...';
    btn.disabled = true;

    fetch('../../includes/admin_control/HapusTes_Controller.php', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id_tes=' + encodeURIComponent(id)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(json => {
        if (json.success) {
            alert(json.message || 'Tes berhasil dihapus.');
            
            // Hapus card dari DOM
            const tesCard = document.getElementById(`tes-card-${id}`);
            if (tesCard) {
                tesCard.style.transition = 'all 0.3s ease';
                tesCard.style.opacity = '0';
                tesCard.style.height = '0';
                tesCard.style.margin = '0';
                tesCard.style.padding = '0';
                tesCard.style.overflow = 'hidden';
                
                setTimeout(() => {
                    tesCard.remove();
                    
                    // Jika tidak ada tes lagi, reload content
                    const remainingCards = document.querySelectorAll('[id^="tes-card-"]');
                    if (remainingCards.length === 0) {
                        setTimeout(() => {
                            window.loadContent('kelolaTes.php');
                        }, 1000);
                    }
                }, 300);
            } else {
                window.loadContent('kelolasoal.php');
            }
        } else {
            throw new Error(json.message || 'Gagal menghapus tes.');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Terjadi kesalahan saat menghapus: ' + err.message);
        btn.innerHTML = originalHTML;
        btn.disabled = false;
    });
};

console.log('✅ kelolasoal.php loaded - hanya hapusTes function available');
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>