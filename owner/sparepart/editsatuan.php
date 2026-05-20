<?php
session_start();
if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'owner') {
    header("Location: ../../auth/login.php");
    exit();
}

include '../../config/config.php';

if (!isset($_GET['id'])) {
    header("Location: listsatuan.php");
    exit;
}

$id_satuan = $_GET['id'];

//proses form pas disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_satuan = trim($_POST['nama_satuan']);
    $singkatan = trim($_POST['singkatan']);
    
    if (empty($nama_satuan) || empty($singkatan)) {
        $_SESSION['pesan_error'] = "Nama satuan dan singkatan harus diisi!";
    } else {
        $query_update = "UPDATE satuan SET nama_satuan = ?, singkatan = ? WHERE id_satuan = ?";
        $stmt_update = $conn->prepare($query_update);
        $stmt_update->bind_param("ssi", $nama_satuan, $singkatan, $id_satuan);
        
        if ($stmt_update->execute()) {
            $_SESSION['pesan_sukses'] = "Satuan sparepart berhasil diupdate!";
            header("Location: listsatuan.php");
            exit;
        } else {
            $_SESSION['pesan_error'] = "Gagal mengupdate satuan sparepart: " . $stmt_update->error;
        }
    }
}

// Ambil data yang mau diedit
$query = "SELECT * FROM satuan WHERE id_satuan = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_satuan);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: listsatuan.php");
    exit;
}

$data = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Satuan Sparepart | Bengkel Bengawan</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/add.css">
</head>
<body>
    <?php include '../../includes/sidebar.php'; ?>
    
    <div class="content">
        <div class="form-container">
            <div style="text-align: left;">
                <a href="listsatuan.php" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>

            <h2 style="margin-top: 0; color: #333; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">
                <i class="fas fa-edit" style="color: #d32f2f"></i> Edit Satuan Sparepart
            </h2>

            <?php if(isset($_SESSION['pesan_error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['pesan_error']; unset($_SESSION['pesan_error']); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label>Nama Satuan:</label>
                    <input type="text" name="nama_satuan" class="form-control" value="<?php echo htmlspecialchars($data['nama_satuan']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Singkatan:</label>
                    <input type="text" name="singkatan" class="form-control" value="<?php echo htmlspecialchars($data['singkatan']); ?>" required>
                </div>

                <button type="submit" class="btn btn-submit">Update Satuan Sparepart</button>
            </form>
        </div>
    </div>
</body>
</html>
