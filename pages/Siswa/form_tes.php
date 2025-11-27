<?php 
session_start();
require_once __DIR__ . "/../../includes/db_connection.php";

// Ambil ID tes
$id_tes = isset($_GET['id']) ? $_GET['id'] : 0;

// Ambil id siswa dari session
$id_siswa = isset($_SESSION['id_siswa']) ? $_SESSION['id_siswa'] : "";

// Jika siswa belum verifikasi
if (empty($id_siswa)) {
    header("Location: form.php");
    exit;
}

// Ambil daftar soal berdasarkan id_tes
$stmt = $pdo->prepare("SELECT * FROM soal_tes WHERE id_tes = ? ORDER BY id_soal ASC");
$stmt->execute([$id_tes]);
$soal_list = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soal Tes Bakat Minat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { background: #e9f0fa; padding-top: 40px; }
        .title-box { background: #003893; color: white; padding: 12px; border-radius: 8px; }
        .question-card {
            background: #f7f7f7; padding: 25px; border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        .question-box { padding: 10px 0; border-bottom: 1px solid #ccc; margin-bottom: 15px; }
        .question-box:last-child { border-bottom: none; }
        .scale-number { font-size: 13px; color: #444; }
    </style>
</head>

<body>

<div class="container">

    <!-- FORM -->
    <form action="submit_tes.php" method="POST" onsubmit="gabungJawaban()">

        <!-- Kirim id tes -->
        <input type="hidden" name="id_tes" value="<?= $id_tes ?>">

        <!-- Kirim id siswa -->
        <input type="hidden" name="id_siswa" value="<?= $id_siswa ?>">

        <!-- Kirim jawaban gabungan -->
        <input type="hidden" name="jawaban" id="jawaban">

        <!-- Judul -->
        <div class="text-center mb-4">
            <div class="title-box">
                <h4 class="m-0">Soal Tes Bakat Minat</h4>
            </div>
        </div>

        <!-- Card Soal -->
        <div class="question-card">

            <?php if (count($soal_list) > 0): ?>
                <?php foreach ($soal_list as $index => $soal): ?>
                    <div class="question-box">
                        <p><strong>Pertanyaan <?= $index + 1 ?>:</strong> <?= htmlspecialchars($soal['pertanyaan']) ?></p>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="scale-number">1</span>

                            <?php for ($j=1; $j<=5; $j++): ?>
                                <label>
                                    <input type="radio" 
                                           name="q<?= $soal['id_soal'] ?>" 
                                           value="<?= $j ?>" 
                                           required>
                                </label>
                            <?php endfor; ?>

                            <span class="scale-number">5</span>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php else: ?>
                <p class="text-danger text-center">Soal belum tersedia untuk tes ini.</p>
            <?php endif; ?>

        </div>

        <!-- Tombol -->
        <div class="d-flex justify-content-between align-items-center mt-3 mb-5">
            <button type="submit" class="btn btn-primary px-4">Submit</button>
            <button type="reset" class="btn btn-link text-danger">Clear Form</button>
        </div>

    </form>
</div>

<script>
function gabungJawaban() {
    let hasil = "";

    document.querySelectorAll(".question-box").forEach((box, index) => {
        let input = box.querySelector("input[type='radio']:checked");
        let soalId = input ? input.name.replace("q", "") : "-";
        let value = input ? input.value : "-";

        hasil += `SoalID ${soalId}: ${value}\n`;
    });

    document.getElementById("jawaban").value = hasil;
}
</script>

</body>
</html>
