<?php 
session_start();
if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'owner')
{
    header("Location: ../../auth/login.php");
    exit();
}

include '../../config/config.php';

if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit();
}

$id_sparepart = $_GET['id'];

// Proses ketika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_sparepart = $_POST['nama_sparepart'];
    $stock = $_POST['stock'];
    $harga_jual = $_POST['harga_jual'];
    $id_kategori = $_POST['id_kategori'];
    $id_merk = $_POST['id_merk'];
    $id_tipe = $_POST['id_tipe'];
    $id_satuan = $_POST['id_satuan'];

    if (empty($nama_sparepart) || empty($harga_jual) || empty($id_kategori) 
        || empty($id_merk) || empty($id_tipe) || empty($stock) 
        || empty($id_satuan)) 
    {
        $_SESSION['pesan_error'] = "Data tidak boleh kosong!";
    }
    else {
        $query_update = "UPDATE sparepart SET 
                            nama_sparepart = ?, 
                            harga_jual = ?, 
                            id_kategori = ?, 
                            id_merk = ?, 
                            id_tipe_sp = ?, 
                            stock = ?, 
                            id_satuan = ? 
                         WHERE id_sparepart = ?";
                         
        $stmt_update = $conn->prepare($query_update);
        $stmt_update->bind_param("sdiiiiii", $nama_sparepart, $harga_jual, $id_kategori, $id_merk, $id_tipe, $stock, $id_satuan, $id_sparepart);
        
        if ($stmt_update->execute()) {
            $_SESSION['pesan_sukses'] = "Data sparepart berhasil diupdate!";
            header("Location: list.php");
            exit();
        } else {
            $_SESSION['pesan_error'] = "Gagal mengupdate data: " . $stmt_update->error;
        }
    }
}

// Ambil data sparepart yang akan diedit
$query_data = "SELECT * FROM sparepart WHERE id_sparepart = ?";
$stmt_data = $conn->prepare($query_data);
$stmt_data->bind_param("i", $id_sparepart);
$stmt_data->execute();
$result_data = $stmt_data->get_result();

if ($result_data->num_rows === 0) {
    header("Location: list.php");
    exit();
}
$data = $result_data->fetch_assoc();

// Ambil data untuk dropdowns
$q_kategori = mysqli_query($conn, "SELECT * FROM kategori_sparepart ORDER BY nama_kategori ASC");
$q_merk = mysqli_query($conn, "SELECT * FROM merk ORDER BY nama_merk ASC");
$q_tipe = mysqli_query($conn, "SELECT * FROM tipe_sparepart ORDER BY nama_tipe ASC");
$q_satuan = mysqli_query($conn, "SELECT * FROM satuan ORDER BY nama_satuan ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Sparepart | Bengkel Bengawan</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/add.css">
</head>
<body>
    <?php include '../../includes/sidebar.php'; ?>

    <div class="content">
        <div class="form-container">
            <div style="text-align:left;">
                <a href="list.php" class="btn btn-back"><i class="fas fa-arrow-left"></i></a>
            </div>

            <h2 style="margin-top : 0; color: #333; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">
                <i class="fas fa-edit" style="color: #d32f2f"></i> Edit Sparepart
            </h2>

            <?php if(isset($_SESSION['pesan_error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                        <?php echo $_SESSION['pesan_error'];
                            unset($_SESSION['pesan_error']);
                        ?>
                </div>
            <?php endif; ?>
            
            <form action="" method="POST">
                <div class="form-group">
                    <label>Nama Sparepart</label>
                    <input type="text" name="nama_sparepart" class="form-control" value="<?php echo htmlspecialchars($data['nama_sparepart']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Stock</label>
                    <input type="number" name="stock" class="form-control" value="<?php echo htmlspecialchars($data['stock']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Harga Jual</label>
                    <input type="number" name="harga_jual" class="form-control" value="<?php echo htmlspecialchars($data['harga_jual']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Kategori</label>
                    <select name="id_kategori" class="form-control" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php while($row = mysqli_fetch_assoc($q_kategori)): ?>
                            <option value="<?php echo $row['id_kategori']; ?>" <?php echo ($data['id_kategori'] == $row['id_kategori']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($row['nama_kategori']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Merk</label>
                    <select name="id_merk" class="form-control" required>
                        <option value="">-- Pilih Merk --</option>
                        <?php while($row = mysqli_fetch_assoc($q_merk)): ?>
                            <option value="<?php echo $row['id_merk']; ?>" <?php echo ($data['id_merk'] == $row['id_merk']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($row['nama_merk']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Tipe</label>
                    <select name="id_tipe" class="form-control" required>
                        <option value="">-- Pilih Tipe --</option>
                        <?php while($row = mysqli_fetch_assoc($q_tipe)): ?>
                            <option value="<?php echo $row['id_tipe_sp']; ?>" <?php echo ($data['id_tipe_sp'] == $row['id_tipe_sp']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($row['nama_tipe']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Satuan</label>
                    <select name="id_satuan" class="form-control" required>
                        <option value="">-- Pilih Satuan --</option>
                        <?php while($row = mysqli_fetch_assoc($q_satuan)): ?>
                            <option value="<?php echo $row['id_satuan']; ?>" <?php echo ($data['id_satuan'] == $row['id_satuan']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($row['nama_satuan']) . ' (' . htmlspecialchars($row['singkatan']) . ')'; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-submit">Update Sparepart</button>
            </form>
        </div>
    </div>

</body>
</html>
<?php include('../../includes/footer.php'); ?>
