<?php
session_start();
if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'service_advisor') {
    header("Location: ../../auth/login.php");
    exit();
}
include '../../config/config.php';

// If form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_permintaan = $_POST['id_permintaan'];
    $jumlah_bayar = str_replace(['Rp', '.', ' '], '', $_POST['jumlah_bayar']); // clean formatting
    $metode_pembayaran = $_POST['metode_pembayaran'];
    $status_pembayaran = $_POST['status_pembayaran'];
    $tanggal_bayar = $_POST['tanggal_bayar'];

    if (empty($id_permintaan) || empty($jumlah_bayar) || empty($metode_pembayaran) || empty($tanggal_bayar)) {
        $_SESSION['pesan_error'] = "Semua field harus diisi!";
    } else {
        $query = "INSERT INTO pembayaran (id_permintaan, jumlah_bayar, metode_pembayaran, status_pembayaran, tanggal_bayar) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iisss", $id_permintaan, $jumlah_bayar, $metode_pembayaran, $status_pembayaran, $tanggal_bayar);
        
        if ($stmt->execute()) {
            $_SESSION['pesan_sukses'] = "Pembayaran berhasil disimpan!";
            header("Location: list.php");
            exit();
        } else {
            $_SESSION['pesan_error'] = "Gagal menyimpan pembayaran: " . $conn->error;
        }
    }
}

// Fetch Permintaan Servis that are Done
// We calculate the total from detail_servis and detail_sparepart
$q_ps = "SELECT ps.id_permintaan, p.nama_pelanggan, m.plat_nomor, ps.keluhan,
         COALESCE(SUM(ds.qty * ds.total_biaya_jasa), 0) + 
         (SELECT COALESCE(SUM(dsp.qty * dsp.harga_satuan), 0) FROM detail_sparepart dsp WHERE dsp.id_permintaan = ps.id_permintaan) as total_tagihan
         FROM permintaan_servis ps
         JOIN mobil m ON ps.id_mobil = m.id_mobil
         JOIN pelanggan p ON m.id_pelanggan = p.id_pelanggan
         LEFT JOIN detail_servis ds ON ps.id_permintaan = ds.id_permintaan
         LEFT JOIN detail_pengerjaan dp ON ps.id_permintaan = dp.id_permintaan
         WHERE dp.status_pengerjaan IN ('assigned', 'pending')
         GROUP BY ps.id_permintaan";
$res_ps = $conn->query($q_ps);
$permintaan_data = [];
while($row = $res_ps->fetch_assoc()) {
    $id_p = $row['id_permintaan'];
    $q_riwayat = "SELECT status_pembayaran FROM pembayaran WHERE id_permintaan = $id_p";
    $res_riwayat = $conn->query($q_riwayat);
    $terbayar = [];
    while($r = $res_riwayat->fetch_assoc()) {
        $terbayar[] = $r['status_pembayaran'];
    }
    $row['terbayar'] = implode(',', $terbayar);
    $permintaan_data[] = $row;
}

