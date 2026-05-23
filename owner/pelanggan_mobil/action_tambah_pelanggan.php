<?php
session_start();
include '../../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_pelanggan = mysqli_real_escape_string($conn, $_POST['nama_pelanggan']);
    $no_telepon     = mysqli_real_escape_string($conn, $_POST['no_telepon']);
    $alamat         = mysqli_real_escape_string($conn, $_POST['alamat']);
    
    $query = "INSERT INTO pelanggan (nama_pelanggan, no_telp, alamat) VALUES ('$nama_pelanggan', '$no_telepon', '$alamat')";
    
    if(mysqli_query($conn, $query)){
        $_SESSION['pesan_sukses'] = "Data Pelanggan berhasil ditambahkan!";
    } else {
        $_SESSION['pesan_error'] = "Gagal menambah pelanggan: " . mysqli_error($conn);
    }
    
    header("Location: list.php");
    exit();
}
?>
