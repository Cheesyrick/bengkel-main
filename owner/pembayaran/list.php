<?php
session_start();
if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'owner') {
    header("Location: ../../auth/login.php");
    exit();
}
include '../../config/config.php';

$query = "SELECT pb.*, ps.keluhan, m.plat_nomor, p.nama_pelanggan
          FROM pembayaran pb
          JOIN permintaan_servis ps ON pb.id_permintaan = ps.id_permintaan
          JOIN mobil m ON ps.id_mobil = m.id_mobil
          JOIN pelanggan p ON m.id_pelanggan = p.id_pelanggan
          ORDER BY pb.tanggal_bayar DESC";
$result = $conn->query($query);
$no = 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pembayaran | Bengkel Bengawan</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
    <link href="../../assets/css/list.css" rel="stylesheet">
</head>
<body>
    <?php include '../../includes/sidebar.php'; ?>

    <div class="content">
        <div class="table-container">
            <h2 style="margin-top: 0; color: #333; text-align: left; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">
                <i class="fas fa-money-bill-wave" style="color: #d32f2f;"></i> Daftar Pembayaran
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
                <a href="add.php" class="btn btn-add"><i class="fas fa-plus"></i> Input Pembayaran</a>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Bayar</th>
                        <th>Pelanggan</th>
                        <th>No. Polisi</th>
                        <th>Jumlah Bayar</th>
                        <th>Metode</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo date('d-m-Y', strtotime($row['tanggal_bayar'])); ?></td>
                                <td><?php echo htmlspecialchars($row['nama_pelanggan']); ?></td>
                                <td><?php echo htmlspecialchars($row['plat_nomor']); ?></td>
                                <td>Rp <?php echo number_format($row['jumlah_bayar'], 0, ',', '.'); ?></td>
                                <td><?php echo ucfirst($row['metode_pembayaran']); ?></td>
                                <td>
                                    <?php
                                    $status_class = 'status-pending';
                                    if(strpos($row['status_pembayaran'], 'lunas') !== false) {
                                        $status_class = 'status-completed';
                                    }
                                    ?>
                                    <span class="<?php echo $status_class; ?>"><?php echo ucfirst($row['status_pembayaran']); ?></span>
                                </td>
                                <td>
                                    <a href="cetak_nota.php?id_bayar=<?php echo $row['id_bayar']; ?>" class="btn btn-edit" target="_blank"><i class="fas fa-print"></i> Cetak Nota</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center;">Tidak ada data pembayaran.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php include('../../includes/footer.php'); ?>
