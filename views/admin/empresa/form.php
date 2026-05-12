<?php 
// Unificamos la gestión en una sola vista para simplificar el mantenimiento.
// Si ya existe la variable $empresa, index.php la detectará y mostrará el modo edición.
$empresas = isset($empresa) ? [$empresa] : [];
include 'index.php';