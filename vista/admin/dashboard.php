<?php
/* =====================================================================
 *  vista/admin/dashboard.php  -  Gestión de productos / stock (Ilustración 9)
 * ===================================================================== */
$tabActiva = 'productos';
include 'vista/admin/header_admin.php';

$prodCtrl  = new ProductoControlador();
$productos = $prodCtrl->listar();
?>

<!-- Tabla de productos -->
<div class="table-responsive">
    <table class="table tabla-admin align-middle">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Categoría</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Ventas</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($productos)): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">
                    No hay productos todavía. Usa el botón "Agregar Producto" para comenzar a cargar tu catálogo.
                </td></tr>
            <?php else: ?>
                <?php foreach ($productos as $p): ?>
                    <tr>
                        <td class="fw-semibold"><?php echo e($p['nombre']); ?></td>
                        <td><span class="chip-cat"><?php echo e($p['categoria']); ?></span></td>
                        <td><?php echo precio($p['precio']); ?></td>
                        <td>
                            <span class="<?php echo $p['stock'] <= $p['stock_minimo'] ? 'stock-bajo' : 'stock-ok'; ?>">
                                <?php echo $p['stock']; ?>
                                <?php if ($p['stock'] <= $p['stock_minimo']): ?>
                                    <i class="fas fa-triangle-exclamation ms-1" title="Stock bajo"></i>
                                <?php endif; ?>
                            </span>
                        </td>
                        <td><?php echo $p['ventas']; ?></td>
                        <td class="text-end">
                            <!-- Editar -->
                            <button class="btn btn-link text-primary p-1"
                                    data-bs-toggle="modal" data-bs-target="#modalEditar<?php echo $p['no_producto']; ?>">
                                <i class="fas fa-pen-to-square"></i>
                            </button>
                            <!-- Eliminar -->
                            <form method="post" action="<?php echo base(''); ?>" class="d-inline"
                                  onsubmit="return confirm('¿Dar de baja este producto?');">
                                <input type="hidden" name="accion" value="admin_eliminar_producto">
                                <input type="hidden" name="no_producto" value="<?php echo $p['no_producto']; ?>">
                                <button class="btn btn-link text-danger p-1"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>

                    <!-- Modal Editar de este producto -->
                    <div class="modal fade" id="modalEditar<?php echo $p['no_producto']; ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content rounded-4">
                                <form method="post" action="<?php echo base(''); ?>" enctype="multipart/form-data">
                                    <input type="hidden" name="accion" value="admin_editar_producto">
                                    <input type="hidden" name="no_producto" value="<?php echo $p['no_producto']; ?>">
                                    <div class="modal-header border-0">
                                        <h5 class="modal-title fw-bold">Editar Producto</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-2">
                                            <label class="form-label small fw-semibold">Nombre</label>
                                            <input type="text" name="nombre" class="form-control" value="<?php echo e($p['nombre']); ?>" required>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small fw-semibold">Categoría</label>
                                            <select name="categoria" class="form-select">
                                                <?php foreach (['Peluches','Accesorios','Papelería','Decoración'] as $c): ?>
                                                    <option <?php echo $p['categoria'] === $c ? 'selected' : ''; ?>><?php echo $c; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col"><label class="form-label small fw-semibold">Precio</label>
                                                <input type="number" step="0.01" min="0.01" name="precio" class="form-control" value="<?php echo $p['precio']; ?>" required></div>
                                            <div class="col"><label class="form-label small fw-semibold">Stock</label>
                                                <input type="number" step="1" min="0" max="100000" name="stock" class="form-control" value="<?php echo $p['stock']; ?>"></div>
                                            <div class="col"><label class="form-label small fw-semibold">Mínimo</label>
                                                <input type="number" step="1" min="1" name="stock_minimo" class="form-control" value="<?php echo max(1, (int) $p['stock_minimo']); ?>"></div>
                                        </div>
                                        <div class="mt-2">
                                            <label class="form-label small fw-semibold">Imagen del producto</label>
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <img src="<?php echo e(imagenSrc($p['imagen'])); ?>" alt="" style="width:44px;height:44px;object-fit:cover;border-radius:8px;border:1px solid #f1e7f5;">
                                                <span class="text-muted small">Imagen actual</span>
                                            </div>
                                            <input type="file" name="imagen" class="form-control" accept="image/png,image/jpeg,image/jpg,image/webp,image/gif,image/svg+xml">
                                            <div class="form-text">Deja este campo vacío para conservar la imagen actual. Formatos: PNG, JPG, JPEG, WEBP, GIF, SVG.</div>
                                        </div>
                                        <div class="form-check mt-2">
                                            <input type="checkbox" name="destacado" class="form-check-input" id="dest<?php echo $p['no_producto']; ?>" <?php echo !empty($p['destacado']) ? 'checked' : ''; ?>>
                                            <label class="form-check-label small" for="dest<?php echo $p['no_producto']; ?>">Producto destacado</label>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                                        <button class="btn btn-sanrio">Guardar cambios</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Agregar Producto -->
<div class="modal fade" id="modalAgregar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4">
            <form method="post" action="<?php echo base(''); ?>" enctype="multipart/form-data">
                <input type="hidden" name="accion" value="admin_agregar_producto">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Agregar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Nombre</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Categoría</label>
                        <select name="categoria" class="form-select">
                            <?php foreach (['Peluches','Accesorios','Papelería','Decoración'] as $c): ?>
                                <option><?php echo $c; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-2">
                        <div class="col"><label class="form-label small fw-semibold">Precio</label>
                            <input type="number" step="0.01" min="0.01" name="precio" class="form-control" required></div>
                        <div class="col"><label class="form-label small fw-semibold">Stock</label>
                            <input type="number" step="1" min="0" max="100000" name="stock" class="form-control" value="0"></div>
                        <div class="col"><label class="form-label small fw-semibold">Mínimo</label>
                            <input type="number" step="1" min="1" name="stock_minimo" class="form-control" value="1"></div>
                    </div>
                    <div class="mt-2">
                        <label class="form-label small fw-semibold">Imagen del producto</label>
                        <input type="file" name="imagen" class="form-control" accept="image/png,image/jpeg,image/jpg,image/webp,image/gif,image/svg+xml" required>
                        <div class="form-text">Formatos permitidos: PNG, JPG, JPEG, WEBP, GIF, SVG.</div>
                    </div>
                    <div class="form-check mt-2">
                        <input type="checkbox" name="destacado" class="form-check-input" id="destNuevo">
                        <label class="form-check-label small" for="destNuevo">Marcar como destacado</label>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-sanrio">Registrar producto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'vista/admin/footer_admin.php'; ?>
