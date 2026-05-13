<?php
session_start();
if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'owner') {
    header("Location: ../../auth/login.php");
    exit();
}

include '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_jasa = $_POST['nama_jasa'];
    $id_jenis_jasa = $_POST['id_jenis_jasa'];
    $estimasi_waktu = $_POST['estimasi_waktu'];
    $harga_jasa = $_POST['harga_jasa'];

    if (empty($nama_jasa) || empty($id_jenis_jasa) || empty($estimasi_waktu) || empty($harga_jasa)) {
        $_SESSION['pesan_error'] = "Semua field harus diisi!";
    } else {
        $query = "INSERT INTO jasa (id_jenis_jasa, nama_jasa, estimasi_waktu, harga_jasa) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isii", $id_jenis_jasa, $nama_jasa, $estimasi_waktu, $harga_jasa);
        
        if ($stmt->execute()) {
            $_SESSION['pesan_sukses'] = "Jasa berhasil ditambahkan!";
            header("Location: list.php");
            exit;
        } else {
            $_SESSION['pesan_error'] = "Gagal menambahkan jasa: " . $stmt->error;
        }
    }
}

$query_jenis = "SELECT * FROM jenis_jasa";
$result_jenis = $conn->query($query_jenis);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Jasa | Bengkel Bengawan</title>
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
                <i class="fas fa-address-book" style="color: #d32f2f"></i> Tambah Jasa
            </h2>

            <?php if(isset($_SESSION['pesan_error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['pesan_error']; unset($_SESSION['pesan_error']); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label>Nama Jasa</label>
                    <input type="text" name="nama_jasa" class="form-control" placeholder="Input nama jasa" required>
                </div>

                <div class="form-group">
                    <label>Jenis Jasa</label>
                    <select name="id_jenis_jasa" class="form-control" required>
                        <option value="">-- Pilih Kategori Jasa --</option>
                        <?php while($row = $result_jenis->fetch_assoc()): ?>
                            <option value="<?php echo $row['id_jenis_jasa']; ?>"><?php echo htmlspecialchars($row['nama_jenis_jasa']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Estimasi Waktu (menit)</label>
                    <input type="number" name="estimasi_waktu" class="form-control" placeholder="Input estimasi waktu (menit)" required>
                </div>

                <div class="form-group">
                    <label>Harga Jasa (Rp)</label>
                    <input type="number" name="harga_jasa" class="form-control" placeholder="Input harga jasa" required>
                </div>

                <button type="submit" class="btn btn-submit">Simpan Jasa</button>
            </form>
        </div>
    </div>
</body>
</html>