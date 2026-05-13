<?php
session_start();
if (!isset($_SESSION['id_pengguna'])) {
    header("Location: login.php");
    exit();
}
if (isset($_SESSION['is_first_login']) && $_SESSION['is_first_login'] == 1) {
    header("Location: force_change_password.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Bengkel Bengawan</title>
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
            <?php
                $my_bg_color = '#d32f2f'; // default red
                if ($_SESSION['role'] == 'mechanic') {
                    $my_bg_color = '#ff9800'; // orange
                } elseif ($_SESSION['role'] == 'service_advisor') {
                    $my_bg_color = '#1b5e20'; // dark green
                }
            ?>
            <div class="role-badge" style="background-color: <?php echo $my_bg_color; ?>;">
                <?php echo htmlspecialchars(strtoupper($_SESSION['role'])); ?>
            </div>
        </div>

        <?php if ($_SESSION['role'] == 'owner'): ?>
        <?php
            include '../config/config.php';
            $req_query = "SELECT pr.id_request, pr.request_date, p.id_pengguna, p.username, p.role 
                          FROM password_requests pr 
                          JOIN pengguna p ON pr.id_pengguna = p.id_pengguna 
                          WHERE pr.status = 'pending' 
                          ORDER BY pr.request_date DESC";
            $req_result = mysqli_query($conn, $req_query);
        ?>
        <div class="card" style="margin-top: 30px; text-align: left;">
            <h3 style="margin-top: 0; color: #333; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">
                <i class="fas fa-exclamation-circle" style="color: #ff9800;"></i> Password Reset Requests
            </h3>
            
            <?php if (mysqli_num_rows($req_result) > 0): ?>
                <ul style="list-style: none; padding: 0; margin: 0;">
                <?php while ($req = mysqli_fetch_assoc($req_result)): ?>
                    <li style="padding: 15px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; background-color: #f9f9f9;">
                        <?php
                            $req_bg_color = '#d32f2f'; 
                            if ($req['role'] == 'mechanic') {
                                $req_bg_color = '#ff9800';
                            } elseif ($req['role'] == 'service_advisor') {
                                $req_bg_color = '#1b5e20';
                            }
                        ?>
                        <div>
                            <strong style="font-size: 16px;"><?php echo htmlspecialchars($req['username']); ?></strong> 
                            <span class="role-badge" style="font-size: 11px; padding: 3px 8px; margin-top: 0; margin-left: 5px; background-color: <?php echo $req_bg_color; ?>;"><?php echo htmlspecialchars(strtoupper($req['role'])); ?></span><br>
                            <small style="color: #777;"><i class="far fa-clock"></i> <?php echo date('d M Y, H:i', strtotime($req['request_date'])); ?></small>
                        </div>
                        <a href="../owner/akun_pengguna/edit.php?id=<?php echo $req['id_pengguna']; ?>" style="background-color: #ff9800; color: white; padding: 8px 15px; border-radius: 4px; text-decoration: none; font-weight: bold; font-size: 14px; transition: background 0.3s;"><i class="fas fa-sync-alt"></i> Action</a>
                    </li>
                <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p style="color: #777; margin: 0;"><i class="fas fa-check-circle" style="color: #28a745;"></i> Tidak ada request reset password saat ini.</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
