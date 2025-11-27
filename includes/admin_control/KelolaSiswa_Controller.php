<?php
// KelolaSiswa_Controller.php

// START SESSION DI AWAL FILE
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// SET HEADER PERTAMA KALI - INI PENTING!
header('Content-Type: application/json; charset=utf-8');

// Cek session admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Include db connection - PASTIKAN PATH BENAR
$db_path = __DIR__ . '/../db_connection.php';
if (!file_exists($db_path)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database configuration not found']);
    exit;
}

require_once $db_path;

// Include log aktivitas
$log_path = __DIR__ . '/../logAktivitas.php';
if (file_exists($log_path)) {
    require_once $log_path;
} else {
    // Fallback jika file log tidak ada
    function log_action($action, $description, $meta = null) {
        error_log("LOG ACTIVITY: $action - $description");
        return true;
    }
}

// Pastikan tidak ada output sebelum ini
if (ob_get_length()) ob_clean();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    // Log untuk debugging
    error_log("Siswa Controller - Action: " . $action . " by Admin: " . ($_SESSION['admin_name'] ?? 'Unknown'));
    
    switch ($action) {
        case 'tambah':
            tambahSiswa();
            break;
        case 'get':
            getSiswa();
            break;
        case 'get_by_id':
            getSiswaById();
            break;
        case 'edit':
            editSiswa();
            break;
        case 'hapus':
            hapusSiswa();
            break;
        case 'import':
            importSiswa();
            break;
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Action tidak valid: ' . $action]);
            break;
    }
} catch (Exception $e) {
    error_log("Error in siswa_controller: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan sistem']);
}

function sendJsonResponse($success, $message, $additionalData = []) {
    // Pastikan tidak ada output sebelumnya
    if (ob_get_length()) ob_clean();
    
    $response = array_merge([
        'success' => $success, 
        'message' => $message
    ], $additionalData);
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function getSiswa() {
    global $pdo;
    
    try {
        error_log("Getting siswa data from database");
        
        // Test koneksi database
        if (!$pdo) {
            throw new Exception("Database connection failed");
        }
        
        $stmt = $pdo->query("SELECT * FROM siswa ORDER BY id_siswa");
        
        if (!$stmt) {
            throw new Exception("Query failed: " . implode(", ", $pdo->errorInfo()));
        }
        
        $siswa = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Found " . count($siswa) . " siswa records");
        
        // TIDAK ADA LOG AKTIVITAS UNTUK VIEW DATA
        sendJsonResponse(true, 'Data berhasil diambil', [
            'data' => $siswa ?: [],
            'count' => count($siswa)
        ]);
        
    } catch (PDOException $e) {
        error_log("Database error in getSiswa: " . $e->getMessage());
        sendJsonResponse(false, 'Gagal mengambil data dari database: ' . $e->getMessage(), [
            'data' => [],
            'count' => 0
        ]);
    } catch (Exception $e) {
        error_log("General error in getSiswa: " . $e->getMessage());
        sendJsonResponse(false, 'Error: ' . $e->getMessage(), [
            'data' => [],
            'count' => 0
        ]);
    }
}

function getSiswaById() {
    global $pdo;
    
    error_log("getSiswaById called with id_siswa: " . ($_GET['id_siswa'] ?? 'NULL'));
    
    if (empty($_GET['id_siswa'])) {
        sendJsonResponse(false, 'ID siswa tidak valid');
    }
    
    $id_siswa = trim($_GET['id_siswa']);
    
    try {
        error_log("Executing query for id_siswa: " . $id_siswa);
        
        $stmt = $pdo->prepare("SELECT * FROM siswa WHERE id_siswa = ?");
        
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . implode(", ", $pdo->errorInfo()));
        }
        
        $stmt->execute([$id_siswa]);
        $siswa = $stmt->fetch(PDO::FETCH_ASSOC);
        
        error_log("Query result: " . ($siswa ? "FOUND" : "NOT FOUND"));
        
        if ($siswa) {
            // TIDAK ADA LOG AKTIVITAS UNTUK VIEW DETAIL
            sendJsonResponse(true, 'Data berhasil diambil', ['data' => $siswa]);
        } else {
            sendJsonResponse(false, 'Data siswa tidak ditemukan untuk ID: ' . $id_siswa);
        }
    } catch (PDOException $e) {
        error_log("Database error in getSiswaById: " . $e->getMessage());
        sendJsonResponse(false, 'Error database: ' . $e->getMessage());
    } catch (Exception $e) {
        error_log("General error in getSiswaById: " . $e->getMessage());
        sendJsonResponse(false, 'Error: ' . $e->getMessage());
    }
}

