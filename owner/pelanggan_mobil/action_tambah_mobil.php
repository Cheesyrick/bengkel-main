<?php
session_start();
include '../../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Tangkap id_pelanggan sebagai Foreign Key
    $id_pelanggan = mysqli_real_escape_string($conn, $_POST['id_pelanggan']);
    $plat_nomor   = mysqli_real_escape_string($conn, $_POST['plat_nomor']);
    $merk_mobil   = mysqli_real_escape_string($conn, $_POST['merk_mobil']);
    $tipe_mobil   = mysqli_real_escape_string($conn, $_POST['tipe_mobil']);
    $tahun_mobil  = mysqli_real_escape_string($conn, $_POST['tahun_mobil']);

    $query = "INSERT INTO mobil (id_pelanggan, merk_mobil, tipe_mobil, plat_nomor, tahun_mobil) 
              VALUES ('$id_pelanggan', '$merk_mobil', '$tipe_mobil', '$plat_nomor', '$tahun_mobil')";
              
    if(mysqli_query($conn, $query)){
        $_SESSION['pesan_sukses'] = "Data Mobil berhasil ditambahkan pada pelanggan tersebut!";
    } else {
        $_SESSION['pesan_error'] = "Gagal menambah mobil: " . mysqli_error($conn);
    }
    
    header("Location: list.php");
    exit();
}
?>
