<?php
/* =====================================================================
 *  vista/header_gral.php  -  Cabecera de la tienda pública
 *  (topbar + navbar). Se incluye al inicio de cada vista pública.
 * ===================================================================== */
$carrito = new CarritoControlador();
$totalCarrito = $carrito->totalItems();
$mensaje = obtenerFlash();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sanrio Shop | Productos Kawaii Oficiales</title>
    <link rel="icon" href="<?php echo base('public/img/logo.png'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo base('estilos/styles.css'); ?>">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<!-- Topbar -->
<div class="topbar">
    <div class="container d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex gap-3">
            <a href="tel:+123456789"><i class="fas fa-phone me-1"></i>+123 456 789</a>
            <a href="mailto:info@sanrioshop.com"><i class="fas fa-envelope me-1"></i>info@sanrioshop.com</a>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span>Lun - Vie: 9:00 - 19:00 | Sáb: 9:00 - 14:00</span>
            <?php if (AuthControlador::autenticado()): ?>
                <?php $destinoSaludo = AuthControlador::esAdmin() ? base('admin') : base('perfil'); ?>
                <a href="<?php echo $destinoSaludo; ?>" class="d-none d-md-inline text-decoration-none text-white">
                    <i class="fas fa-user-circle me-1"></i>Hola, <?php echo e($_SESSION['nombre']); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Navbar -->
<nav class="main-nav">
    <div class="container d-flex align-items-center justify-content-between">
        <a href="<?php echo base('inicio'); ?>" class="d-flex align-items-center gap-2 text-decoration-none">
            <img src="<?php echo base('public/img/logo.png'); ?>" alt="Sanrio Shop" class="brand-logo">
            <div>
                <h1 class="brand-title grad-text">Sanrio Shop</h1>
                <p class="brand-sub">Productos Kawaii Oficiales</p>
            </div>
        </a>

        <div class="d-none d-md-flex gap-4 align-items-center">
            <a href="<?php echo base('inicio'); ?>" class="nav-link-sanrio">Inicio</a>
            <a href="<?php echo base('productos'); ?>" class="nav-link-sanrio">Productos</a>
            <a href="<?php echo base('inicio'); ?>#sobre" class="nav-link-sanrio">Sobre Nosotros</a>
            <a href="<?php echo base('inicio'); ?>#contacto" class="nav-link-sanrio">Contacto</a>
        </div>

        <div class="d-flex align-items-center gap-3">
            <a href="<?php echo base('carrito'); ?>" class="position-relative text-decoration-none text-dark fs-5">
                <i class="fas fa-shopping-cart"></i>
                <?php if ($totalCarrito > 0): ?>
                    <span class="cart-badge"><?php echo $totalCarrito; ?></span>
                <?php endif; ?>
            </a>
            <?php if (AuthControlador::autenticado()): ?>
                <form method="post" action="<?php echo base(''); ?>" class="m-0">
                    <input type="hidden" name="accion" value="logout">
                    <button class="btn btn-sm btn-outline-sanrio"><i class="fas fa-sign-out-alt"></i></button>
                </form>
            <?php else: ?>
                <a href="<?php echo base('login'); ?>" class="btn btn-sm btn-sanrio">
                    <i class="fas fa-user me-1"></i>Ingresar
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<?php if ($mensaje): ?>
    <div class="container mt-3">
        <div class="alert alert-<?php echo $mensaje['tipo'] === 'exito' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
            <?php echo e($mensaje['mensaje']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
<?php endif; ?>
