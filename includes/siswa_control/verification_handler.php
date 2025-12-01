<?php

require_once __DIR__ . "/../../includes/db_connection.php";

class VerificationHandler {
    private $pdo;
    private $redirect_urls = [
        'jadwal' => 'jadwaltemu.php',
        'pengaduan' => 'pengaduan.php',
        'tes' => 'tesbk.php'
    ];
    
    private $session_timeout = 14400; // 4 jam

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Verifikasi ID siswa dan redirect ke halaman tujuan
     */
    public function verifyAndRedirect($id_siswa, $type) {
        // Validasi type
        if (!array_key_exists($type, $this->redirect_urls)) {
            return [
                'success' => false,
                'message' => 'Tipe verifikasi tidak valid'
            ];
        }

        // Cek koneksi database
        if (!$this->pdo) {
            return [
                'success' => false,
                'message' => 'Koneksi database gagal'
            ];
        }

        // Validasi ID siswa
        if (empty($id_siswa)) {
            return [
                'success' => false,
                'message' => 'ID Siswa harus diisi'
            ];
        }

        // Bersihkan ID siswa
        $id_siswa = trim($id_siswa);

        try {
            // Query ke database
            $stmt = $this->pdo->prepare("SELECT id_siswa, nama, kelas FROM siswa WHERE id_siswa = ?");
            $stmt->execute([$id_siswa]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                // Set session berdasarkan type
                $this->setSession($data, $type);

                // Redirect ke halaman tujuan
                $redirect_url = $this->redirect_urls[$type];
                
                return [
                    'success' => true,
                    'redirect_url' => $redirect_url,
                    'data' => $data
                ];

            } else {
                return [
                    'success' => false,
                    'message' => '❌ ID Siswa tidak ditemukan. Periksa kembali.'
                ];
            }
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => '❌ Terjadi kesalahan database: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Set session berdasarkan tipe verifikasi
     */
    private function setSession($data, $type) {
        // Hapus session lama terlebih dahulu
        $this->destroyVerificationSession();

        switch ($type) {
            case 'jadwal':
                $_SESSION['siswa_logged_in'] = true;
                $_SESSION['siswa_id'] = $data['id_siswa'];
                $_SESSION['siswa_nama'] = $data['nama'];
                $_SESSION['siswa_kelas'] = $data['kelas'] ?? '';
                $_SESSION['verified_for'] = 'jadwal';
                break;

            case 'pengaduan':
                $_SESSION['verified_siswa_id'] = $data['id_siswa'];
                $_SESSION['siswa_nama'] = $data['nama'];
                $_SESSION['siswa_kelas'] = $data['kelas'] ?? '';
                $_SESSION['verified_for'] = 'pengaduan';
                break;

            case 'tes':
                $_SESSION['id_valid'] = true;
                $_SESSION['id_siswa'] = $data['id_siswa'];
                $_SESSION['siswa_nama'] = $data['nama'];
                $_SESSION['siswa_kelas'] = $data['kelas'] ?? '';
                $_SESSION['verified_for'] = 'tes';
                break;
        }

        // Set waktu verifikasi dan aktivitas terakhir
        $_SESSION['verified_at'] = time();
        $_SESSION['last_activity'] = time();
        $_SESSION['session_expires'] = time() + $this->session_timeout;
    }

    /**
     * Validasi apakah session masih aktif untuk type tertentu
     */
    public static function validateSession($type) {
        // Cek apakah session sudah expired
        if (isset($_SESSION['verified_at']) && (time() - $_SESSION['verified_at']) > 14400) {
            self::destroyVerificationSession();
            return false;
        }

        // Update last activity time
        if (isset($_SESSION['last_activity'])) {
            $_SESSION['last_activity'] = time();
        }

        switch ($type) {
            case 'jadwal':
                return isset($_SESSION['siswa_logged_in']) && 
                       $_SESSION['siswa_logged_in'] === true &&
                       ($_SESSION['verified_for'] ?? '') === 'jadwal';
            
            case 'pengaduan':
                return isset($_SESSION['verified_siswa_id']) &&
                       ($_SESSION['verified_for'] ?? '') === 'pengaduan';
            
            case 'tes':
                return isset($_SESSION['id_valid']) && 
                       $_SESSION['id_valid'] === true &&
                       ($_SESSION['verified_for'] ?? '') === 'tes';
            
            default:
                return false;
        }
    }

    /**
     * Redirect ke halaman verifikasi berdasarkan tipe
     */
    public static function redirectToVerification($type) {
        $verification_pages = [
            'jadwal' => 'verifikasi_jadwal.php',
            'pengaduan' => 'verifikasi_pengaduan.php',
            'tes' => 'verifikasi_tes.php'
        ];

        if (isset($verification_pages[$type])) {
            header("Location: " . $verification_pages[$type]);
            exit;
        } else {
            header("Location: verifikasi_jadwal.php");
            exit;
        }
    }

    /**
     * Dapatkan data siswa dari session
     */
    public static function getStudentData() {
        return [
            'id_siswa' => $_SESSION['siswa_id'] ?? $_SESSION['id_siswa'] ?? $_SESSION['verified_siswa_id'] ?? null,
            'nama' => $_SESSION['siswa_nama'] ?? null,
            'kelas' => $_SESSION['siswa_kelas'] ?? null,
            'verified_for' => $_SESSION['verified_for'] ?? null
        ];
    }

    /**
     * Hancurkan session verifikasi
     */
    public static function destroyVerificationSession() {
        unset(
            $_SESSION['siswa_logged_in'],
            $_SESSION['siswa_id'],
            $_SESSION['siswa_nama'],
            $_SESSION['siswa_kelas'],
            $_SESSION['verified_siswa_id'],
            $_SESSION['id_valid'],
            $_SESSION['id_siswa'],
            $_SESSION['verified_for'],
            $_SESSION['verified_at'],
            $_SESSION['last_activity'],
            $_SESSION['session_expires']
        );
    }
}

// Inisialisasi handler
try {
    $verificationHandler = new VerificationHandler($pdo);
} catch (Exception $e) {
    $verificationHandler = null;
}

/**
 * FUNGSI HELPER untuk digunakan di file lain
 */

/**
 * Proses verifikasi dari form POST
 */
function processVerification($type) {
    global $verificationHandler;
    
    if (!$verificationHandler) {
        return "❌ Sistem verifikasi sedang tidak tersedia. Silakan coba lagi.";
    }
    
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['cek_id'])) {
        $id_siswa = trim($_POST['id_siswa'] ?? '');
        
        $result = $verificationHandler->verifyAndRedirect($id_siswa, $type);
        
        if ($result['success']) {
            header("Location: " . $result['redirect_url']);
            exit;
        } else {
            return $result['message'];
        }
    }
    return null;
}

/**
 * Validasi session dan redirect jika tidak valid
 */
function validateAndRedirect($type) {
    if (!VerificationHandler::validateSession($type)) {
        VerificationHandler::redirectToVerification($type);
    }
}

/**
 * Cek apakah user sudah terverifikasi untuk akses tertentu
 */
function isVerifiedFor($type) {
    return VerificationHandler::validateSession($type);
}

/**
 * Dapatkan data siswa yang sedang login
 */
function getCurrentStudent() {
    return VerificationHandler::getStudentData();
}

/**
 * Logout / clear verification session
 */
function logoutVerification() {
    VerificationHandler::destroyVerificationSession();
}
?>