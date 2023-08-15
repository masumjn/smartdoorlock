<?php
include "koneksi.php";

// Koneksi ke Database
$koneksi = mysqli_connect($hostname, $username, $password, $database);

// Read Data from Sensor
$status = $_GET['status'];
// $status = 1; // For Testing
if ($status == "1"){
    $status = "#Terbuka#";
} else {
    $status = "Terkunci";
}
// Save Data to Tabel tabel_udara
$simpan = mysqli_query($koneksi, "insert into smartdoorlock.tabel_history (`status_pintu`) values ('$status')");

//Response when Succesfully Connected
if ($simpan) {
    echo "Data Berhasil Terkirim";
} else {
    echo "Data Gagal Terkirim";
}
