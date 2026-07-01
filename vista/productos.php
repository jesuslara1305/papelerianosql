<?php
/* =====================================================================
 *  vista/productos.php  -  Catálogo completo con filtro por categoría
 * ===================================================================== */
include 'vista/header_gral.php';

$prodCtrl = new ProductoControlador();
$catSel   = $_GET['cat'] ?? '';
$productos = $catSel !== '' ? $prodCtrl->porCategoria($catSel) : $prodCtrl->listar();

$categorias = ['Peluches', 'Accesorios', 'Papelería', 'Decoración'];
?>
<main class="container py-5">
    <h2 class="seccion-titulo">Nuestros Productos</h2>
    <p class="seccion-sub">Explora todo nuestro catálogo kawaii</p>

    <!-- Filtros por categoría -->
    <div class="d-flex justify-content-center flex-wrap gap-2 mb-5">
        <a href="<?php echo base('productos'); ?>"
           class="btn btn-sm <?php echo $catSel === '' ? 'btn-sanrio' : 'btn-outline-sanrio'; ?>">Todos</a>
        <?php foreach ($categorias as $c): ?>
            <a href="<?php echo base('productos?cat=' . urlencode($c)); ?>"
               class="btn btn-sm <?php echo $catSel === $c ? 'btn-sanrio' : 'btn-outline-sanrio'; ?>">
                <?php echo e($c); ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="row g-4">
        <?php if (empty($productos)): ?>
            <div class="col-12 text-center text-muted py-5">
                <i class="fas fa-box-open fa-2x mb-2"></i>
                <p>No hay productos en esta categoría.</p>
            </div>
        <?php else: ?>
            <?php foreach ($productos as $p): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="prod-card">
                        <div class="position-relative">
                            <img class="prod-img" src="<?php echo e($p['imagen']); ?>" alt="<?php echo e($p['nombre']); ?>">
                            <span class="prod-badge"><?php echo e($p['categoria']); ?></span>
                        </div>
                        <div class="p-3 d-flex flex-column flex-grow-1">
                            <h6 class="fw-bold mb-1"><?php echo e($p['nombre']); ?></h6>
                            <div class="stars mb-2">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                    <i class="fa<?php echo $i < round($p['rating']) ? 's' : 'r'; ?> fa-star"></i>
                                <?php endfor; ?>
                            </div>
                            <span class="prod-precio mb-2"><?php echo precio($p['precio']); ?></span>
                            <?php if ($p['stock'] <= 0): ?>
                                <button class="btn btn-secondary w-100 mt-auto" disabled>Agotado</button>
                            <?php else: ?>
                                <form method="post" action="<?php echo base(''); ?>" class="mt-auto">
                                    <input type="hidden" name="accion" value="agregar_carrito">
                                    <input type="hidden" name="no_producto" value="<?php echo $p['no_producto']; ?>">
                                    <input type="hidden" name="volver" value="productos<?php echo $catSel ? '?cat=' . urlencode($catSel) : ''; ?>">
                                    <button class="btn btn-sanrio w-100">
                                        <i class="fas fa-shopping-cart me-1"></i>Agregar
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<?php include 'vista/footer_gral.php'; ?>
