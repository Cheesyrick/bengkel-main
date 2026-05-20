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
    $singkatan = $_POST['singkatan'];

    if (empty($nama_satuan) || empty($singkatan))
    {
        $_SESSION['pesan_error'] = "Data tidak boleh kosong";
        header("Location: addsatuan.php");
        exit();
    }
    else {
        $nama_satuan = mysqli_real_escape_string($conn, $nama_satuan);
        $singkatan = mysqli_real_escape_string($conn, $singkatan);

        $query = "INSERT INTO satuan (nama_satuan, singkatan) VALUES ('$nama_satuan', '$singkatan')";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['pesan_sukses'] = "Data satuan berhasil ditambahkan!";
            header("Location: listsatuan.php");
            exit();
        } else {
            $_SESSION['pesan_error'] = "Gagal menambahkan data: " . mysqli_error($conn);
            header("Location: addsatuan.php");
            exit();
        }
    }
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
            
            <form action="" method="POST">
                <div class="form-group">
                    <label>Nama Satuan</label>
                    <input type="text" name="nama_satuan" class="form-control" placeholder="Input nama satuan" required>
                </div>

                <div class="form-group">
                    <label>Singkatan</label>
                    <input type="text" name="singkatan" class="form-control" placeholder="Input singkatan satuan" required>
                </div>

                <button type="submit" class="btn btn-submit">Simpan Satuan</button>
            </form>
        </div>
    </div>

</body>
</html>
<?php include('../../includes/footer.php'); ?>