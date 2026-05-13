<?php
session_start();
include '../config/config.php';

if (!isset($_SESSION['id_pengguna'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $old_password = md5($_POST['old_password']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $id_pengguna = $_SESSION['id_pengguna'];

    // Cek password lama
    $cek_query = "SELECT password FROM pengguna WHERE id_pengguna='$id_pengguna'";
    $cek_result = mysqli_query($conn, $cek_query);
    $user = mysqli_fetch_assoc($cek_result);

    if ($user['password'] !== $old_password) {
        $_SESSION['pesan_error'] = "Password lama salah!";
    } elseif ($new_password !== $confirm_password) {
        $_SESSION['pesan_error'] = "Password baru dan konfirmasi password tidak cocok!";
    } elseif (strlen($new_password) < 6) {
        $_SESSION['pesan_error'] = "Password baru minimal 6 karakter!";
    } else {
        $hashed_password = md5($new_password);
        $query = "UPDATE pengguna SET password='$hashed_password' WHERE id_pengguna='$id_pengguna'";
        
        if(mysqli_query($conn, $query)) {
            $_SESSION['pesan_sukses'] = "Password berhasil diubah!";
        } else {
            $_SESSION['pesan_error'] = "Gagal mengubah password: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Password | Bengkel Bengawan</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/dashboard.css" rel="stylesheet">
    <link href="../assets/css/add.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="content">
        <div class="form-container" style="max-width: 600px; margin: 0 auto;">
            <h2 style="margin-top: 0; color: #333; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">
                <i class="fas fa-key" style="color: #d32f2f;"></i> Ganti Password
            </h2>
            
            <?php if(isset($_SESSION['pesan_error'])): ?>
                <div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['pesan_error']; unset($_SESSION['pesan_error']); ?>
                </div>
            <?php endif; ?>

            <?php if(isset($_SESSION['pesan_sukses'])): ?>
                <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                    <i class="fas fa-check-circle"></i> <?php echo $_SESSION['pesan_sukses']; unset($_SESSION['pesan_sukses']); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label>Password Lama</label>
                    <input type="password" name="old_password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Password Baru</label>
                    <input type="password" name="new_password" class="form-control" required minlength="6">
                </div>
                <div class="form-group">
                    <label>Konfirmasi Password Baru</label>
                    <input type="password" name="confirm_password" class="form-control" required minlength="6">
                </div>
                <button type="submit" class="btn btn-submit" style="width: 100%;"><i class="fas fa-save"></i> Simpan Password</button>
            </form>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>
