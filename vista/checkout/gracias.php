<?php
/* =====================================================================
 *  vista/checkout/gracias.php  -  Confirmación de compra
 * ===================================================================== */
include 'vista/header_gral.php';
?>
<main class="container py-5">
    <div class="text-center py-5">
        <div class="mb-3">
            <i class="fas fa-circle-check" style="font-size:4rem;color:#16a34a;"></i>
        </div>
        <h2 class="fw-bold mb-2">¡Gracias por tu compra!</h2>
        <p class="text-muted mb-4">Tu pedido fue registrado correctamente y el inventario se actualizó en tiempo real.
           Pronto recibirás la confirmación de envío.</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="<?php echo base('productos'); ?>" class="btn btn-sanrio">Seguir comprando</a>
            <a href="<?php echo base('inicio'); ?>" class="btn btn-outline-sanrio">Volver al inicio</a>
        </div>
    </div>
</main>
<?php include 'vista/footer_gral.php'; ?>
