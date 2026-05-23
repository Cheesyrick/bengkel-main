<?php
session_start();
if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'service_advisor') {
    header("Location: ../../auth/login.php");
    exit();
}
include '../../config/config.php';

if (!isset($_GET['id_permintaan'])) {
    header("Location: list.php");
    exit();
}

$id_permintaan = $_GET['id_permintaan'];

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_pelanggan = $_POST['id_pelanggan'] ?? '';
    $id_mobil = $_POST['id_mobil'] ?? '';
    $id_jasa = $_POST['id_jasa'] ?? '';
    $id_spareparts = $_POST['id_sparepart'] ?? [];
    $keluhan = $_POST['keluhan'] ?? '';
    $status = $_POST['status'] ?? 'pending';
    $id_mekanik = $_POST['id_mekanik'] ?? '';

    if (empty($id_mobil) || empty($id_jasa) || empty($keluhan)) {
        $_SESSION['pesan_error'] = "Mobil, Jasa, dan Keluhan harus diisi!";
    } else {
        $conn->begin_transaction();
        try {
            // 1. Update permintaan_servis
            $query_ps = "UPDATE permintaan_servis SET id_mobil = ?, keluhan = ? WHERE id_permintaan = ?";
            $stmt_ps = $conn->prepare($query_ps);
            $stmt_ps->bind_param("isi", $id_mobil, $keluhan, $id_permintaan);
            $stmt_ps->execute();

            // 2. Update detail_servis (assuming 1 to 1 for simplicity in this form)
            $query_harga_jasa = "SELECT harga_jasa FROM jasa WHERE id_jasa = ?";
            $stmt_hj = $conn->prepare($query_harga_jasa);
            $stmt_hj->bind_param("i", $id_jasa);
            $stmt_hj->execute();
            $res_hj = $stmt_hj->get_result()->fetch_assoc();
            $harga_jasa = $res_hj['harga_jasa'];
            
            $qty_jasa = 1;
            
            // Check if detail_servis exists
            $check_ds = $conn->query("SELECT id_detail_servis FROM detail_servis WHERE id_permintaan = $id_permintaan");
            if ($check_ds->num_rows > 0) {
                $query_ds = "UPDATE detail_servis SET id_jasa = ?, total_biaya_jasa = ? WHERE id_permintaan = ?";
                $stmt_ds = $conn->prepare($query_ds);
                $stmt_ds->bind_param("iii", $id_jasa, $harga_jasa, $id_permintaan);
            } else {
                $query_ds = "INSERT INTO detail_servis (id_permintaan, id_jasa, qty, total_biaya_jasa) VALUES (?, ?, ?, ?)";
                $stmt_ds = $conn->prepare($query_ds);
                $stmt_ds->bind_param("iiii", $id_permintaan, $id_jasa, $qty_jasa, $harga_jasa);
            }
            $stmt_ds->execute();

            // 3. Update detail_sparepart
            // First, restore stock for old spareparts
            $old_dsp = $conn->query("SELECT id_sparepart, qty FROM detail_sparepart WHERE id_permintaan = $id_permintaan");
            while ($row = $old_dsp->fetch_assoc()) {
                $conn->query("UPDATE sparepart SET stock = stock + " . $row['qty'] . " WHERE id_sparepart = " . $row['id_sparepart']);
            }
            
            // Remove old details
            $conn->query("DELETE FROM detail_sparepart WHERE id_permintaan = $id_permintaan");

            if (!empty($id_spareparts) && is_array($id_spareparts)) {
                $qty_spareparts = $_POST['qty_sparepart'] ?? [];
                $query_harga_sp = "SELECT harga_jual, stock FROM sparepart WHERE id_sparepart = ?";
                $stmt_hsp = $conn->prepare($query_harga_sp);
                
                $query_dsp = "INSERT INTO detail_sparepart (id_permintaan, id_sparepart, qty, harga_satuan) VALUES (?, ?, ?, ?)";
                $stmt_dsp = $conn->prepare($query_dsp);
                
                $query_upd_sp = "UPDATE sparepart SET stock = stock - ? WHERE id_sparepart = ?";
                $stmt_upd_sp = $conn->prepare($query_upd_sp);

                foreach($id_spareparts as $index => $id_sp) {
                    if(empty($id_sp)) continue;
                    $qty_sp = isset($qty_spareparts[$index]) ? (int)$qty_spareparts[$index] : 1;
                    if($qty_sp <= 0) continue;
                    
                    $stmt_hsp->bind_param("i", $id_sp);
                    $stmt_hsp->execute();
                    $res_hsp = $stmt_hsp->get_result()->fetch_assoc();
                    
                    if($res_hsp && $res_hsp['stock'] >= $qty_sp) {
                        $harga_sp = $res_hsp['harga_jual'];
                        
                        // Insert detail
                        $stmt_dsp->bind_param("iiii", $id_permintaan, $id_sp, $qty_sp, $harga_sp);
                        $stmt_dsp->execute();
                        
                        // Deduct stock
                        $stmt_upd_sp->bind_param("ii", $qty_sp, $id_sp);
                        $stmt_upd_sp->execute();
                    } else if ($res_hsp) {
                        throw new Exception("Stok untuk sparepart ID $id_sp tidak mencukupi (Diminta: $qty_sp, Tersedia: {$res_hsp['stock']}).");
                    }
                }
            }

            // 4. Update detail_pengerjaan
            if (!empty($id_mekanik)) {
                $tgl_selesai = ($status == 'done' || $status == 'selesai') ? date('Y-m-d') : null;
                
                $check_dp = $conn->query("SELECT id_detail_pengerjaan FROM detail_pengerjaan WHERE id_permintaan = $id_permintaan");
                if ($check_dp->num_rows > 0) {
                    $query_dp = "UPDATE detail_pengerjaan SET id_pengguna = ?, status_pengerjaan = ?, tanggal_selesai_kerja = ? WHERE id_permintaan = ?";
                    $stmt_dp = $conn->prepare($query_dp);
                    $stmt_dp->bind_param("issi", $id_mekanik, $status, $tgl_selesai, $id_permintaan);
                } else {
                    $tgl_mulai = date('Y-m-d');
                    $query_dp = "INSERT INTO detail_pengerjaan (id_pengguna, id_permintaan, status_pengerjaan, tanggal_mulai_kerja, tanggal_selesai_kerja) VALUES (?, ?, ?, ?, ?)";
                    $stmt_dp = $conn->prepare($query_dp);
                    $stmt_dp->bind_param("iisss", $id_mekanik, $id_permintaan, $status, $tgl_mulai, $tgl_selesai);
                }
                $stmt_dp->execute();
            } else {
                 $conn->query("DELETE FROM detail_pengerjaan WHERE id_permintaan = $id_permintaan");
            }

            // Update tanggal_keluar in permintaan_servis if done
            if ($status == 'done' || $status == 'selesai') {
                $conn->query("UPDATE permintaan_servis SET tanggal_keluar = NOW() WHERE id_permintaan = $id_permintaan");
            } else {
                $conn->query("UPDATE permintaan_servis SET tanggal_keluar = NULL WHERE id_permintaan = $id_permintaan");
            }

            $conn->commit();
            $_SESSION['pesan_sukses'] = "Permintaan servis berhasil diperbarui!";
            header("Location: list.php");
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['pesan_error'] = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}

// Fetch Current Data
$query_current_ps = "SELECT ps.*, m.id_pelanggan FROM permintaan_servis ps JOIN mobil m ON ps.id_mobil = m.id_mobil WHERE ps.id_permintaan = ?";
$stmt_curr_ps = $conn->prepare($query_current_ps);
$stmt_curr_ps->bind_param("i", $id_permintaan);
$stmt_curr_ps->execute();
$current_ps = $stmt_curr_ps->get_result()->fetch_assoc();

if (!$current_ps) {
    $_SESSION['pesan_error'] = "Data tidak ditemukan!";
    header("Location: list.php");
    exit();
}

$current_ds = $conn->query("SELECT * FROM detail_servis WHERE id_permintaan = $id_permintaan")->fetch_assoc();
$current_dsp_query = $conn->query("SELECT id_sparepart, qty FROM detail_sparepart WHERE id_permintaan = $id_permintaan");
$current_dsp_data = [];
while ($r = $current_dsp_query->fetch_assoc()) {
    $current_dsp_data[] = [
        'id' => $r['id_sparepart'],
        'qty' => $r['qty']
    ];
}
$current_dp = $conn->query("SELECT * FROM detail_pengerjaan WHERE id_permintaan = $id_permintaan")->fetch_assoc();

// Fetch Master Data
$pelanggan_data = [];
$res_pelanggan = $conn->query("SELECT * FROM pelanggan");
while ($r = $res_pelanggan->fetch_assoc()) $pelanggan_data[] = $r;

$mobil_data = [];
$res_mobil = $conn->query("SELECT * FROM mobil");
while ($r = $res_mobil->fetch_assoc()) $mobil_data[] = $r;

$jasa_data = [];
$res_jasa = $conn->query("SELECT * FROM jasa");
while ($r = $res_jasa->fetch_assoc()) $jasa_data[] = $r;

$sp_data = [];
$res_sp = $conn->query("SELECT * FROM sparepart");
while ($r = $res_sp->fetch_assoc()) $sp_data[] = $r;

$mekanik_data = [];
$res_mekanik = $conn->query("SELECT * FROM pengguna WHERE role = 'mechanic'");
while ($r = $res_mekanik->fetch_assoc()) $mekanik_data[] = $r;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Permintaan Servis | Bengkel Bengawan</title>
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
                <i class="fas fa-edit" style="color: #d32f2f"></i> Edit Permintaan Servis
            </h2>

            <?php if(isset($_SESSION['pesan_error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['pesan_error']; unset($_SESSION['pesan_error']); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label>Pelanggan</label>
                    <select name="id_pelanggan" id="id_pelanggan" class="form-control" required>
                        <option value="">-- Pilih Pelanggan --</option>
                        <?php foreach($pelanggan_data as $p): ?>
                            <option value="<?= $p['id_pelanggan'] ?>" <?= ($p['id_pelanggan'] == $current_ps['id_pelanggan']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['nama_pelanggan']) ?> (<?= htmlspecialchars($p['no_telp']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Mobil</label>
                    <select name="id_mobil" id="id_mobil" class="form-control" required>
                        <option value="">-- Pilih Mobil --</option>
                        <!-- Options populated by JS -->
                    </select>
                </div>

                <div class="form-group">
                    <label>Jasa Servis</label>
                    <select name="id_jasa" class="form-control" required>
                        <option value="">-- Pilih Jasa --</option>
                        <?php foreach($jasa_data as $j): ?>
                            <option value="<?= $j['id_jasa'] ?>" <?= ($current_ds && $j['id_jasa'] == $current_ds['id_jasa']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($j['nama_jasa']) ?> (Rp <?= number_format($j['harga_jasa'],0,',','.') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Sparepart (Opsional)</label>
                    <div id="sparepartContainer">
                        <!-- Baris sparepart dinamis -->
                    </div>
                    <button type="button" class="btn btn-add-row btn-sm" id="btnTambahSparepart" style="margin-top: 10px;">
                        <i class="fas fa-plus"></i> Tambah Baris Sparepart
                    </button>
                </div>

                <div class="form-group">
                    <label>Mekanik</label>
                    <select name="id_mekanik" class="form-control" required>
                        <option value="">-- Pilih Mekanik --</option>
                        <?php foreach($mekanik_data as $m): ?>
                            <option value="<?= $m['id_pengguna'] ?>" <?= ($current_dp && $m['id_pengguna'] == $current_dp['id_pengguna']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($m['username']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control" required>
                        <?php $curr_status = $current_dp ? $current_dp['status_pengerjaan'] : 'pending'; ?>
                        <option value="pending" <?= ($curr_status == 'pending') ? 'selected' : '' ?>>Pending</option>
                        <option value="assigned" <?= ($curr_status == 'assigned') ? 'selected' : '' ?>>Assigned</option>
                        <option value="done" <?= ($curr_status == 'done') ? 'selected' : '' ?>>Done</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Keluhan</label>
                    <textarea name="keluhan" class="form-control" rows="4" placeholder="Jelaskan keluhan pelanggan..." required><?= htmlspecialchars($current_ps['keluhan']) ?></textarea>
                </div>

                <button type="submit" class="btn btn-submit">Update Permintaan</button>
            </form>
        </div>
    </div>

    <script>
        const mobilData = [
            <?php foreach($mobil_data as $m): ?>
            {
                id_mobil: "<?= addslashes($m['id_mobil']) ?>",
                id_pelanggan: "<?= addslashes($m['id_pelanggan']) ?>",
                merk_mobil: "<?= addslashes($m['merk_mobil']) ?>",
                tipe_mobil: "<?= addslashes($m['tipe_mobil']) ?>",
                plat_nomor: "<?= addslashes($m['plat_nomor']) ?>"
            },
            <?php endforeach; ?>
        ];
        const pelangganSelect = document.getElementById('id_pelanggan');
        const mobilSelect = document.getElementById('id_mobil');
        const currentIdMobil = '<?= $current_ps['id_mobil'] ?>';

        function populateMobil(idPelanggan, selectedIdMobil) {
            mobilSelect.innerHTML = '<option value="">-- Pilih Mobil --</option>';
            if (idPelanggan) {
                const filteredMobil = mobilData.filter(m => m.id_pelanggan == idPelanggan);
                filteredMobil.forEach(m => {
                    const option = document.createElement('option');
                    option.value = m.id_mobil;
                    option.textContent = m.merk_mobil + ' ' + m.tipe_mobil + ' (' + m.plat_nomor + ')';
                    if (m.id_mobil == selectedIdMobil) {
                        option.selected = true;
                    }
                    mobilSelect.appendChild(option);
                });
            }
        }

        // Initialize on load
        populateMobil(pelangganSelect.value, currentIdMobil);

        // Event listener
        pelangganSelect.addEventListener('change', function() {
            populateMobil(this.value, null);
        });

        const sparepartData = [
            <?php foreach($sp_data as $sp): ?>
            {
                id: "<?= addslashes($sp['id_sparepart']) ?>",
                nama: "<?= addslashes($sp['nama_sparepart']) ?>",
                harga: <?= $sp['harga_jual'] ?>,
                stok: <?= $sp['stock'] ?>
            },
            <?php endforeach; ?>
        ];

        const currentSpareparts = <?= json_encode($current_dsp_data) ?>;

        const sparepartContainer = document.getElementById('sparepartContainer');
        const btnTambahSparepart = document.getElementById('btnTambahSparepart');

        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
        }

        function createSparepartRow(selectedId = '', qty = 1) {
            const row = document.createElement('div');
            row.style.display = 'flex';
            row.style.gap = '10px';
            row.style.marginBottom = '10px';
            row.style.alignItems = 'center';

            let optionsHtml = '<option value="">-- Pilih Sparepart --</option>';
            sparepartData.forEach(sp => {
                const disabled = (sp.stok <= 0 && sp.id != selectedId) ? 'disabled' : '';
                const selected = sp.id == selectedId ? 'selected' : '';
                optionsHtml += `<option value="${sp.id}" ${disabled} ${selected}>${sp.nama} (${formatRupiah(sp.harga)}) - Stok: ${sp.stok}</option>`;
            });

            row.innerHTML = `
                <select name="id_sparepart[]" class="form-control" style="flex: 1;" required>
                    ${optionsHtml}
                </select>
                <input type="number" name="qty_sparepart[]" class="form-control" value="${qty}" placeholder="Qty" min="1" style="width: 100px;" required>
                <button type="button" class="btn btn-danger btn-sm btn-hapus-sp" title="Hapus"><i class="fas fa-times"></i></button>
            `;

            row.querySelector('.btn-hapus-sp').addEventListener('click', function() {
                row.remove();
            });

            sparepartContainer.appendChild(row);
        }

        btnTambahSparepart.addEventListener('click', () => createSparepartRow());

        // Pre-fill existing data
        if (currentSpareparts && currentSpareparts.length > 0) {
            currentSpareparts.forEach(sp => {
                createSparepartRow(sp.id, sp.qty);
            });
        }

    </script>
</body>
</html>
<?php include('../../includes/footer.php'); ?>
