<?php
session_start();

include '../../config/config.php';

if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'owner') {
    header("Location: ../../auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Cek jika username sudah ada
    $cek = mysqli_query($conn, "SELECT * FROM pengguna WHERE username='$username'");
    if(mysqli_num_rows($cek) > 0) {
        $_SESSION['pesan_error'] = "Username sudah digunakan!";
    } else {
        $query = "INSERT INTO pengguna (username, password, role) VALUES ('$username', '$password', '$role')";
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
    <title>Tambah Pengguna | Broom Garage</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
    <style>
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin: 20px auto;
            max-width: 500px;
            border-top: 4px solid #d32f2f;
            text-align: left;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; color: #555; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-size: 14px;}
        .form-control:focus { border-color: #d32f2f; outline: none; }
        .btn { padding: 12px 15px; border: none; border-radius: 4px; cursor: pointer; color: white; font-weight: bold; text-decoration: none; font-size: 15px; }
        .btn-submit { background-color: #d32f2f; width: 100%; transition: background-color 0.3s; }
        .btn-submit:hover { background-color: #b71c1c; }
        .btn-back { background-color: #b71c1c; display: inline-block; margin-bottom: 20px; transition: background-color 0.3s;}
        .btn-back:hover { background-color: #da190b; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; font-weight: bold; }
        .alert-danger { background-color: #f2dede; color: #a94442; border: 1px solid #ebccd1; }
    </style>
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
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
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
