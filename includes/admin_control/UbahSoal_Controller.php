<?php
// includes/admin_control/UbahSoal_Controller.php - OPTION 3
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    die("Unauthorized");
}

$base_dir = $_SERVER['DOCUMENT_ROOT'] . '/BK_DIGITAL/';
require_once $base_dir . 'includes/db_connection.php';
require_once $base_dir . 'includes/logAktivitas.php';

header('Content-Type: text/plain');

try {
    // VALIDASI INPUT
    $id_soal = isset($_POST['id_soal']) ? intval($_POST['id_soal']) : 0;
    $id_tes = isset($_POST['id_tes']) ? intval($_POST['id_tes']) : 0;
    $pertanyaan = isset($_POST['pertanyaan']) ? trim($_POST['pertanyaan']) : '';

    if ($id_soal <= 0) {
        throw new Exception("ID Soal tidak valid: $id_soal");
    }

    if (empty($pertanyaan)) {
        throw new Exception("Pertanyaan tidak boleh kosong");
    }

    // VALIDASI OPSI
    $opsi_labels = ['a', 'b', 'c', 'd', 'e'];
    $opsi_data = [];

    foreach ($opsi_labels as $label) {
        $opsi = isset($_POST["opsi_$label"]) ? trim($_POST["opsi_$label"]) : '';
        $bobot = isset($_POST["bobot_$label"]) ? intval($_POST["bobot_$label"]) : 1;

        if (empty($opsi)) {
            throw new Exception("Opsi $label tidak boleh kosong");
        }

        if ($bobot < 1 || $bobot > 5) {
            throw new Exception("Bobot opsi $label harus antara 1-5");
        }

        $opsi_data[] = [
            'opsi' => $opsi,
            'bobot' => $bobot
        ];
    }

    // BEGIN TRANSACTION
    $pdo->beginTransaction();

    // 1. UPDATE SOAL
    $stmt_soal = $pdo->prepare("UPDATE soal_tes SET pertanyaan = ? WHERE id_soal = ?");
    $stmt_soal->execute([$pertanyaan, $id_soal]);
    
    $affected_soal = $stmt_soal->rowCount();
    error_log("UPDATE_SOAL: id_soal=$id_soal, affected_rows=$affected_soal");

    // 2. DAPATKAN id_opsi YANG SUDAH ADA (35,36,37,38,39)
    $stmt_get_ids = $pdo->prepare("
        SELECT id_opsi 
        FROM opsi_jawaban 
        WHERE id_soal = ? 
        ORDER BY id_opsi 
        LIMIT 5
    ");
    $stmt_get_ids->execute([$id_soal]);
    $existing_ids = $stmt_get_ids->fetchAll(PDO::FETCH_COLUMN);
    
    error_log("EXISTING_OPSI_IDS: " . implode(',', $existing_ids));
    error_log("TOTAL_EXISTING_OPSI: " . count($existing_ids));

    // 3. UPDATE OPSI BERDASARKAN POSISI
    $updated_count = 0;
    $inserted_count = 0;
    
    foreach ($opsi_data as $index => $data) {
        $opsi = $data['opsi'];
        $bobot = $data['bobot'];
        $label = $opsi_labels[$index];
        
        if (isset($existing_ids[$index])) {
            // ✅ UPDATE OPSI YANG SUDAH ADA (pertahankan id_opsi)
            $id_opsi = $existing_ids[$index];
            $stmt_update = $pdo->prepare("UPDATE opsi_jawaban SET opsi = ?, bobot = ? WHERE id_opsi = ?");
            $stmt_update->execute([$opsi, $bobot, $id_opsi]);
            $affected = $stmt_update->rowCount();
            
            $updated_count++;
            error_log("UPDATE_OPSI: id_opsi=$id_opsi, opsi='$opsi', bobot=$bobot, affected_rows=$affected");
        } else {
            // ➕ INSERT OPSI BARU JIKA KURANG
            // Cari id_opsi berikutnya yang available
            $next_id = $index + 1;
            $stmt_insert = $pdo->prepare("INSERT INTO opsi_jawaban (id_soal, id_opsi, opsi, bobot) VALUES (?, ?, ?, ?)");
            $stmt_insert->execute([$id_soal, $next_id, $opsi, $bobot]);
            $affected = $stmt_insert->rowCount();
            
            $inserted_count++;
            error_log("INSERT_OPSI: id_soal=$id_soal, id_opsi=$next_id, opsi='$opsi', bobot=$bobot, affected_rows=$affected");
        }
    }

    // 4. HAPUS OPSI EXTRA JIKA ADA LEBIH DARI 5
    $deleted_count = 0;
    if (count($existing_ids) > 5) {
        $extra_ids = array_slice($existing_ids, 5);
        $placeholders = implode(',', array_fill(0, count($extra_ids), '?'));
        
        $stmt_delete_extra = $pdo->prepare("DELETE FROM opsi_jawaban WHERE id_opsi IN ($placeholders)");
        $stmt_delete_extra->execute($extra_ids);
        $deleted_count = $stmt_delete_extra->rowCount();
        
        error_log("DELETE_EXTRA_OPSI: deleted $deleted_count extra opsi: " . implode(',', $extra_ids));
    }

    // COMMIT TRANSACTION
    $pdo->commit();

    error_log("SUCCESS: Soal $id_soal updated - $updated_count opsi updated, $inserted_count opsi inserted");

    // LOG AKTIVITAS: Ubah soal
    $adminId = $_SESSION['admin_id'] ?? 0;
    $adminName = $_SESSION['admin_name'] ?? 'Unknown Admin';
    
    $log_description = "Mengubah soal (ID: $id_soal) pada tes (ID: $id_tes) - $updated_count opsi diupdate, $inserted_count opsi ditambahkan, $deleted_count opsi dihapus";
    $log_meta = [
        'id_soal' => $id_soal,
        'id_tes' => $id_tes,
        'pertanyaan' => $pertanyaan,
        'opsi_updated' => $updated_count,
        'opsi_inserted' => $inserted_count,
        'opsi_deleted' => $deleted_count,
        'admin_id' => $adminId,
        'admin_name' => $adminName
    ];
    
    log_action('UBAH_SOAL', $log_description, $log_meta);

    echo "Soal berhasil diperbarui";

} catch (Exception $e) {
    // ROLLBACK JIKA ERROR
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // LOG ERROR jika terjadi kegagalan
    $adminId = $_SESSION['admin_id'] ?? 0;
    $adminName = $_SESSION['admin_name'] ?? 'Unknown Admin';
    
    log_action('ERROR_UBAH_SOAL', "Gagal mengubah soal: " . $e->getMessage(), [
        'id_soal' => $id_soal,
        'id_tes' => $id_tes,
        'error' => $e->getMessage(),
        'admin_id' => $adminId,
        'admin_name' => $adminName
    ]);
    
    error_log("ERROR UbahSoal_Controller: " . $e->getMessage());
    echo "Error: " . $e->getMessage();
}
?>