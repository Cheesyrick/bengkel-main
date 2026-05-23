<?php
session_start();
if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'owner') {
    header("Location: ../../auth/login.php");
    exit();
}
include '../../config/config.php';

// Fetch all pelanggan and mobil
$query = "SELECT p.*, m.id_mobil, m.merk_mobil, m.tipe_mobil, m.plat_nomor, m.tahun_mobil 
          FROM pelanggan p 
          LEFT JOIN mobil m ON p.id_pelanggan = m.id_pelanggan 
          ORDER BY p.id_pelanggan DESC";
$result = mysqli_query($conn, $query);

// Ambil data pelanggan untuk Dropdown & JavaScript
$q_pelanggan = mysqli_query($conn, "SELECT id_pelanggan, nama_pelanggan, no_telp, alamat FROM pelanggan ORDER BY nama_pelanggan ASC");
$data_pelanggan = [];
while ($row = mysqli_fetch_assoc($q_pelanggan)) {
    $data_pelanggan[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pelanggan dan Mobil | Bengkel Bengawan</title>
    <!-- Memanggil Bootstrap CSS untuk Modal -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/list.css">
</head>
<body>
    <?php include '../../includes/sidebar.php'; ?>
    
    <div class="content">
        <div class="table-container">
            <h2 style="margin-top: 0; color: #333; text-align: left; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">
                <i class="fas fa-users" style="color: #d32f2f;"></i> Daftar Pelanggan dan Mobil
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

            <style>
                .btn-tambah {
                    padding: 10px 20px;
                    font-size: 15px;
                    font-weight: bold;
                    border-radius: 4px;
                    color: white;
                    border: none;
                    cursor: pointer;
                    display: inline-block;
                }
                .btn-pelanggan { background-color: #4CAF50; }
                .btn-pelanggan:hover { background-color: #45a049; color: white; }
                .btn-mobil { background-color: #2196F3; }
                .btn-mobil:hover { background-color: #0b7dda; color: white; }
                .btn-switch-green {
                    background-color: #4CAF50;
                    color: white;
                    border: 1px solid #4CAF50;
                    transition: 0.3s;
                }
                .btn-switch-green:hover {
                    background-color: white;
                    color: #4CAF50;
                }
            </style>
            <div style="text-align: left; margin-bottom: 20px;">
                <button type="button" class="btn-tambah btn-pelanggan" data-toggle="modal" data-target="#modalTambahPelanggan" style="margin-right: 10px;">
                    <i class="fas fa-user-plus"></i> Tambah Pelanggan
                </button>
                <button type="button" class="btn-tambah btn-mobil" data-toggle="modal" data-target="#modalTambahMobil">
                    <i class="fas fa-car"></i> Tambah Mobil
                </button>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pelanggan</th>
                        <th>No Telepon</th>
                        <th>Alamat</th>
                        <th>Mobil</th>
                        <th>Plat Nomor</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    if($result && mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)): 
                            $mobil_info = $row['merk_mobil'] ? $row['merk_mobil'] . ' ' . $row['tipe_mobil'] : '-';
                            $plat_nomor = $row['plat_nomor'] ? $row['plat_nomor'] : '-';
                    ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><strong><?php echo htmlspecialchars($row['nama_pelanggan']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['no_telp']); ?></td>
                            <td><?php echo $row['alamat']; ?></td>
                            <td><?php echo htmlspecialchars($mobil_info); ?></td>
                            <td><?php echo htmlspecialchars($plat_nomor); ?></td>
                            <td>
                                <a href="edit.php?id_pelanggan=<?php echo $row['id_pelanggan']; ?>" class="btn btn-edit"><i class="fas fa-edit"></i></a>
                                <a href="delete.php?id_pelanggan=<?php echo $row['id_pelanggan']; ?>" class="btn btn-delete" onclick="return confirm('Yakin ingin menghapus data ini beserta data mobilnya?');"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php 
                        endwhile;
                    } else {
                        echo "<tr><td colspan='6' style='text-align:center; padding: 20px;'>Tidak ada data</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php include '../../includes/footer.php'; ?>

    <!-- Modal Tambah Pelanggan -->
    <div class="modal fade" id="modalTambahPelanggan" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <form action="action_tambah_pelanggan.php" method="POST">
            <div class="modal-header">
              <h5 class="modal-title">Tambah Pelanggan Baru</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Nama Pelanggan</label>
                    <input type="text" name="nama_pelanggan" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>No. Telepon</label>
                    <input type="text" name="no_telepon" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="alamat" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary">Simpan Pelanggan</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal Tambah Mobil -->
    <div class="modal fade" id="modalTambahMobil" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <form action="action_tambah_mobil.php" method="POST">
            <div class="modal-header">
              <h5 class="modal-title">Tambah Mobil Baru</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Pilih Pelanggan</label>
                    <div class="input-group">
                        <select name="id_pelanggan" id="selectPelanggan" class="form-control" required>
                            <option value="">-- Pilih Pelanggan --</option>
                            <?php foreach($data_pelanggan as $p): ?>
                                <option value="<?= $p['id_pelanggan'] ?>"><?= htmlspecialchars($p['nama_pelanggan']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-switch-green" id="btnSwitchPelanggan" title="Tambah Pelanggan Baru">
                                <i class="fas fa-plus"></i> Baru
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>No Telepon (Konfirmasi)</label>
                    <input type="text" id="infoNoTelp" class="form-control" readonly style="background-color: #e9ecef;">
                </div>
                <div class="form-group">
                    <label>Alamat (Konfirmasi)</label>
                    <textarea id="infoAlamat" class="form-control" readonly style="background-color: #e9ecef;"></textarea>
                </div>
                
                <hr>

                <div class="form-group">
                    <label>Plat Nomor</label>
                    <input type="text" name="plat_nomor" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Merk Mobil</label>
                    <input type="text" name="merk_mobil" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Tipe Mobil</label>
                    <input type="text" name="tipe_mobil" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Tahun</label>
                    <input type="number" name="tahun_mobil" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary">Simpan Mobil</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script>
    const pelangganData = <?= json_encode($data_pelanggan); ?>;

    document.getElementById('selectPelanggan').addEventListener('change', function() {
        const idSelected = this.value;
        const infoTelp = document.getElementById('infoNoTelp');
        const infoAlamat = document.getElementById('infoAlamat');
        
        if(idSelected) {
            const p = pelangganData.find(item => item.id_pelanggan == idSelected);
            if(p) {
                infoTelp.value = p.no_telp;
                infoAlamat.value = p.alamat;
            }
        } else {
            infoTelp.value = '';
            infoAlamat.value = '';
        }
    });

    document.getElementById('btnSwitchPelanggan').addEventListener('click', function() {
        $('#modalTambahMobil').modal('hide');
        setTimeout(function() {
            $('#modalTambahPelanggan').modal('show');
        }, 400);
    });
    </script>
</body>
</html>
