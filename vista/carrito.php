<?php
/* =====================================================================
 *  vista/carrito.php  -  Carrito de compras (Ilustración 6)
 * ===================================================================== */
include 'vista/header_gral.php';

$car   = new CarritoControlador();
$items = $car->items();
$sub   = $car->subtotal();
?>
<main class="container py-5">
    <a href="<?php echo base('productos'); ?>" class="text-decoration-none text-muted mb-3 d-inline-block">
        <i class="fas fa-arrow-left me-1"></i>Continuar Comprando
    </a>
    <h2 class="fw-bold mb-4">Carrito de Compras</h2>

    <?php if (empty($items)): ?>
        <div class="text-center text-muted py-5">
            <i class="fas fa-shopping-cart fa-3x mb-3"></i>
            <p>Tu carrito está vacío.</p>
            <a href="<?php echo base('productos'); ?>" class="btn btn-sanrio">Ir a la tienda</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <!-- Lista de productos -->
            <div class="col-lg-8">
                <?php foreach ($items as $i): ?>
                    <div class="linea-item">
                        <img src="<?php echo e($i['imagen']); ?>" alt="<?php echo e($i['nombre']); ?>">
                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-0"><?php echo e($i['nombre']); ?></h6>
                            <small class="text-muted"><?php echo e($i['categoria']); ?></small>
                            <div class="prod-precio mt-1" style="font-size:1.05rem;"><?php echo precio($i['precio']); ?></div>
                        </div>
                        <!-- Controles de cantidad -->
                        <div class="d-flex align-items-center gap-2">
                            <form method="post" action="<?php echo base(''); ?>" class="m-0">
                                <input type="hidden" name="accion" value="actualizar_carrito">
                                <input type="hidden" name="no_producto" value="<?php echo $i['no_producto']; ?>">
                                <input type="hidden" name="cantidad" value="<?php echo $i['cantidad'] - 1; ?>">
                                <button class="qty-btn">−</button>
                            </form>
                            <span class="fw-semibold"><?php echo $i['cantidad']; ?></span>
                            <form method="post" action="<?php echo base(''); ?>" class="m-0">
                                <input type="hidden" name="accion" value="actualizar_carrito">
                                <input type="hidden" name="no_producto" value="<?php echo $i['no_producto']; ?>">
                                <input type="hidden" name="cantidad" value="<?php echo $i['cantidad'] + 1; ?>">
                                <button class="qty-btn">+</button>
                            </form>
                        </div>
                        <!-- Eliminar -->
                        <form method="post" action="<?php echo base(''); ?>" class="m-0">
                            <input type="hidden" name="accion" value="quitar_carrito">
                            <input type="hidden" name="no_producto" value="<?php echo $i['no_producto']; ?>">
                            <button class="btn btn-link text-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Resumen del pedido -->
            <div class="col-lg-4">
                <div class="resumen-card">
                    <h5 class="fw-bold mb-3">Resumen del Pedido</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Productos (<?php echo $car->totalItems(); ?>)</span>
                        <span><?php echo precio($sub); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Envío</span>
                        <span class="text-success fw-semibold">Gratis</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-bold">Total</span>
                        <span class="prod-precio"><?php echo precio($sub); ?></span>
                    </div>
                    <a href="<?php echo base('envio'); ?>" class="btn btn-sanrio w-100">Proceder al Pago</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php include 'vista/footer_gral.php'; ?>
