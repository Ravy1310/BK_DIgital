<?php
// includes/admin_control/proses_tambah_tes.php

session_start();

// CEK LOGIN
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

// CEK ROLE
if ($_SESSION['admin_role'] !== 'admin' && $_SESSION['admin_role'] !== 'superadmin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

require_once '../db_connection.php';
require_once '../logAktivitas.php';

header('Content-Type: application/json');

// Function to detect CSV delimiter
function detectDelimiter($file_path) {
    $delimiters = [',', ';', "\t"];
    $handle = fopen($file_path, 'r');
    $first_line = fgets($handle);
    fclose($handle);
    
    $max_count = 0;
    $detected_delimiter = ',';
    
    foreach ($delimiters as $delimiter) {
        $count = count(explode($delimiter, $first_line));
        if ($count > $max_count) {
            $max_count = $count;
            $detected_delimiter = $delimiter;
        }
    }
    
    return $detected_delimiter;
}

// Function to remove BOM
function removeBOM($data) {
    if (substr($data, 0, 3) == pack('CCC', 0xef, 0xbb, 0xbf)) {
        $data = substr($data, 3);
    }
    return $data;
}

// Function to validate CSV file
function validateCSVFile($file_path) {
    if (!file_exists($file_path)) {
        return ['status' => 'error', 'message' => 'File tidak ditemukan'];
    }
    
    // Auto-detect delimiter
    $delimiter = detectDelimiter($file_path);
    error_log("Detected delimiter: " . $delimiter);
    
    $handle = fopen($file_path, 'r');
    if (!$handle) {
        return ['status' => 'error', 'message' => 'Tidak dapat membuka file'];
    }
    
    // Read header dengan delimiter yang terdeteksi
    $header = fgetcsv($handle, 0, $delimiter);
    
    // Remove BOM from first column if exists
    if (isset($header[0])) {
        $header[0] = removeBOM($header[0]);
        $header[0] = str_replace("\xEF\xBB\xBF", '', $header[0]);
        $header[0] = str_replace("\ufeff", '', $header[0]);
    }
    
    $expected_header = ['PERTANYAAN', 'OPSI_A', 'BOBOT_A', 'OPSI_B', 'BOBOT_B', 'OPSI_C', 'BOBOT_C', 'OPSI_D', 'BOBOT_D', 'OPSI_E', 'BOBOT_E'];
    
    // Validasi jumlah kolom
    if (count($header) !== count($expected_header)) {
        fclose($handle);
        return [
            'status' => 'error', 
            'message' => "Jumlah kolom tidak sesuai. Diterima: " . count($header) . ", Diharapkan: " . count($expected_header)
        ];
    }
    
    // Check each column name (case-insensitive dan trim spasi)
    foreach ($expected_header as $index => $expected) {
        $actual = isset($header[$index]) ? trim($header[$index]) : '';
        $expected_clean = trim($expected);
        
        if (strtoupper($actual) !== strtoupper($expected_clean)) {
            fclose($handle);
            return [
                'status' => 'error', 
                'message' => "Kolom " . ($index + 1) . " tidak sesuai. Diterima: '$actual', Diharapkan: '$expected_clean'"
            ];
        }
    }
    
    // Lanjutkan dengan validasi data menggunakan delimiter yang sama
    $row_count = 1;
    $questions = [];
    
    while (($row = fgetcsv($handle, 0, $delimiter)) !== FALSE) {
        $row_count++;
        
        // Skip empty rows
        if (empty(array_filter($row))) {
            continue;
        }
        
        // Pastikan row memiliki cukup kolom
        if (count($row) < 11) {
            fclose($handle);
            return ['status' => 'error', 'message' => "Baris $row_count: Format data tidak lengkap. Dibutuhkan 11 kolom."];
        }
        
        // Ambil semua data dengan index yang benar
        $pertanyaan = isset($row[0]) ? trim(removeBOM($row[0])) : '';
        $opsi_a = isset($row[1]) ? trim($row[1]) : '';
        $bobot_a = isset($row[2]) ? trim($row[2]) : '';
        $opsi_b = isset($row[3]) ? trim($row[3]) : '';
        $bobot_b = isset($row[4]) ? trim($row[4]) : '';
        $opsi_c = isset($row[5]) ? trim($row[5]) : '';
        $bobot_c = isset($row[6]) ? trim($row[6]) : '';
        $opsi_d = isset($row[7]) ? trim($row[7]) : '';
        $bobot_d = isset($row[8]) ? trim($row[8]) : '';
        $opsi_e = isset($row[9]) ? trim($row[9]) : '';
        $bobot_e = isset($row[10]) ? trim($row[10]) : '';
        
        if (empty($pertanyaan)) {
            fclose($handle);
            return ['status' => 'error', 'message' => "Baris $row_count: Pertanyaan tidak boleh kosong"];
        }
        
        if (empty($opsi_a) || empty($bobot_a) || empty($opsi_b) || empty($bobot_b)) {
            fclose($handle);
            return ['status' => 'error', 'message' => "Baris $row_count: Minimal harus ada 2 opsi (A dan B) dengan bobot"];
        }
        
        // Validasi bobot untuk semua opsi yang diisi
        $bobots = [
            'A' => ['opsi' => $opsi_a, 'bobot' => $bobot_a],
            'B' => ['opsi' => $opsi_b, 'bobot' => $bobot_b],
            'C' => ['opsi' => $opsi_c, 'bobot' => $bobot_c],
            'D' => ['opsi' => $opsi_d, 'bobot' => $bobot_d],
            'E' => ['opsi' => $opsi_e, 'bobot' => $bobot_e]
        ];
        
        foreach ($bobots as $huruf => $data) {
            // Jika opsi diisi, bobot harus diisi dan valid
            if (!empty($data['opsi'])) {
                if (empty($data['bobot'])) {
                    fclose($handle);
                    return ['status' => 'error', 'message' => "Baris $row_count: Bobot $huruf harus diisi karena opsi $huruf sudah diisi"];
                }
                if (!is_numeric($data['bobot']) || $data['bobot'] < 1 || $data['bobot'] > 5) {
                    fclose($handle);
                    return ['status' => 'error', 'message' => "Baris $row_count: Bobot $huruf harus angka 1-5, diterima: '{$data['bobot']}'"];
                }
            }
            // Jika bobot diisi, opsi harus diisi
            elseif (!empty($data['bobot'])) {
                fclose($handle);
                return ['status' => 'error', 'message' => "Baris $row_count: Opsi $huruf harus diisi karena bobot $huruf sudah diisi"];
            }
        }
        
        // Simpan data pertanyaan dan opsi
        $questions[] = [
            'pertanyaan' => $pertanyaan,
            'opsi' => [
                'A' => ['teks' => $opsi_a, 'bobot' => intval($bobot_a)],
                'B' => ['teks' => $opsi_b, 'bobot' => intval($bobot_b)],
                'C' => ['teks' => !empty($opsi_c) ? $opsi_c : null, 'bobot' => !empty($bobot_c) ? intval($bobot_c) : null],
                'D' => ['teks' => !empty($opsi_d) ? $opsi_d : null, 'bobot' => !empty($bobot_d) ? intval($bobot_d) : null],
                'E' => ['teks' => !empty($opsi_e) ? $opsi_e : null, 'bobot' => !empty($bobot_e) ? intval($bobot_e) : null],
            ]
        ];
    }
    
    fclose($handle);
    
    if (empty($questions)) {
        return ['status' => 'error', 'message' => 'Tidak ada data soal yang valid dalam file.'];
    }
    
    return ['status' => 'success', 'questions' => $questions];
}

try {
    // Validasi input form
    if (!isset($_POST['nama_tes']) || empty(trim($_POST['nama_tes']))) {
        throw new Exception('Nama tes tidak boleh kosong');
    }
    
    if (!isset($_POST['deskripsi_tes']) || empty(trim($_POST['deskripsi_tes']))) {
        throw new Exception('Deskripsi tes tidak boleh kosong');
    }
    
    if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('File CSV wajib diupload');
    }
    
    $kategori_tes = trim($_POST['nama_tes']);
    $deskripsi_tes = trim($_POST['deskripsi_tes']);
    
    // Validasi panjang input
    if (strlen($kategori_tes) < 3) {
        throw new Exception('Nama tes minimal 3 karakter');
    }
    
    if (strlen($kategori_tes) > 255) {
        throw new Exception('Nama tes maksimal 255 karakter');
    }
    
    if (strlen($deskripsi_tes) > 500) {
        throw new Exception('Deskripsi tes maksimal 500 karakter');
    }
    
    // Check if nama tes already exists
    $check_sql = "SELECT id_tes FROM tes WHERE kategori_tes = ?";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([$kategori_tes]);
    
    if ($check_stmt->rowCount() > 0) {
        throw new Exception('Nama tes sudah digunakan');
    }
    
    // Process uploaded file
    $upload_dir = '../../uploads/csv/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file_name = uniqid() . '_' . basename($_FILES['csv_file']['name']);
    $file_path = $upload_dir . $file_name;
    
    if (!move_uploaded_file($_FILES['csv_file']['tmp_name'], $file_path)) {
        throw new Exception('Gagal mengupload file');
    }
    
    // Validate CSV file
    $validation_result = validateCSVFile($file_path);
    if ($validation_result['status'] === 'error') {
        unlink($file_path);
        
        // Log aktivitas - Gagal validasi CSV
        log_action(
            'ADD_TEST_VALIDATION_FAILED', 
            "Validasi CSV gagal untuk tes: $kategori_tes", 
            [
                'nama_tes' => $kategori_tes,
                'error_message' => $validation_result['message'],
                'file_name' => $_FILES['csv_file']['name']
            ]
        );
        
        throw new Exception($validation_result['message']);
    }
    
    $questions = $validation_result['questions'];
    
    // Begin transaction
    $pdo->beginTransaction();
    
    try {
        // PERBAIKAN: Gunakan atribut 'status' dengan default 'nonaktif'
        $tes_sql = "INSERT INTO tes (kategori_tes, deskripsi_tes, status, created_at) VALUES (?, ?, 'nonaktif', NOW())";
        $tes_stmt = $pdo->prepare($tes_sql);
        
        if (!$tes_stmt->execute([$kategori_tes, $deskripsi_tes])) {
            throw new Exception('Gagal menyimpan data tes');
        }
        
        $tes_id = $pdo->lastInsertId();
        
        // Insert soal
        $soal_sql = "INSERT INTO soal_tes (id_tes, pertanyaan, created_at) VALUES (?, ?, NOW())";
        $soal_stmt = $pdo->prepare($soal_sql);
        
        // Insert opsi jawaban
        $opsi_sql = "INSERT INTO opsi_jawaban (id_soal, opsi, bobot, created_at) VALUES (?, ?, ?, NOW())";
        $opsi_stmt = $pdo->prepare($opsi_sql);
        
        $inserted_questions = 0;
        $inserted_options = 0;
        
        foreach ($questions as $question) {
            // Insert soal
            if (!$soal_stmt->execute([$tes_id, $question['pertanyaan']])) {
                throw new Exception('Gagal menyimpan soal');
            }
            
            $soal_id = $pdo->lastInsertId();
            $inserted_questions++;
            
            // Insert opsi jawaban
            foreach ($question['opsi'] as $huruf_opsi => $data_opsi) {
                if (!empty($data_opsi['teks']) && !is_null($data_opsi['bobot'])) {
                    if (!$opsi_stmt->execute([$soal_id, $data_opsi['teks'], $data_opsi['bobot']])) {
                        throw new Exception('Gagal menyimpan opsi jawaban');
                    }
                    
                    $inserted_options++;
                }
            }
        }
        
        // Commit transaction
        $pdo->commit();
        
        // Clean up uploaded file
        unlink($file_path);
        
        // Log aktivitas - Sukses tambah tes
        log_action(
            'ADD_TEST_SUCCESS', 
            "Berhasil menambahkan tes baru: $kategori_tes", 
            [
                'tes_id' => $tes_id,
                'nama_tes' => $kategori_tes,
                'jumlah_soal' => $inserted_questions,
                'jumlah_opsi' => $inserted_options,
                'file_name' => $_FILES['csv_file']['name'],
                'status' => 'nonaktif'
            ]
        );
        
        echo json_encode([
            'status' => 'success',
            'message' => "Tes berhasil ditambahkan dengan $inserted_questions soal dan $inserted_options opsi jawaban. Status: Nonaktif",
            'tes_id' => $tes_id
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        // Log aktivitas - Gagal tambah tes
        log_action(
            'ADD_TEST_FAILED', 
            "Gagal menambahkan tes: $kategori_tes", 
            [
                'nama_tes' => $kategori_tes,
                'error_message' => $e->getMessage(),
                'file_name' => $_FILES['csv_file']['name']
            ]
        );
        
        throw $e;
    }
    
} catch (Exception $e) {
    error_log("Error adding test: " . $e->getMessage());
    
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>