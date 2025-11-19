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

// AMBIL DATA POST
$id_soal = intval($_POST['id_soal']);
$id_tes  = intval($_POST['id_tes']);
$pertanyaan = trim($_POST['pertanyaan']);

$opsiA = trim($_POST['opsi_a']);
$opsiB = trim($_POST['opsi_b']);
$opsiC = trim($_POST['opsi_c']);

$jawaban_benar = $_POST['jawaban_benar'];

// VALIDASI
if ($id_soal <= 0 || empty($pertanyaan) || empty($opsiA) || empty($opsiB) || empty($opsiC)) {
    die("Data tidak lengkap");
}

// DB
$base_dir = $_SERVER['DOCUMENT_ROOT'] . '/BK_DIGITAL/';
require_once $base_dir . "includes/db_connection.php";

try {
    $pdo->beginTransaction();

    // UPDATE PERTANYAAN
    $updateSoal = $pdo->prepare("UPDATE soal_tes SET pertanyaan = ? WHERE id_soal = ?");
    $updateSoal->execute([$pertanyaan, $id_soal]);

    // AMBIL ID OPSI (selalu urut ASC)
    $getOpsi = $pdo->prepare("SELECT id_opsi FROM opsi_jawaban WHERE id_soal = ? ORDER BY id_opsi ASC");
    $getOpsi->execute([$id_soal]);
    $opsi_list = $getOpsi->fetchAll(PDO::FETCH_COLUMN);

    // JIKA OPSI KURANG DARI 3 → TAMBAHKAN
    if (count($opsi_list) < 3) {
        $insertOpsi = $pdo->prepare("
            INSERT INTO opsi_jawaban (id_soal, opsi, bobot) 
            VALUES (?, ?, 0)
        ");

        while (count($opsi_list) < 3) {
            $insertOpsi->execute([$id_soal, '-', 0]);
            $opsi_list[] = $pdo->lastInsertId();
        }
    }

    // TENTUKAN BOBOT (A=2, B=1, C=0)
    $bobotA = ($jawaban_benar == "A") ? 2 : 0;
    $bobotB = ($jawaban_benar == "B") ? 2 : 0;
    $bobotC = ($jawaban_benar == "C") ? 2 : 0;

    // UPDATE OPSI
    $updateOpsi = $pdo->prepare("
        UPDATE opsi_jawaban 
        SET opsi = ?, bobot = ?
        WHERE id_opsi = ? AND id_soal = ?
    ");

    $updateOpsi->execute([$opsiA, $bobotA, $opsi_list[0], $id_soal]);
    $updateOpsi->execute([$opsiB, $bobotB, $opsi_list[1], $id_soal]);
    $updateOpsi->execute([$opsiC, $bobotC, $opsi_list[2], $id_soal]);

    $pdo->commit();

    header("Location: ../../pages/Admin/kelolasoal.php?id_tes=" . $id_tes . "&status=success");
    exit;

} catch (Exception $e) {

    $pdo->rollBack();
    die("Gagal update soal: " . $e->getMessage());
}
