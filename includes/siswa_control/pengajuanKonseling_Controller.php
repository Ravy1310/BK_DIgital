<?php
// controller/pengajuan_konseling.php

// Set header JSON pertama
header('Content-Type: application/json');

// Start session dan include database
session_start();

// Cek path database connection
$db_path_1 = __DIR__ . "/../db_connection.php";
$db_path_2 = __DIR__ . "/../../db_connection.php";

if (file_exists($db_path_1)) {
    require_once $db_path_1;
} elseif (file_exists($db_path_2)) {
    require_once $db_path_2;
} else {
    echo json_encode(['success' => false, 'message' => 'Database connection file not found']);
    exit;
}

try {
    // Validasi session
    if (!isset($_SESSION['siswa_logged_in'])) {
        throw new Exception("Anda harus login terlebih dahulu");
    }

    $id_siswa = $_SESSION['siswa_id'];

    // ======================
    // TANGANI JADWALKAN ULANG
    // ======================
    if (isset($_POST['action']) && $_POST['action'] === 'reschedule' && !empty($_POST['id_jadwal'])) {
        handleReschedule($pdo, $id_siswa);
        exit;
    }

    // ======================
    // TANGANI PENGAJUAN BARU
    // ======================
    handleNewSubmission($pdo, $id_siswa);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}

