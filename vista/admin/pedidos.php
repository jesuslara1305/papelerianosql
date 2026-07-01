<?php
/* =====================================================================
 *  vista/admin/pedidos.php  -  Gestión de pedidos (Ilustración 10)
 * ===================================================================== */
$tabActiva = 'pedidos';
include 'vista/admin/header_admin.php';

$pedCtrl = new PedidoControlador();
$filtro  = $_GET['estado'] ?? 'todos';
$pedidos = $pedCtrl->listar($filtro);
$estados = ['pendiente', 'en proceso', 'enviado', 'entregado'];
?>

<!-- Filtro por estado -->
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <form method="get" action="<?php echo base('admin/pedidos'); ?>" class="d-flex align-items-center gap-2">
        <i class="fas fa-filter text-muted"></i>
        <label class="small text-muted">Filtrar por estado:</label>
        <select name="estado" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
            <option value="todos" <?php echo $filtro === 'todos' ? 'selected' : ''; ?>>Todos los estados</option>
            <?php foreach ($estados as $e): ?>
                <option value="<?php echo $e; ?>" <?php echo $filtro === $e ? 'selected' : ''; ?>><?php echo ucfirst($e); ?></option>
            <?php endforeach; ?>
        </select>
    </form>
    <span class="small text-muted">Mostrando <?php echo count($pedidos); ?> pedido(s)</span>
</div>

<div class="table-responsive">
    <table class="table tabla-admin align-middle">
        <thead>
            <tr>
                <th>ID Pedido</th>
                <th>Cliente</th>
                <th>Email</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pedidos)): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">No hay pedidos registrados.</td></tr>
            <?php else: ?>
                <?php foreach ($pedidos as $ped): ?>
                    <tr>
                        <td class="fw-semibold">#<?php echo $ped['no_pedido']; ?></td>
                        <td><?php echo e($ped['cliente']); ?></td>
                        <td class="text-rosa small"><?php echo e($ped['email']); ?></td>
                        <td class="fw-semibold"><?php echo precio($ped['total']); ?></td>
                        <td>
                            <!-- Cambiar estado del pedido -->
                            <form method="post" action="<?php echo base(''); ?>" class="m-0">
                                <input type="hidden" name="accion" value="admin_cambiar_estado">
                                <input type="hidden" name="no_pedido" value="<?php echo $ped['no_pedido']; ?>">
                                <select name="estado" class="form-select form-select-sm border-0 bg-<?php echo colorEstado($ped['estado']); ?> bg-opacity-10 text-<?php echo colorEstado($ped['estado']); ?> fw-semibold"
                                        style="width:auto;" onchange="this.form.submit()">
                                    <?php foreach ($estados as $e): ?>
                                        <option value="<?php echo $e; ?>" <?php echo $ped['estado'] === $e ? 'selected' : ''; ?>>
                                            <?php echo ucfirst($e); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </td>
                        <td class="small text-muted"><?php echo e($ped['fecha']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'vista/admin/footer_admin.php'; ?>
