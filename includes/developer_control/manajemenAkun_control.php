
<?php
// TAMBAHKAN SESSION CHECK DI AWAL
session_start();

// CEK APAKAH SUDAH LOGIN
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

header('Content-Type: application/json');
require '../db_connection.php';

$method = $_SERVER['REQUEST_METHOD'];

// AMBIL DATA ADMIN DARI SESSION
$adminId = $_SESSION['admin_id'];
$adminName = $_SESSION['admin_name'];

try {
    // 1. Inisialisasi koneksi PDO
    if (!$pdo) {
        throw new Exception('Koneksi database gagal');
    }

    switch ($method) {
        case 'GET':
            // 🔹 Ambil semua data admin + username
            $query = "
                SELECT a.id_admin, a.nama, a.no_telp, u.email, u.username
                FROM admin a
                LEFT JOIN users u ON a.id_admin = u.id_admin
                WHERE u.role = 'admin'
                ORDER BY a.id_admin DESC
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
            // 🔹 Logika untuk INSERT (Akun Baru) dan UPDATE (Edit Akun)
            
            // Cek apakah ini mode UPDATE (ada id_admin) atau INSERT (tidak ada id_admin)
            $is_update = isset($_POST['id_admin']) && !empty($_POST['id_admin']);

            // Ambil semua data
            $id_admin = intval($_POST['id_admin'] ?? 0);
            $nama = $_POST['nama'] ?? '';
            $no_telp = $_POST['no_telp'] ?? ''; 
            $email = $_POST['email'] ?? '';
            $username = $_POST['username'] ?? ''; 
            $password = $_POST['password'] ?? ''; // Akan kosong jika tidak diubah saat edit

            // --- Validasi Data Umum ---
            if (!$nama || !$no_telp || !$email || !$username) {
                throw new Exception('Data nama, no. telp, email, dan username tidak boleh kosong');
            }

            // --- Validasi Password (HANYA untuk INSERT BARU) ---
            if (!$is_update && !$password) {
                throw new Exception('Password wajib diisi untuk akun baru');
            }

            // --- Validasi Keunikan Username ---
            if ($is_update) {
                $check_query = "SELECT 1 FROM users WHERE username = :username AND id_admin != :id_admin LIMIT 1";
                $check_stmt = $pdo->prepare($check_query);
                $check_stmt->bindParam(':username', $username);
                $check_stmt->bindParam(':id_admin', $id_admin);
            } else {
                $check_query = "SELECT 1 FROM users WHERE username = :username LIMIT 1";
                $check_stmt = $pdo->prepare($check_query);
                $check_stmt->bindParam(':username', $username);
            }
            
            $check_stmt->execute();
            
            if ($check_stmt->rowCount() > 0) {
                throw new Exception('Username sudah digunakan');
            }

            // Gunakan transaksi PDO
            $pdo->beginTransaction();

            if ($is_update) {
                // ==================
                // 🔹 LOGIKA UPDATE 🔹
                // ==================
                
                // 1. Update tabel admin
                $admin_query = "UPDATE admin SET nama = :nama, no_telp = :no_telp WHERE id_admin = :id_admin";
                $admin_stmt = $pdo->prepare($admin_query);
                $admin_stmt->bindParam(':nama', $nama);
                $admin_stmt->bindParam(':no_telp', $no_telp);
                $admin_stmt->bindParam(':id_admin', $id_admin);
                $admin_stmt->execute();

                // 2. Update tabel users (Cek apakah password diisi atau tidak)
                if (!empty($password)) {
                    // Jika password diisi, update password baru
                    $hashed = password_hash($password, PASSWORD_BCRYPT);
                    $user_query = "UPDATE users SET username = :username, email = :email, password = :password WHERE id_admin = :id_admin";
                    $user_stmt = $pdo->prepare($user_query);
                    $user_stmt->bindParam(':username', $username);
                    $user_stmt->bindParam(':email', $email);
                    $user_stmt->bindParam(':password', $hashed);
                    $user_stmt->bindParam(':id_admin', $id_admin);
                } else {
                    // Jika password kosong, JANGAN update password
                    $user_query = "UPDATE users SET username = :username, email = :email WHERE id_admin = :id_admin";
                    $user_stmt = $pdo->prepare($user_query);
                    $user_stmt->bindParam(':username', $username);
                    $user_stmt->bindParam(':email', $email);
                    $user_stmt->bindParam(':id_admin', $id_admin);
                }
                $user_stmt->execute();

                $pdo->commit();

                echo json_encode(['status' => 'success', 'message' => 'Akun admin berhasil diperbarui']);

            } else {
                // ==================
                // 🔹 LOGIKA INSERT 🔹
                // ==================
                $hashed = password_hash($password, PASSWORD_BCRYPT);

                // 1. Insert ke tabel admin
                $admin_query = "INSERT INTO admin (nama, no_telp) VALUES (:nama, :no_telp)";
                $admin_stmt = $pdo->prepare($admin_query);
                $admin_stmt->bindParam(':nama', $nama);
                $admin_stmt->bindParam(':no_telp', $no_telp);
                $admin_stmt->execute();
                
                $new_id_admin = $pdo->lastInsertId(); // Ambil ID admin yang baru dibuat
                
                if (!$new_id_admin) {
                    throw new Exception('Gagal membuat record admin.');
                }

                // 2. Insert ke tabel users
                $user_query = "INSERT INTO users (username, password, email, role, id_admin) VALUES (:username, :password, :email, 'admin', :id_admin)";
                $user_stmt = $pdo->prepare($user_query);
                $user_stmt->bindParam(':username', $username);
                $user_stmt->bindParam(':password', $hashed);
                $user_stmt->bindParam(':email', $email);
                $user_stmt->bindParam(':id_admin', $new_id_admin);
                $user_stmt->execute();

                $pdo->commit();

                echo json_encode(['status' => 'success', 'message' => 'Akun admin berhasil ditambahkan']);
            }
            break;

        case 'DELETE':
            // 🔹 Hapus akun admin
            parse_str(file_get_contents("php://input"), $_DELETE);
            $id_admin = intval($_DELETE['id_admin'] ?? 0);

            if (!$id_admin) {
                throw new Exception('ID Admin tidak valid');
            }

            $pdo->beginTransaction();
            
            // Hapus dari tabel users terlebih dahulu (foreign key constraint)
            $users_query = "DELETE FROM users WHERE id_admin = :id_admin";
            $users_stmt = $pdo->prepare($users_query);
            $users_stmt->bindParam(':id_admin', $id_admin);
            $users_stmt->execute();

            // Hapus dari tabel admin
            $admin_query = "DELETE FROM admin WHERE id_admin = :id_admin";
            $admin_stmt = $pdo->prepare($admin_query);
            $admin_stmt->bindParam(':id_admin', $id_admin);
            $admin_stmt->execute();

            $pdo->commit();

            echo json_encode(['status' => 'success', 'message' => 'Akun admin berhasil dihapus']);
            break;

        default:
            // 🔹 Metode tidak diizinkan
            echo json_encode(['status' => 'error', 'message' => 'Metode tidak diizinkan']);
            break;
    }

} catch (Exception $e) {
    // Rollback transaction jika ada error
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    echo json_encode([
        'status' => 'error', 
        'message' => $e->getMessage()
    ]);
}

// PDO tidak perlu manual close connection
exit;
?>
