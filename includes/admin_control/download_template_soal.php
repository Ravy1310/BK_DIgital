<?php
// includes/admin_control/download_template_soal.php
session_start();

// CEK LOGIN
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../../login.php");
    exit;
}

// CEK ROLE
if ($_SESSION['admin_role'] !== 'admin' && $_SESSION['admin_role'] !== 'superadmin') {
    header("Location: ../../login.php?error=unauthorized");
    exit;
}

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="template_import_soal.xls"');
header('Pragma: no-cache');
header('Expires: 0');
?>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        .instruction { 
            background-color: #f8f9fa; 
            padding: 15px; 
            border-left: 4px solid #004AAD;
        }
        .warning { 
            background-color: #fff3cd; 
            padding: 10px; 
            margin: 5px 0; 
            border-left: 4px solid #ffc107; 
        }
        th { 
            background-color: #004AAD; 
            color: white; 
            font-weight: bold; 
            padding: 10px;
            text-align: center;
        }
        td { 
            padding: 8px;
            border: 1px solid #ddd;
        }
        .example-row { 
            background-color: #f0f8ff; 
        }
    </style>
</head>
<body>
    <table border="1" width="100%">
        <!-- HEADER -->
        <tr>
            <th>PERTANYAAN</th>
            <th>OPSI_A</th>
            <th>BOBOT_A</th>
            <th>OPSI_B</th>
            <th>BOBOT_B</th>
            <th>OPSI_C</th>
            <th>BOBOT_C</th>
            <th>OPSI_D</th>
            <th>BOBOT_D</th>
            <th>OPSI_E</th>
            <th>BOBOT_E</th>
        </tr>
        
        <!-- CONTOH DATA 1 -->
        <tr class="example-row">
            <td>Apa hobi Anda?</td>
            <td>Membaca</td>
            <td>2</td>
            <td>Olahraga</td>
            <td>4</td>
            <td>Musik</td>
            <td>3</td>
            <td>Menulis</td>
            <td>1</td>
            <td>Bermain Game</td>
            <td>5</td>
        </tr>
        
        <!-- CONTOH DATA 2 -->
        <tr class="example-row">
            <td>Bagaimana cara Anda belajar?</td>
            <td>Visual</td>
            <td>3</td>
            <td>Auditori</td>
            <td>2</td>
            <td>Kinestetik</td>
            <td>4</td>
            <td>Membaca</td>
            <td>1</td>
            <td>Diskusi</td>
            <td>5</td>
        </tr>
        
        <!-- CONTOH DATA 3 -->
        <tr class="example-row">
            <td>Ketika menghadapi masalah, Anda biasanya?</td>
            <td>Analisis dulu</td>
            <td>4</td>
            <td>Tanya orang lain</td>
            <td>2</td>
            <td>Coba langsung</td>
            <td>5</td>
            <td>Tunda dulu</td>
            <td>1</td>
            <td>Cari referensi</td>
            <td>3</td>
        </tr>
        
        <!-- CONTOH DATA 4 -->
        <tr class="example-row">
            <td>Apakah Anda suka bekerja dalam tim?</td>
            <td>Sangat suka</td>
            <td>5</td>
            <td>Suka</td>
            <td>4</td>
            <td>Netral</td>
            <td>3</td>
            <td>Kurang suka</td>
            <td>2</td>
            <td>Tidak suka</td>
            <td>1</td>
        </tr>
        
        <!-- BARIS KOSONG UNTUK DIISI -->
        <tr>
            <td>Isi pertanyaan Anda di sini...</td>
            <td>Opsi A</td>
            <td>3</td>
            <td>Opsi B</td>
            <td>4</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>Isi pertanyaan Anda di sini...</td>
            <td>Opsi A</td>
            <td>2</td>
            <td>Opsi B</td>
            <td>5</td>
            <td>Opsi C</td>
            <td>3</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        
        <!-- PETUNJUK -->
        <tr>
            <td colspan="11" class="instruction">
                <strong style="color: #004AAD; font-size: 16px;">PETUNJUK PENGGUNAAN:</strong><br><br>
                
                <div class="warning">
                    <strong>📝 CARA MENYIMPAN:</strong><br>
                    1. Klik <strong>File → Save As</strong><br>
                    2. Pilih <strong>CSV (Comma delimited) (*.csv)</strong><br>
                    3. Klik <strong>Save</strong><br>
                    4. Jika ada peringatan, pilih <strong>Yes</strong>
                </div>
                
                <strong>🎯 ATURAN PENGISIAN:</strong><br>
                • <strong>PERTANYAAN:</strong> Teks pertanyaan (wajib diisi)<br>
                • <strong>OPSI_A sampai OPSI_E:</strong> Teks pilihan jawaban<br>
                • <strong>BOBOT_A sampai BOBOT_E:</strong> Angka 1-5 (wajib diisi jika opsinya diisi)<br>
                • <strong>Minimal 2 opsi</strong> harus diisi (contoh: OPSI_A dan OPSI_B)<br><br>
                
                <strong>📊 CONTOH BOBOT:</strong><br>
                1 = Sangat Tidak Setuju / Tidak Pernah<br>
                2 = Tidak Setuju / Jarang<br>
                3 = Netral / Kadang-kadang<br>
                4 = Setuju / Sering<br>
                5 = Sangat Setuju / Selalu<br><br>
                
                <strong>⚠️ PERHATIAN:</strong><br>
                • Jangan ubah nama kolom header<br>
                • File harus disimpan sebagai <strong>CSV</strong><br>
                • Hapus baris petunjuk ini sebelum upload jika perlu
            </td>
        </tr>
    </table>
</body>
</html>