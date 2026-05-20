<?php 
session_start();
if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'owner')
{
    header("Location: ../../auth/login.php");
    exit();
}

include '../../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_merk = $_POST['nama_merk'];

    if (empty($nama_merk))
    {
        $_SESSION['pesan_error'] = "Data tidak boleh kosong";
        header("Location: addmerk.php");
        exit();
    }
    else {
        $nama_merk = mysqli_real_escape_string($conn, $nama_merk);
        
        $query = "INSERT INTO merk (nama_merk) 
        VALUES ('$nama_merk')";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['pesan_sukses'] = "Data merk sparepart berhasil ditambahkan!";
            header("Location: listmerk.php");
            exit();
        } else {
            $_SESSION['pesan_error'] = "Gagal menambahkan data: " . mysqli_error($conn);
            header("Location: addmerk.php");
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
    <title>Tambah Merk Sparepart | Bengkel Bengawan</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/add.css">
</head>
<body>
        <?php include '../../includes/sidebar.php'; ?>
    
    <div class="content">
        <div class="form-container">
            <div style="text-align:left;">
                <a href="listmerk.php" class="btn btn-back"><i class="fas fa-arrow-left"></i></a>
            </div>

            <h2 style="margin-top : 0; color: #333; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">
                <i class="fas fa-tags" style="color: #d32f2f"></i> Tambah Merk Sparepart
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
                    <label>Nama Merk Sparepart</label>
                    <input type="text" name="nama_merk" class="form-control" placeholder="Input nama merk sparepart" required>
                </div>

                <button type="submit" class="btn btn-submit">Simpan Merk Sparepart</button>
            </form>
        </div>
    </div>

</body>
</html>
<?php include('../../includes/footer.php'); ?>