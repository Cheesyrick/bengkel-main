<?php
session_start();
include '../../config/config.php';

if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'service_advisor') {
    header("Location: ../../auth/login.php");
    exit();
}

if(isset($_GET['id_permintaan'])){
    $id_permintaan = $_GET['id_permintaan'];

    // Restore sparepart stock first
    $old_dsp = $conn->query("SELECT id_sparepart, qty FROM detail_sparepart WHERE id_permintaan = '$id_permintaan'");
    while ($row = $old_dsp->fetch_assoc()) {
        $conn->query("UPDATE sparepart SET stock = stock + " . $row['qty'] . " WHERE id_sparepart = " . $row['id_sparepart']);
    }

    $query = "DELETE FROM permintaan_servis WHERE id_permintaan = '$id_permintaan'";
    
    if(mysqli_query($conn, $query)){
        $_SESSION['pesan_sukses'] = "Permintaan servis berhasil dihapus";
    } else {
        $_SESSION['pesan_error'] = "Gagal menghapus permintaan servis: " . mysqli_error($conn);
    }
}

header("Location: list.php");
exit();
?>