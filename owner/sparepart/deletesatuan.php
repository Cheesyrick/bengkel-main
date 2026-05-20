<?php
session_start();
include '../../config/config.php';

if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'owner') {
    header("Location: ../../auth/login.php");
    exit();
}

if(isset($_GET['id'])){
    $id_satuan = $_GET['id'];

    $query = "DELETE FROM satuan WHERE id_satuan = '$id_satuan'";
    
    if(mysqli_query($conn, $query)){
        $_SESSION['pesan_sukses'] = "Satuan sparepart berhasil dihapus";
    } else {
        $_SESSION['pesan_error'] = "Gagal menghapus satuan sparepart: " . mysqli_error($conn);
    }
}

header("Location: listsatuan.php");
exit();
?>