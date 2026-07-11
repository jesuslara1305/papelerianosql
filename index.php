<?php
/* =====================================================================
 *  index.php  -  CONTROLADOR FRONTAL (Front Controller)
 * =====================================================================
 *  Punto único de entrada del sistema. Igual que en el proyecto de
 *  referencia ProyectoWeb:
 *    1. Inicia sesión y carga configuración.
 *    2. Incluye Modelos, Controladores y Helpers.
 *    3. Procesa las acciones POST (login, carrito, checkout, admin...).
 *    4. Delega el renderizado de la vista al router (control/router.php).
 *
 *  El enrutamiento "amigable" lo hace el .htaccess, que reescribe
 *  cualquier URL a  index.php?url=<ruta>.
 * ===================================================================== */

ob_start();
session_start();

$config = require __DIR__ . '/config/config.php';
date_default_timezone_set($config['timezone']);

/* ---------- Carga de la capa de datos (Modelos) ---------- */
require 'modelo/Conexion.php';
require 'modelo/Producto.php';
require 'modelo/Usuario.php';
require 'modelo/Pedido.php';

/* ---------- Carga de Controladores ---------- */
require 'control/ProductoControlador.php';
require 'control/AuthControlador.php';
require 'control/PedidoControlador.php';
require 'control/ReporteControlador.php';
require 'control/CarritoControlador.php';

/* ---------- Helpers ---------- */
require 'helpers/Helpers.php';

/* =====================================================================
 *  PROCESAMIENTO DE ACCIONES (POST / GET con efectos)
 *  Aquí es donde "se ingresan datos desde PHP a MongoDB".
 * ===================================================================== */
$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

switch ($accion) {

    /* ---- Autenticación ---- */
    case 'login':
        $auth = new AuthControlador();
        [$tipo, $msg, $destino] = $auth->login($_POST);
        flash($tipo, $msg);
        redirigir($tipo === 'exito' ? $destino : 'login');
        break;

    case 'registro':
        $auth = new AuthControlador();
        [$tipo, $msg] = $auth->registrar($_POST);
        flash($tipo, $msg);
        redirigir('login');
        break;

    case 'logout':
        (new AuthControlador())->logout();
        redirigir('login');
        break;

    /* ---- Carrito (solo clientes; el admin no tiene carrito) ---- */
    case 'agregar_carrito':
        if (!AuthControlador::autenticado()) { redirigir('login'); }
        if (AuthControlador::esAdmin()) { redirigir('admin'); }
        $car = new CarritoControlador();
        [$tipo, $msg] = $car->agregar((int) $_POST['no_producto'], (int) ($_POST['cantidad'] ?? 1));
        flash($tipo, $msg);
        redirigir($_POST['volver'] ?? 'productos');
        break;

    case 'actualizar_carrito':
        if (AuthControlador::esAdmin()) { redirigir('admin'); }
        (new CarritoControlador())->actualizar((int) $_POST['no_producto'], (int) $_POST['cantidad']);
        redirigir('carrito');
        break;

    case 'quitar_carrito':
        if (AuthControlador::esAdmin()) { redirigir('admin'); }
        (new CarritoControlador())->quitar((int) $_POST['no_producto']);
        redirigir('carrito');
        break;

    /* ---- Checkout: crear pedido ---- */
    case 'crear_pedido':
        if (!AuthControlador::autenticado()) { redirigir('login'); }
        if (AuthControlador::esAdmin()) { redirigir('admin'); }
        $car = new CarritoControlador();
        $ped = new PedidoControlador();
        [$tipo, $msg, $no] = $ped->crearPedido($_POST, $car->items());
        if ($tipo === 'exito') {
            $car->vaciar();
            flash('exito', $msg);
            redirigir('gracias');
        } else {
            flash('error', $msg);
            redirigir('pago');
        }
        break;

    /* ---- Administración: productos ---- */
    case 'admin_agregar_producto':
        if (!AuthControlador::esAdmin()) { redirigir('login'); }
        [$tipo, $msg] = (new ProductoControlador())->agregarProducto($_POST, $_FILES);
        flash($tipo, $msg);
        redirigir('admin');
        break;

    case 'admin_editar_producto':
        if (!AuthControlador::esAdmin()) { redirigir('login'); }
        [$tipo, $msg] = (new ProductoControlador())->editarProducto($_POST, $_FILES);
        flash($tipo, $msg);
        redirigir('admin');
        break;

    case 'admin_eliminar_producto':
        if (!AuthControlador::esAdmin()) { redirigir('login'); }
        [$tipo, $msg] = (new ProductoControlador())->eliminarProducto((int) $_POST['no_producto']);
        flash($tipo, $msg);
        redirigir('admin');
        break;

    /* ---- Administración: reabastecer inventario ---- */
    case 'admin_reabastecer_producto':
        if (!AuthControlador::esAdmin()) { redirigir('login'); }
        [$tipo, $msg] = (new ProductoControlador())->reabastecer((int) $_POST['no_producto'], (int) $_POST['cantidad']);
        flash($tipo, $msg);
        redirigir('admin/reabastecer');
        break;

    /* ---- Administración: cambiar estado de pedido ---- */
    case 'admin_cambiar_estado':
        if (!AuthControlador::esAdmin()) { redirigir('login'); }
        [$tipo, $msg] = (new PedidoControlador())->cambiarEstado((int) $_POST['no_pedido'], $_POST['estado']);
        flash($tipo, $msg);
        redirigir('admin/pedidos');
        break;
}

/* =====================================================================
 *  RENDERIZADO: el router decide qué vista mostrar.
 * ===================================================================== */
require 'control/router.php';
