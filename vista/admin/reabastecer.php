<?php
/* =====================================================================
 *  vista/admin/reabastecer.php  -  Reabastecimiento de inventario
 * =====================================================================
 *  Permite al administrador SUMAR unidades al stock de cada producto
 *  (recepción de mercancía), sin tener que pasar por el formulario de
 *  edición completo. Igual que el resto del sistema: nunca se acepta
 *  una cantidad negativa o en cero, y el stock resultante nunca supera
 *  el tope máximo de inventario (ver ProductoControlador::STOCK_MAXIMO).
 * ===================================================================== */
$tabActiva = 'reabastecer';
include 'vista/admin/header_admin.php';

$prodCtrl  = new ProductoControlador();
$productos = $prodCtrl->listar();

// Los productos con stock bajo se muestran primero para atenderlos antes.
usort($productos, function ($a, $b) {
    $bajoA = $a['stock'] <= $a['stock_minimo'] ? 0 : 1;
    $bajoB = $b['stock'] <= $b['stock_minimo'] ? 0 : 1;
    return $bajoA <=> $bajoB;
});
?>

<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div>
        <h5 class="fw-bold mb-0">Reabastecer Inventario</h5>
        <small class="text-muted">Registra la mercancía que llega y súmala al stock actual de cada producto.</small>
    </div>
</div>

<div class="table-responsive">
    <table class="table tabla-admin align-middle">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Categoría</th>
                <th>Stock Actual</th>
                <th>Stock Mínimo</th>
                <th style="min-width:260px;">Reabastecer</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($productos)): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">
                    No hay productos registrados todavía.
                </td></tr>
            <?php else: ?>
                <?php foreach ($productos as $p):
                    $stockBajo   = $p['stock'] <= $p['stock_minimo'];
                    $alMaximo    = $p['stock'] >= ProductoControlador::STOCK_MAXIMO;
                    $disponible  = ProductoControlador::STOCK_MAXIMO - (int) $p['stock'];
                ?>
                    <tr>
                        <td class="fw-semibold">
                            <div class="d-flex align-items-center gap-2">
                                <img src="<?php echo e(imagenSrc($p['imagen'])); ?>" alt=""
                                     style="width:38px;height:38px;object-fit:cover;border-radius:8px;">
                                <?php echo e($p['nombre']); ?>
                            </div>
                        </td>
                        <td><span class="chip-cat"><?php echo e($p['categoria']); ?></span></td>
                        <td>
                            <span class="<?php echo $stockBajo ? 'stock-bajo' : 'stock-ok'; ?>">
                                <?php echo $p['stock']; ?>
                                <?php if ($stockBajo): ?>
                                    <i class="fas fa-triangle-exclamation ms-1" title="Stock bajo"></i>
                                <?php endif; ?>
                            </span>
                        </td>
                        <td><?php echo $p['stock_minimo']; ?></td>
                        <td>
                            <?php if ($alMaximo): ?>
                                <span class="text-muted small">
                                    <i class="fas fa-circle-check me-1 text-success"></i>Inventario al máximo (<?php echo number_format(ProductoControlador::STOCK_MAXIMO); ?>)
                                </span>
                            <?php else: ?>
                                <form method="post" action="<?php echo base(''); ?>" class="d-flex align-items-center gap-2">
                                    <input type="hidden" name="accion" value="admin_reabastecer_producto">
                                    <input type="hidden" name="no_producto" value="<?php echo $p['no_producto']; ?>">
                                    <input type="number" name="cantidad" class="form-control form-control-sm"
                                           style="width:100px;" min="1" max="<?php echo $disponible; ?>"
                                           value="1" required>
                                    <button class="btn btn-sm btn-sanrio">
                                        <i class="fas fa-plus me-1"></i>Agregar
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'vista/admin/footer_admin.php'; ?>
