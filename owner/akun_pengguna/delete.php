<?php
session_start();

include '../../config/config.php';

if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'owner') {
    header("Location: ../../auth/login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Jangan hapus diri sendiri
    if ($id == $_SESSION['id_pengguna']) {
         $_SESSION['pesan_error'] = "Anda tidak dapat menghapus akun Anda sendiri!";
    } else {
         $query = "DELETE FROM pengguna WHERE id_pengguna='$id'";
         if(mysqli_query($conn, $query)) {
             $_SESSION['pesan_sukses'] = "Pengguna berhasil dihapus.";
         } else {
             $_SESSION['pesan_error'] = "Gagal menghapus pengguna: " . mysqli_error($conn);
         }
    }
}

header("Location: list.php");
exit();
?>
