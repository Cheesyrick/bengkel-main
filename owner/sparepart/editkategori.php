<?php
session_start();
if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'owner') {
    header("Location: ../../auth/login.php");
    exit();
}

include '../../config/config.php';

if (!isset($_GET['id'])) {
    header("Location: listkategori.php");
    exit;
}

$id_kategori_sparepart = $_GET['id'];

//proses form pas disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_kategori_sparepart = $_POST['nama_kategori_sparepart'];
    
    if (empty($nama_kategori_sparepart)) {
        $_SESSION['pesan_error'] = "Nama kategori sparepart harus diisi!";
    } else {
        $query_update = "UPDATE kategori_sparepart SET nama_kategori = ? WHERE id_kategori = ?";
        $stmt_update = $conn->prepare($query_update);
        $stmt_update->bind_param("si", $nama_kategori_sparepart, $id_kategori_sparepart);
        
        if ($stmt_update->execute()) {
            $_SESSION['pesan_sukses'] = "Kategori sparepart berhasil diupdate!";
            header("Location: listkategori.php");
            exit;
        } else {
            $_SESSION['pesan_error'] = "Gagal mengupdate kategori sparepart: " . $stmt_update->error;
        }
    }
}

// Ambil data yang mau diedit
$query = "SELECT * FROM kategori_sparepart WHERE id_kategori = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_kategori_sparepart);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: listkategori.php");
    exit;
}

$data = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kategori Sparepart | Bengkel Bengawan</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/add.css">
</head>
<body>
    <?php include '../../includes/sidebar.php'; ?>
    
    <div class="content">
        <div class="form-container">
            <div style="text-align: left;">
                <a href="listkategori.php" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>

            <h2 style="margin-top: 0; color: #333; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">
                <i class="fas fa-edit" style="color: #d32f2f"></i> Edit Kategori Sparepart
            </h2>

            <?php if(isset($_SESSION['pesan_error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['pesan_error']; unset($_SESSION['pesan_error']); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label>Nama Kategori Sparepart:</label>
                    <input type="text" name="nama_kategori_sparepart" class="form-control" value="<?php echo htmlspecialchars($data['nama_kategori']); ?>" required>
                </div>

                <button type="submit" class="btn btn-submit">Update Kategori Sparepart</button>
            </form>
        </div>
    </div>
</body>
</html>
<?php include('../../includes/footer.php'); ?>