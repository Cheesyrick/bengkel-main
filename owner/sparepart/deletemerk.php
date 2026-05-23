<?php
session_start();
include '../../config/config.php';

if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'owner') {
    header("Location: ../../auth/login.php");
    exit();
}

if(isset($_GET['id'])){
    $id_merk = $_GET['id'];

    $query = "DELETE FROM merk WHERE id_merk = '$id_merk'";
    
    if(mysqli_query($conn, $query)){
        $_SESSION['pesan_sukses'] = "Merek sparepart berhasil dihapus";
    } else {
        $_SESSION['pesan_error'] = "Gagal menghapus merek sparepart: " . mysqli_error($conn);
    }
}

header("Location: listmerk.php");
exit();
?>