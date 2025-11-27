<?php
// includes/admin_control/download_template_excel.php
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="template_import_siswa.xls"');
header('Pragma: no-cache');
header('Expires: 0');
?>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body>
    <table border="1">
        <tr style="background-color: #004AAD; color: white; font-weight: bold;">
            <th>ID_SISWA</th>
            <th>NAMA</th>
            <th>KELAS</th>
            <th>TAHUN_MASUK</th>
            <th>JENIS_KELAMIN</th>
        </tr>
        <tr>
            <td>1011</td>
            <td>Nama Siswa 1</td>
            <td>X.1</td>
            <td>2024</td>
            <td>Laki-laki</td>
        </tr>
        <tr>
            <td>1021</td>
            <td>Nama Siswa 2</td>
            <td>X.2</td>
            <td>2024</td>
            <td>Perempuan</td>
        </tr>
        <tr style="background-color: #f8f9fa;">
            <td colspan="5" style="color: #666; font-style: italic;">
                <strong>Petunjuk:</strong><br>
                1. Save file sebagai CSV (File > Save As > CSV)<br>
                2. Jangan ubah nama kolom<br>
                3. Jenis kelamin harus: Laki-laki atau Perempuan<br>
                4. Tahun masuk harus 4 digit angka<br>
                5. ID Siswa harus unik
            </td>
        </tr>
    </table>
</body>
</html>