<?php
session_start();
require_once "db_connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Ambil data dari form
        $id_siswa = $_POST['id_siswa'] ?? null;
        $anonim = isset($_POST['anonim']) ? (int)$_POST['anonim'] : 0;
        $jenis_laporan = $_POST['jenis_laporan'] ?? '';
        $jenis_kejadian = $_POST['jenis_kejadian'] ?? '';
        $penjelasan = $_POST['penjelasan'] ?? '';
        $nama_siswa = $_POST['nama_siswa'] ?? '';
        $kelas_siswa = $_POST['kelas_siswa'] ?? '';
        
        // Validasi data
        if (empty($jenis_laporan) || empty($jenis_kejadian) || empty($penjelasan)) {
            $_SESSION['error'] = "Semua field harus diisi!";
            header("Location: ../../pages/siswa/pengaduan.php");
            exit();
        }
        
        // Validasi panjang penjelasan
        if (strlen($penjelasan) < 20) {
            $_SESSION['error'] = "Penjelasan harus minimal 20 karakter!";
            header("Location: ../../pages/siswa/pengaduan.php");
            exit();
        }
        
        // ============================================
        // LOGIKA UNTUK TERIDENTIFIKASI
        // ============================================
        if ($anonim == 0) { // Mode TERIDENTIFIKASI
            // Validasi: Harus ada ID siswa
            if (empty($id_siswa)) {
                $_SESSION['error'] = "ID siswa tidak valid untuk pengaduan teridentifikasi!";
                header("Location: ../../pages/siswa/pengaduan.php");
                exit();
            }
            
            // Validasi: Pastikan siswa ada di database
            $stmt_check = $pdo->prepare("SELECT nama, kelas FROM siswa WHERE id_siswa = ?");
            $stmt_check->execute([$id_siswa]);
            $siswa_data = $stmt_check->fetch(PDO::FETCH_ASSOC);
            
            if (!$siswa_data) {
                $_SESSION['error'] = "Siswa dengan ID tersebut tidak ditemukan!";
                header("Location: ../../pages/siswa/pengaduan.php");
                exit();
            }
            
            // Ambil nama dan kelas dari database (lebih aman daripada dari form)
            $nama_siswa = $siswa_data['nama'];
            $kelas_siswa = $siswa_data['kelas'];
            
            // Insert ke database DENGAN id_siswa yang valid
            $sql = "INSERT INTO pengaduan 
                    (id_siswa, jenis_laporan, jenis_kejadian, deskripsi, nama_siswa, kelas_siswa, status, tanggal_pengaduan) 
                    VALUES 
                    (:id_siswa, :jenis_laporan, :jenis_kejadian, :deskripsi, :nama_siswa, :kelas_siswa, 'menunggu', NOW())";
            
            $stmt = $pdo->prepare($sql);
            
            $stmt->execute([
                ':id_siswa' => $id_siswa, // ID siswa asli
                ':jenis_laporan' => $jenis_laporan,
                ':jenis_kejadian' => $jenis_kejadian,
                ':deskripsi' => $penjelasan,
                ':nama_siswa' => $nama_siswa,
                ':kelas_siswa' => $kelas_siswa
            ]);
            
            $_SESSION['success'] = "Pengaduan berhasil dikirim! Status: Menunggu. Nama: " . $nama_siswa;
            
        } else { // Mode ANONIM
            // Set semua data siswa menjadi NULL atau 'Anonim'
            $id_siswa = null;
            $nama_siswa = 'Anonim';
            $kelas_siswa = null;
            
            // Insert ke database DENGAN id_siswa = NULL
            $sql = "INSERT INTO pengaduan 
                    (id_siswa, jenis_laporan, jenis_kejadian, deskripsi, nama_siswa, kelas_siswa, status, tanggal_pengaduan) 
                    VALUES 
                    (NULL, :jenis_laporan, :jenis_kejadian, :deskripsi, :nama_siswa, :kelas_siswa, 'menunggu', NOW())";
            
            $stmt = $pdo->prepare($sql);
            
            $stmt->execute([
                ':jenis_laporan' => $jenis_laporan,
                ':jenis_kejadian' => $jenis_kejadian,
                ':deskripsi' => $penjelasan,
                ':nama_siswa' => $nama_siswa,
                ':kelas_siswa' => $kelas_siswa
            ]);
            
            $_SESSION['success'] = "Pengaduan anonim berhasil dikirim! Status: Menunggu";
        }
        
        // Redirect kembali ke halaman yang sesuai
        if ($anonim == 1) {
            header("Location: ../../pages/siswa/pengaduan.php?anonim=1");
        } else {
            // Ambil id siswa dari session untuk redirect
            require_once __DIR__ . "/../../includes/siswa_control/verification_handler.php";
            $siswa_data = getCurrentStudent();
            $id_redirect = $siswa_data['id_siswa'];
            header("Location: ../../pages/siswa/pengaduan.php?idsiswa=" . urlencode($id_redirect) . "&anonim=0");
        }
        exit();
        
    } catch (Exception $e) {
        $_SESSION['error'] = "Gagal mengirim pengaduan: " . $e->getMessage();
        
        // Redirect kembali dengan parameter yang sesuai
        if (isset($anonim) && $anonim == 1) {
            header("Location: ../../pages/siswa/pengaduan.php?anonim=1");
        } else {
            $id_redirect = $_POST['id_siswa'] ?? '';
            header("Location: ../../pages/siswa/pengaduan.php?idsiswa=" . urlencode($id_redirect) . "&anonim=0");
        }
        exit();
    }
} else {
    $_SESSION['error'] = "Metode request tidak valid!";
    header("Location: ../../pages/siswa/pengaduan.php");
    exit();
}
?>