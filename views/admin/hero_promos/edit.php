<?php 
// Redirigimos la configuración a la vista principal unificada
$promos = isset($promo) ? [$promo] : [];
include 'index.php';