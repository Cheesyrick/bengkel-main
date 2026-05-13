<?php
session_start();
if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'owner') {
    header("Location: ../../auth/login.php");
    exit();
}
include '../../config/config.php';

if (isset($_GET['id_pelanggan'])) {
    $id_pelanggan = $_GET['id_pelanggan'];
    
    $query = "DELETE FROM pelanggan WHERE id_pelanggan = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_pelanggan);
    
    if ($stmt->execute()) {
        $_SESSION['pesan_sukses'] = "Pelanggan dan mobil berhasil dihapus!";
    } else {
        $_SESSION['pesan_error'] = "Gagal menghapus pelanggan dan mobil: " . $stmt->error;
    }
}

header("Location: list.php");
exit();
?>