<?php
// no funca
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($path == '/') {
    // Si la ruta es '/', sirve 'home.html'
    return false;
} else {
    // De lo contrario, deja que Slim maneje la solicitud
    require 'index.php';
}