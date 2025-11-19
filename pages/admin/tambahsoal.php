<?php
session_start();

// CEK LOGIN
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../../login.php");
    exit;
}
if ($_SESSION['admin_role'] !== 'admin' && $_SESSION['admin_role'] !== 'superadmin') {
    header("Location: ../../login.php?error=unauthorized");
    exit;
}

// ===========================
// AMBIL ID TES
// ===========================
$id_tes = isset($_GET['id_tes']) ? intval($_GET['id_tes']) : 0;
if ($id_tes <= 0) die("<h1 style='color:red'>ERROR: id_tes tidak dikirim</h1>");

$base_dir = $_SERVER['DOCUMENT_ROOT'] . '/BK_DIGITAL/';
require_once $base_dir . 'includes/db_connection.php';

// Ambil data tes
$stmt_tes = $pdo->prepare("SELECT kategori_tes FROM tes WHERE id_tes = ?");
$stmt_tes->execute([$id_tes]);
$tesData = $stmt_tes->fetch(PDO::FETCH_ASSOC);
if (!$tesData) die("<h1 style='color:red'>Tes tidak ditemukan di database.</h1>");
$tes = $tesData['kategori_tes'];

$error = "";

// ===========================
// PROSES SIMPAN SOAL
// ===========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pertanyaan = trim($_POST['pertanyaan'] ?? '');
    $opsi = $_POST['opsi'] ?? [];
    $bobot = $_POST['bobot'] ?? [];

    $opsi = array_map('trim', $opsi);
    $bobot = array_map('intval', $bobot);

    $validCount = 0;
    foreach ($opsi as $o) if ($o !== '') $validCount++;

    if (!$pertanyaan || $validCount < 2) {
        $error = "Pertanyaan dan minimal 2 opsi wajib diisi.";
    } else {
        try {
            $pdo->beginTransaction();

            // Simpan soal
            $stmt = $pdo->prepare("INSERT INTO soal_tes (id_tes, pertanyaan, created_at) VALUES (?, ?, NOW())");
            $stmt->execute([$id_tes, $pertanyaan]);
            $id_soal = $pdo->lastInsertId();

            // Simpan opsi jawaban
            foreach ($opsi as $i => $opsi_teks) {
                if ($opsi_teks === '') continue;
                $b = $bobot[$i] ?? 1;
                $stmt_opsi = $pdo->prepare(
                    "INSERT INTO opsi_jawaban (id_soal, opsi, bobot, created_at) VALUES (?, ?, ?, NOW())"
                );
                $stmt_opsi->execute([$id_soal, $opsi_teks, $b]);
            }

            $pdo->commit();

            // Redirect ke kelola soal (atau edit soal)
            header("Location: kelolaSoal.php?id_tes=$id_tes");
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Gagal menyimpan soal: " . $e->getMessage();
        }
    }
}

// ===========================
// Ambil opsi lama (untuk edit, kosongkan jika tambah baru)
// ===========================
$old_opsi = ['', '']; // minimal 2
$old_bobot = [1,1];

?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tambah Soal | <?= htmlspecialchars($tes) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { font-family: 'Poppins', sans-serif; background: #f5f5f5; padding: 30px; }
.card { border-radius: 14px; }
.opsi-item { display: flex; gap: 10px; margin-bottom: 10px; }
.opsi-item input[type=text] { flex: 1; }
.bobot-input { width: 90px; }
</style>
</head>
<body>
<div class="container">
<div class="card p-4">

<h4 class="fw-bold mb-3">Tambah Soal untuk Tes: <?= htmlspecialchars($tes) ?></h4>
<?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

<form method="POST">

    <div class="mb-3">
        <label class="form-label">Pertanyaan</label>
        <textarea name="pertanyaan" class="form-control" required><?= htmlspecialchars($_POST['pertanyaan'] ?? '') ?></textarea>
    </div>

    <div id="opsi-wrapper" class="mb-3">
        <label class="form-label">Opsi Jawaban + Bobot</label>

        <?php
        foreach ($old_opsi as $i => $o):
        ?>
        <div class="opsi-item">
            <input type="text" name="opsi[]" class="form-control" placeholder="Opsi jawaban" value="<?= htmlspecialchars($o) ?>" required>
            <input type="number" name="bobot[]" class="form-control bobot-input" min="1" max="10" value="<?= intval($old_bobot[$i] ?? 1) ?>" required>
            <button type="button" class="btn btn-danger" onclick="hapusOpsi(this)">×</button>
        </div>
        <?php endforeach; ?>
    </div>

    <button type="button" class="btn btn-secondary mb-3" onclick="tambahOpsi()">+ Tambah Opsi</button>

    <div class="d-flex justify-content-between mt-4 pt-3 border-top">
        <a href="kelolaSoal.php?id_tes=<?= $id_tes ?>" class="btn btn-danger">Kembali</a>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>
</div>
</div>

<script>
function tambahOpsi() {
    const wrap = document.getElementById('opsi-wrapper');
    const div = document.createElement('div');
    div.className = 'opsi-item';
    div.innerHTML = `
        <input type="text" name="opsi[]" class="form-control" placeholder="Opsi jawaban" required>
        <input type="number" name="bobot[]" class="form-control bobot-input" min="1" max="10" value="1" required>
        <button type="button" class="btn btn-danger" onclick="hapusOpsi(this)">×</button>
    `;
    wrap.appendChild(div);
}
function hapusOpsi(btn) {
    btn.closest('.opsi-item').remove();
}
</script>

</body>
</html>
