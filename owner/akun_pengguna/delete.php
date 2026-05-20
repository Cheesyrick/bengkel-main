<?php
session_start();

include '../../config/config.php';

if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'owner') {
    header("Location: ../../auth/login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id_pengguna = $_GET['id'];

    $query = "DELETE FROM pengguna WHERE id_pengguna = '$id_pengguna'";
    if(mysqli_query($conn, $query)) {
        $_SESSION['pesan_sukses'] = "Pengguna berhasil dihapus.";
    } else {
        $_SESSION['pesan_error'] = "Gagal menghapus pengguna: " . mysqli_error($conn);
    }
}

header("Location: list.php");
exit();
?>
