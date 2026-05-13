<?php
session_start();
if (!isset($_SESSION["id_pengguna"]) || $_SESSION['role'] != 'owner')
{
    header("Location: ../../auth/login.php");
    exit();
}

include '../../config/config.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_jenis_jasa = $_POST['nama_jenis_jasa'];

    if (empty($nama_jenis_jasa)) {
        $_SESSION['pesan_error'] = "Nama jenis jasa harus diisi!";
    } else {
        $query = "INSERT INTO jenis_jasa (nama_jenis_jasa) VALUES (?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $nama_jenis_jasa);
        
        if ($stmt->execute()) {
            $_SESSION['pesan_sukses'] = "Jenis jasa berhasil ditambahkan!";
            header("Location: listjenis.php");
            exit;
        } else {
            $_SESSION['pesan_error'] = "Gagal menambahkan jenis jasa: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kategori Jasa | Bengkel Bengawan</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/add.css">
</head>
<body>
    <?php include '../../includes/sidebar.php'; ?>
    
    <div class="content">
        <div class="form-container">
            <div style="text-align: left;">
                <a href="listjenis.php" class="btn btn-back">
                <i class="fas fa-arrow-left"></i>
                </a>
            </div>

            <h2 style="margin-top : 0; color: #333; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">
                <i class="fas fa-tags" style="color: #d32f2f"></i> Tambah Jenis Jasa
            </h2>

            <?php if(isset($_SESSION['pesan_error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['pesan_error']; unset($_SESSION['pesan_error']); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label>Nama jenis Jasa:</label>
                    <input type="text" name="nama_jenis_jasa" class="form-control" placeholder="Masukkan nama kategori jasa" required>
                </div>

                <button type="submit" class="btn btn-submit">Simpan Jenis Jasa</button>
            </form>
        </div>
    </div>
</body>
</html>