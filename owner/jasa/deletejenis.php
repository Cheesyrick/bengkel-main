<?php
session_start();
if(!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'owner') {
    header("Location: ../../auth/login.php");
    exit();
}

include '../../config/config.php';

if(isset($_GET['id'])) {
    $id_jenis_jasa = $_GET['id'];

    $query = "DELETE FROM jenis_jasa WHERE id_jenis_jasa = '$id_jenis_jasa'";
    if(mysqli_query($conn, $query)) {
        $_SESSION['pesan_sukses'] = "Jenis jasa berhasil dihapus.";
    } else {
        $_SESSION['pesan_error'] = "Gagal menghapus jenis jasa: " . mysqli_error($conn);
    }
}

header("Location: listjenis.php");
exit();
?>