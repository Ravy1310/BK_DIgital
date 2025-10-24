<?php
// create_superadmin.php
header('Content-Type: application/json');
include 'db_connection.php';

// Hanya terima POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Metode tidak diizinkan']);
    exit;
}

// Ambil input & trim
$username = trim($_POST['username'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validasi
if ($username === '' || $email === '' || $password === '') {
    echo json_encode(['status' => 'error', 'message' => 'Username, email, dan password wajib diisi.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Format email tidak valid.']);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(['status' => 'error', 'message' => 'Password minimal 6 karakter.']);
    exit;
}

// (Opsional) Batasi 1 superadmin — uncomment jika mau
// $sqlCheckSuper = "SELECT COUNT(*) AS cnt FROM users WHERE role = 'superadmin'";
// $res = $conn->query($sqlCheckSuper);
// if ($res) {
//     $row = $res->fetch_assoc();
//     if ((int)$row['cnt'] > 0) {
//         echo json_encode(['status'=>'error','message'=>'Superadmin sudah ada.']);
//         exit;
//     }
// }

// Cek duplikasi username atau email
$checkSql = "SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1";
$stmt = $conn->prepare($checkSql);
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal mempersiapkan query.']);
    exit;
}
$stmt->bind_param('ss', $username, $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->close();
    echo json_encode(['status' => 'error', 'message' => 'Username atau email sudah terdaftar.']);
    exit;
}
$stmt->close();

// Hash password
$hash = password_hash($password, PASSWORD_DEFAULT);

// Insert user baru dengan role superadmin
$insertSql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'superadmin')";
$stmt = $conn->prepare($insertSql);
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal mempersiapkan query insert.']);
    exit;
}
$stmt->bind_param('sss', $username, $email, $hash);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Akun superadmin berhasil dibuat.', 'user' => $username, 'email' => $email]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
