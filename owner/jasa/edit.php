<?php
session_start();
if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'owner') {
    header("Location: ../../auth/login.php");
    exit();
}
include '../../config/config.php';

// Cek apakah ada ID
if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit;
}

$id_jasa = $_GET['id'];

// Proses form jika di-submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_jasa = $_POST['nama_jasa'];
    $id_jenis_jasa = $_POST['id_jenis_jasa'];
    $estimasi_waktu = $_POST['estimasi_waktu'];
    $harga_jasa = $_POST['harga_jasa'];

    if (empty($nama_jasa) || empty($id_jenis_jasa) || empty($estimasi_waktu) || empty($harga_jasa)) {
        $_SESSION['pesan_error'] = "Semua field harus diisi!";
    } else {
        $query = "UPDATE jasa SET id_jenis_jasa = ?, nama_jasa = ?, estimasi_waktu = ?, harga_jasa = ? WHERE id_jasa = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isiii", $id_jenis_jasa, $nama_jasa, $estimasi_waktu, $harga_jasa, $id_jasa);
        
        if ($stmt->execute()) {
            $_SESSION['pesan_sukses'] = "Jasa berhasil diupdate!";
            header("Location: list.php");
            exit;
        } else {
            $_SESSION['pesan_error'] = "Gagal mengupdate jasa: " . $stmt->error;
        }
    }
}

// Ambil data jasa yang akan diedit
$query_jasa = "SELECT * FROM jasa WHERE id_jasa = ?";
$stmt = $conn->prepare($query_jasa);
$stmt->bind_param("i", $id_jasa);
$stmt->execute();
$result_jasa = $stmt->get_result();

if ($result_jasa->num_rows === 0) {
    header("Location: list.php");
    exit;
}

$jasa = $result_jasa->fetch_assoc();

// Ambil data jenis_jasa untuk dropdown
$query_jenis = "SELECT * FROM jenis_jasa";
$result_jenis = $conn->query($query_jenis);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Jasa | Bengkel Bengawan</title>
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
                <i class="fas fa-edit" style="color: #d32f2f"></i> Edit Jasa
            </h2>

            <?php if(isset($_SESSION['pesan_error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['pesan_error']; unset($_SESSION['pesan_error']); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label>Nama Jasa</label>
                    <input type="text" name="nama_jasa" class="form-control" placeholder="Input nama jasa" value="<?php echo htmlspecialchars($jasa['nama_jasa']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Jenis Jasa</label>
                    <select name="id_jenis_jasa" class="form-control" required>
                        <option value="">-- Pilih Jenis Jasa --</option>
                        <?php while($row = $result_jenis->fetch_assoc()): ?>
                            <option value="<?php echo $row['id_jenis_jasa']; ?>" <?php echo ($row['id_jenis_jasa'] == $jasa['id_jenis_jasa']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($row['nama_jenis_jasa']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Estimasi Waktu (menit)</label>
                    <input type="number" name="estimasi_waktu" class="form-control" placeholder="Input estimasi waktu (menit)" value="<?php echo htmlspecialchars($jasa['estimasi_waktu']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Harga Jasa (Rp)</label>
                    <input type="number" name="harga_jasa" class="form-control" placeholder="Input harga jasa" value="<?php echo htmlspecialchars($jasa['harga_jasa']); ?>" required>
                </div>

                <button type="submit" class="btn btn-submit">Update Jasa</button>
            </form>
        </div>
    </div>
</body>
</html>