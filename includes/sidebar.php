<?php
// Tentukan base URL proyek untuk link yang absolut
$base_url = '/bengkel-main';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
?>

<link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/sidebar.css">

<!-- Simple Side Navbar -->
<div class="sidebar">
    <div class="sidebar-header">
        <img src="<?php echo $base_url; ?>/assets/images/logo_broom_2.png" alt="Logo" onerror="this.style.display='none'" style="max-width: 50px; margin-bottom: 10px; border-radius: 50px">
        <h3>Broom Garage</h3>
    </div>
    <ul class="nav-links">
        <?php if ($role == 'owner'): ?>
            <li class="nav-section">OWNER</li>
            <li><a href="<?php echo $base_url; ?>/auth/dashboardadmin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            
            <li class="nav-item">
                <input type="checkbox" id="akunMenuToggle" class="dropdown-cb">
                <label for="akunMenuToggle" class="dropdown-toggle">
                    <div style="display:flex; align-items:center;"><i class="fas fa-users"></i> Akun Pengguna</div>
                    <i class="fas fa-chevron-down float-right icon-down"></i>
                    <i class="fas fa-chevron-up float-right icon-up"></i>
                </label>
                <ul class="dropdown-menu">
                    <li><a href="<?php echo $base_url; ?>/owner/akun_pengguna/list.php">Lihat Akun</a></li>
                    <li><a href="<?php echo $base_url; ?>/owner/akun_pengguna/add.php">Tambah Akun</a></li>
                    <li><a href="<?php echo $base_url; ?>/owner/akun_pengguna/edit.php">Edit Akun</a></li>
                    <li><a href="<?php echo $base_url; ?>/owner/akun_pengguna/delete.php">Hapus Akun</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <input type="checkbox" id="pelangganMenuToggle" class="dropdown-cb">
                <label for="pelangganMenuToggle" class="dropdown-toggle">
                    <div style="display:flex; align-items:center;"><i class="fas fa-car"></i> Pelanggan & Mobil</div>
                    <i class="fas fa-chevron-down float-right icon-down"></i>
                    <i class="fas fa-chevron-up float-right icon-up"></i>
                </label>
                <ul class="dropdown-menu">
                    <li><a href="<?php echo $base_url; ?>/owner/pelanggan_mobil/list.php">Lihat Pelanggan & Mobil</a></li>
                    <li><a href="<?php echo $base_url; ?>/owner/pelanggan_mobil/add.php">Input Pelanggan & Mobil</a></li>
                    <li><a href="<?php echo $base_url; ?>/owner/pelanggan_mobil/edit.php">Edit Pelanggan & Mobil</a></li>
                    <li><a href="<?php echo $base_url; ?>/owner/pelanggan_mobil/delete.php">Hapus Pelanggan & Mobil</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <input type="checkbox" id="jasaMenuToggle" class="dropdown-cb">
                <label for="jasaMenuToggle" class="dropdown-toggle">
                    <div style="display:flex; align-items:center;"><i class="fas fa-wrench"></i> Data Jasa</div>
                    <i class="fas fa-chevron-down float-right icon-down"></i>
                    <i class="fas fa-chevron-up float-right icon-up"></i>
                </label>
                <ul class="dropdown-menu">
                    <li><a href="<?php echo $base_url; ?>/owner/jasa/list.php">Lihat Jasa</a></li>
                    <li><a href="<?php echo $base_url; ?>/owner/jasa/add.php">Input Jasa</a></li>
                    <li><a href="<?php echo $base_url; ?>/owner/jasa/edit.php">Edit Jasa</a></li>
                    <li><a href="<?php echo $base_url; ?>/owner/jasa/delete.php">Delete Jasa</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <input type="checkbox" id="sparepartMenuToggle" class="dropdown-cb">
                <label for="sparepartMenuToggle" class="dropdown-toggle">
                    <div style="display:flex; align-items:center;"><i class="fas fa-cogs"></i> Data Sparepart</div>
                    <i class="fas fa-chevron-down float-right icon-down"></i>
                    <i class="fas fa-chevron-up float-right icon-up"></i>
                </label>
                <ul class="dropdown-menu">
                    <li><a href="<?php echo $base_url; ?>/owner/sparepart/list.php">Lihat Sparepart</a></li>
                    <li><a href="<?php echo $base_url; ?>/owner/sparepart/add.php">Input Sparepart</a></li>
                    <li><a href="<?php echo $base_url; ?>/owner/sparepart/edit.php">Edit Sparepart</a></li>
                    <li><a href="<?php echo $base_url; ?>/owner/sparepart/delete.php">Delete Sparepart</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <input type="checkbox" id="permintaanMenuToggle" class="dropdown-cb">
                <label for="permintaanMenuToggle" class="dropdown-toggle">
                    <div style="display:flex; align-items:center;"><i class="fas fa-clipboard-list"></i> Permintaan Servis</div>
                    <i class="fas fa-chevron-down float-right icon-down"></i>
                    <i class="fas fa-chevron-up float-right icon-up"></i>
                </label>
                <ul class="dropdown-menu">
                    <li><a href="<?php echo $base_url; ?>/owner/permintaan_servis/list.php">Lihat Permintaan Servis</a></li>
                    <li><a href="<?php echo $base_url; ?>/owner/permintaan_servis/add_permintaan.php">Input Permintaan Servis</a></li>
                    <li><a href="<?php echo $base_url; ?>/owner/permintaan_servis/edit_permintaan.php">Edit Permintaan Servis</a></li>
                    <li><a href="<?php echo $base_url; ?>/owner/permintaan_servis/delete.php">Hapus Permintaan Servis</a></li>
                </ul>
            </li>

            <li><a href="<?php echo $base_url; ?>/owner/laporan/laporan_bulanan.php"><i class="fas fa-file-alt"></i> Laporan Bulanan</a></li>
        
        <?php elseif ($role == 'service_advisor'): ?>
            <li class="nav-section">SERVICE ADVISOR</li>
            <li><a href="<?php echo $base_url; ?>/auth/dashboardadmin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            
            <li class="nav-item">
                <input type="checkbox" id="pelangganMenuToggleSA" class="dropdown-cb">
                <label for="pelangganMenuToggleSA" class="dropdown-toggle">
                    <div style="display:flex; align-items:center;"><i class="fas fa-car"></i> Pelanggan & Mobil</div>
                    <i class="fas fa-chevron-down float-right icon-down"></i>
                    <i class="fas fa-chevron-up float-right icon-up"></i>
                </label>
                <ul class="dropdown-menu">
                    <li><a href="<?php echo $base_url; ?>/service_advisor/pelanggan_mobil/list.php">Lihat Pelanggan & Mobil</a></li>
                    <li><a href="<?php echo $base_url; ?>/service_advisor/pelanggan_mobil/add.php">Input Pelanggan & Mobil</a></li>
                    <li><a href="<?php echo $base_url; ?>/service_advisor/pelanggan_mobil/edit.php">Edit Pelanggan & Mobil</a></li>
                    <li><a href="<?php echo $base_url; ?>/service_advisor/pelanggan_mobil/delete.php">Hapus Pelanggan & Mobil</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <input type="checkbox" id="permintaanMenuToggleSA" class="dropdown-cb">
                <label for="permintaanMenuToggleSA" class="dropdown-toggle">
                    <div style="display:flex; align-items:center;"><i class="fas fa-clipboard-list"></i> Permintaan Servis</div>
                    <i class="fas fa-chevron-down float-right icon-down"></i>
                    <i class="fas fa-chevron-up float-right icon-up"></i>
                </label>
                <ul class="dropdown-menu">
                    <li><a href="<?php echo $base_url; ?>/service_advisor/permintaan_servis/list.php">Lihat Permintaan Servis</a></li>
                    <li><a href="<?php echo $base_url; ?>/service_advisor/permintaan_servis/add.php">Input Permintaan Servis</a></li>
                    <li><a href="<?php echo $base_url; ?>/service_advisor/permintaan_servis/edit_permintaan.php">Edit Permintaan Servis</a></li>
                    <li><a href="<?php echo $base_url; ?>/service_advisor/permintaan_servis/delete.php">Hapus Permintaan Servis</a></li>
                </ul>
            </li>

            <li><a href="<?php echo $base_url; ?>/service_advisor/laporan/laporan_bulanan.php"><i class="fas fa-file-alt"></i> Laporan Bulanan</a></li>
            <li><a href="<?php echo $base_url; ?>/service_advisor/cetak_nota.php"><i class="fas fa-print"></i> Cetak Nota</a></li>
            
        <?php elseif ($role == 'mechanic'): ?>
            <li class="nav-section">MECHANIC</li>
            <li><a href="<?php echo $base_url; ?>/auth/dashboardadmin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            
            <li class="nav-item">
                <input type="checkbox" id="permintaanMenuToggleMech" class="dropdown-cb">
                <label for="permintaanMenuToggleMech" class="dropdown-toggle">
                    <div style="display:flex; align-items:center;"><i class="fas fa-clipboard-list"></i> Permintaan Servis</div>
                    <i class="fas fa-chevron-down float-right icon-down"></i>
                    <i class="fas fa-chevron-up float-right icon-up"></i>
                </label>
                <ul class="dropdown-menu">
                    <li><a href="<?php echo $base_url; ?>/mechanic/permintaan_servis/list.php">Lihat Permintaan Servis</a></li>
                    <li><a href="<?php echo $base_url; ?>/mechanic/permintaan_servis/update_status.php">Update Status</a></li>
                </ul>
            </li>
        <?php endif; ?>
        
        <li class="nav-section">AKUN</li>
        <li><a href="<?php echo $base_url; ?>/auth/logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>
