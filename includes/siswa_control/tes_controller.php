<?php
// controllers/siswa/tes_controller.php

// Koneksi database
require_once __DIR__ . "/../../includes/db_connection.php";

/**
 * Fungsi untuk mendapatkan daftar tes yang BELUM dikerjakan oleh siswa
 */
function getDaftarTesBelumDikerjakan($id_siswa) {
    global $pdo;
    $daftarTes = [];
    
    try {
        // Ambil semua tes aktif yang BELUM dikerjakan oleh siswa ini
        $stmt = $pdo->prepare("
            SELECT t.*, 
                   COUNT(st.id_soal) as total_soal
            FROM tes t
            LEFT JOIN soal_tes st ON t.id_tes = st.id_tes
            WHERE t.status = 'aktif'
              AND t.id_tes NOT IN (
                  SELECT h.id_tes 
                  FROM hasil_tes h 
                  WHERE h.id_siswa = ?
              )
            GROUP BY t.id_tes
            ORDER BY t.id_tes ASC
        ");
        $stmt->execute([$id_siswa]);
        $daftarTes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        $_SESSION['error_tes'] = "Error mengambil data tes: " . $e->getMessage();
    }
    
    return $daftarTes;
}

/**
 * Fungsi untuk mendapatkan riwayat tes siswa
 */
/**
 * Fungsi untuk mendapatkan riwayat tes siswa - SESUAIKAN
 */
function getRiwayatTes($id_siswa) {
    global $pdo;
    $riwayat = [];
    
    try {
        $q = $pdo->prepare("
            SELECT h.*, t.kategori_tes 
            FROM hasil_tes h
            JOIN tes t ON h.id_tes = t.id_tes
            WHERE h.id_siswa = ?
            ORDER BY h.tanggal_submit DESC
        ");
        $q->execute([$id_siswa]);
        $riwayat = $q->fetchAll(PDO::FETCH_ASSOC);
        
        // Tambahkan informasi jumlah soal untuk setiap riwayat
        foreach ($riwayat as &$r) {
            $jawaban = json_decode($r['jawaban'], true);
            $r['jumlah_soal'] = is_array($jawaban) ? count($jawaban) : 0;
        }
        unset($r);
        
    } catch (PDOException $e) {
        $_SESSION['error_riwayat'] = "Error mengambil riwayat tes: " . $e->getMessage();
    }
    
    return $riwayat;
}

/**
 * Fungsi untuk mendapatkan detail tes berdasarkan ID
 */
function getTesById($id_tes) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM tes WHERE id_tes = ? AND status = 'aktif'");
        $stmt->execute([$id_tes]);
        $tes = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($tes) {
            // Ambil soal untuk tes ini
            $stmtSoal = $pdo->prepare("
                SELECT s.* 
                FROM soal_tes s 
                WHERE s.id_tes = ? 
                ORDER BY s.id_soal ASC
            ");
            $stmtSoal->execute([$id_tes]);
            $soal = $stmtSoal->fetchAll(PDO::FETCH_ASSOC);
            
            // Ambil opsi jawaban untuk setiap soal
            foreach ($soal as &$s) {
                $stmtOpsi = $pdo->prepare("
                    SELECT * 
                    FROM opsi_jawaban 
                    WHERE id_soal = ? 
                    ORDER BY id_opsi ASC
                ");
                $stmtOpsi->execute([$s['id_soal']]);
                $s['opsi'] = $stmtOpsi->fetchAll(PDO::FETCH_ASSOC);
            }
            unset($s);
            
            $tes['soal'] = $soal;
        }
        
        return $tes;
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error mengambil detail tes: " . $e->getMessage();
        return null;
    }
}
/**
 * Fungsi untuk submit tes - SESUAIKAN DENGAN STRUKTUR TABEL
 */
function submitTes($id_siswa, $id_tes, $jawaban) {
    global $pdo;
    
    error_log("submitTes called: Siswa=$id_siswa, Tes=$id_tes");
    
    try {
        // Validasi input
        if (empty($id_siswa) || empty($id_tes) || empty($jawaban)) {
            throw new Exception("Data tidak lengkap");
        }
        
        // 1. HITUNG NILAI DARI JAWABAN
        $total_nilai = 0;
        $jawaban_json = json_encode($jawaban); // Konversi ke JSON untuk disimpan di kolom 'jawaban'
        
        foreach ($jawaban as $id_soal => $id_opsi) {
            $id_soal = (int)$id_soal;
            $id_opsi = (int)$id_opsi;
            
            // Ambil bobot opsi
            $stmtBobot = $pdo->prepare("SELECT bobot FROM opsi_jawaban WHERE id_opsi = ?");
            $stmtBobot->execute([$id_opsi]);
            $bobot = $stmtBobot->fetchColumn() ?? 0;
            $total_nilai += $bobot;
        }
        
        // 2. SIMPAN KE hasil_tes (TANPA id_sesi)
        $stmtHasil = $pdo->prepare("
            INSERT INTO hasil_tes (id_siswa, id_tes, nilai, jawaban, tanggal_submit) 
            VALUES (?, ?, ?, ?, NOW())
        ");
        
        if (!$stmtHasil->execute([$id_siswa, $id_tes, $total_nilai, $jawaban_json])) {
            $errorInfo = $stmtHasil->errorInfo();
            throw new Exception("Gagal menyimpan hasil tes: " . ($errorInfo[2] ?? "Unknown error"));
        }
        
        $id_hasil = $pdo->lastInsertId();
        
        error_log("Hasil tes berhasil disimpan: ID=$id_hasil, Nilai=$total_nilai");
        
        return [
            'success' => true,
            'id_hasil' => $id_hasil,
            'nilai' => $total_nilai,
            'jawaban_count' => count($jawaban),
            'message' => 'Tes berhasil disubmit!'
        ];
        
    } catch (Exception $e) {
        error_log("ERROR submitTes: " . $e->getMessage());
        
        return [
            'success' => false,
            'message' => 'Gagal menyimpan tes: ' . $e->getMessage()
        ];
    }
}
/**
 * Fungsi untuk cek apakah siswa sudah mengerjakan tes
 */
function isTesSudahDikerjakan($id_siswa, $id_tes) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM hasil_tes 
            WHERE id_siswa = ? AND id_tes = ?
        ");
        $stmt->execute([$id_siswa, $id_tes]);
        $count = $stmt->fetchColumn();
        
        return $count > 0;
        
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Fungsi untuk mendapatkan hasil tes berdasarkan ID
 */
/**
 * Fungsi untuk mendapatkan hasil tes berdasarkan ID - SESUAIKAN
 */
/**
 * Fungsi untuk mendapatkan hasil tes berdasarkan ID - VERSI DIPERBAIKI
 */
/**
 * Fungsi untuk mendapatkan hasil tes berdasarkan ID - VERSI DIPERBAIKI
 */
function getHasilTesById($id_hasil) {
    global $pdo;
    
    // Log untuk debugging
    error_log("getHasilTesById dipanggil dengan ID: $id_hasil");
    
    try {
        // ✅ Coba cari dengan id_hash (kolom primary key)
        $stmt = $pdo->prepare("
            SELECT h.*, t.kategori_tes, t.deskripsi_tes, s.nama as nama_siswa, s.kelas as kelas_siswa
            FROM hasil_tes h
            JOIN tes t ON h.id_tes = t.id_tes
            JOIN siswa s ON h.id_siswa = s.id_siswa
            WHERE h.id_hasil = ?
        ");
        $stmt->execute([$id_hasil]);
        $hasil = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($hasil) {
            error_log("Data ditemukan dengan id_hasil: " . print_r($hasil, true));
        } else {
            // ✅ Jika tidak ditemukan dengan id_hash, coba cari dengan kolom lain
            error_log("Tidak ditemukan dengan id_hasil, mencoba pencarian lain...");
            
            // Coba cari berdasarkan kombinasi id_siswa dan id_tes
            // Atau cari berdasarkan created_at terbaru untuk siswa tersebut
            $stmt2 = $pdo->prepare("
                SELECT h.*, t.kategori_tes, t.deskripsi_tes, s.nama as nama_siswa, s.kelas as kelas_siswa
                FROM hasil_tes h
                JOIN tes t ON h.id_tes = t.id_tes
                JOIN siswa s ON h.id_siswa = s.id_siswa
                WHERE h.id_siswa = ?
                ORDER BY h.tanggal_submit DESC
                LIMIT 1
            ");
            // Kita tidak punya id_siswa di sini, jadi skip dulu
            // Kembalikan false
            return null;
        }
        
        // Parse jawaban dari JSON
        if ($hasil && !empty($hasil['jawaban'])) {
            $jawaban_data = json_decode($hasil['jawaban'], true);
            
            if (is_array($jawaban_data) && !empty($jawaban_data)) {
                $hasil['jawaban_detail'] = [];
                
                foreach ($jawaban_data as $id_soal => $id_opsi) {
                    $id_soal = (int)$id_soal;
                    $id_opsi = (int)$id_opsi;
                    
                    if ($id_soal <= 0 || $id_opsi <= 0) {
                        continue;
                    }
                    
                    // Ambil detail soal dan opsi
                    $stmtDetail = $pdo->prepare("
                        SELECT st.pertanyaan, oj.opsi, oj.bobot
                        FROM soal_tes st
                        JOIN opsi_jawaban oj ON st.id_soal = oj.id_soal
                        WHERE st.id_soal = ? AND oj.id_opsi = ?
                    ");
                    
                    if ($stmtDetail->execute([$id_soal, $id_opsi])) {
                        $detail = $stmtDetail->fetch(PDO::FETCH_ASSOC);
                        
                        if ($detail) {
                            $hasil['jawaban_detail'][] = [
                                'id_soal' => $id_soal,
                                'id_opsi' => $id_opsi,
                                'pertanyaan' => $detail['pertanyaan'] ?? 'Tidak tersedia',
                                'opsi' => $detail['opsi'] ?? 'Tidak tersedia',
                                'bobot' => $detail['bobot'] ?? 0
                            ];
                        }
                    }
                }
            }
        }
        
        return $hasil;
        
    } catch (PDOException $e) {
        error_log("Error getHasilTesById: " . $e->getMessage());
        return null;
    }
}
/**
 * Fungsi untuk mendapatkan semua tes (untuk admin)
 */
function getAllTes() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM tes ORDER BY id_tes ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error mengambil data tes: " . $e->getMessage();
        return [];
    }
}
?>