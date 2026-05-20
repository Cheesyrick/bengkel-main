<?php
session_start();
include '../../config/config.php';

if(!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'owner') {
    header("Location: ../../auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_pelanggan = $_POST['nama_pelanggan'];
    $no_telepon = $_POST['no_telepon'];
    $alamat = $_POST['alamat'];
    $merk = $_POST['merk'];
    $tipe = $_POST['tipe'];
    $tahun = $_POST['tahun'];
    $platno = $_POST['platno'];

    if (empty($nama_pelanggan) || empty($no_telepon) ||
        empty($merk) || empty($tipe) || empty($platno)) {
        $_SESSION['pesan_error'] = "Semua field harus diisi!";
    } else {
        $conn->begin_transaction();

        $query_pelanggan = "INSERT INTO pelanggan (nama_pelanggan, no_telp, alamat) VALUES (?, ?, ?)";
        $stmt_pelanggan = $conn->prepare($query_pelanggan);
        $stmt_pelanggan->bind_param("sss", $nama_pelanggan, $no_telepon, $alamat);

        if ($stmt_pelanggan->execute()) {
            $id_pelanggan = $conn->insert_id;

            $query_mobil = "INSERT INTO mobil (id_pelanggan, merk_mobil, tipe_mobil, plat_nomor, tahun_mobil) VALUES (?, ?, ?, ?, ?)";
            $stmt_mobil = $conn->prepare($query_mobil);
            $stmt_mobil->bind_param("isssi", $id_pelanggan, $merk, $tipe, $platno, $tahun);

            if ($stmt_mobil->execute()) {
                $conn->commit();
                $_SESSION['pesan_sukses'] = "Pelanggan dan mobil berhasil ditambahkan!";
                header("Location: list.php");
                exit;
            } else {
                $conn->rollback();
                $_SESSION['pesan_error'] = "Gagal menambahkan mobil: " . $stmt_mobil->error;
            }
        } else {
            $conn->rollback();
            $_SESSION['pesan_error'] = "Gagal menambahkan pelanggan: " . $stmt_pelanggan->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pelanggan dan Mobil | Bengkel Bengawan</title>
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
                <i class="fas fa-address-book" style="color: #d32f2f"></i> Tambah Pelanggan dan Mobil
            </h2>

            <?php if(isset($_SESSION['pesan_error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['pesan_error']; unset($_SESSION['pesan_error']); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label>Nama Pelanggan</label>
                    <input type="text" name="nama_pelanggan" class="form-control" placeholder="Input nama pelanggan" required>
                </div>

                <div class="form-group">
                    <label>No Telepon</label>
                    <input type="text" name="no_telepon" class="form-control" placeholder="Input no telepon pelanggan" required>
                </div>

                <div class="form-group">
                    <label>Alamat</label>
                    <input type="text" name="alamat" class="form-control" placeholder="Input alamat pelanggan">
                </div>

                <div class="form-group">
                    <label>Merk</label>
                    <input type="text" name="merk" class="form-control" placeholder="Input merk mobil" required>
                </div>

                <div class="form-group">
                    <label>Tipe</label>
                    <input type="text" name="tipe" class="form-control" placeholder="Input tipe mobil" required>
                </div>

                <div class="form-group">
                    <label>Tahun</label>
                    <input type="text" name="tahun" class="form-control" placeholder="Input tahun mobil">
                </div>

                <div class="form-group">
                    <label>Plat Nomor</label>
                    <input type="text" name="platno" class="form-control" placeholder="Input plat nomor mobil" required>
                </div>

                <button type="submit" class="btn btn-submit">Simpan Pelanggan dan Mobil</button>
            </form>
        </div>
    </div>
</body>
</html>
<?php include('../../includes/footer.php'); ?>