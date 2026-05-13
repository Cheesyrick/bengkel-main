<?php
session_start();
if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'owner') {
    header("Location: ../../auth/login.php");
    exit();
}

include '../../config/config.php';

$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Total Permintaan Servis
$query_total_servis = "SELECT COUNT(*) as total FROM permintaan_servis WHERE MONTH(tanggal_masuk) = '$bulan' AND YEAR(tanggal_masuk) = '$tahun'";
$result_total_servis = mysqli_query($conn, $query_total_servis);
$total_servis = mysqli_fetch_assoc($result_total_servis)['total'];

// Top Jasa
$query_top_jasa = "SELECT j.nama_jasa, SUM(ds.qty) as total_qty 
                   FROM detail_servis ds 
                   JOIN jasa j ON ds.id_jasa = j.id_jasa 
                   JOIN permintaan_servis ps ON ds.id_permintaan = ps.id_permintaan 
                   WHERE MONTH(ps.tanggal_masuk) = '$bulan' AND YEAR(ps.tanggal_masuk) = '$tahun' 
                   GROUP BY j.id_jasa 
                   ORDER BY total_qty DESC LIMIT 5";
$result_top_jasa = mysqli_query($conn, $query_top_jasa);

// Top Sparepart
$query_top_sparepart = "SELECT sp.nama_sparepart, SUM(ds.qty) as total_qty 
                        FROM detail_sparepart ds 
                        JOIN sparepart sp ON ds.id_sparepart = sp.id_sparepart 
                        JOIN permintaan_servis ps ON ds.id_permintaan = ps.id_permintaan 
                        WHERE MONTH(ps.tanggal_masuk) = '$bulan' AND YEAR(ps.tanggal_masuk) = '$tahun' 
                        GROUP BY sp.id_sparepart 
                        ORDER BY total_qty DESC LIMIT 5";
$result_top_sparepart = mysqli_query($conn, $query_top_sparepart);

// Pendapatan Jasa
$query_pendapatan_jasa = "SELECT SUM(ds.total_biaya_jasa) as pendapatan_jasa 
                          FROM detail_servis ds 
                          JOIN permintaan_servis ps ON ds.id_permintaan = ps.id_permintaan 
                          WHERE MONTH(ps.tanggal_masuk) = '$bulan' AND YEAR(ps.tanggal_masuk) = '$tahun'";
$result_pendapatan_jasa = mysqli_query($conn, $query_pendapatan_jasa);
$pendapatan_jasa = mysqli_fetch_assoc($result_pendapatan_jasa)['pendapatan_jasa'] ?? 0;

// Pendapatan Sparepart
$query_pendapatan_sparepart = "SELECT SUM(ds.qty * ds.harga_satuan) as pendapatan_sparepart 
                               FROM detail_sparepart ds 
                               JOIN permintaan_servis ps ON ds.id_permintaan = ps.id_permintaan 
                               WHERE MONTH(ps.tanggal_masuk) = '$bulan' AND YEAR(ps.tanggal_masuk) = '$tahun'";
$result_pendapatan_sparepart = mysqli_query($conn, $query_pendapatan_sparepart);
$pendapatan_sparepart = mysqli_fetch_assoc($result_pendapatan_sparepart)['pendapatan_sparepart'] ?? 0;

$total_pendapatan = $pendapatan_jasa + $pendapatan_sparepart;

// Detail Permintaan
$query_detail = "SELECT ps.id_permintaan, m.plat_nomor, p.nama_pelanggan, ps.tanggal_masuk, ps.keluhan 
                 FROM permintaan_servis ps
                 JOIN mobil m ON ps.id_mobil = m.id_mobil
                 JOIN pelanggan p ON m.id_pelanggan = p.id_pelanggan
                 WHERE MONTH(ps.tanggal_masuk) = '$bulan' AND YEAR(ps.tanggal_masuk) = '$tahun'
                 ORDER BY ps.tanggal_masuk DESC";
