<?php
/* =====================================================================
 *  vista/login.php  -  Ingreso al sistema (Ilustración 3)
 *  Pestañas Cliente / Administrador, con datos demo de prueba.
 * ===================================================================== */
$mensaje = obtenerFlash();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar Sesión | Sanrio Shop</title>
    <link rel="icon" href="<?php echo base('public/img/logo.png'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo base('estilos/styles.css'); ?>">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="login-wrap">
    <div class="login-card">
        <div class="text-center mb-4">
            <img src="<?php echo base('public/img/logo.png'); ?>" alt="Sanrio Shop" class="login-logo mb-2">
            <h3 class="grad-text fw-bold mb-0">Sanrio Shop</h3>
            <p class="text-muted small">Bienvenido de vuelta</p>
        </div>

        <?php if ($mensaje): ?>
            <div class="alert alert-<?php echo $mensaje['tipo'] === 'exito' ? 'success' : 'danger'; ?> py-2 small">
                <?php echo e($mensaje['mensaje']); ?>
            </div>
        <?php endif; ?>

        <!-- Pestañas Cliente / Administrador -->
        <ul class="nav nav-pills nav-fill bg-light rounded-3 p-1 mb-4" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-cliente">
                    <i class="fas fa-user me-1"></i>Cliente
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-admin">
                    <i class="fas fa-shield-halved me-1"></i>Administrador
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <!-- CLIENTE -->
            <div class="tab-pane fade show active" id="tab-cliente">
                <form method="post" action="<?php echo base(''); ?>">
                    <input type="hidden" name="accion" value="login">
                    <input type="hidden" name="rol" value="cliente">
                    <label class="form-label fw-semibold small">Email</label>
                    <input type="email" name="email" class="form-control mb-3" placeholder="cliente@sanrio.com" required>
                    <label class="form-label fw-semibold small">Contraseña</label>
                    <input type="password" name="password" class="form-control mb-3" placeholder="••••••••" required>
                  <!--  <div class="demo-box mb-3">
                        <strong>Demo - Usuario de prueba:</strong><br>
                        cliente@sanrio.com<br>cliente123
                    </div>-->
                    <button class="btn btn-sanrio w-100"><i class="fas fa-right-to-bracket me-1"></i>Iniciar Sesión</button>
                </form>
            </div>

            <!-- ADMINISTRADOR -->
            <div class="tab-pane fade" id="tab-admin">
                <form method="post" action="<?php echo base(''); ?>">
                    <input type="hidden" name="accion" value="login">
                    <input type="hidden" name="rol" value="admin">
                    <label class="form-label fw-semibold small">Email</label>
                    <input type="email" name="email" class="form-control mb-3" placeholder="admin@sanrio.com" required>
                    <label class="form-label fw-semibold small">Contraseña</label>
                    <input type="password" name="password" class="form-control mb-3" placeholder="••••••••" required>
                   <!--<div class="demo-box mb-3">
                        <strong>Demo - Administrador:</strong><br>
                        admin@sanrio.com<br>admin123
                    </div>-->
                    <button class="btn btn-sanrio w-100"><i class="fas fa-right-to-bracket me-1"></i>Iniciar Sesión</button>
                </form>
            </div>
        </div>

        <!-- Registro -->
        <hr class="my-4">
        <details>
            <summary class="text-muted small" style="cursor:pointer;">¿No tienes cuenta? Regístrate aquí</summary>
            <form method="post" action="<?php echo base(''); ?>" class="mt-3">
                <input type="hidden" name="accion" value="registro">
                <input type="text" name="nombre" class="form-control mb-2" placeholder="Nombre completo" required>
                <input type="email" name="email" class="form-control mb-2" placeholder="Correo electrónico" required>
                <input type="password" name="password" class="form-control mb-3" placeholder="Contraseña (mín. 6)" required>
                <button class="btn btn-outline-sanrio w-100">Crear cuenta</button>
            </form>
        </details>

        <div class="text-center mt-3">
            <a href="<?php echo base('inicio'); ?>" class="text-muted small text-decoration-none">
                <i class="fas fa-arrow-left me-1"></i>Volver a la tienda
            </a>
        </div>
    </div>
</div>
</body>
</html>
