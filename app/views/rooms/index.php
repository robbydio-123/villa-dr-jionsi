<div class="container py-5">
  <h1>Selamat Datang di Villa D-R Jionsi</h1>
  <p class="lead">Penginapan nuansa alam pegunungan di Kota Batu.</p>
</div>
<?php
$pageTitle = 'Beranda';
ob_start();
include __DIR__ . '/../app/views/home/index.php';
$content = ob_get_clean();
include __DIR__ . '/../app/views/layouts/main.php';