function tambahSiswa() {
    global $pdo;
    
    // Validasi input
    $required_fields = ['id_siswa', 'nama', 'kelas', 'tahun_masuk', 'jenis_kelamin'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            sendJsonResponse(false, "Field $field harus diisi");
        }
    }
    
    $id_siswa = trim($_POST['id_siswa']);
    $nama = trim($_POST['nama']);
    $kelas = trim($_POST['kelas']);
    $tahun_masuk = trim($_POST['tahun_masuk']);
    $jenis_kelamin = trim($_POST['jenis_kelamin']);
    
    // Validasi tahun masuk
    if (!is_numeric($tahun_masuk) || strlen($tahun_masuk) != 4) {
        sendJsonResponse(false, 'Tahun masuk harus 4 digit angka');
    }
    
    // Cek apakah ID siswa sudah ada
    try {
        $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM siswa WHERE id_siswa = ?");
        $check_stmt->execute([$id_siswa]);
        
        if ($check_stmt->fetchColumn() > 0) {
            sendJsonResponse(false, 'ID siswa sudah ada');
        }
    } catch (PDOException $e) {
        error_log("Database error in tambahSiswa check: " . $e->getMessage());
        sendJsonResponse(false, 'Error database: ' . $e->getMessage());
    }
    
    // Insert data baru
    try {
        $stmt = $pdo->prepare("INSERT INTO siswa (id_siswa, nama, kelas, tahun_masuk, jenis_kelamin) VALUES (?, ?, ?, ?, ?)");
        $result = $stmt->execute([$id_siswa, $nama, $kelas, $tahun_masuk, $jenis_kelamin]);
        
        if ($result) {
            // Log aktivitas berhasil tambah siswa
            log_action('ADD_SISWA', 'Menambah data siswa baru', [
                'id_siswa' => $id_siswa,
                'nama' => $nama,
                'kelas' => $kelas,
                'tahun_masuk' => $tahun_masuk,
                'jenis_kelamin' => $jenis_kelamin,
                'admin_id' => $_SESSION['admin_id'] ?? 0,
                'admin_name' => $_SESSION['admin_name'] ?? 'Unknown'
            ]);
            
            sendJsonResponse(true, 'Data siswa berhasil ditambahkan', ['inserted_id' => $id_siswa]);
        } else {
            // Log aktivitas gagal tambah siswa
            log_action('ADD_SISWA_FAILED', 'Gagal menambah data siswa', [
                'id_siswa' => $id_siswa,
                'nama' => $nama,
                'admin_id' => $_SESSION['admin_id'] ?? 0,
                'admin_name' => $_SESSION['admin_name'] ?? 'Unknown'
            ]);
            
            sendJsonResponse(false, 'Gagal menambah data siswa');
        }
    } catch (PDOException $e) {
        error_log("Database error in tambahSiswa insert: " . $e->getMessage());
        
        // Log aktivitas error tambah siswa
        log_action('ADD_SISWA_ERROR', 'Error database saat menambah siswa', [
            'id_siswa' => $id_siswa,
            'error' => $e->getMessage(),
            'admin_id' => $_SESSION['admin_id'] ?? 0,
            'admin_name' => $_SESSION['admin_name'] ?? 'Unknown'
        ]);
        
        sendJsonResponse(false, 'Error database: ' . $e->getMessage());
    }
}

