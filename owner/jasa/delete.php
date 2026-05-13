<?php
session_start();
if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'owner') {
    header("Location: ../../auth/login.php");
    exit();
}
include '../../config/config.php';

if (isset($_GET['id'])) {
    $id_jasa = $_GET['id'];
    
    $query = "DELETE FROM jasa WHERE id_jasa = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_jasa);
    
    if ($stmt->execute()) {
        $_SESSION['pesan_sukses'] = "Jasa berhasil dihapus!";
    } else {
        $_SESSION['pesan_error'] = "Gagal menghapus jasa: " . $stmt->error;
    }
}

header("Location: list.php");
exit();
?>
