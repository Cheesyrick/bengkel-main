<?php
session_start();
if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'service_advisor') {
    header("Location: ../../auth/login.php");
    exit();
}
include '../../config/config.php';

if (isset($_GET['id_pelanggan'])) {
    $id_pelanggan = $_GET['id_pelanggan'];
    
    $query = "DELETE FROM pelanggan WHERE id_pelanggan = '$id_pelanggan'";
    if(mysqli_query($conn, $query)) {
        $_SESSION['pesan_sukses'] = "Pelanggan berhasil dihapus.";
    } else {
        $_SESSION['pesan_error'] = "Gagal menghapus pelanggan: " . mysqli_error($conn);
    }
}

header("Location: list.php");
exit();
?>