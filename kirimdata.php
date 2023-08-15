<?php
    include "koneksi.php";

    // Read Data from ESP
    $wifi = $_GET['wifi'];

    // Save Data to Tabel tabel_udara
    // $simpan = mysqli_query($koneksi, "INSERT into dbudara.tabel_udara (karbondioksida, oksigen) values ('$karbondioksida', '$oksigen')");
    if ($wifi){        
        $simpan = mysqli_query($koneksi ,"UPDATE `smartdoorlock`.`tabel_wifi` SET `status` = '$wifi' WHERE (`id` = '1')");   
    } else {
        $simpan = mysqli_query($koneksi ,"UPDATE `smartdoorlock`.`tabel_wifi` SET `status` = '0' WHERE (`id` = '1')");
    }

    //Response when Succesfully Connected
    if($simpan) {
        echo "Berhasil Terkirim";
    } else {
        echo "Gagal Terkirim";
    }
?>    
