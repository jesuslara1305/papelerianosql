<?php
/* =====================================================================
 *  control/router.php  -  Enrutador de vistas
 * =====================================================================
 *  Lee la ruta solicitada ($_GET['url'], generada por el .htaccess) y
 *  decide qué vista incluir. Equivale al navbar.php del proyecto de
 *  referencia. Aplica control de acceso para las rutas protegidas.
 * ===================================================================== */

$url = isset($_GET['url']) && $_GET['url'] !== '' ? $_GET['url'] : 'inicio';
$url = rtrim($url, '/');
$partes = explode('/', $url);
$ruta = mb_strtolower($partes[0]);
$sub  = mb_strtolower($partes[1] ?? '');

switch ($ruta) {

    /* ---------------- Tienda pública ---------------- */
    case 'inicio':
    case '':
        include 'vista/inicio.php';
        break;

    case 'productos':
        include 'vista/productos.php';
        break;

    case 'login':
        include 'vista/login.php';
        break;

    /* ---------------- Perfil del cliente (requiere sesión) ---------------- */
    case 'perfil':
        if (!AuthControlador::autenticado()) { redirigir('login'); }
        if (AuthControlador::esAdmin()) { redirigir('admin'); }
        include 'vista/perfil.php';
        break;

    /* ---------------- Carrito y checkout (requiere sesión) ---------------- */
    case 'carrito':
        if (!AuthControlador::autenticado()) { redirigir('login'); }
        include 'vista/carrito.php';
        break;

    case 'envio':
        if (!AuthControlador::autenticado()) { redirigir('login'); }
        include 'vista/checkout/direccion.php';
        break;

    case 'pago':
        if (!AuthControlador::autenticado()) { redirigir('login'); }
        include 'vista/checkout/pago.php';
        break;

    case 'gracias':
        include 'vista/checkout/gracias.php';
        break;

    /* ---------------- Panel administrativo (requiere admin) ---------------- */
    case 'admin':
        if (!AuthControlador::esAdmin()) { redirigir('login'); }
        switch ($sub) {
            case 'pedidos':
                include 'vista/admin/pedidos.php';
                break;
            case 'reportes':
                include 'vista/admin/reportes.php';
                break;
            default:
                include 'vista/admin/dashboard.php';
                break;
        }
        break;

    /* ---------------- 404 ---------------- */
    default:
        http_response_code(404);
        include 'vista/404.php';
        break;
}
