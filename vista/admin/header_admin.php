<?php
/* =====================================================================
 *  vista/admin/header_admin.php  -  Cabecera del panel administrativo
 *  $tabActiva define qué pestaña se resalta y se incluyen las tarjetas
 *  de resumen superior (Ventas, Pedidos, Productos, Stock).
 * ===================================================================== */
$mensaje = obtenerFlash();
$rep = new ReporteControlador();
$res = $rep->resumen();
$tabActiva = $tabActiva ?? 'productos';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel de Administración | Sanrio Shop</title>
    <link rel="icon" href="<?php echo base('public/img/logo.png'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo base('estilos/styles.css'); ?>">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
<body class="admin-body">

<!-- Cabecera -->
<div class="admin-header">
    <div class="container d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <img src="<?php echo base('public/img/logo.png'); ?>" class="brand-logo" alt="logo" style="width:46px;height:46px;">
            <div>
                <h5 class="grad-text fw-bold mb-0">Panel de Administración</h5>
                <small class="text-muted">Bienvenido, <?php echo e($_SESSION['nombre']); ?></small>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo base('inicio'); ?>" class="btn btn-sm btn-outline-sanrio">
                <i class="fas fa-store me-1"></i>Ver Tienda
            </a>
            <form method="post" action="<?php echo base(''); ?>" class="m-0">
                <input type="hidden" name="accion" value="logout">
                <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-right-from-bracket me-1"></i>Cerrar Sesión</button>
            </form>
        </div>
    </div>
</div>

<div class="container py-4">
    <?php if ($mensaje): ?>
        <div class="alert alert-<?php echo $mensaje['tipo'] === 'exito' ? 'success' : 'danger'; ?> alert-dismissible fade show">
            <?php echo e($mensaje['mensaje']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Tarjetas de resumen -->
    <div class="row g-3 mb-2">
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div>
                    <p class="stat-label">Ventas Totales</p>
                    <p class="stat-value"><?php echo precio($res['ventas_totales']); ?></p>
                </div>
                <span class="stat-icon"><i class="fas fa-dollar-sign"></i></span>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div>
                    <p class="stat-label">Pedidos</p>
                    <p class="stat-value"><?php echo $res['pedidos']; ?></p>
                </div>
                <span class="stat-icon"><i class="fas fa-chart-line"></i></span>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div>
                    <p class="stat-label">Productos</p>
                    <p class="stat-value"><?php echo $res['productos']; ?></p>
                </div>
                <span class="stat-icon"><i class="fas fa-box"></i></span>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div>
                    <p class="stat-label">Stock Total</p>
                    <p class="stat-value"><?php echo $res['stock_total']; ?></p>
                </div>
                <span class="stat-icon"><i class="fas fa-warehouse"></i></span>
            </div>
        </div>
    </div>

    <!-- Pestañas de navegación del panel -->
    <div class="admin-panel">
        <div class="d-flex gap-2 flex-wrap mb-4 border-bottom pb-3">
            <a href="<?php echo base('admin'); ?>"          class="admin-tab <?php echo $tabActiva === 'productos' ? 'activo' : ''; ?>"><i class="fas fa-box me-1"></i>Productos</a>
            <a href="<?php echo base('admin/reportes'); ?>" class="admin-tab <?php echo $tabActiva === 'reportes' ? 'activo' : ''; ?>"><i class="fas fa-chart-pie me-1"></i>Reportes</a>
            <a href="<?php echo base('admin/pedidos'); ?>"  class="admin-tab <?php echo $tabActiva === 'pedidos' ? 'activo' : ''; ?>"><i class="fas fa-clipboard-list me-1"></i>Gestión de Pedidos</a>
            <?php if ($tabActiva === 'productos'): ?>
                <button class="admin-tab ms-auto" data-bs-toggle="modal" data-bs-target="#modalAgregar">
                    <i class="fas fa-plus me-1"></i>Agregar Producto
                </button>
            <?php endif; ?>
        </div>
