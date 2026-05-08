<?php
session_start();
if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'owner') {
    header("Location: ../../auth/login.php");
    exit();
}
include '../../config/config.php';

// Fetch all users
$query = "SELECT * FROM pengguna ORDER BY id_pengguna DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Pengguna | Broom Garage</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
    <style>
        .table-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin: 20px auto;
            max-width: 900px;
            border-top: 4px solid #d32f2f;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: 600;
        }
        .btn {
            padding: 8px 12px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 13px;
            color: white;
            display: inline-block;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .btn-add { background-color: #4CAF50; margin-bottom: 20px; font-size: 15px; padding: 10px 20px;}
        .btn-edit { background-color: #2196F3; }
        .btn-delete { background-color: #f44336; }
        .btn-add:hover { background-color: #45a049; }
        .btn-edit:hover { background-color: #0b7dda; }
        .btn-delete:hover { background-color: #da190b; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; font-weight: bold;}
        .alert-success { background-color: #dff0d8; color: #3c763d; border: 1px solid #d6e9c6; }
        .alert-danger { background-color: #f2dede; color: #a94442; border: 1px solid #ebccd1; }
    </style>
</head>
<body>
    <?php include '../../includes/sidebar.php'; ?>
    
    <div class="content">
        <div class="table-container">
            <h2 style="margin-top: 0; color: #333; text-align: left; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">
                <i class="fas fa-users" style="color: #d32f2f;"></i> Daftar Akun Pengguna
            </h2>
            
            <?php if(isset($_SESSION['pesan_sukses'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $_SESSION['pesan_sukses']; unset($_SESSION['pesan_sukses']); ?>
                </div>
            <?php endif; ?>
            
            <?php if(isset($_SESSION['pesan_error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['pesan_error']; unset($_SESSION['pesan_error']); ?>
                </div>
            <?php endif; ?>

            <div style="text-align: left;">
                <a href="add.php" class="btn btn-add"><i class="fas fa-plus"></i> Tambah Pengguna</a>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    if(mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)): 
                    ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><strong><?php echo htmlspecialchars($row['username']); ?></strong></td>
                            <td>
                                <span class="role-badge" style="font-size: 12px; margin-top:0;">
                                    <?php echo strtoupper(htmlspecialchars($row['role'])); ?>
                                </span>
                            </td>
                            <td>
                                <a href="edit.php?id=<?php echo $row['id_pengguna']; ?>" class="btn btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                <?php if($row['id_pengguna'] != $_SESSION['id_pengguna']): ?>
                                <a href="delete.php?id=<?php echo $row['id_pengguna']; ?>" class="btn btn-delete" onclick="return confirm('Yakin ingin menghapus pengguna ini?');"><i class="fas fa-trash"></i> Hapus</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php 
                        endwhile;
                    } else {
                        echo "<tr><td colspan='4' style='text-align:center; padding: 20px;'>Tidak ada data pengguna</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php include '../../includes/footer.php'; ?>
</body>
</html>
