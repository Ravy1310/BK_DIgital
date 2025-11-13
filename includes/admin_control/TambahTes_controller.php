<?php
// TambahTes_controller.php - PDO COMPATIBLE VERSION
session_start();

// CEK LOGIN
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// CEK ROLE
if ($_SESSION['admin_role'] !== 'admin' && $_SESSION['admin_role'] !== 'superadmin') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Include database connection PDO
require_once '../db_connection.php';

header('Content-Type: application/json');

// Cek jika koneksi database gagal
if ($pdo === null) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed. Please check server configuration.']);
    exit;
}

// Function untuk response JSON
function sendJsonResponse($success, $message, $additionalData = []) {
    $response = ['success' => $success, 'message' => $message];
    if (!empty($additionalData)) {
        $response = array_merge($response, $additionalData);
    }
    echo json_encode($response);
    exit;
}

// Clean input function
function cleanInput($data) {
    if (empty($data)) return '';
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Validate test data
function validateTestData($nama_tes, $deskripsi_tes) {
    $errors = [];
    
    if (empty($nama_tes)) {
        $errors[] = 'Nama tes harus diisi';
    } elseif (strlen($nama_tes) < 3) {
        $errors[] = 'Nama tes minimal 3 karakter';
    }
    
    if (empty($deskripsi_tes)) {
        $errors[] = 'Deskripsi tes harus diisi';
    } elseif (strlen($deskripsi_tes) < 10) {
        $errors[] = 'Deskripsi tes minimal 10 karakter';
    }
    
    return $errors;
}

// Check if test name already exists
function checkDuplicateTest($nama_tes, $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT id_tes FROM tes WHERE kategori_tes = ?");
        $stmt->execute([$nama_tes]);
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log("Error checking duplicate test: " . $e->getMessage());
        return false;
    }
}

// Validate CSV file
function validateCSV($filePath) {
    if (!file_exists($filePath)) {
        return ['success' => false, 'message' => 'File tidak ditemukan'];
    }

    $handle = fopen($filePath, 'r');
    if (!$handle) {
        return ['success' => false, 'message' => 'Tidak dapat membuka file CSV'];
    }

    // Read header
    $header = fgetcsv($handle);
    if ($header === FALSE) {
        fclose($handle);
        return ['success' => false, 'message' => 'File CSV kosong atau tidak valid'];
    }

    $expectedHeader = ['PERTANYAAN', 'OPSI_A', 'BOBOT_A', 'OPSI_B', 'BOBOT_B', 'OPSI_C', 'BOBOT_C', 'OPSI_D', 'BOBOT_D', 'OPSI_E', 'BOBOT_E'];
    
    // Case insensitive comparison
    $headerLower = array_map('strtolower', array_map('trim', $header));
    $expectedLower = array_map('strtolower', $expectedHeader);
    
    if ($headerLower !== $expectedLower) {
        fclose($handle);
        return ['success' => false, 'message' => 'Format CSV tidak valid. Gunakan template yang disediakan.'];
    }

    $rowCount = 1;
    $errors = [];
    $validRows = 0;

    while (($row = fgetcsv($handle)) !== FALSE) {
        $rowCount++;
        
        // Skip empty rows
        if (empty(array_filter($row, function($value) { return trim($value) !== ''; }))) {
            continue;
        }

        // Validasi jumlah kolom
        if (count($row) !== 11) {
            $errors[] = "Baris $rowCount: Jumlah kolom tidak sesuai";
            continue;
        }

        $row = array_map('trim', $row);

        // Validasi pertanyaan
        if (empty($row[0])) {
            $errors[] = "Baris $rowCount: Pertanyaan tidak boleh kosong";
            continue;
        }

        // Validasi opsi minimal 2
        $filledOptions = 0;
        for ($i = 1; $i < 10; $i += 2) {
            if (!empty($row[$i])) {
                $filledOptions++;
                
                // Validasi bobot
                if (empty($row[$i + 1]) || !is_numeric($row[$i + 1])) {
                    $errors[] = "Baris $rowCount: Bobot harus angka";
                    break;
                }
            }
        }
        
        if ($filledOptions < 2) {
            $errors[] = "Baris $rowCount: Minimal harus ada 2 opsi jawaban";
            continue;
        }

        $validRows++;
    }

    fclose($handle);

    if ($validRows === 0 && empty($errors)) {
        $errors[] = "Tidak ada data soal yang valid dalam file CSV";
    }

    if (!empty($errors)) {
        return [
            'success' => false, 
            'message' => 'Error validasi CSV: ' . implode(', ', array_slice($errors, 0, 3))
        ];
    }

    return [
        'success' => true, 
        'message' => "CSV valid dengan $validRows soal",
        'valid_rows' => $validRows
    ];
}

