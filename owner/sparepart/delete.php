<?php
session_start();
if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'owner') {
    header("Location: ../../auth/login.php");
    exit();
}
include '../../config/config.php';

if(isset($_GET['id'])){
    $id_sparepart = $_GET['id'];

    $query = "DELETE FROM sparepart WHERE id_sparepart = '$id_sparepart'";
    if(mysqli_query($conn, $query)){
        $_SESSION['pesan_sukses'] = "Sparepart berhasil dihapus.";
    } else {
        $_SESSION['pesan_error'] = "Gagal menghapus sparepart: " . mysqli_error($conn);
    }
}
header("Location: list.php");
exit();
?>