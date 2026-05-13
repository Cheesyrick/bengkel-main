<?php
session_start();
include '../config/config.php';

if (!isset($_SESSION['id_pengguna'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['is_first_login']) || $_SESSION['is_first_login'] == 0) {
    header("Location: dashboardadmin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $error_message = "Password baru dan konfirmasi password tidak cocok!";
    } elseif (strlen($new_password) < 6) {
        $error_message = "Password minimal 6 karakter!";
    } else {
        $id_pengguna = $_SESSION['id_pengguna'];
        $hashed_password = md5($new_password);
        
        $query = "UPDATE pengguna SET password='$hashed_password', is_first_login=0 WHERE id_pengguna='$id_pengguna'";
        
        if(mysqli_query($conn, $query)) {
            $_SESSION['is_first_login'] = 0;
            header("Location: dashboardadmin.php");
            exit();
        } else {
            $error_message = "Gagal mengubah password: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Ganti Password | Bengkel Bengawan</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
  <link href="../assets/css/loginstyle.css" rel="stylesheet">
  <style>
      .login-container {
          max-width: 450px;
      }
      .alert-warning {
          background-color: #fff3cd;
          color: #856404;
          padding: 10px;
          border-radius: 4px;
          margin-bottom: 20px;
          border: 1px solid #ffeeba;
          text-align: center;
      }
  </style>
</head>
<body>
    <div class="login-container">
        <img src="../assets/images/logo_broom_2.png" alt="Broom Garage Logo" class="logo-img" onerror="this.style.display='none'">
        <h2>Ubah Password Default</h2>
        
        <div class="alert-warning">
            <i class="fas fa-exclamation-triangle"></i> Ini adalah login pertama Anda. Demi keamanan, silakan ubah password default Anda sebelum melanjutkan ke Dashboard.
        </div>

        <?php if(isset($error_message)): ?>
            <div class="alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="new_password"><i class="fas fa-lock"></i> Password Baru</label>
                <input type="password" class="form-control" name="new_password" id="new_password" required minlength="6">
            </div>
            <div class="form-group">
                <label for="confirm_password"><i class="fas fa-check-circle"></i> Konfirmasi Password Baru</label>
                <input type="password" class="form-control" name="confirm_password" id="confirm_password" required minlength="6">
            </div>
            <button type="submit" name="submit" class="btn-login" style="background-color: #28a745;">Simpan Password & Lanjutkan</button>
        </form>
    </div>
</body>
</html>
