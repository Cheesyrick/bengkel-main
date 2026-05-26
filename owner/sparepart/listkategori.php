<?php
session_start();

if(!isset($_SESSION["id_pengguna"]) || $_SESSION['role'] != 'owner'){
    header("Location: ../../auth/login.php");
    exit(); 
}

include '../../config/config.php';

$query = 'SELECT * FROM kategori_sparepart ORDER BY id_kategori ASC';
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kategori Sparepart | Bengkel Bengawan</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">    
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/list.css">
</head>
<body>
    <?php include '../../includes/sidebar.php'; ?>

    <div class="content">
        <div class="table-container">
            <h2 style="margin-top: 0; color: #333; text-align: left; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">
                <i class="fas fa-tags" style="color: #d32f2f;"></i> Daftar Kategori Sparepart
            </h2>
            
            <?php if(isset($_SESSION['pesan_sukses'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                        <?php echo $_SESSION['pesan_sukses'];
                            unset($_SESSION['pesan_sukses']);
                        ?>
                </div>
            <?php endif; ?>

            <?php if(isset($_SESSION['pesan_error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                        <?php echo $_SESSION['pesan_error'];
                            unset($_SESSION['pesan_error']);
                        ?>
                </div>
            <?php endif; ?>

             <div style="text-align: left;">
                <a href="addkategori.php" class="btn btn-add"><i class="fas fa-plus"></i> Tambah Kategori Sparepart</a>
             </div>

            <table>
                <thead>
                    <th>No</th>
                    <th>Nama Kategori Sparepart</th>
                    <th>Aksi</th>
                </thead>
                <tbody>
                    <?php 
                     $no = 1;
                     if($result && mysqli_num_rows($result) > 0 ) {
                        while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><strong><?php echo htmlspecialchars($row['nama_kategori']); ?></strong></td>
                            <td>
                                <a href="editkategori.php?id=<?php echo $row['id_kategori'];?>" class="btn btn-edit"><i class="fas fa-edit"></i>Edit</a>
                                <a href="deletekategori.php?id=<?php echo $row['id_kategori'];?>" class="btn btn-delete" onclick="return confirm('Yakin ingin menghapus kategori sparepart ini?');"><i class="fas fa-trash"></i>Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile;
                    } else {
                        echo "<tr><td colspan='3' style='text-align:center; padding: 20px;'>Tidak ada data kategori sparepart</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php include('../../includes/footer.php'); ?>