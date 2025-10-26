<?php
header('Content-Type: application/json');
require '../db_connection.php';

$method = $_SERVER['REQUEST_METHOD'];

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
    
        $nama = $_POST['nama'] ?? '';
        $no_telp = $_POST['no_telp'] ?? ''; 
        $email = $_POST['email'] ?? '';
        $username = $_POST['username'] ?? ''; 
        $password = $_POST['password'] ?? '';

        // Validasi diperbarui
        if (!$nama || !$no_telp || !$email || !$username || !$password) {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
            exit;
        }

       
       
        $stmt_check = mysqli_prepare($conn, "SELECT 1 FROM users WHERE username = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt_check, "s", $username);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);

        if (mysqli_num_rows($result_check) > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Username sudah digunakan']);
            mysqli_stmt_close($stmt_check);
            exit;
        }
        mysqli_stmt_close($stmt_check);
        // ---------------------------------------------------

        $hashed = password_hash($password, PASSWORD_BCRYPT);

        // Gunakan transaksi agar aman
        mysqli_begin_transaction($conn);
        try {
         
            $stmt_admin = mysqli_prepare($conn, "INSERT INTO admin (nama, no_telp) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt_admin, "ss", $nama, $no_telp);
            mysqli_stmt_execute($stmt_admin);
            
            $id_admin = mysqli_insert_id($conn); // Ambil ID admin yang baru dibuat
            mysqli_stmt_close($stmt_admin);
            
            if (!$id_admin) {
                throw new Exception('Gagal membuat record admin.');
            }

            $stmt_user = mysqli_prepare($conn, "INSERT INTO users (username, password, email, role, id_admin) VALUES (?, ?, ?, 'admin', ?)");
            mysqli_stmt_bind_param($stmt_user, "sssi", $username, $hashed, $email, $id_admin);
            mysqli_stmt_execute($stmt_user);
            mysqli_stmt_close($stmt_user);

            mysqli_commit($conn);
            echo json_encode(['status' => 'success', 'message' => 'Akun admin berhasil ditambahkan']);

        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
        break;


    case 'DELETE':
        // 🔹 Hapus akun admin
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
        echo json_encode(['status' => 'error', 'message' => 'Metode tidak diizinkan']);
        break;
}
?>  