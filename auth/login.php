<!DOCTYPE html>
<html lang="en">

<?php 
    include "../config/config.php";

    ob_start();
    session_start();

    if(isset($_POST["submitlogin"])) {
        $username = $_POST['username'];
        $password = md5($_POST['password']);
        
        $sql_login = mysqli_query($conn, "SELECT * FROM pengguna 
        WHERE username = '$username' AND password = '$password'");
        
        if(mysqli_num_rows($sql_login) > 0) 
        {
            $row_pengguna = mysqli_fetch_array($sql_login);
            $_SESSION['id_pengguna'] = $row_pengguna['id_pengguna'];
            $_SESSION['username'] = $row_pengguna['username'];
            $_SESSION['role'] = $row_pengguna['role'];
            $_SESSION['is_first_login'] = $row_pengguna['is_first_login'];

            if ($row_pengguna['is_first_login'] == 1) {
                header("location:force_change_password.php");
                exit();
            } else {
                header("location:dashboardadmin.php");
                exit();
            }
        }
        else
        {
            $error_message = "Username atau password salah!";
        }
    }

?>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Bengkel Bengawan Login</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
  <link href="../assets/css/loginstyle.css" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <!-- Menggunakan logo yang direquest -->
        <img src="../assets/images/logo_broom_2.png" alt="Broom Garage Logo" class="logo-img" onerror="this.style.display='none'">
        <h2>Login Dashboard</h2>
        
        <?php if(isset($error_message)): ?>
            <div class="alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>


        <form method="POST" action="">
            <div class="form-group">
                <label for="username"><i class="fas fa-user"></i> Username</label>
                <input type="text" class="form-control" name="username" id="username" required autocomplete="off">
            </div>
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <input type="password" class="form-control" name="password" id="password" required>
            </div>
            <button type="submit" name="submitlogin" class="btn-login" style="margin-bottom: 10px;">Login</button>
            <a href="request_reset.php" class="btn-login" style="background-color: #ff9800; border: none; color: white; display: inline-block; text-align: center; text-decoration: none; padding: 12px; border-radius: 4px; width: 100%; box-sizing: border-box;">Lupa Password?</a>
        </form>
    </div>

    <?php 
        mysqli_close($conn);
        ob_end_flush();
    ?>
</body>
</html>