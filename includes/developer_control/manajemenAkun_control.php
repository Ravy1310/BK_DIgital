<?php
header('Content-Type: application/json');
require '../db_connection.php';

$method = $_SERVER['REQUEST_METHOD'];

// 1. Inisialisasi koneksi (seperti di file asli Anda)
if (!$conn) {
    echo json_encode(['status' => 'error', 'message' => 'Koneksi database gagal: ' . mysqli_connect_error()]);
    exit;
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
        $result = mysqli_query($conn, $query); 

        if (!$result) {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengambil data: ' . mysqli_error($conn)]);
            exit;
        }

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        echo json_encode([
            'status' => 'success',
            'data' => $data
        ]);
        break;


    case 'POST':
        // 🔹 Logika ini sekarang menangani INSERT (Akun Baru) dan UPDATE (Edit Akun)
        
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
            echo json_encode(['status' => 'error', 'message' => 'Data nama, no. telp, email, dan username tidak boleh kosong']);
            exit;
        }

        // --- Validasi Password (HANYA untuk INSERT BARU) ---
        if (!$is_update && !$password) {
            echo json_encode(['status' => 'error', 'message' => 'Password wajib diisi untuk akun baru']);
            exit;
        }

        // --- Validasi Keunikan Username ---
        // Saat UPDATE, kita harus mengecualikan id_admin saat ini dari pengecekan
        if ($is_update) {
            $stmt_check = mysqli_prepare($conn, "SELECT 1 FROM users WHERE username = ? AND id_admin != ? LIMIT 1");
            mysqli_stmt_bind_param($stmt_check, "si", $username, $id_admin);
        } else {
            $stmt_check = mysqli_prepare($conn, "SELECT 1 FROM users WHERE username = ? LIMIT 1");
            mysqli_stmt_bind_param($stmt_check, "s", $username);
        }
        
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);

        if (mysqli_num_rows($result_check) > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Username sudah digunakan']);
            mysqli_stmt_close($stmt_check);
            exit;
        }
        mysqli_stmt_close($stmt_check);
        // ---------------------------------------------------


        // Gunakan transaksi
        mysqli_begin_transaction($conn);
        try {

            if ($is_update) {
                // ==================
                // 🔹 LOGIKA UPDATE 🔹
                // ==================
                
                // 1. Update tabel admin
                $stmt_admin = mysqli_prepare($conn, "UPDATE admin SET nama = ?, no_telp = ? WHERE id_admin = ?");
                mysqli_stmt_bind_param($stmt_admin, "ssi", $nama, $no_telp, $id_admin);
                mysqli_stmt_execute($stmt_admin);
                mysqli_stmt_close($stmt_admin);

                // 2. Update tabel users (Cek apakah password diisi atau tidak)
                if (!empty($password)) {
                    // Jika password diisi, update password baru
                    $hashed = password_hash($password, PASSWORD_BCRYPT);
                    $stmt_user = mysqli_prepare($conn, "UPDATE users SET username = ?, email = ?, password = ? WHERE id_admin = ?");
                    mysqli_stmt_bind_param($stmt_user, "sssi", $username, $email, $hashed, $id_admin);
                } else {
                    // Jika password kosong, JANGAN update password
                    $stmt_user = mysqli_prepare($conn, "UPDATE users SET username = ?, email = ? WHERE id_admin = ?");
                    mysqli_stmt_bind_param($stmt_user, "ssi", $username, $email, $id_admin);
                }
                mysqli_stmt_execute($stmt_user);
                mysqli_stmt_close($stmt_user);

                mysqli_commit($conn);
                echo json_encode(['status' => 'success', 'message' => 'Akun admin berhasil diperbarui']);

            } else {
                // ==================
                // 🔹 LOGIKA INSERT (KODE LAMA ANDA, DISESUAIKAN) 🔹
                // ==================
                $hashed = password_hash($password, PASSWORD_BCRYPT);

                $stmt_admin = mysqli_prepare($conn, "INSERT INTO admin (nama, no_telp) VALUES (?, ?)");
                mysqli_stmt_bind_param($stmt_admin, "ss", $nama, $no_telp);
                mysqli_stmt_execute($stmt_admin);
                
                $new_id_admin = mysqli_insert_id($conn); // Ambil ID admin yang baru dibuat
                mysqli_stmt_close($stmt_admin);
                
                if (!$new_id_admin) {
                    throw new Exception('Gagal membuat record admin.');
                }

                $stmt_user = mysqli_prepare($conn, "INSERT INTO users (username, password, email, role, id_admin) VALUES (?, ?, ?, 'admin', ?)");
                mysqli_stmt_bind_param($stmt_user, "sssi", $username, $hashed, $email, $new_id_admin);
                mysqli_stmt_execute($stmt_user);
                mysqli_stmt_close($stmt_user);

                mysqli_commit($conn);
                echo json_encode(['status' => 'success', 'message' => 'Akun admin berhasil ditambahkan']);
            }

        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
        break;


    case 'DELETE':
        // 🔹 Hapus akun admin (Kode asli Anda)
        parse_str(file_get_contents("php://input"), $_DELETE);
        $id_admin = intval($_DELETE['id_admin'] ?? 0);

        if (!$id_admin) {
            echo json_encode(['status' => 'error', 'message' => 'ID Admin tidak valid']);
            exit;
        }

        mysqli_begin_transaction($conn);
        try {
          
            $stmt_users = mysqli_prepare($conn, "DELETE FROM users WHERE id_admin = ?");
            mysqli_stmt_bind_param($stmt_users, "i", $id_admin);
            mysqli_stmt_execute($stmt_users);
            mysqli_stmt_close($stmt_users);

            $stmt_admin = mysqli_prepare($conn, "DELETE FROM admin WHERE id_admin = ?");
            mysqli_stmt_bind_param($stmt_admin, "i", $id_admin);
            mysqli_stmt_execute($stmt_admin);
            mysqli_stmt_close($stmt_admin);

            mysqli_commit($conn);
            echo json_encode(['status' => 'success', 'message' => 'Akun admin berhasil dihapus']);

        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus: ' . $e->getMessage()]);
        }
        break;


    default:
        // 🔹 Metode tidak diizinkan (Kode asli Anda)
        echo json_encode(['status' => 'error', 'message' => 'Metode tidak diizinkan']);
        break;
} // <-- INI ADALAH '}' YANG MUNGKIN HILANG/SALAH TEMPAT

// Tutup koneksi di akhir
mysqli_close($conn);
?>