$selected_id = isset($_GET['id_permintaan']) ? $_GET['id_permintaan'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Pembayaran | Bengkel Bengawan</title>
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
                <i class="fas fa-money-check-alt" style="color: #d32f2f"></i> Input Pembayaran
            </h2>

            <?php if(isset($_SESSION['pesan_error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['pesan_error']; unset($_SESSION['pesan_error']); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label>Permintaan Servis</label>
                    <select name="id_permintaan" id="id_permintaan" class="form-control" required>
                        <option value="">-- Pilih Permintaan Servis (Assigned/Pending) --</option>
                        <?php foreach($permintaan_data as $ps): ?>
                            <option value="<?= $ps['id_permintaan'] ?>" data-tagihan="<?= $ps['total_tagihan'] ?>" data-terbayar='<?= $ps['terbayar'] ?>' <?= ($selected_id == $ps['id_permintaan']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ps['nama_pelanggan']) ?> - <?= htmlspecialchars($ps['plat_nomor']) ?> (Tagihan: Rp <?= number_format($ps['total_tagihan'],0,',','.') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Total Tagihan Keseluruhan</label>
                    <input type="text" id="total_tagihan_display" class="form-control" readonly placeholder="Pilih Permintaan Servis dulu">
                </div>

                <div class="form-group">
                    <label>Skema Pembayaran (Otomatis Dihitung)</label>
                    <select id="skema_termin" class="form-control" onchange="updateTagihan()">
                        <option value="1">Bayar Penuh Sekaligus (1x Bayar)</option>
                        <option value="2">Cicilan 2x Termin (Dibagi 2)</option>
                        <option value="3">Cicilan 3x Termin (Dibagi 3)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Jumlah Bayar</label>
                    <input type="number" name="jumlah_bayar" id="jumlah_bayar" class="form-control" required placeholder="Masukkan jumlah bayar">
                </div>

                <div class="form-group">
                    <label>Metode Pembayaran</label>
                    <select name="metode_pembayaran" class="form-control" required>
                        <option value="cash">Cash</option>
                        <option value="transfer">Transfer</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Status Pembayaran (Pilih Termin)</label>
                    <select name="status_pembayaran" class="form-control" required>
                        <option value="">Pilih Permintaan Servis dan Skema Termin</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Tanggal Bayar</label>
                    <input type="date" name="tanggal_bayar" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>

                <button type="submit" class="btn btn-submit">Simpan Pembayaran</button>
            </form>
        </div>
    </div>

    <script>
        const selectPermintaan = document.getElementById('id_permintaan');
        const displayTagihan = document.getElementById('total_tagihan_display');
        const inputJumlahBayar = document.getElementById('jumlah_bayar');
        const statusSelect = document.querySelector('select[name="status_pembayaran"]');

        function updateTagihan() {
            const selectedOption = selectPermintaan.options[selectPermintaan.selectedIndex];
            if (selectedOption && selectedOption.value !== "") {
                const tagihan = parseInt(selectedOption.getAttribute('data-tagihan'));
                const terbayarStr = selectedOption.getAttribute('data-terbayar');
                const terbayar = terbayarStr ? terbayarStr.split(',') : [];
                const skema = parseInt(document.getElementById('skema_termin').value);
                
                displayTagihan.value = "Rp " + new Intl.NumberFormat('id-ID').format(tagihan);
                
                // Kalkulasi nominal termin menggunakan floor
                const nominal_per_termin = Math.floor(tagihan / skema);
                let tagihan_termin = {};
                for(let i=1; i<=skema; i++){
                    if(i === skema) {
                        tagihan_termin['lunas termin'+i] = tagihan - (nominal_per_termin * (skema - 1));
                    } else {
                        tagihan_termin['lunas termin'+i] = nominal_per_termin;
                    }
                }
                
                statusSelect.innerHTML = "";
                
                for(let i=1; i<=skema; i++){
                    let status_val = 'lunas termin' + i;
                    let option = document.createElement("option");
                    option.value = status_val;
                    
                    if(terbayar.includes(status_val)) {
                        option.text = `Termin ${i} - Rp ${new Intl.NumberFormat('id-ID').format(tagihan_termin[status_val])} (SUDAH LUNAS)`;
                        option.disabled = true;
                    } else {
                        option.text = `Termin ${i} - Rp ${new Intl.NumberFormat('id-ID').format(tagihan_termin[status_val])} (Belum Bayar)`;
                    }
                    statusSelect.add(option);
                }
                
                // Pilih otomatis termin yang belum dibayar
                let foundUnpaid = false;
                for(let i=0; i<statusSelect.options.length; i++) {
                    if(!statusSelect.options[i].disabled) {
                        statusSelect.selectedIndex = i;
                        inputJumlahBayar.value = tagihan_termin[statusSelect.options[i].value];
                        foundUnpaid = true;
                        break;
                    }
                }
                
                if(!foundUnpaid) {
                    inputJumlahBayar.value = 0;
                }
                
            } else {
                displayTagihan.value = "";
                inputJumlahBayar.value = "";
                statusSelect.innerHTML = '<option value="">Pilih Permintaan Servis Dulu</option>';
            }
        }

        selectPermintaan.addEventListener('change', updateTagihan);
        
        statusSelect.addEventListener('change', function() {
            const selectedOption = selectPermintaan.options[selectPermintaan.selectedIndex];
            if(selectedOption && selectedOption.value !== "") {
                const tagihan = parseInt(selectedOption.getAttribute('data-tagihan'));
                const skema = parseInt(document.getElementById('skema_termin').value);
                const nominal_per_termin = Math.floor(tagihan / skema);
                
                const termin_num = parseInt(this.value.replace('lunas termin', ''));
                if(termin_num === skema) {
                    inputJumlahBayar.value = tagihan - (nominal_per_termin * (skema - 1));
                } else {
                    inputJumlahBayar.value = nominal_per_termin;
                }
            }
        });
        
        // run on load in case a value is pre-selected
        if (selectPermintaan.value !== "") {
            updateTagihan();
        }
    </script>
</body>
</html>
<?php include('../../includes/footer.php'); ?>