function editSiswa() {
    global $pdo;
    
    // Validasi input
    $required_fields = ['id_siswa_lama', 'id_siswa', 'nama', 'kelas', 'tahun_masuk', 'jenis_kelamin'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            sendJsonResponse(false, "Field $field harus diisi");
        }
    }
    
    $id_siswa_lama = trim($_POST['id_siswa_lama']);
    $id_siswa = trim($_POST['id_siswa']);
    $nama = trim($_POST['nama']);
    $kelas = trim($_POST['kelas']);
    $tahun_masuk = trim($_POST['tahun_masuk']);
    $jenis_kelamin = trim($_POST['jenis_kelamin']);
    
    // Validasi tahun masuk
    if (!is_numeric($tahun_masuk) || strlen($tahun_masuk) != 4) {
        sendJsonResponse(false, 'Tahun masuk harus 4 digit angka');
    }
    
    // Jika ID berubah, cek apakah ID baru sudah ada
    if ($id_siswa_lama !== $id_siswa) {
        try {
            $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM siswa WHERE id_siswa = ? AND id_siswa != ?");
            $check_stmt->execute([$id_siswa, $id_siswa_lama]);
            
            if ($check_stmt->fetchColumn() > 0) {
                sendJsonResponse(false, 'ID siswa baru sudah ada');
            }
        } catch (PDOException $e) {
            error_log("Database error in editSiswa check: " . $e->getMessage());
            sendJsonResponse(false, 'Error database: ' . $e->getMessage());
        }
    }
    
    // Update data
    try {
        $stmt = $pdo->prepare("UPDATE siswa SET id_siswa = ?, nama = ?, kelas = ?, tahun_masuk = ?, jenis_kelamin = ? WHERE id_siswa = ?");
        $result = $stmt->execute([$id_siswa, $nama, $kelas, $tahun_masuk, $jenis_kelamin, $id_siswa_lama]);
        
        if ($result) {
            // Log aktivitas berhasil edit siswa
            log_action('EDIT_SISWA', 'Mengedit data siswa', [
                'id_siswa_lama' => $id_siswa_lama,
                'id_siswa_baru' => $id_siswa,
                'nama' => $nama,
                'kelas' => $kelas,
                'tahun_masuk' => $tahun_masuk,
                'jenis_kelamin' => $jenis_kelamin,
                'admin_id' => $_SESSION['admin_id'] ?? 0,
                'admin_name' => $_SESSION['admin_name'] ?? 'Unknown'
            ]);
            
            sendJsonResponse(true, 'Data siswa berhasil diupdate');
        } else {
            // Log aktivitas gagal edit siswa
            log_action('EDIT_SISWA_FAILED', 'Gagal mengedit data siswa', [
                'id_siswa' => $id_siswa_lama,
                'admin_id' => $_SESSION['admin_id'] ?? 0,
                'admin_name' => $_SESSION['admin_name'] ?? 'Unknown'
            ]);
            
            sendJsonResponse(false, 'Gagal mengupdate data siswa');
        }
    } catch (PDOException $e) {
        error_log("Database error in editSiswa update: " . $e->getMessage());
        
        // Log aktivitas error edit siswa
        log_action('EDIT_SISWA_ERROR', 'Error database saat mengedit siswa', [
            'id_siswa' => $id_siswa_lama,
            'error' => $e->getMessage(),
            'admin_id' => $_SESSION['admin_id'] ?? 0,
            'admin_name' => $_SESSION['admin_name'] ?? 'Unknown'
        ]);
        
        sendJsonResponse(false, 'Error database: ' . $e->getMessage());
    }
}

