<?php
session_start();
include '../../config/config.php';

if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'owner') {
    header("Location: ../../auth/login.php");
    exit();
}

if(isset($_GET['id'])){
    $id_tipe_sp = $_GET['id'];

    $query = "DELETE FROM tipe_sparepart WHERE id_tipe_sp = '$id_tipe_sp'";
    
    if(mysqli_query($conn, $query)){
        $_SESSION['pesan_sukses'] = "Tipe sparepart berhasil dihapus";
    } else {
        $_SESSION['pesan_error'] = "Gagal menghapus tipe sparepart: " . mysqli_error($conn);
    }
}

header("Location: listtipe.php");
exit();
?>