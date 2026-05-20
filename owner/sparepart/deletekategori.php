<?php
session_start();
include '../../config/config.php';

if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'owner') {
    header("Location: ../../auth/login.php");
    exit();
}

if(isset($_GET['id'])){
    $id_kategori = $_GET['id'];

    $query = "DELETE FROM kategori_sparepart WHERE id_kategori = '$id_kategori'";
    
    if(mysqli_query($conn, $query)){
        $_SESSION['pesan_sukses'] = "Kategori sparepart berhasil dihapus";
    } else {
        $_SESSION['pesan_error'] = "Gagal menghapus kategori sparepart: " . mysqli_error($conn);
    }
}

header("Location: listkategori.php");
exit();
?>