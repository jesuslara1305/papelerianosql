<?php
/* =====================================================================
 *  vista/checkout/pago.php  -  Información de pago (Ilustración 8)
 *  Recibe por GET los datos de envío del paso anterior y, al pagar,
 *  envía todo por POST a la acción "crear_pedido" que guarda en MongoDB.
 * ===================================================================== */
include 'vista/header_gral.php';

$car   = new CarritoControlador();
$items = $car->items();
$sub   = $car->subtotal();

if (empty($items)) { redirigir('carrito'); }

/* Datos de envío recibidos del paso previo */
$envio = [
    'nombre'    => $_GET['nombre']    ?? ($_SESSION['nombre'] ?? ''),
    'email'     => $_GET['email']     ?? ($_SESSION['email'] ?? ''),
    'direccion' => $_GET['direccion'] ?? '',
    'ciudad'    => $_GET['ciudad']    ?? '',
    'cp'        => $_GET['cp']         ?? '',
];
?>
<main class="container py-5">
    <h2 class="fw-bold mb-4">Finalizar Compra</h2>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="admin-panel">
                <h5 class="fw-bold mb-4"><i class="fas fa-credit-card text-rosa me-2"></i>Información de Pago</h5>

                <!-- Formulario que dispara la inserción del pedido en MongoDB -->
                <form method="post" action="<?php echo base(''); ?>">
                    <input type="hidden" name="accion" value="crear_pedido">
                    <!-- Reenvía los datos de envío -->
                    <?php foreach ($envio as $k => $v): ?>
                        <input type="hidden" name="<?php echo $k; ?>" value="<?php echo e($v); ?>">
                    <?php endforeach; ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Número de Tarjeta</label>
                        <input type="text" class="form-control" placeholder="1234 5678 9123 456"
                               maxlength="19" inputmode="numeric" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nombre en la Tarjeta</label>
                        <input type="text" class="form-control" value="<?php echo e(mb_strtoupper($envio['nombre'])); ?>" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Fecha de Vencimiento</label>
                            <input type="text" class="form-control" placeholder="01/28" maxlength="5" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">CVV</label>
                            <input type="text" class="form-control" placeholder="123" maxlength="4" required>
                        </div>
                    </div>
                    <p class="text-muted small mt-3"><i class="fas fa-lock me-1"></i>Tus datos están protegidos y encriptados</p>
                    <button class="btn btn-sanrio w-100 mt-2">Pagar <?php echo precio($sub); ?></button>
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
