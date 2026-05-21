<?php
session_start();
if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'mechanic') {
    header("Location: ../../auth/login.php");
    exit();
}
include '../../config/config.php';

if (!isset($_GET['id_permintaan'])) {
    header("Location: list.php");
    exit();
}

$id_permintaan = $_GET['id_permintaan'];
$ps_query = "SELECT ps.id_permintaan, dp.status_pengerjaan 
             FROM permintaan_servis ps
             LEFT JOIN detail_pengerjaan dp ON ps.id_permintaan = dp.id_permintaan
             WHERE ps.id_permintaan = ?";
$ps_stmt = $conn->prepare($ps_query);
$ps_stmt->bind_param("i", $id_permintaan);
$ps_stmt->execute();
$ps_result = $ps_stmt->get_result();
$current_dp = $ps_result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $status = $_POST['status'];
    $tgl_selesai = ($status == 'done' || $status == 'selesai') ? date('Y-m-d') : null;
    
    $check_dp = $conn->query("SELECT id_detail_pengerjaan FROM detail_pengerjaan WHERE id_permintaan = $id_permintaan");
    if ($check_dp->num_rows > 0) {
        $query_dp = "UPDATE detail_pengerjaan SET status_pengerjaan = ?, tanggal_selesai_kerja = ? WHERE id_permintaan = ?";
        $stmt_dp = $conn->prepare($query_dp);
        $stmt_dp->bind_param("ssi", $status, $tgl_selesai, $id_permintaan);
        
        if ($stmt_dp->execute()) {
            if ($status == 'done' || $status == 'selesai') {
                $conn->query("UPDATE permintaan_servis SET tanggal_keluar = NOW() WHERE id_permintaan = $id_permintaan");
            } else {
                $conn->query("UPDATE permintaan_servis SET tanggal_keluar = NULL WHERE id_permintaan = $id_permintaan");
            }
            $_SESSION['pesan_sukses'] = "Status pekerjaan berhasil diperbarui!";
            header("Location: list.php");
            exit();
        } else {
            $_SESSION['pesan_error'] = "Gagal memperbarui status: " . $conn->error;
        }
    } else {
        $_SESSION['pesan_error'] = "Data pengerjaan tidak ditemukan. Hubungi Owner/Service Advisor.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Status Pekerjaan | Bengkel Bengawan</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/add.css">
</head>
<body>
    <?php include '../../includes/sidebar.php'; ?>

    <div class="content">
        <div class="form-container">
            <div style="text-align: left;">
                <a href="list.php" class="btn btn-back"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h2 style="margin-top : 0; color: #333; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">
                <i class="fas fa-edit" style="color: #d32f2f"></i> Edit Status Pekerjaan
            </h2>

            <?php if(isset($_SESSION['pesan_error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['pesan_error']; unset($_SESSION['pesan_error']); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control" required>
                        <?php $curr_status = $current_dp ? $current_dp['status_pengerjaan'] : 'assigned'; ?>
                        <option value="assigned" <?= ($curr_status == 'assigned' || $curr_status == 'pending') ? 'selected' : '' ?>>Assigned</option>
                        <option value="done" <?= ($curr_status == 'done') ? 'selected' : '' ?>>Done</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-submit">Update Status</button>
            </form>
        </div>
    </div>
</body>
</html>
<?php include('../../includes/footer.php'); ?>