// Process CSV data dengan PDO
function processCSV($filePath, $id_tes, $pdo) {
    $handle = fopen($filePath, 'r');
    if (!$handle) {
        return ['success' => false, 'message' => 'Tidak dapat membuka file CSV'];
    }
    
    // Skip header
    fgetcsv($handle);
    
    $soalCount = 0;
    $opsiCount = 0;

    try {
        $pdo->beginTransaction();

        while (($row = fgetcsv($handle)) !== FALSE) {
            // Skip empty rows
            if (empty(array_filter($row, function($value) { return trim($value) !== ''; }))) {
                continue;
            }

            $row = array_map('trim', $row);

            // Insert soal
            $stmt_soal = $pdo->prepare("INSERT INTO soal_tes (id_tes, pertanyaan) VALUES (?, ?)");
            $stmt_soal->execute([$id_tes, $row[0]]);
            
            $id_soal = $pdo->lastInsertId();
            $soalCount++;

            // Insert opsi jawaban
            $opsiData = [
                ['opsi' => $row[1], 'bobot' => $row[2]],
                ['opsi' => $row[3], 'bobot' => $row[4]],
                ['opsi' => $row[5], 'bobot' => $row[6]],
                ['opsi' => $row[7], 'bobot' => $row[8]],
                ['opsi' => $row[9], 'bobot' => $row[10]]
            ];

            $stmt_opsi = $pdo->prepare("INSERT INTO opsi_jawaban (id_soal, opsi, bobot) VALUES (?, ?, ?)");

            foreach ($opsiData as $data) {
                $opsi = $data['opsi'];
                $bobot = $data['bobot'];
                
                if (!empty($opsi) && !empty($bobot) && is_numeric($bobot)) {
                    $stmt_opsi->execute([$id_soal, $opsi, $bobot]);
                    $opsiCount++;
                }
            }
        }

        $pdo->commit();
        fclose($handle);
        
        return [
            'success' => true, 
            'message' => "Berhasil menambahkan $soalCount soal dengan $opsiCount opsi jawaban",
            'soal_count' => $soalCount,
            'opsi_count' => $opsiCount
        ];

    } catch (Exception $e) {
        $pdo->rollBack();
        fclose($handle);
        return ['success' => false, 'message' => 'Error processing CSV: ' . $e->getMessage()];
    }
}

// MAIN PROCESSING
try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendJsonResponse(false, 'Invalid request method');
    }

    // Get input data
    $nama_tes = cleanInput($_POST['nama_tes'] ?? '');
    $deskripsi_tes = cleanInput($_POST['deskripsi_tes'] ?? '');
    
    // Validate input
    $validationErrors = validateTestData($nama_tes, $deskripsi_tes);
    if (!empty($validationErrors)) {
        sendJsonResponse(false, implode(', ', $validationErrors));
    }

    // Check duplicate
    if (checkDuplicateTest($nama_tes, $pdo)) {
        sendJsonResponse(false, 'Nama tes sudah digunakan. Silakan gunakan nama lain.');
    }

    // Insert test data
    $stmt = $pdo->prepare("INSERT INTO tes (kategori_tes, deskripsi_tes, created_at) VALUES (?, ?, NOW())");
    $stmt->execute([$nama_tes, $deskripsi_tes]);
    
    $id_tes = $pdo->lastInsertId();
    
    $result = [
        'success' => true, 
        'message' => 'Tes berhasil ditambahkan', 
        'id_tes' => $id_tes
    ];

    // Process CSV file if uploaded
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
        $csvFile = $_FILES['csv_file']['tmp_name'];
        
        // Validate CSV
        $validation = validateCSV($csvFile);
        if (!$validation['success']) {
            // Delete the test if CSV validation fails
            $deleteStmt = $pdo->prepare("DELETE FROM tes WHERE id_tes = ?");
            $deleteStmt->execute([$id_tes]);
            
            sendJsonResponse(false, $validation['message']);
        }
        
        // Process CSV
        $processResult = processCSV($csvFile, $id_tes, $pdo);
        if (!$processResult['success']) {
            // Delete the test if CSV processing fails
            $deleteStmt = $pdo->prepare("DELETE FROM tes WHERE id_tes = ?");
            $deleteStmt->execute([$id_tes]);
            
            sendJsonResponse(false, $processResult['message']);
        }
        
        $result['csv_result'] = $processResult;
    }

    sendJsonResponse(true, 'Tes berhasil ditambahkan', $result);

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    sendJsonResponse(false, 'Database error: ' . $e->getMessage());
} catch (Exception $e) {
    error_log("System Error: " . $e->getMessage());
    sendJsonResponse(false, 'System error: ' . $e->getMessage());
}
?>