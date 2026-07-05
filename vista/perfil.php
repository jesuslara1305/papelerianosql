<?php
/* =====================================================================
 *  vista/perfil.php  -  Perfil del cliente (datos + sus pedidos)
 * =====================================================================
 *  Muestra los datos de la cuenta del cliente que inició sesión y el
 *  historial de SUS pedidos únicamente (filtrados por su email).
 *  El acceso ya está protegido desde control/router.php.
 * ===================================================================== */
include 'vista/header_gral.php';

$pedCtrl = new PedidoControlador();
$pedidos = $pedCtrl->listarPorCliente($_SESSION['email']);
?>

<div class="container my-5">

    <!-- Encabezado del perfil -->
    <div class="d-flex align-items-center gap-3 mb-4">
        <div class="perfil-avatar">
            <i class="fas fa-user"></i>
        </div>
        <div>
            <h2 class="fw-bold mb-0"><?php echo e($_SESSION['nombre']); ?></h2>
            <p class="text-muted mb-0"><?php echo e($_SESSION['email']); ?></p>
        </div>
    </div>

    <!-- Historial de pedidos -->
    <h4 class="fw-bold mb-3"><i class="fas fa-clock-rotate-left me-2"></i>Mis pedidos</h4>

    <?php if (empty($pedidos)): ?>
        <div class="text-center text-muted py-5 border rounded-4">
            <i class="fas fa-box-open fs-1 mb-3 d-block"></i>
            Todavía no has realizado ningún pedido.<br>
            <a href="<?php echo base('productos'); ?>" class="btn btn-sanrio mt-3">Ver catálogo</a>
        </div>
    <?php else: ?>
        <div class="d-grid gap-3">
            <?php foreach ($pedidos as $ped): ?>
                <div class="pedido-card">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                        <div>
                            <span class="fw-bold">Pedido #<?php echo $ped['no_pedido']; ?></span>
                            <span class="text-muted small ms-2"><?php echo e($ped['fecha']); ?></span>
                        </div>
                        <span class="badge bg-<?php echo colorEstado($ped['estado']); ?> bg-opacity-25 text-<?php echo colorEstado($ped['estado']); ?> fw-semibold px-3 py-2">
                            <?php echo ucfirst($ped['estado']); ?>
                        </span>
                    </div>

                    <hr class="my-3">

                    <!-- Productos del pedido (items embebidos) -->
                    <div class="d-grid gap-2 mb-3">
                        <?php foreach ($ped['items'] as $item): ?>
                            <div class="d-flex justify-content-between small">
                                <span><?php echo $item['cantidad']; ?> x <?php echo e($item['nombre']); ?></span>
                                <span class="text-muted"><?php echo precio($item['precio'] * $item['cantidad']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="d-flex justify-content-between align-items-center border-top pt-3">
                        <span class="text-muted small">
                            <i class="fas fa-location-dot me-1"></i>
                            <?php echo e($ped['direccion']); ?>, <?php echo e($ped['ciudad']); ?>
                        </span>
                        <span class="fw-bold fs-5 text-rosa"><?php echo precio($ped['total']); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<?php include 'vista/footer_gral.php'; ?>
