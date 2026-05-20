<?php
session_start();

if(!isset($_SESSION["id_pengguna"]) || $_SESSION['role'] != 'owner'){
    header("Location: ../../auth/login.php");
    exit(); 
}

include '../../config/config.php';

$query = "SELECT sp.*, ks.nama_kategori, ts.nama_tipe, mk.nama_merk, st.nama_satuan, st.singkatan
          FROM sparepart sp
          LEFT JOIN kategori_sparepart ks ON sp.id_kategori = ks.id_kategori
          LEFT JOIN tipe_sparepart ts ON sp.id_tipe_sp = ts.id_tipe_sp
          LEFT JOIN merk mk ON sp.id_merk = mk.id_merk
          LEFT JOIN satuan st ON sp.id_satuan = st.id_satuan
          ORDER BY sp.id_sparepart ASC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Sparepart | Bengkel Bengawan</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/list.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php include '../../includes/sidebar.php'; ?>

    <div class="content">
        <div class="table-container">
            <h2 style="margin-top: 0; color: #333; text-align: left; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">
                <i class="fas fa-tools" style="color: #d32f2f;"></i> Daftar Sparepart
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
                <a href="add.php" class="btn btn-add"><i class="fas fa-plus"></i> Tambah Sparepart</a>
            </div>

            <table>
                <thead>
                    <th>No</th>
                    <th>Nama Sparepart</th>
                    <th>Stock</th>
                    <th>Harga Jual</th>
                    <th>Kategori Sparepart</th>
                    <th>Merk Sparepart</th>
                    <th>Tipe Sparepart</th>
                    <th>Satuan</th>
                    <th>Singkatan</th>
                    <th>Aksi</th>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    if($result && mysqli_num_rows($result) > 0 ) {
                        while($row = mysqli_fetch_assoc($result)):
                    ?>

                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><strong><?php echo htmlspecialchars($row['nama_sparepart']);?></strong></td>
                        <td><?php echo htmlspecialchars($row['stock']);?></td>
                        <td>Rp <?php echo number_format($row['harga_jual'], 0, ',', '.');?></td>
                        <td><?php echo htmlspecialchars($row['nama_kategori']);?></td>
                        <td><?php echo htmlspecialchars($row['nama_merk']);?></td>
                        <td><?php echo htmlspecialchars($row['nama_tipe']);?></td>
                        <td><?php echo htmlspecialchars($row['nama_satuan']);?></td>
                        <td><?php echo htmlspecialchars($row['singkatan']);?></td>
                        <td>
                            <a href="edit.php?id=<?php echo $row['id_sparepart'];?>" class="btn btn-edit"><i class="fas fa-edit"></i>Edit</a>
                            <a href="delete.php?id=<?php echo $row['id_sparepart'];?>" class="btn btn-delete" onclick="return confirm('Yakin ingin menghapus sparepart ini?');"><i class="fas fa-trash"></i>Hapus</a>
                        </td>
                    </tr>
                    <?php 
                    endwhile;
                    } else {
                    echo "<tr><td colspan='9' style='text-align:center; padding: 20px;'>Tidak ada data sparepart</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php include '../../includes/footer.php';?>
</body>
</html>