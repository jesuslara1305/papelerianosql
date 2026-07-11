<?php
/* =====================================================================
 *  vista/checkout/pago.php  -  Información de pago (Ilustración 8)
 *  Recibe por GET los datos de envío del paso anterior y, al pagar,
 *  envía todo por POST a la acción "crear_pedido" que guarda en MongoDB.
 *
 *  La vista visual (pasos de checkout, tarjeta 3D con flip, selector de
 *  método de pago) está portada de ProyectoWeb/vista/Producto/pago.php.
 *  A diferencia de ProyectoWeb, aquí NO se usa localStorage ni un
 *  sistema de reserva de stock por sesión: el carrito vive en
 *  $_SESSION['carrito'] y el inventario se valida en tiempo real
 *  contra MongoDB (ver CarritoControlador y PedidoControlador).
 * ===================================================================== */
include 'vista/header_gral.php';

$car   = new CarritoControlador();
$items = $car->items();
$sub   = $car->subtotal();
$iva   = $sub * 0.16;
$total = $sub + $iva;

if (empty($items)) { redirigir('carrito'); }

/* Datos de envío recibidos del paso previo */
$envio = [
    'nombre'    => $_GET['nombre']    ?? ($_SESSION['nombre'] ?? ''),
    'email'     => $_GET['email']     ?? ($_SESSION['email'] ?? ''),
    'direccion' => $_GET['direccion'] ?? '',
    'ciudad'    => $_GET['ciudad']    ?? '',
    'cp'        => $_GET['cp']        ?? '',
];
?>
<main class="container py-5">
    <!-- Pasos del checkout -->
    <div class="checkout-steps">
        <div class="step done">
            <div class="step-circle"><i class="fas fa-shopping-cart"></i></div>
            <div class="step-label">Carro de Compras</div>
        </div>
        <div class="step-line done"></div>
        <div class="step done">
            <div class="step-circle"><i class="fas fa-info-circle"></i></div>
            <div class="step-label">Información de Envío</div>
        </div>
        <div class="step-line done"></div>
        <div class="step active">
            <div class="step-circle"><i class="fas fa-credit-card"></i></div>
            <div class="step-label">Pago</div>
        </div>
    </div>

    <h2 class="fw-bold mb-4">Finalizar Compra</h2>

    <div class="row g-4">
        <!-- ── Columna izquierda: Método de pago ── -->
        <div class="col-lg-8">
            <div class="pago-card">
                <h5><i class="fas fa-lock me-2 text-rosa"></i>Método de pago</h5>

                <!-- Selector de método (por ahora solo tarjeta, igual que ProyectoWeb) -->
                <div class="pago-metodos">
                    <button type="button" class="pago-metodo-btn active" onclick="setMetodo('tarjeta', this)">
                        <i class="fas fa-credit-card"></i>Tarjeta
                    </button>
                </div>

                <!-- Formulario que dispara la inserción del pedido en MongoDB -->
                <form method="post" action="<?php echo base(''); ?>" id="formPago">
                    <input type="hidden" name="accion" value="crear_pedido">
                    <!-- Reenvía los datos de envío del paso anterior -->
                    <?php foreach ($envio as $k => $v): ?>
                        <input type="hidden" name="<?php echo $k; ?>" value="<?php echo e($v); ?>">
                    <?php endforeach; ?>

                    <div class="pago-panel active" id="panel-tarjeta">
                        <!-- Vista previa de tarjeta con flip 3D -->
                        <div class="card-preview">
                            <div class="card-inner" id="cardInner">
                                <div class="card-front">
                                    <div class="card-chip"></div>
                                    <div class="card-logo" id="cardLogoFront">
                                        <i class="fab fa-cc-visa"></i>
                                    </div>
                                    <div class="card-number" id="cardNumberDisplay">•••• •••• •••• ••••</div>
                                    <div class="card-info-row">
                                        <div>
                                            <div class="card-label">Titular</div>
                                            <div class="card-value" id="cardNameDisplay"><?php echo e(mb_strtoupper($envio['nombre'] ?: 'NOMBRE APELLIDO')); ?></div>
                                        </div>
                                        <div>
                                            <div class="card-label">Vence</div>
                                            <div class="card-value" id="cardExpDisplay">MM/AA</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-back">
                                    <div class="card-strip"></div>
                                    <div class="card-cvv-area">
                                        <div class="card-cvv-label">CVV</div>
                                        <div class="card-cvv-box" id="cardCvvDisplay">•••</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Íconos aceptados -->
                        <div class="tarjetas-aceptadas">
                            <span style="font-size:.72rem;color:#999">Aceptamos:</span>
                            <i class="fab fa-cc-visa tarjeta-icon"></i>
                            <i class="fab fa-cc-mastercard tarjeta-icon"></i>
                            <i class="fab fa-cc-amex tarjeta-icon"></i>
                        </div>

                        <!-- Campos -->
                        <div class="pago-group">
                            <label>Número de tarjeta</label>
                            <input type="text" id="cardNumber" class="pago-input"
                                   placeholder="1234 5678 9012 3456" maxlength="19" inputmode="numeric"
                                   oninput="formatCardNumber(this)" onfocus="flipCard(false)" required>
                        </div>
                        <div class="pago-group">
                            <label>Nombre del titular</label>
                            <input type="text" id="cardName" class="pago-input"
                                   placeholder="Como aparece en tu tarjeta"
                                   value="<?php echo e(mb_strtoupper($envio['nombre'])); ?>"
                                   oninput="updateCardName(this)" onfocus="flipCard(false)" required>
                        </div>
                        <div class="pago-row">
                            <div class="pago-group">
                                <label>Fecha de vencimiento</label>
                                <input type="text" id="cardExp" class="pago-input"
                                       placeholder="MM/AA" maxlength="5" inputmode="numeric"
                                       oninput="formatExp(this)" onfocus="flipCard(false)" required>
                            </div>
                            <div class="pago-group">
                                <label>CVV</label>
                                <input type="text" id="cardCvv" class="pago-input"
                                       placeholder="•••" minlength="3" maxlength="4" inputmode="numeric"
                                       oninput="updateCvv(this)"
                                       onfocus="flipCard(true)" onblur="flipCard(false)" required>
                            </div>
                        </div>
                    </div>

                    <!-- Botón confirmar -->
                    <button type="submit" class="btn-confirmar" id="btnConfirmar">
                        <span class="btn-confirmar-text">
                            <i class="fas fa-lock me-1"></i>Confirmar y Pagar <?php echo precio($total); ?>
                        </span>
                        <div class="spinner-confirmar"></div>
                    </button>

                    <div class="seguridad-badge">
                        <i class="fas fa-shield-alt"></i>
                        Pago seguro con cifrado SSL de 256 bits
                    </div>
                </form>
            </div>
        </div>

        <!-- ── Columna derecha: dirección + resumen ── -->
        <div class="col-lg-4">
            <!-- Dirección de envío confirmada en el paso anterior -->
            <div class="pago-card">
                <h5><i class="fas fa-map-marker-alt me-2 text-rosa"></i>Enviando a</h5>
                <div class="d-flex align-items-start gap-2">
                    <i class="fas fa-home mt-1 text-rosa"></i>
                    <div>
                        <div class="fw-bold small"><?php echo e($envio['nombre']); ?></div>
                        <div class="small text-muted" style="line-height:1.6">
                            <?php echo e($envio['direccion'] ?: 'Dirección no especificada'); ?><br>
                            <?php echo e($envio['ciudad']); ?><?php echo $envio['ciudad'] && $envio['cp'] ? ', ' : ''; ?><?php echo e($envio['cp']); ?>
                        </div>
                        <a href="<?php echo base('envio'); ?>" class="small text-rosa">
                            <i class="fas fa-pencil-alt me-1"></i>Cambiar dirección
                        </a>
                    </div>
                </div>
            </div>

            <!-- Resumen del pedido -->
            <div class="pago-card">
                <h5><i class="fas fa-receipt me-2 text-rosa"></i>Resumen del pedido</h5>
                <?php foreach ($items as $i): ?>
                    <div class="resumen-item">
                        <img src="<?php echo e(imagenSrc($i['imagen'])); ?>" alt="<?php echo e($i['nombre']); ?>"
                             style="width:56px;height:56px;object-fit:cover;border-radius:8px;">
                        <div class="resumen-item-info">
                            <div class="resumen-item-name"><?php echo e($i['nombre']); ?></div>
                            <div class="resumen-item-qty">Cantidad: <?php echo $i['cantidad']; ?></div>
                        </div>
                        <div class="resumen-item-precio"><?php echo precio($i['precio'] * $i['cantidad']); ?></div>
                    </div>
                <?php endforeach; ?>

                <div class="resumen-totales">
                    <div class="resumen-linea">
                        <span>Subtotal</span><span><?php echo precio($sub); ?></span>
                    </div>
                    <div class="resumen-linea">
                        <span>Envío</span><span style="color:#27ae60;font-weight:600">GRATIS</span>
                    </div>
                    <div class="resumen-linea">
                        <span>IVA (16%)</span><span><?php echo precio($iva); ?></span>
                    </div>
                    <div class="resumen-linea total">
                        <span>Total</span><span><?php echo precio($total); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="<?php echo base('js/pago.js'); ?>"></script>

<?php include 'vista/footer_gral.php'; ?>
