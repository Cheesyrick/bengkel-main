<?php 
session_start();
if(!isset($_SESSION["id_pengguna"]) || $_SESSION["role"] != "owner"){
    header("Location: ../../auth/login.php");
    exit();
}
include '../../config/config.php';

if (!isset($_GET['id_pelanggan'])) {
    header("Location: list.php");
    exit();
}

$id_pelanggan = $_GET['id_pelanggan'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_pelanggan = $_POST['nama_pelanggan'];
    $no_telepon = $_POST['no_telepon'];
    $alamat = $_POST['alamat'];
    
    $id_mobil = $_POST['id_mobil']; // Needed for UPDATE
    $merk = $_POST['merk'];
    $tipe = $_POST['tipe'];
    $tahun = $_POST['tahun'];
    $platno = $_POST['platno'];

    if (empty($nama_pelanggan) || empty($no_telepon) ||
        empty($merk) || empty($tipe) || empty($platno)) {
        $_SESSION['pesan_error'] = "Semua field harus diisi!";
    } else {
        $conn->begin_transaction();

        try {
            $query_pelanggan = "UPDATE pelanggan SET nama_pelanggan=?, no_telp=?, alamat=? WHERE id_pelanggan=?";
            $stmt_pelanggan = $conn->prepare($query_pelanggan);
            $stmt_pelanggan->bind_param("sssi", $nama_pelanggan, $no_telepon, $alamat, $id_pelanggan);
            
            if (!$stmt_pelanggan->execute()) {
                throw new Exception("Gagal update pelanggan: " . $stmt_pelanggan->error);
            }

            if (!empty($id_mobil)) {
                $query_mobil = "UPDATE mobil SET merk_mobil=?, tipe_mobil=?, plat_nomor=?, tahun_mobil=? WHERE id_mobil=?";
                $stmt_mobil = $conn->prepare($query_mobil);
                $stmt_mobil->bind_param("sssii", $merk, $tipe, $platno, $tahun, $id_mobil);
                if (!$stmt_mobil->execute()) {
                    throw new Exception("Gagal update mobil: " . $stmt_mobil->error);
                }
            } else {
                // Jika mobil belum ada, insert baru
                $query_mobil = "INSERT INTO mobil (id_pelanggan, merk_mobil, tipe_mobil, plat_nomor, tahun_mobil) VALUES (?, ?, ?, ?, ?)";
                $stmt_mobil = $conn->prepare($query_mobil);
                $stmt_mobil->bind_param("isssi", $id_pelanggan, $merk, $tipe, $platno, $tahun);
                if (!$stmt_mobil->execute()) {
                    throw new Exception("Gagal insert mobil: " . $stmt_mobil->error);
                }
            }

            $conn->commit();
            $_SESSION['pesan_sukses'] = "Data pelanggan dan mobil berhasil diupdate!";
            header("Location: list.php");
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['pesan_error'] = $e->getMessage();
        }
    }
}

// Fetch existing data
$query = "SELECT p.*, m.id_mobil, m.merk_mobil, m.tipe_mobil, m.plat_nomor, m.tahun_mobil 
          FROM pelanggan p 
          LEFT JOIN mobil m ON p.id_pelanggan = m.id_pelanggan 
          WHERE p.id_pelanggan = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_pelanggan);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: list.php");
    exit();
}

$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pelanggan dan Mobil | Bengkel Bengawan</title>
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
                <i class="fas fa-edit" style="color: #d32f2f"></i> Edit Pelanggan dan Mobil
            </h2>

            <?php if(isset($_SESSION['pesan_error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['pesan_error']; unset($_SESSION['pesan_error']); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <!-- Hidden inputs -->
                <input type="hidden" name="id_mobil" value="<?php echo isset($data['id_mobil']) ? $data['id_mobil'] : ''; ?>">

                <div class="form-group">
                    <label>Nama Pelanggan</label>
                    <input type="text" name="nama_pelanggan" class="form-control" value="<?php echo htmlspecialchars($data['nama_pelanggan']); ?>" required>
                </div>

                <div class="form-group">
                    <label>No Telepon</label>
                    <input type="text" name="no_telepon" class="form-control" value="<?php echo htmlspecialchars($data['no_telp']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Alamat</label>
                    <input type="text" name="alamat" class="form-control" value="<?php echo htmlspecialchars($data['alamat'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label>Merk</label>
                    <input type="text" name="merk" class="form-control" value="<?php echo htmlspecialchars($data['merk_mobil'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label>Tipe</label>
                    <input type="text" name="tipe" class="form-control" value="<?php echo htmlspecialchars($data['tipe_mobil'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label>Tahun</label>
                    <input type="text" name="tahun" class="form-control" value="<?php echo htmlspecialchars($data['tahun_mobil'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label>Plat Nomor</label>
                    <input type="text" name="platno" class="form-control" value="<?php echo htmlspecialchars($data['plat_nomor'] ?? ''); ?>" required>
                </div>

                <button type="submit" class="btn btn-submit">Update Pelanggan dan Mobil</button>
            </form>
        </div>
    </div>
</body>
</html>
<?php include('../../includes/footer.php'); ?>