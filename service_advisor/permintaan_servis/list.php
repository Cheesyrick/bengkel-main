<?php
session_start();

if(!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'service_advisor'){
    header("Location: ../../auth/login.php");
    exit();
}
include "../../config/config.php";

$query = "SELECT ps.*, 
                 m.merk_mobil, m.tipe_mobil, m.plat_nomor,
                 pl.nama_pelanggan,
                 (SELECT GROUP_CONCAT(j.nama_jasa SEPARATOR ', ') FROM detail_servis ds JOIN jasa j ON ds.id_jasa = j.id_jasa WHERE ds.id_permintaan = ps.id_permintaan) AS nama_jasa,
                 (SELECT GROUP_CONCAT(sp.nama_sparepart SEPARATOR ', ') FROM detail_sparepart dsp JOIN sparepart sp ON dsp.id_sparepart = sp.id_sparepart WHERE dsp.id_permintaan = ps.id_permintaan) AS nama_sparepart,
                 (SELECT dp.status_pengerjaan FROM detail_pengerjaan dp WHERE dp.id_permintaan = ps.id_permintaan LIMIT 1) AS status
          FROM permintaan_servis ps
          JOIN mobil m ON ps.id_mobil = m.id_mobil
          JOIN pelanggan pl ON m.id_pelanggan = pl.id_pelanggan
          ORDER BY ps.tanggal_masuk DESC";
$result = $conn->query($query);

$no = 1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permintaan Servis | Bengkel Bengawan</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
    <link href="../../assets/css/list.css" rel="stylesheet">
</head>
<body>
    <?php include '../../includes/sidebar.php'; ?>

    <div class="content">
        <div class="table-container">
            <h2 style="margin-top: 0; color: #333; text-align: left; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">
                <i class="fas fa-clipboard-list" style="color: #d32f2f;"></i> Permintaan Servis
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
                        <th>Pelanggan</th>
                        <th>Mobil</th>
                        <th>No. Polisi</th>
                        <th>Jenis Jasa</th>
                        <th>Sparepart</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo htmlspecialchars($row['nama_pelanggan']); ?></td>
                            <td><?php echo htmlspecialchars($row['merk_mobil'] . " " . $row['tipe_mobil']); ?></td>
                            <td><?php echo htmlspecialchars($row['plat_nomor']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_jasa']); ?></td>
                            <td><?php echo $row['nama_sparepart'] ? htmlspecialchars($row['nama_sparepart']) : '-'; ?></td>
                            <td>
                                <?php
                                $status_val = $row['status'] ? $row['status'] : 'pending'; // Default to pending if null
                                $status_class = '';
                                switch($status_val) {
                                    case 'pending':
                                        $status_class = 'status-pending';
                                        break;
                                    case 'sedang_dikerjakan':
                                    case 'assigned':
                                        $status_class = 'status-working';
                                        break;
                                    case 'selesai':
                                    case 'done':
                                        $status_class = 'status-completed';
                                        break;
                                }
                                ?>
                                <span class="<?php echo $status_class; ?>"><?php echo ucfirst($status_val); ?></span>
                            </td>
                            <td>
                                <?php if($status_val == 'assigned' || $status_val == 'pending'): ?>
                                    <a href="../pembayaran/add.php?id_permintaan=<?php echo $row['id_permintaan']; ?>" class="btn btn-success" style="background-color: #28a745; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 14px; margin-right: 5px;"><i class="fas fa-money-bill-wave"></i> Bayar</a>
                                <?php endif; ?>
                                <a href="edit.php?id_permintaan=<?php echo $row['id_permintaan']; ?>" class="btn btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                <a href="delete.php?id_permintaan=<?php echo $row['id_permintaan']; ?>" class="btn btn-delete" onclick="return confirm('Yakin ingin menghapus data ini?');"><i class="fas fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php include('../../includes/footer.php'); ?>