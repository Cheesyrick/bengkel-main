<?php
session_start();
if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'owner') {
    header("Location: ../../auth/login.php");
    exit();
}

include '../../config/config.php';

if (!isset($_GET['id'])) {
    header("Location: listmerk.php");
    exit;
}

$id_merk = $_GET['id'];

//proses form pas disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_merk = trim($_POST['nama_merk']);
    
    if (empty($nama_merk)) {
        $_SESSION['pesan_error'] = "Nama merk sparepart harus diisi!";
    } else {
        $query_update = "UPDATE merk SET nama_merk = ? WHERE id_merk = ?";
        $stmt_update = $conn->prepare($query_update);
        $stmt_update->bind_param("si", $nama_merk, $id_merk);
        
        if ($stmt_update->execute()) {
            $_SESSION['pesan_sukses'] = "Merk sparepart berhasil diupdate!";
            header("Location: listmerk.php");
            exit;
        } else {
            $_SESSION['pesan_error'] = "Gagal mengupdate merk sparepart: " . $stmt_update->error;
        }
    }
}

// Ambil data yang mau diedit
$query = "SELECT * FROM merk WHERE id_merk = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_merk);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: listmerk.php");
    exit;
}

$data = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Merk Sparepart | Bengkel Bengawan</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/add.css">
</head>
<body>
    <?php include '../../includes/sidebar.php'; ?>
    
    <div class="content">
        <div class="form-container">
            <div style="text-align: left;">
                <a href="listmerk.php" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>

            <h2 style="margin-top: 0; color: #333; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">
                <i class="fas fa-edit" style="color: #d32f2f"></i> Edit Merk Sparepart
            </h2>

            <?php if(isset($_SESSION['pesan_error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['pesan_error']; unset($_SESSION['pesan_error']); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label>Nama Merk Sparepart:</label>
                    <input type="text" name="nama_merk" class="form-control" value="<?php echo htmlspecialchars($data['nama_merk']); ?>" required>
                </div>

                <button type="submit" class="btn btn-submit">Update Merk Sparepart</button>
            </form>
        </div>
    </div>
</body>
</html>
