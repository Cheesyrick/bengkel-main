<?php 
session_start();
if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'owner')
{
    header("Location: ../../auth/login.php");
    exit();
}

include '../../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_satuan = $_POST['nama_satuan'];
    $singkatan = $_POST['satuan'];

    if (empty($nama_satuan) || empty($singkatan)) 
        
    {
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Satuan | Bengkel Bengawan</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/add.css">
</head>
<body>
    <?php include '../../includes/sidebar.php'; ?>

    <div class="content">
        <div class="form-container">
            <div style="text-align:left;">
                <a href="listsatuan.php" class="btn btn-back"><i class="fas fa-arrow-left"></i></a>
            </div>

            <h2 style="margin-top : 0; color: #333; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">
                <i class="fas fa-tags" style="color: #d32f2f"></i> Tambah Satuan
            </h2>

            <form action="" method="POST">
                <div class="form-group">
                    <label>Nama Satuan</label>
                    <input type="text" name="nama_satuan" class="form-control" placeholder="Input nama satuan" required>
                </div>

                <div class="form-group">
                    <label>Singkatan</label>
                    <input type="text" name="satuan" class="form-control" placeholder="Input singkatan satuan" required>
                </div>

                <button type="submit" class="btn btn-submit">Simpan Satuan</button>
            </form>
        </div>
    </div>

</body>
</html>
<?php include('../../includes/footer.php'); ?>