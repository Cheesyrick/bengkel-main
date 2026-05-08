<?php
// Tentukan base URL proyek untuk link yang absolut
$base_url = '/bengkel-main';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
?>

<link href="<?php echo $base_url; ?>/assets/css/footer.css" rel="stylesheet">
<footer>
    <div class="container-fluid px-4">
        <div class="mid">
            <div class="text-muted">Copyright &copy; Broom Garage</div>
        </div>
    </div>
</footer>