$result_detail = mysqli_query($conn, $query_detail);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Bulanan | Bengkel Bengawan</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/list.css">
    <style>
        .report-summary {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }
        .summary-card {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            flex: 1;
            border-left: 4px solid #d32f2f;
        }
        .summary-card h3 {
            margin-top: 0;
            color: #555;
            font-size: 14px;
        }
        .summary-card p {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0 0;
            color: #d32f2f;
        }
        .filter-form {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .filter-form form {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        .filter-form select, .filter-form input[type="submit"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .filter-form input[type="submit"] {
            background-color: #d32f2f;
            color: white;
            border: none;
            cursor: pointer;
            padding: 10px 20px;
        }
        .filter-form input[type="submit"]:hover {
            background-color: #b71c1c;
        }
        .tables-container {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }
        .tables-container > div {
            flex: 1;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .tables-container h3 {
            margin-top: 0;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 15px;
            font-size: 16px;
            color: #333;
        }
        /* Make sure it's responsive */
        @media (max-width: 900px) {
            .report-summary {
                flex-wrap: wrap;
            }
            .summary-card {
                min-width: 45%;
            }
            .tables-container {
                flex-direction: column;
            }
        }
        @media (max-width: 600px) {
            .summary-card {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include '../../includes/sidebar.php'; ?>
    
    <div class="content">
        <h2 style="margin-top: 0; color: #333; text-align: left; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">
            <i class="fas fa-file-alt" style="color: #d32f2f;"></i> Laporan Bulanan
        </h2>

        <div class="filter-form">
            <form method="GET" action="">
                <label for="bulan" style="font-weight:bold;">Bulan:</label>
                <select name="bulan" id="bulan">
                    <?php
                    $nama_bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                    for ($i = 1; $i <= 12; $i++) {
                        $val = str_pad($i, 2, "0", STR_PAD_LEFT);
                        $selected = ($val == $bulan) ? "selected" : "";
                        echo "<option value='$val' $selected>{$nama_bulan[$i-1]}</option>";
                    }
                    ?>
                </select>

                <label for="tahun" style="font-weight:bold;">Tahun:</label>
                <select name="tahun" id="tahun">
                    <?php
                    $tahun_sekarang = date('Y');
                    for ($i = $tahun_sekarang - 5; $i <= $tahun_sekarang + 1; $i++) {
                        $selected = ($i == $tahun) ? "selected" : "";
                        echo "<option value='$i' $selected>$i</option>";
                    }
                    ?>
                </select>

                <input type="submit" value="Tampilkan Laporan">
            </form>
        </div>

        <div class="report-summary">
            <div class="summary-card">
                <h3><i class="fas fa-clipboard-list"></i> Total Permintaan</h3>
                <p><?php echo $total_servis; ?> <span style="font-size: 14px; font-weight: normal; color: #555;">Servis</span></p>
            </div>
            <div class="summary-card">
                <h3><i class="fas fa-wrench"></i> Pendapatan Jasa</h3>
                <p>Rp <?php echo number_format($pendapatan_jasa, 0, ',', '.'); ?></p>
            </div>
            <div class="summary-card">
                <h3><i class="fas fa-cogs"></i> Pendapatan Sparepart</h3>
                <p>Rp <?php echo number_format($pendapatan_sparepart, 0, ',', '.'); ?></p>
            </div>
            <div class="summary-card" style="background-color: #d32f2f; color: white;">
                <h3 style="color: white;"><i class="fas fa-money-bill-wave"></i> Total Pendapatan</h3>
                <p style="color: white;">Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></p>
            </div>
        </div>

        <div class="tables-container">
            <div>
                <h3><i class="fas fa-chart-line"></i> Top 5 Jasa Terpopuler</h3>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Jasa</th>
                            <th>Total Digunakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        if(mysqli_num_rows($result_top_jasa) > 0) {
                            while($row = mysqli_fetch_assoc($result_top_jasa)): 
                        ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($row['nama_jasa']); ?></td>
                                <td><span class="role-badge" style="background:#28a745; margin:0; padding:2px 8px;"><?php echo htmlspecialchars($row['total_qty']); ?>x</span></td>
                            </tr>
                        <?php 
                            endwhile;
                        } else {
                            echo "<tr><td colspan='3' style='text-align:center; padding:15px;'>Tidak ada data jasa bulan ini</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div>
                <h3><i class="fas fa-box"></i> Top 5 Sparepart Terpopuler</h3>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Sparepart</th>
                            <th>Total Digunakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        if(mysqli_num_rows($result_top_sparepart) > 0) {
                            while($row = mysqli_fetch_assoc($result_top_sparepart)): 
                        ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($row['nama_sparepart']); ?></td>
                                <td><span class="role-badge" style="background:#17a2b8; margin:0; padding:2px 8px;"><?php echo htmlspecialchars($row['total_qty']); ?>x</span></td>
                            </tr>
                        <?php 
                            endwhile;
                        } else {
                            echo "<tr><td colspan='3' style='text-align:center; padding:15px;'>Tidak ada data sparepart bulan ini</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="table-container">
            <h3><i class="fas fa-list"></i> Rincian Permintaan Servis</h3>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Masuk</th>
                        <th>Plat Nomor</th>
                        <th>Pelanggan</th>
                        <th>Keluhan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    if(mysqli_num_rows($result_detail) > 0) {
                        while($row = mysqli_fetch_assoc($result_detail)): 
                    ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo date('d-m-Y H:i', strtotime($row['tanggal_masuk'])); ?></td>
                            <td><span style="font-family:monospace; background:#eee; padding:3px 6px; border-radius:3px;"><strong><?php echo htmlspecialchars($row['plat_nomor']); ?></strong></span></td>
                            <td><?php echo htmlspecialchars($row['nama_pelanggan']); ?></td>
                            <td><?php echo htmlspecialchars($row['keluhan']); ?></td>
                        </tr>
                    <?php 
                        endwhile;
                    } else {
                        echo "<tr><td colspan='5' style='text-align:center; padding: 20px;'>Tidak ada permintaan servis bulan ini</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php include '../../includes/footer.php'; ?>
</body>
</html>
