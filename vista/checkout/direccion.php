<?php
/* =====================================================================
 *  vista/checkout/direccion.php  -  Información de envío (Ilustración 7)
 * ===================================================================== */
include 'vista/header_gral.php';

$car   = new CarritoControlador();
$items = $car->items();
$sub   = $car->subtotal();

if (empty($items)) { redirigir('carrito'); }
?>
<main class="container py-5">
    <h2 class="fw-bold mb-4">Finalizar Compra</h2>
    <div class="row g-4">
        <!-- Formulario de envío -->
        <div class="col-lg-8">
            <div class="admin-panel">
                <h5 class="fw-bold mb-4"><i class="fas fa-truck text-rosa me-2"></i>Información de Envío</h5>
                <!-- Estos datos viajan al siguiente paso y luego se guardan en MongoDB -->
                <form method="get" action="<?php echo base('pago'); ?>" id="formEnvio">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nombre Completo</label>
                        <input type="text" name="nombre" class="form-control"
                               value="<?php echo e($_SESSION['nombre'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Email</label>
                        <input type="email" name="email" class="form-control"
                               value="<?php echo e($_SESSION['email'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Dirección</label>
                        <input type="text" name="direccion" class="form-control" placeholder="Calle, número, colonia" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Ciudad</label>
                            <input type="text" name="ciudad" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Código Postal</label>
                            <input type="text" name="cp" class="form-control" required>
                        </div>
                    </div>
                    <button class="btn btn-sanrio w-100 mt-4">Continuar al Pago</button>
                </form>
            </div>
        </div>

        <!-- Resumen -->
        <div class="col-lg-4">
            <div class="resumen-card">
                <h5 class="fw-bold mb-3">Resumen del Pedido</h5>
                <?php foreach ($items as $i): ?>
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <img src="<?php echo e($i['imagen']); ?>" style="width:46px;height:46px;object-fit:cover;border-radius:8px;">
                        <div class="flex-grow-1">
                            <div class="small fw-semibold"><?php echo e($i['nombre']); ?></div>
                            <div class="small text-muted">Cantidad: <?php echo $i['cantidad']; ?></div>
                        </div>
                        <span class="small"><?php echo precio($i['precio'] * $i['cantidad']); ?></span>
                    </div>
                <?php endforeach; ?>
                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span><span><?php echo precio($sub); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Envío</span><span class="text-success">Gratis</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold">Total</span><span class="prod-precio"><?php echo precio($sub); ?></span>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'vista/footer_gral.php'; ?>
