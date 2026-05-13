<?php
session_start();
if (!isset($_SESSION["id_pengguna"]) || $_SESSION['role'] != 'owner') {
    header("Location: ../../auth/login.php");
    exit();
}
include '../../config/config.php';

$query = "SELECT * FROM jenis_jasa ORDER BY id_jenis_jasa ASC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Jenis Jasa | Bengkel Bengawan</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">    
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/list.css">

</head>
<body>
    <?php include '../../includes/sidebar.php'; ?>

    <div class="content">
        <div class="table-container">
            <h2 style="margin-top: 0; color: #333; border-bottom: 1px solid #eee;
            padding-bottom: 15px; margin-bottom: 20px;"> Daftar Jenis Jasa</h2>
            
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
                <a href="addjenis.php" class="btn btn-add"><i class="fas fa-plus"></i> Tambah Jenis Jasa</a>
             </div>

             <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Jenis Jasa</th>
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
                        <td><strong><?php echo htmlspecialchars($row['nama_jenis_jasa']); ?></strong></td>
                        <td>
                            <a href="editjenis.php?id=<?php echo $row['id_jenis_jasa']; ?>" class="btn btn-edit"><i class="fas fa-edit"></i> Edit</a>
                            <a href="deletejenis.php?id=<?php echo $row['id_jenis_jasa']; ?>" class="btn btn-delete"><i class="fas fa-trash"></i> Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; 
                    } else {
                        echo "<tr><td colspan='6' style='text-align: center; padding :20px;'>Belum ada jenis jasa</td></tr>";
                    } 
                    ?>
                </tbody>
             </table>
        </div>
    </div>
</body>
</html>