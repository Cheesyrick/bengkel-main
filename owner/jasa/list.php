<?php
session_start();
if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'owner') {
    header("Location: ../../auth/login.php");
    exit();
}
include '../../config/config.php';

// Fetch all jasa
$query = "SELECT j.*, jj.nama_jenis_jasa 
          FROM jasa j 
          JOIN jenis_jasa jj ON j.id_jenis_jasa = jj.id_jenis_jasa 
          ORDER BY j.id_jasa ASC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Jasa | Bengkel Bengawan</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/list.css">
</head>
<body>
    <?php include '../../includes/sidebar.php'; ?>
    
    <div class="content">
        <div class="table-container">
            <h2 style="margin-top: 0; color: #333; text-align: left; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">
                <i class="fas fa-tools" style="color: #d32f2f;"></i> Daftar Jasa
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
                <a href="add.php" class="btn btn-add"><i class="fas fa-plus"></i> Tambah Jasa</a>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Jasa</th>
                        <th>Jenis Jasa</th>
                        <th>Estimasi Waktu</th>
                        <th>Harga (Rp)</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    if($result && mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)): 
                    ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><strong><?php echo htmlspecialchars($row['nama_jasa']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['nama_jenis_jasa']); ?></td>
                            <td><?php echo htmlspecialchars($row['estimasi_waktu']); ?> menit</td>
                            <td>Rp <?php echo number_format($row['harga_jasa'], 0, ',', '.'); ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo $row['id_jasa']; ?>" class="btn btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                <a href="delete.php?id=<?php echo $row['id_jasa']; ?>" class="btn btn-delete" onclick="return confirm('Yakin ingin menghapus jasa ini?');"><i class="fas fa-trash"></i> Hapus</a>
                            </td>
                        </tr>
                    <?php 
                        endwhile;
                    } else {
                        echo "<tr><td colspan='6' style='text-align:center; padding: 20px;'>Tidak ada data jasa</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php include '../../includes/footer.php'; ?>
</body>
</html>