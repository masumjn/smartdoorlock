<?php
include "koneksi.php";

// Koneksi ke Database
$koneksi = mysqli_connect($hostname, $username, $password, $database);

// Tangkap Parameter dari Ajax
$status = $_GET['status'];

// Cek untuk Update ke tabel_relay
if ($status == "1") {
    mysqli_query($koneksi, "UPDATE `tabel_relay` SET `status` = '1'");
    //Respon
    echo "ON";
} else {
    mysqli_query($koneksi, "UPDATE `tabel_relay` SET `status` = '0'");
    //Respon
    echo "OFF";
}
