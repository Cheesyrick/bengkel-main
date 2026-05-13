<?php
session_start();
if(!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'owner') {
    header("Location: ../../auth/login.php");
    exit();
}

include '../../config/config.php';

if(isset($_GET['id'])) {
    $id_jenis_jasa = $_GET['id'];

    $query = "DELETE FROM jenis_jasa WHERE id_jenis_jasa = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_jenis_jasa);

    if ($stmt-> execute()) {
        $_SESSION['pesan_sukses'] = "Jenis jasa berhasil dihapus!";
    } else {
        $_SESSION['pesan_error'] = "Gagal menghapus jenis jasa: " . $stmt->error;
    }
}

header("Location: listjenis.php");
exit();
?>