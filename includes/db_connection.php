<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bk_digital";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Koneksi gagal: " . $conn->connect_error]));
}
?>
