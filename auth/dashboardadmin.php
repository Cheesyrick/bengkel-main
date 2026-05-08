<?php
session_start();
if (!isset($_SESSION['id_pengguna'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Broom Garage</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/dashboard.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="content">
        <div class="card">
            <i class="fas fa-user-circle" style="font-size: 64px; color: #cccccc; margin-bottom: 20px;"></i>
            <h1 style="margin-top: 0; color: #333;">Selamat Datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <p style="color: #666; margin-bottom: 5px;">Anda login dengan hak akses:</p>
            <div class="role-badge">
                <?php echo htmlspecialchars(strtoupper($_SESSION['role'])); ?>
            </div>
            
            <hr style="border: 0; border-top: 1px solid #eeeeee; margin: 30px 0;">
            <p style="color: #888;">Ini adalah halaman dashboard sementara. Fitur-fitur sesuai role Anda akan ditambahkan di sini nantinya.</p>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
