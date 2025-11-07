<?php
// PERBAIKAN: Mulai output buffering
ob_start();

header('Content-Type: application/json');
require_once __DIR__ . '/../db_connection.php';
require_once __DIR__ . '/../logAktivitas.php'; // Tambahkan ini

$method = $_SERVER['REQUEST_METHOD'];

try {
    if (!$pdo) {
        throw new Exception('Koneksi database gagal');
    }

    switch ($method) {
        case 'GET':
            // Ambil data guru dengan username
            $query = "
                SELECT g.id_guru, g.nama, g.telepon, g.alamat, g.status, 
                       u.username, u.email, u.role
                FROM guru g
                LEFT JOIN users u ON g.id_guru = u.id_guru
                ORDER BY g.id_guru DESC
            ";
            
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'status' => 'success',
                'data' => $data
            ]);
            break;

        case 'POST':
            // Handle form submissions via AJAX
            $is_update = isset($_POST['id_guru']) && !empty($_POST['id_guru']);
            $id_guru = intval($_POST['id_guru'] ?? 0);
            $nama = $_POST['nama_guru'] ?? '';
            $telepon = $_POST['telepon_guru'] ?? ''; 
            $alamat = $_POST['alamat_guru'] ?? '';
            $username = $_POST['username'] ?? ''; 
            $password = $_POST['password'] ?? '';
            $email = $_POST['email'] ?? '';

            // Validasi
            if (!$nama || !$telepon || !$alamat || !$username || !$email) {
                throw new Exception('Semua field harus diisi!');
            }

            if (!preg_match('/^[0-9]{10,15}$/', $telepon)) {
                throw new Exception('Nomor telepon harus 10-15 digit angka');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Format email tidak valid');
            }

            if (!$is_update && (empty($password) || strlen($password) < 6)) {
                throw new Exception('Password wajib diisi (minimal 6 karakter) untuk akun baru');
            }

            if ($is_update && !empty($password) && strlen($password) < 6) {
                 throw new Exception('Password baru harus minimal 6 karakter');
            }

            $pdo->beginTransaction();

            if ($is_update) {
                // PENGECEKAN DUPLIKAT SAAT UPDATE
                $check_sql = "SELECT id FROM users WHERE (username = ? OR email = ?) AND id_guru != ?";
                $check_stmt = $pdo->prepare($check_sql);
                $check_stmt->execute([$username, $email, $id_guru]);
                if ($check_stmt->fetch()) {
                    throw new Exception('Username atau Email sudah digunakan oleh akun lain.');
                }
                
                // Ambil data lama untuk log
                $old_data_query = "SELECT g.nama, g.telepon, g.alamat, g.status, u.username, u.email 
                                 FROM guru g 
                                 LEFT JOIN users u ON g.id_guru = u.id_guru 
                                 WHERE g.id_guru = ?";
                $old_data_stmt = $pdo->prepare($old_data_query);
                $old_data_stmt->execute([$id_guru]);
                $old_data = $old_data_stmt->fetch(PDO::FETCH_ASSOC);
                
                // UPDATE DATA GURU
                $guru_query = "UPDATE guru SET nama = ?, telepon = ?, alamat = ? WHERE id_guru = ?";
                $guru_stmt = $pdo->prepare($guru_query);
                $guru_stmt->execute([$nama, $telepon, $alamat, $id_guru]);

                // Cek apakah user sudah ada
                $check_user = "SELECT id FROM users WHERE id_guru = ?";
                $check_stmt = $pdo->prepare($check_user);
                $check_stmt->execute([$id_guru]);
                $user_exists = $check_stmt->fetch();

                if ($user_exists) {
                    // UPDATE user yang sudah ada
                    if (!empty($password)) {
                        $hashed = password_hash($password, PASSWORD_BCRYPT);
                        $user_query = "UPDATE users SET username = ?, email = ?, password = ? WHERE id_guru = ?";
                        $user_stmt = $pdo->prepare($user_query);
                        $user_stmt->execute([$username, $email, $hashed, $id_guru]);
                    } else {
                        $user_query = "UPDATE users SET username = ?, email = ? WHERE id_guru = ?";
                        $user_stmt = $pdo->prepare($user_query);
                        $user_stmt->execute([$username, $email, $id_guru]);
                    }
                } else {
                    // INSERT user baru jika belum ada
                    if (empty($password)) {
                        throw new Exception('Password wajib untuk membuat akun baru');
                    }
                    $hashed = password_hash($password, PASSWORD_BCRYPT);
                    $user_query = "INSERT INTO users (username, password, email, role, id_guru) VALUES (?, ?, ?, 'user', ?)";
                    $user_stmt = $pdo->prepare($user_query);
                    $user_stmt->execute([$username, $hashed, $email, $id_guru]);
                }

                $pdo->commit();
                
                // LOG AKTIVITAS: Update data guru
                $meta = [
                    'id_guru' => $id_guru,
                    'data_lama' => $old_data,
                    'data_baru' => [
                        'nama' => $nama,
                        'telepon' => $telepon,
                        'alamat' => $alamat,
                        'username' => $username,
                        'email' => $email,
                        'password_changed' => !empty($password)
                    ]
                ];
                
                log_action('UPDATE_GURU', "Memperbarui data guru: {$nama}", $meta);
                
                echo json_encode(['status' => 'success', 'message' => 'Data guru berhasil diperbarui']);

            } else {
                // PENGECEKAN DUPLIKAT SAAT INSERT
                $check_sql = "SELECT id FROM users WHERE username = ? OR email = ?";
                $check_stmt = $pdo->prepare($check_sql);
                $check_stmt->execute([$username, $email]);
                if ($check_stmt->fetch()) {
                    throw new Exception('Username atau Email sudah terdaftar.');
                }
                
                // INSERT DATA GURU BARU
                $guru_query = "INSERT INTO guru (nama, telepon, alamat, status) VALUES (?, ?, ?, 'Aktif')";
                $guru_stmt = $pdo->prepare($guru_query);
                $guru_stmt->execute([$nama, $telepon, $alamat]);
                $new_id_guru = $pdo->lastInsertId();

                // Insert ke tabel users dengan role 'user'
                $hashed = password_hash($password, PASSWORD_BCRYPT);
                $user_query = "INSERT INTO users (username, password, email, role, id_guru) VALUES (?, ?, ?, 'user', ?)";
                $user_stmt = $pdo->prepare($user_query);
                $user_stmt->execute([$username, $hashed, $email, $new_id_guru]);

                $pdo->commit();
                
                // LOG AKTIVITAS: Tambah data guru baru
                $meta = [
                    'id_guru' => $new_id_guru,
                    'nama' => $nama,
                    'telepon' => $telepon,
                    'alamat' => $alamat,
                    'username' => $username,
                    'email' => $email
                ];
                
                log_action('CREATE_GURU', "Menambah data guru baru: {$nama}", $meta);
                
                echo json_encode(['status' => 'success', 'message' => 'Data guru berhasil ditambahkan']);
            }
            break;

        case 'PUT':
            parse_str(file_get_contents("php://input"), $_PUT);
            $id_guru = intval($_PUT['id_guru'] ?? 0);
            $action = $_PUT['action'] ?? '';

            if (!$id_guru) throw new Exception('ID Guru tidak valid');

            if ($action === 'ubah_status') {
                $status_query = "SELECT status, nama FROM guru WHERE id_guru = ?";
                $status_stmt = $pdo->prepare($status_query);
                $status_stmt->execute([$id_guru]);
                $result = $status_stmt->fetch(PDO::FETCH_ASSOC);
                $current_status = $result['status'];
                $nama_guru = $result['nama'];
                $new_status = ($current_status === 'Aktif') ? 'Nonaktif' : 'Aktif';

                $update_query = "UPDATE guru SET status = ? WHERE id_guru = ?";
                $update_stmt = $pdo->prepare($update_query);
                $update_stmt->execute([$new_status, $id_guru]);

                // LOG AKTIVITAS: Ubah status guru
                $meta = [
                    'id_guru' => $id_guru,
                    'nama_guru' => $nama_guru,
                    'status_lama' => $current_status,
                    'status_baru' => $new_status
                ];
                
                log_action('UPDATE_STATUS_GURU', "Mengubah status guru {$nama_guru} dari {$current_status} menjadi {$new_status}", $meta);

                echo json_encode([
                    'status' => 'success', 
                    'message' => 'Status guru berhasil diubah',
                    'new_status' => $new_status
                ]);
            }
            break;

        case 'DELETE':
            parse_str(file_get_contents("php://input"), $_DELETE);
            $id_guru = intval($_DELETE['id_guru'] ?? 0);

            if (!$id_guru) throw new Exception('ID Guru tidak valid');

            // Ambil data sebelum dihapus untuk log
            $data_query = "SELECT g.nama, g.telepon, g.alamat, u.username, u.email 
                         FROM guru g 
                         LEFT JOIN users u ON g.id_guru = u.id_guru 
                         WHERE g.id_guru = ?";
            $data_stmt = $pdo->prepare($data_query);
            $data_stmt->execute([$id_guru]);
            $deleted_data = $data_stmt->fetch(PDO::FETCH_ASSOC);

            if (!$deleted_data) {
                throw new Exception('Data guru tidak ditemukan');
            }

            $pdo->beginTransaction();
            
            // Hapus dari users terlebih dahulu
            $users_query = "DELETE FROM users WHERE id_guru = ?";
            $users_stmt = $pdo->prepare($users_query);
            $users_stmt->execute([$id_guru]);

            // Hapus dari guru
            $guru_query = "DELETE FROM guru WHERE id_guru = ?";
            $guru_stmt = $pdo->prepare($guru_query);
            $guru_stmt->execute([$id_guru]);

            $pdo->commit();
            
            // LOG AKTIVITAS: Hapus data guru
            $meta = [
                'id_guru' => $id_guru,
                'data_dihapus' => $deleted_data
            ];
            
            log_action('DELETE_GURU', "Menghapus data guru: {$deleted_data['nama']}", $meta);
            
            echo json_encode(['status' => 'success', 'message' => 'Data guru berhasil dihapus']);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Metode tidak diizinkan']);
            break;
    }

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // LOG AKTIVITAS: Error
    log_action('ERROR_GURU', "Error pada operasi guru: " . $e->getMessage());
    
    // PERBAIKAN: Bersihkan output buffer sebelum mengirim JSON
    ob_clean(); 
    
    // Kirim status error ke client
    http_response_code(400); 
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

// PERBAIKAN: Akhiri output buffering
ob_end_flush();
exit;
?>