function hapusSiswa() {
    global $pdo;
    
    if (empty($_POST['id_siswa'])) {
        sendJsonResponse(false, 'ID siswa tidak valid');
    }
    
    $id_siswa = trim($_POST['id_siswa']);
    
    try {
        // Ambil data siswa sebelum dihapus untuk logging
        $stmt_select = $pdo->prepare("SELECT * FROM siswa WHERE id_siswa = ?");
        $stmt_select->execute([$id_siswa]);
        $siswa_data = $stmt_select->fetch(PDO::FETCH_ASSOC);
        
        $stmt = $pdo->prepare("DELETE FROM siswa WHERE id_siswa = ?");
        $result = $stmt->execute([$id_siswa]);
        
        if ($result && $stmt->rowCount() > 0) {
            // Log aktivitas berhasil hapus siswa
            log_action('DELETE_SISWA', 'Menghapus data siswa', [
                'id_siswa' => $id_siswa,
                'nama' => $siswa_data['nama'] ?? 'Unknown',
                'kelas' => $siswa_data['kelas'] ?? 'Unknown',
                'tahun_masuk' => $siswa_data['tahun_masuk'] ?? 'Unknown',
                'admin_id' => $_SESSION['admin_id'] ?? 0,
                'admin_name' => $_SESSION['admin_name'] ?? 'Unknown'
            ]);
            
            sendJsonResponse(true, 'Data siswa berhasil dihapus');
        } else {
            // Log aktivitas gagal hapus siswa
            log_action('DELETE_SISWA_FAILED', 'Gagal menghapus data siswa', [
                'id_siswa' => $id_siswa,
                'admin_id' => $_SESSION['admin_id'] ?? 0,
                'admin_name' => $_SESSION['admin_name'] ?? 'Unknown'
            ]);
            
            sendJsonResponse(false, 'Data siswa tidak ditemukan');
        }
    } catch (PDOException $e) {
        error_log("Database error in hapusSiswa: " . $e->getMessage());
        
        // Log aktivitas error hapus siswa
        log_action('DELETE_SISWA_ERROR', 'Error database saat menghapus siswa', [
            'id_siswa' => $id_siswa,
            'error' => $e->getMessage(),
            'admin_id' => $_SESSION['admin_id'] ?? 0,
            'admin_name' => $_SESSION['admin_name'] ?? 'Unknown'
        ]);
        
        sendJsonResponse(false, 'Error database: ' . $e->getMessage());
    }
}