function handleReschedule($pdo, $id_siswa) {
    // Validasi input
    if (empty($_POST['id_jadwal']) || empty($_POST['tanggal']) || empty($_POST['jam'])) {
        throw new Exception("ID jadwal, tanggal, dan jam harus diisi");
    }

    $id_jadwal = $_POST['id_jadwal'];
    $tanggal = $_POST['tanggal'];
    $jam = $_POST['jam'];
    $keterangan = $_POST['keterangan'] ?? '';

    // Validasi kepemilikan jadwal
    $stmt_cek = $pdo->prepare("SELECT id_siswa, id_guru, Status FROM jadwal_konseling WHERE id_jadwal = ?");
    $stmt_cek->execute([$id_jadwal]);
    $jadwal = $stmt_cek->fetch(PDO::FETCH_ASSOC);

    if (!$jadwal || $jadwal['id_siswa'] != $id_siswa) {
        throw new Exception("Jadwal tidak ditemukan");
    }

    if ($jadwal['Status'] !== 'Jadwalkan Ulang') {
        throw new Exception("Hanya jadwal dengan status 'Jadwalkan Ulang' yang bisa dijadwalkan ulang");
    }

    // Validasi tanggal
    $today = date('Y-m-d');
    if ($tanggal < $today) {
        throw new Exception("Tanggal tidak boleh kurang dari hari ini");
    }

    // Validasi hari Minggu
    $hari = date('w', strtotime($tanggal));
    if ($hari == 0) {
        throw new Exception("Tidak bisa memilih hari Minggu");
    }

    // Cek jadwal bentrok dengan guru yang sama
    $stmt_cek_bentrok = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM jadwal_konseling 
        WHERE Tanggal_Konseling = ? 
        AND Waktu_Konseling = ? 
        AND id_guru = ? 
        AND id_jadwal != ?
        AND Status IN ('Menunggu', 'Disetujui')
    ");
    $stmt_cek_bentrok->execute([$tanggal, $jam, $jadwal['id_guru'], $id_jadwal]);
    $cek_bentrok = $stmt_cek_bentrok->fetch(PDO::FETCH_ASSOC);
    
    if ($cek_bentrok['total'] > 0) {
        throw new Exception("Guru BK sudah memiliki jadwal pada tanggal dan jam tersebut");
    }

    // Update jadwal
    $stmt_update = $pdo->prepare("
        UPDATE jadwal_konseling 
        SET Tanggal_Konseling = ?, 
            Waktu_Konseling = ?, 
            Status = 'Menunggu',
            keterangan = CONCAT(IFNULL(keterangan, ''), ' | Jadwal diatur ulang: ', ?),
            updated_at = NOW()
        WHERE id_jadwal = ?
    ");
    
    $keterangan_reschedule = "Tanggal baru: $tanggal, Jam baru: $jam" . ($keterangan ? " - $keterangan" : "");
    $result = $stmt_update->execute([$tanggal, $jam, $keterangan_reschedule, $id_jadwal]);

    if (!$result) {
        throw new Exception("Gagal mengupdate jadwal");
    }

    echo json_encode([
        'success' => true,
        'message' => 'Jadwal berhasil diatur ulang! Menunggu konfirmasi guru BK.',
        'data' => [
            'id_jadwal' => $id_jadwal,
            'tanggal' => $tanggal,
            'jam' => $jam
        ]
    ]);
}

function handleNewSubmission($pdo, $id_siswa) {
    // Validasi input
    $required_fields = ['tanggal', 'jam', 'topik', 'id_guru', 'id_siswa'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Field $field harus diisi");
        }
    }

    $tanggal = $_POST['tanggal'];
    $jam = $_POST['jam'];
    $topik = $_POST['topik'];
    $id_guru = $_POST['id_guru'];
    $keterangan = $_POST['keterangan'] ?? '';
    $id_siswa_post = $_POST['id_siswa'];

    // Validasi ID siswa
    if ($id_siswa != $id_siswa_post) {
        throw new Exception("Data siswa tidak valid");
    }

    // Validasi tanggal
    $today = date('Y-m-d');
    if ($tanggal < $today) {
        throw new Exception("Tanggal tidak boleh kurang dari hari ini");
    }

    // Validasi hari Minggu
    $hari = date('w', strtotime($tanggal));
    if ($hari == 0) {
        throw new Exception("Tidak bisa memilih hari Minggu");
    }

    // Validasi guru aktif
    $stmt_guru = $pdo->prepare("SELECT id_guru, nama FROM guru WHERE id_guru = ? AND status = 'aktif'");
    $stmt_guru->execute([$id_guru]);
    $guru = $stmt_guru->fetch(PDO::FETCH_ASSOC);

    if (!$guru) {
        throw new Exception("Guru BK tidak valid atau tidak aktif");
    }

    // Cek jadwal bentrok
    $stmt_cek = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM jadwal_konseling 
        WHERE Tanggal_Konseling = ? 
        AND Waktu_Konseling = ? 
        AND id_guru = ? 
        AND Status IN ('Menunggu', 'Disetujui')
    ");
    $stmt_cek->execute([$tanggal, $jam, $id_guru]);
    $cek_bentrok = $stmt_cek->fetch(PDO::FETCH_ASSOC);
    
    if ($cek_bentrok['total'] > 0) {
        throw new Exception("Guru BK sudah memiliki jadwal pada tanggal dan jam tersebut");
    }

    // Cek jumlah pengajuan aktif siswa
    $stmt_cek_siswa = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM jadwal_konseling 
        WHERE id_siswa = ? 
        AND Status IN ('Menunggu', 'Disetujui')
    ");
    $stmt_cek_siswa->execute([$id_siswa]);
    $cek_siswa = $stmt_cek_siswa->fetch(PDO::FETCH_ASSOC);
    
    if ($cek_siswa['total'] >= 3) {
        throw new Exception("Anda sudah memiliki 3 jadwal yang masih aktif");
    }

    // Simpan ke database
    $stmt_insert = $pdo->prepare("
        INSERT INTO jadwal_konseling 
        (id_siswa, id_guru, Tanggal_Konseling, Waktu_Konseling, Topik_Konseling, keterangan, Status, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, 'Menunggu', NOW())
    ");
    
    $result = $stmt_insert->execute([
        $id_siswa,
        $id_guru,
        $tanggal,
        $jam,
        $topik,
        $keterangan
    ]);
    
    if (!$result) {
        throw new Exception("Gagal menyimpan jadwal ke database");
    }
    
    $id_jadwal = $pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'Pengajuan jadwal konseling berhasil dikirim! Menunggu konfirmasi guru BK.',
        'data' => [
            'id_jadwal' => $id_jadwal,
            'tanggal' => $tanggal,
            'jam' => $jam,
            'topik' => $topik,
            'guru' => $guru['nama'],
            'status' => 'Menunggu'
        ]
    ]);
}
?>