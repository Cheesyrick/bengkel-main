<?php
    include "../config/config.php";

    ob_start();
    session_start();

    if(isset($_POST["request_reset"])) {
        $username_req = mysqli_real_escape_string($conn, $_POST['username']);
        if(empty($username_req)) {
            $error_message = "Silakan masukkan username Anda untuk mereset password.";
        } else {
            $sql_find = mysqli_query($conn, "SELECT id_pengguna FROM pengguna WHERE username = '$username_req'");
            if(mysqli_num_rows($sql_find) > 0) {
                $row = mysqli_fetch_assoc($sql_find);
                $id_pengguna = $row['id_pengguna'];
                
                $sql_insert = "INSERT INTO password_requests (id_pengguna, status) VALUES ('$id_pengguna', 'pending')";
                if(mysqli_query($conn, $sql_insert)) {
                    $success_message = "Request sent to Owner. Silakan tunggu konfirmasi reset dari Owner.";
                } else {
                    $error_message = "Gagal mengirim request.";
                }
            } else {
                $error_message = "Username tidak ditemukan!";
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Request Reset Password | Bengkel Bengawan</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
  <link href="../assets/css/loginstyle.css" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <img src="../assets/images/logo_broom_2.png" alt="Broom Garage Logo" class="logo-img" onerror="this.style.display='none'">
        <h2>Request Reset Password</h2>
        
        <?php if(isset($error_message)): ?>
            <div class="alert-danger" style="margin-bottom: 20px;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($success_message)): ?>
            <div class="alert-success" style="background-color: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 4px; border: 1px solid #c3e6cb; font-weight: bold;">
                <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group" style="text-align: left;">
                <label for="username"><i class="fas fa-user"></i> Username</label>
                <input type="text" class="form-control" name="username" id="username" required autocomplete="off" placeholder="Masukkan username Anda">
            </div>
            <button type="submit" name="request_reset" class="btn-login" style="background-color: #ff9800; border: none; margin-bottom: 10px;">Kirim Request ke Owner</button>
            <a href="login.php" class="btn-login" style="background-color: #cccccc; color: #333; display: inline-block; text-align: center; text-decoration: none; padding: 12px; border-radius: 4px; width: 100%; box-sizing: border-box;">Kembali ke Login</a>
        </form>
    </div>

    <?php 
        mysqli_close($conn);
        ob_end_flush();
    ?>
</body>
</html>
