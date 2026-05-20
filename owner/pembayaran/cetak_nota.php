<?php
session_start();
if (!isset($_SESSION['id_pengguna']) || !in_array($_SESSION['role'], ['owner', 'service_advisor'])) {
    header("Location: ../../auth/login.php");
    exit();
}
include '../../config/config.php';

if (!isset($_GET['id_bayar'])) {
    echo "ID Pembayaran tidak ditemukan.";
    exit();
}

$id_bayar = $_GET['id_bayar'];

// Fetch Pembayaran, Permintaan, Pelanggan, Mobil
$query = "SELECT pb.*, ps.keluhan, ps.tanggal_masuk, ps.tanggal_keluar, 
          m.merk_mobil, m.tipe_mobil, m.plat_nomor, 
          p.nama_pelanggan, p.no_telp, p.alamat 
          FROM pembayaran pb
          JOIN permintaan_servis ps ON pb.id_permintaan = ps.id_permintaan
          JOIN mobil m ON ps.id_mobil = m.id_mobil
          JOIN pelanggan p ON m.id_pelanggan = p.id_pelanggan
          WHERE pb.id_bayar = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_bayar);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    echo "Data pembayaran tidak ditemukan.";
    exit();
}

$id_permintaan = $data['id_permintaan'];

// Fetch Detail Servis
$q_servis = "SELECT ds.*, j.nama_jasa 
             FROM detail_servis ds 
             JOIN jasa j ON ds.id_jasa = j.id_jasa 
             WHERE ds.id_permintaan = $id_permintaan";
$res_servis = $conn->query($q_servis);

// Fetch Detail Sparepart
$q_sparepart = "SELECT dsp.*, sp.nama_sparepart 
                FROM detail_sparepart dsp 
                JOIN sparepart sp ON dsp.id_sparepart = sp.id_sparepart 
                WHERE dsp.id_permintaan = $id_permintaan";
$res_sparepart = $conn->query($q_sparepart);

$total_jasa = 0;
$total_sparepart = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Pembayaran #<?php echo $id_bayar; ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/cetak_nota.css">
</head>
<body>
    <div class="nota-container">
        <div class="header">
            <h1>BENGKEL BENGAWAN</h1>
            <p>Jl. Contoh Alamat No. 123, Kota Bengawan</p>
            <p>Telp: (021) 1234567 | Email: info@bengkelbengawan.com</p>
        </div>

        <div class="info-section">
            <div class="info-box">
                <table>
                    <tr><td>No. Nota</td><td>: #<?php echo str_pad($data['id_bayar'], 5, '0', STR_PAD_LEFT); ?></td></tr>
                    <tr><td>Tanggal Nota</td><td>: <?php echo date('d-m-Y', strtotime($data['tanggal_bayar'])); ?></td></tr>
                    <tr><td>Kasir</td><td>: <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></td></tr>
                </table>
            </div>
            <div class="info-box">
                <table>
                    <tr><td>Pelanggan</td><td>: <?php echo htmlspecialchars($data['nama_pelanggan']); ?></td></tr>
                    <tr><td>Kendaraan</td><td>: <?php echo htmlspecialchars($data['merk_mobil'] . ' ' . $data['tipe_mobil']); ?> (<?php echo htmlspecialchars($data['plat_nomor']); ?>)</td></tr>
                    <tr><td>No. HP</td><td>: <?php echo htmlspecialchars($data['no_telp']); ?></td></tr>
                </table>
            </div>
        </div>

        <table class="details-table">
            <thead>
                <tr>
                    <th>Deskripsi</th>
                    <th class="right">Qty</th>
                    <th class="right">Harga Satuan</th>
                    <th class="right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="4" style="background-color: #f0f0f0; font-weight: bold;">JASA SERVIS</td>
                </tr>
                <?php while($s = $res_servis->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($s['nama_jasa']); ?></td>
                        <td class="right"><?php echo $s['qty']; ?></td>
                        <td class="right">Rp <?php echo number_format($s['total_biaya_jasa'], 0, ',', '.'); ?></td>
                        <td class="right">Rp <?php echo number_format($s['qty'] * $s['total_biaya_jasa'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php $total_jasa += ($s['qty'] * $s['total_biaya_jasa']); ?>
                <?php endwhile; ?>
                
                <?php if($res_sparepart->num_rows > 0): ?>
                    <tr>
                        <td colspan="4" style="background-color: #f0f0f0; font-weight: bold;">SPAREPART</td>
                    </tr>
                    <?php while($sp = $res_sparepart->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($sp['nama_sparepart']); ?></td>
                            <td class="right"><?php echo $sp['qty']; ?></td>
                            <td class="right">Rp <?php echo number_format($sp['harga_satuan'], 0, ',', '.'); ?></td>
                            <td class="right">Rp <?php echo number_format($sp['qty'] * $sp['harga_satuan'], 0, ',', '.'); ?></td>
                        </tr>
                        <?php $total_sparepart += ($sp['qty'] * $sp['harga_satuan']); ?>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-box">
                <table>
                    <tr>
                        <td>Total Jasa</td>
                        <td class="right">Rp <?php echo number_format($total_jasa, 0, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <td>Total Sparepart</td>
                        <td class="right">Rp <?php echo number_format($total_sparepart, 0, ',', '.'); ?></td>
                    </tr>
                    <tr class="grand-total">
                        <td>Total Tagihan</td>
                        <td class="right">Rp <?php echo number_format($total_jasa + $total_sparepart, 0, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Jumlah Bayar</strong> (<?php echo ucfirst($data['metode_pembayaran']); ?>)</td>
                        <td class="right"><strong>Rp <?php echo number_format($data['jumlah_bayar'], 0, ',', '.'); ?></strong></td>
                    </tr>
                    <tr>
                        <td>Status Pembayaran</td>
                        <td class="right">
                            <span style="display:inline-block; padding:3px 8px; border-radius:3px; background:#28a745; color:white; font-size:12px;">
                                <?php echo strtoupper($data['status_pembayaran']); ?>
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="footer">
            <p>Terima kasih atas kunjungan Anda!</p>
            <p>Barang yang sudah dibeli tidak dapat ditukar atau dikembalikan.</p>
        </div>
    </div>
    
    <a href="#" class="btn-print" onclick="window.print(); return false;"><i class="fas fa-print"></i> Cetak</a>
</body>
</html>
