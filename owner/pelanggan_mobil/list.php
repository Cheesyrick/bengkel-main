<?php
session_start();
if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'owner') {
    header("Location: ../../auth/login.php");
    exit();
}
include '../../config/config.php';

// Fetch all pelanggan and mobil
$query = "SELECT p.*, m.id_mobil, m.merk_mobil, m.tipe_mobil, m.plat_nomor, m.tahun_mobil 
          FROM pelanggan p 
          LEFT JOIN mobil m ON p.id_pelanggan = m.id_pelanggan 
          ORDER BY p.id_pelanggan DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pelanggan dan Mobil | Bengkel Bengawan</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/list.css">
</head>
<body>
    <?php include '../../includes/sidebar.php'; ?>
    
    <div class="content">
        <div class="table-container">
            <h2 style="margin-top: 0; color: #333; text-align: left; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">
                <i class="fas fa-users" style="color: #d32f2f;"></i> Daftar Pelanggan dan Mobil
            </h2>
            
            <?php if(isset($_SESSION['pesan_sukses'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $_SESSION['pesan_sukses']; unset($_SESSION['pesan_sukses']); ?>
                </div>
            <?php endif; ?>
            
            <?php if(isset($_SESSION['pesan_error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['pesan_error']; unset($_SESSION['pesan_error']); ?>
                </div>
            <?php endif; ?>

            <div style="text-align: left;">
                <a href="add.php" class="btn btn-add"><i class="fas fa-plus"></i> Tambah Data</a>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pelanggan</th>
                        <th>No Telepon</th>
                        <th>Alamat</th>
                        <th>Mobil</th>
                        <th>Plat Nomor</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    if($result && mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)): 
                            $mobil_info = $row['merk_mobil'] ? $row['merk_mobil'] . ' ' . $row['tipe_mobil'] : '-';
                            $plat_nomor = $row['plat_nomor'] ? $row['plat_nomor'] : '-';
                    ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><strong><?php echo htmlspecialchars($row['nama_pelanggan']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['no_telp']); ?></td>
                            <td><?php echo $row['alamat']; ?></td>
                            <td><?php echo htmlspecialchars($mobil_info); ?></td>
                            <td><?php echo htmlspecialchars($plat_nomor); ?></td>
                            <td>
                                <a href="edit.php?id_pelanggan=<?php echo $row['id_pelanggan']; ?>" class="btn btn-edit"><i class="fas fa-edit"></i></a>
                                <a href="delete.php?id_pelanggan=<?php echo $row['id_pelanggan']; ?>" class="btn btn-delete" onclick="return confirm('Yakin ingin menghapus data ini beserta data mobilnya?');"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php 
                        endwhile;
                    } else {
                        echo "<tr><td colspan='6' style='text-align:center; padding: 20px;'>Tidak ada data</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php include '../../includes/footer.php'; ?>
</body>
</html>
