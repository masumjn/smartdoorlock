<?php
    include "koneksi.php";

    // Koneksi ke Database
    $koneksi = mysqli_connect($hostname, $username, $password, $database);

    // Read Data dari Tabel tabel_udara
    $result = mysqli_query($koneksi, "select * from tabel_history order by id desc limit 10");

    if (!$result) {
        die("Query failed: " . mysqli_error($koneksi));
    }
    
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row; // Add each row to the data array
    }
    
    mysqli_close($koneksi);
    
    // Return data as JSON
    header("Content-Type: application/json");
    echo json_encode($data);
?>