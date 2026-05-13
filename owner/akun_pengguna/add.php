<?php
session_start();

include '../../config/config.php';

    if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'owner') {
        header("Location: ../../auth/login.php");
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = md5('bengkel123'); // Default password
        $role = mysqli_real_escape_string($conn, $_POST['role']);

        // Cek jika username sudah ada
        $cek = mysqli_query($conn, "SELECT * FROM pengguna WHERE username='$username'");
        if(mysqli_num_rows($cek) > 0) {
            $_SESSION['pesan_error'] = "Username sudah digunakan!";
        } else {
            $query = "INSERT INTO pengguna (username, password, role, is_first_login) VALUES ('$username', '$password', '$role', 1)";
            if(mysqli_query($conn, $query)) {
                $_SESSION['pesan_sukses'] = "Pengguna berhasil ditambahkan.";
                header("Location: list.php");
                exit();
            } else {
                $_SESSION['pesan_error'] = "Gagal menambahkan pengguna: " . mysqli_error($conn);
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pengguna | Bengkel Bengawan</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
    <link href="../../assets/css/add.css" rel="stylesheet">
</head>
<body>
    <?php include '../../includes/sidebar.php'; ?>
    
    <div class="content">
        <div class="form-container">
        <div style="text-align: left;">
            <a href="list.php" class="btn btn-back"><i class="fas fa-arrow-left"></i></a>
        </div>
            <h2 style="margin-top: 0; color: #333; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">
                <i class="fas fa-user-plus" style="color: #d32f2f;"></i> Tambah Pengguna Baru
            </h2>
            
            <?php if(isset($_SESSION['pesan_error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['pesan_error']; unset($_SESSION['pesan_error']); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan username" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="text" class="form-control" value="bengkel123 (Default)" disabled>
                    <small class="text-muted" style="display: block; margin-top: 5px;">Password default adalah 'bengkel123'. User akan diminta mengganti saat login pertama kali.</small>
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="role" class="form-control" required>
                        <option value="">-- Pilih Role Pengguna --</option>
                        <option value="owner">Owner</option>
                        <option value="service_advisor">Service Advisor</option>
                        <option value="mechanic">Mechanic</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-submit"><i class="fas fa-save"></i> Simpan Pengguna</button>
            </form>
        </div>
    </div>
    
    <?php include '../../includes/footer.php'; ?>
</body>
</html>