function importSiswa() {
    global $pdo;
    
    if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
        sendJsonResponse(false, 'File tidak valid atau gagal diupload');
    }
    
    $file = $_FILES['excel_file']['tmp_name'];
    $file_name = $_FILES['excel_file']['name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $overwrite = isset($_POST['overwrite']) && $_POST['overwrite'] === '1';
    
    // Hanya terima CSV
    if ($file_ext !== 'csv') {
        sendJsonResponse(false, 'Hanya file CSV yang didukung untuk import. Silakan save Excel sebagai CSV.');
    }
    
    try {
        // Baca file CSV dengan delimiter yang benar
        $handle = fopen($file, 'r');
        if (!$handle) {
            throw new Exception('Tidak dapat membuka file');
        }
        
        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];
        $row_count = 0;
        
        // Deteksi delimiter
        $first_line = fgets($handle);
        $delimiter = detectDelimiter($first_line);
        fseek($handle, 0); // Reset pointer ke awal
        
        // Mulai transaction untuk performance
        $pdo->beginTransaction();
        
        // Baca per baris dengan delimiter yang terdeteksi
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
            $row_count++;
            
            // Skip header (baris pertama)
            if ($row_count === 1) continue;
            
            // Skip row kosong
            if (empty(array_filter($row))) continue;
            
            // Validasi jumlah kolom
            if (count($row) < 5) {
                $errors[] = "Baris $row_count: Data tidak lengkap (harus 5 kolom, ditemukan " . count($row) . " kolom)";
                $skipped++;
                continue;
            }
            
            // Ambil data dari kolom
            $id_siswa = isset($row[0]) ? trim($row[0]) : '';
            $nama = isset($row[1]) ? trim($row[1]) : '';
            $kelas = isset($row[2]) ? trim($row[2]) : '';
            $tahun_masuk = isset($row[3]) ? trim($row[3]) : '';
            $jenis_kelamin_input = isset($row[4]) ? trim($row[4]) : '';
            
            // Validasi data wajib
            if (empty($id_siswa)) {
                $errors[] = "Baris $row_count: ID Siswa kosong";
                $skipped++;
                continue;
            }
            
            if (empty($nama)) {
                $errors[] = "Baris $row_count: Nama kosong";
                $skipped++;
                continue;
            }
            
            if (empty($kelas)) {
                $errors[] = "Baris $row_count: Kelas kosong";
                $skipped++;
                continue;
            }
            
            if (empty($tahun_masuk)) {
                $errors[] = "Baris $row_count: Tahun masuk kosong";
                $skipped++;
                continue;
            }
            
            if (empty($jenis_kelamin_input)) {
                $errors[] = "Baris $row_count: Jenis kelamin kosong";
                $skipped++;
                continue;
            }
            
            // Validasi tahun masuk
            if (!is_numeric($tahun_masuk) || strlen($tahun_masuk) != 4) {
                $errors[] = "Baris $row_count: Tahun masuk harus 4 digit angka";
                $skipped++;
                continue;
            }
            
            // NORMALISASI JENIS KELAMIN - PERBAIKAN PENTING!
            $jenis_kelamin = normalizeGender($jenis_kelamin_input);
            if (!$jenis_kelamin) {
                $errors[] = "Baris $row_count: Jenis kelamin tidak valid ('$jenis_kelamin_input'). Harus 'Laki-laki' atau 'Perempuan'";
                $skipped++;
                continue;
            }
            
            // Cek duplikat ID
            try {
                $check_stmt = $pdo->prepare("SELECT COUNT(*) as count, nama, kelas, tahun_masuk, jenis_kelamin FROM siswa WHERE id_siswa = ?");
                $check_stmt->execute([$id_siswa]);
                $existing_data = $check_stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($existing_data['count'] > 0) {
                    if ($overwrite) {
                        // CEK APAKAH ADA PERUBAHAN DATA
                        $has_changes = ($existing_data['nama'] !== $nama) ||
                                     ($existing_data['kelas'] !== $kelas) ||
                                     ($existing_data['tahun_masuk'] !== $tahun_masuk) ||
                                     ($existing_data['jenis_kelamin'] !== $jenis_kelamin);
                        
                        if ($has_changes) {
                            // Update data yang sudah ada
                            $update_stmt = $pdo->prepare("UPDATE siswa SET nama = ?, kelas = ?, tahun_masuk = ?, jenis_kelamin = ? WHERE id_siswa = ?");
                            $result = $update_stmt->execute([$nama, $kelas, $tahun_masuk, $jenis_kelamin, $id_siswa]);
                            
                            if ($result) {
                                $updated++;
                                $imported++;
                                
                                // Log perubahan data
                                log_action('UPDATE_SISWA_IMPORT', 'Update data siswa melalui import', [
                                    'id_siswa' => $id_siswa,
                                    'changes' => [
                                        'nama' => ['old' => $existing_data['nama'], 'new' => $nama],
                                        'kelas' => ['old' => $existing_data['kelas'], 'new' => $kelas],
                                        'tahun_masuk' => ['old' => $existing_data['tahun_masuk'], 'new' => $tahun_masuk],
                                        'jenis_kelamin' => ['old' => $existing_data['jenis_kelamin'], 'new' => $jenis_kelamin]
                                    ],
                                    'admin_id' => $_SESSION['admin_id'] ?? 0,
                                    'admin_name' => $_SESSION['admin_name'] ?? 'Unknown'
                                ]);
                            } else {
                                $errors[] = "Baris $row_count: Gagal update data";
                                $skipped++;
                            }
                        } else {
                            // Data sama persis, skip
                            $skipped++;
                            // Tidak perlu log, karena tidak ada perubahan
                            continue;
                        }
                    } else {
                        $errors[] = "Baris $row_count: ID Siswa '$id_siswa' sudah ada (nama: " . $existing_data['nama'] . ")";
                        $skipped++;
                    }
                    continue;
                }
            } catch (PDOException $e) {
                $errors[] = "Baris $row_count: Error database: " . $e->getMessage();
                $skipped++;
                continue;
            }
            
            // Insert data baru
            try {
                $stmt = $pdo->prepare("INSERT INTO siswa (id_siswa, nama, kelas, tahun_masuk, jenis_kelamin) VALUES (?, ?, ?, ?, ?)");
                $result = $stmt->execute([$id_siswa, $nama, $kelas, $tahun_masuk, $jenis_kelamin]);
                
                if ($result) {
                    $imported++;
                    
                    // Log tambah data baru
                    log_action('ADD_SISWA_IMPORT', 'Menambah data siswa melalui import', [
                        'id_siswa' => $id_siswa,
                        'nama' => $nama,
                        'kelas' => $kelas,
                        'tahun_masuk' => $tahun_masuk,
                        'jenis_kelamin' => $jenis_kelamin,
                        'admin_id' => $_SESSION['admin_id'] ?? 0,
                        'admin_name' => $_SESSION['admin_name'] ?? 'Unknown'
                    ]);
                } else {
                    $errors[] = "Baris $row_count: Gagal menyimpan data";
                    $skipped++;
                }
            } catch (PDOException $e) {
                $errors[] = "Baris $row_count: Error database: " . $e->getMessage();
                $skipped++;
            }
        }
        
        fclose($handle);
        
        // Commit transaction
        $pdo->commit();
        
        // Log aktivitas import
        if ($imported > 0) {
            log_action('IMPORT_SISWA_SUCCESS', 'Import data siswa berhasil', [
                'file_name' => $file_name,
                'total_processed' => $row_count - 1,
                'imported' => $imported,
                'updated' => $updated,
                'added' => $imported - $updated,
                'skipped' => $skipped,
                'overwrite' => $overwrite,
                'admin_id' => $_SESSION['admin_id'] ?? 0,
                'admin_name' => $_SESSION['admin_name'] ?? 'Unknown'
            ]);
        } else {
            log_action('IMPORT_SISWA_FAILED', 'Gagal import data siswa', [
                'file_name' => $file_name,
                'errors' => count($errors),
                'admin_id' => $_SESSION['admin_id'] ?? 0,
                'admin_name' => $_SESSION['admin_name'] ?? 'Unknown'
            ]);
        }
        
        // Siapkan pesan hasil
        $message = "Import selesai. ";
        if ($imported > 0) {
            $message .= "$imported data berhasil diproses ";
            if ($updated > 0) {
                $message .= "($updated data diperbarui, " . ($imported - $updated) . " data ditambahkan baru)";
            } else {
                $message .= "(semua data baru)";
            }
        }
        if ($skipped > 0) {
            $message .= " $skipped data dilewati.";
        }
        
        if (!empty($errors)) {
            $message .= " Terdapat " . count($errors) . " error.";
            error_log("Import errors: " . implode(", ", $errors));
        }
        
        sendJsonResponse(true, $message, [
            'imported' => $imported,
            'updated' => $updated,
            'added' => $imported - $updated,
            'skipped' => $skipped,
            'errors' => $errors,
            'delimiter_detected' => $delimiter
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction jika error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        error_log("Import error: " . $e->getMessage());
        
        // Log error import
        log_action('IMPORT_SISWA_ERROR', 'Error saat import data siswa', [
            'file_name' => $file_name,
            'error' => $e->getMessage(),
            'admin_id' => $_SESSION['admin_id'] ?? 0,
            'admin_name' => $_SESSION['admin_name'] ?? 'Unknown'
        ]);
        
        sendJsonResponse(false, 'Error membaca file: ' . $e->getMessage());
    }
}

// FUNGSI BARU: Normalisasi jenis kelamin
function normalizeGender($gender_input) {
    $gender = strtolower(trim($gender_input));
    
    $gender_map = [
        'laki-laki' => 'Laki-laki',
        'laki' => 'Laki-laki',
        'pria' => 'Laki-laki',
        'cowok' => 'Laki-laki',
        'l' => 'Laki-laki',
        'perempuan' => 'Perempuan',
        'permpuan' => 'Perempuan', // typo correction
        'wanita' => 'Perempuan',
        'cewek' => 'Perempuan',
        'p' => 'Perempuan'
    ];
    
    return $gender_map[$gender] ?? null;
}   
// Fungsi untuk deteksi delimiter
function detectDelimiter($data) {
    $delimiters = [",", ";", "\t", "|"];
    $maxCount = 0;
    $detectedDelimiter = ',';
    
    foreach ($delimiters as $delimiter) {
        $count = count(str_getcsv($data, $delimiter));
        if ($count > $maxCount) {
            $maxCount = $count;
            $detectedDelimiter = $delimiter;
        }
    }
    
    return $detectedDelimiter;
}
?>