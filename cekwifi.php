<?php
    include "koneksi.php";

    // Koneksi ke Database
    $koneksi = mysqli_connect($hostname, $username, $password, $database);

    // Read Data dari Tabel tabel_udara
    $sql = mysqli_query($koneksi, "select * from tabel_wifi");

    // Read Data Terbaru
    $data = mysqli_fetch_array($sql);
    $status = $data['status'];

    $value = "";

    if ($status == "1"){
        $value = "On";
    } else {
        $value = "Off";
    }
    // Cetak Nilai
    echo $value;
?>