<?php
session_start();

include '../../config/config.php';

if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'owner') {
    header("Location: ../../auth/login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$query_get = mysqli_query($conn, "SELECT * FROM pengguna WHERE id_pengguna='$id'");
if (mysqli_num_rows($query_get) == 0) {
    header("Location: list.php");
    exit();
}

$user = mysqli_fetch_assoc($query_get);

$spesialisasi = '';
if ($user['role'] == 'mechanic') {
    $q_mekanik = mysqli_query($conn, "SELECT spesialisasi FROM mekanik WHERE id_pengguna='$id'");
    if (mysqli_num_rows($q_mekanik) > 0) {
        $spesialisasi = mysqli_fetch_assoc($q_mekanik)['spesialisasi'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $spesialisasi_post = isset($_POST['spesialisasi']) ? mysqli_real_escape_string($conn, $_POST['spesialisasi']) : '';
    
    // Cek username duplikat selain punya diri sendiri
    $cek = mysqli_query($conn, "SELECT * FROM pengguna 
    WHERE username='$username' AND id_pengguna != '$id'");
    
    if(mysqli_num_rows($cek) > 0) {
        $_SESSION['pesan_error'] = "Username sudah digunakan oleh akun lain!";
    } 
    else {
        $conn->begin_transaction();
        try {
            if (isset($_POST['reset_password'])) 
            {
                $password = md5('bengkel123');
                $query = "UPDATE pengguna SET username='$username', password='$password', role='$role', is_first_login=1 WHERE id_pengguna='$id'";
                
                // Cleanup request
                mysqli_query($conn, "UPDATE password_requests SET status='completed' WHERE id_pengguna='$id' AND status='pending'");
            } 
            else 
            {
                $query = "UPDATE pengguna SET username='$username', role='$role' WHERE id_pengguna='$id'";
            }
            
            mysqli_query($conn, $query);

            if ($role == 'mechanic') {
                $cek_mekanik = mysqli_query($conn, "SELECT id_pengguna FROM mekanik WHERE id_pengguna='$id'");
                if (mysqli_num_rows($cek_mekanik) > 0) {
                    mysqli_query($conn, "UPDATE mekanik SET nama_mekanik='$username', spesialisasi='$spesialisasi_post' WHERE id_pengguna='$id'");
                } else {
                    mysqli_query($conn, "INSERT INTO mekanik (id_pengguna, nama_mekanik, spesialisasi) VALUES ('$id', '$username', '$spesialisasi_post')");
                }
            } else {
                mysqli_query($conn, "DELETE FROM mekanik WHERE id_pengguna='$id'");
            }

            $conn->commit();

            // Update session jika mengedit akun sendiri
            if($id == $_SESSION['id_pengguna']) {
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $role;
            }
            $_SESSION['pesan_sukses'] = "Pengguna berhasil diupdate.";
            header("Location: list.php");
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['pesan_error'] = "Gagal mengupdate pengguna: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengguna | Bengkel Bengawan</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/dashboard.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../../assets/css/edit.css?v=<?php echo time(); ?>" rel="stylesheet">
</head>
<body>
    <?php include '../../includes/sidebar.php'; ?>
    
    <div class="content">
        <div class="form-container">
            <div style="text-align: left;">
                <a href="list.php" class="btn btn-back"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h2 style="margin-top: 0; color: #333; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">
                <i class="fas fa-user-edit" style="color: #d32f2f;"></i> Edit Pengguna
            </h2>
            
            <?php if(isset($_SESSION['pesan_error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['pesan_error']; unset($_SESSION['pesan_error']); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="role" class="form-control" required>
                        <option value="owner" <?php echo $user['role'] == 'owner' ? 'selected' : ''; ?>>Owner</option>
                        <option value="service_advisor" <?php echo $user['role'] == 'service_advisor' ? 'selected' : ''; ?>>Service Advisor</option>
                        <option value="mechanic" <?php echo $user['role'] == 'mechanic' ? 'selected' : ''; ?>>Mechanic</option>
                    </select>
                </div>
                <div class="form-group" id="spesialisasi-group" style="display: <?php echo $user['role'] == 'mechanic' ? 'block' : 'none'; ?>;">
                    <label>Spesialisasi Mekanik</label>
                    <input type="text" name="spesialisasi" class="form-control" value="<?php echo htmlspecialchars($spesialisasi); ?>" placeholder="Contoh: Mesin, Kelistrikan, dll" autocomplete="off">
                </div>
                <button type="submit" name="update_user" class="btn btn-submit"><i class="fas fa-save"></i> Update Pengguna</button>
                <button type="submit" name="reset_password" class="btn btn-warning" onclick="return confirm('Apakah Anda yakin ingin mereset password pengguna ini ke default (bengkel123)?');"><i class="fas fa-sync-alt"></i> Reset Password ke Default</button>
            </form>
        </div>
    </div>
    
    <script>
        document.querySelector('select[name="role"]').addEventListener('change', function() {
            if (this.value === 'mechanic') {
                document.getElementById('spesialisasi-group').style.display = 'block';
            } else {
                document.getElementById('spesialisasi-group').style.display = 'none';
            }
        });
    </script>
    
    <?php include '../../includes/footer.php'; ?>
</body>
</html>
