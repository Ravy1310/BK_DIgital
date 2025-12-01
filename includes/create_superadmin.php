<?php
// create_superadmin.php
header('Content-Type: application/json');

// Mulai output buffering untuk mencegah output lain
ob_start();

try {
    // Include db_connection.php yang menggunakan PDO
    include 'db_connection.php';
    
    // Pastikan $pdo ada
    if (!isset($pdo)) {
        throw new Exception('Koneksi database gagal: $pdo tidak terdefinisi');
    }
    
    // Hanya terima POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        throw new Exception('Metode tidak diizinkan');
    }
    
    // Ambil input & trim
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Debug: Log input
    error_log("Create Superadmin - Input: username=$username, email=$email");
    
    // Validasi
    if ($username === '' || $email === '' || $password === '') {
        throw new Exception('Username, email, dan password wajib diisi.');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Format email tidak valid.');
    }
    
    if (strlen($password) < 6) {
        throw new Exception('Password minimal 6 karakter.');
    }
    
    // Opsional: Batasi 1 superadmin
    /*
    $sqlCheckSuper = "SELECT COUNT(*) as cnt FROM users WHERE role = 'superadmin'";
    $stmt = $pdo->prepare($sqlCheckSuper);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ((int)$result['cnt'] > 0) {
        throw new Exception('Superadmin sudah ada.');
    }
    */
    
    // Cek duplikasi username atau email
    $checkSql = "SELECT id FROM users WHERE username = :username OR email = :email LIMIT 1";
    $stmt = $pdo->prepare($checkSql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        throw new Exception('Username atau email sudah terdaftar.');
    }
    
    // Hash password
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user baru dengan role superadmin
    $insertSql = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, 'superadmin')";
    $stmt = $pdo->prepare($insertSql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':password', $hash, PDO::PARAM_STR);
    
    if ($stmt->execute()) {
        $response = [
            'status' => 'success',
            'message' => 'Akun superadmin berhasil dibuat.',
            'user' => $username,
            'email' => $email,
            'user_id' => $pdo->lastInsertId()
        ];
        
        // Log sukses
        error_log("Superadmin berhasil dibuat: $username ($email)");
        
        echo json_encode($response);
    } else {
        $errorInfo = $stmt->errorInfo();
        throw new Exception('Gagal menyimpan data: ' . ($errorInfo[2] ?? 'Unknown error'));
    }
    
} catch (Exception $e) {
    // Bersihkan output buffer
    ob_clean();
    
    // Set HTTP status code jika perlu
    if ($e->getMessage() === 'Metode tidak diizinkan') {
        http_response_code(405);
    } else {
        http_response_code(400);
    }
    
    // Response error
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
    
    // Log error
    error_log("Create Superadmin Error: " . $e->getMessage());
    
} finally {
    // Pastikan tidak ada output lain
    ob_end_flush();
}
?>