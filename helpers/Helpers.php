<?php
/* =====================================================================
 *  helpers/Helpers.php
 * =====================================================================
 *  Funciones de apoyo reutilizables en las vistas.
 * ===================================================================== */

/* Devuelve la URL base configurada (para construir enlaces y rutas). */
function base(string $ruta = ''): string
{
    static $base = null;
    if ($base === null) {
        $config = require __DIR__ . '/../config/config.php';
        $base = rtrim($config['base_url'], '/');
    }
    return $base . '/' . ltrim($ruta, '/');
}

/* Escapa texto para imprimirlo en HTML de forma segura. */
function e($texto): string
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

/* Formatea un número como precio. */
function precio($n): string
{
    return '$' . number_format((float) $n, 2);
}

/* Guarda un mensaje flash que se mostrará en la siguiente vista. */
function flash(string $tipo, string $mensaje): void
{
    $_SESSION['flash'] = ['tipo' => $tipo, 'mensaje' => $mensaje];
}

/* Recupera y limpia el mensaje flash. */
function obtenerFlash(): ?array
{
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

/* Redirige a una ruta interna y termina la ejecución. */
function redirigir(string $ruta): void
{
    header('Location: ' . base($ruta));
    exit;
}

/* Devuelve una clase de color de Bootstrap según el estado del pedido. */
function colorEstado(string $estado): string
{
    return match ($estado) {
        'entregado'  => 'success',
        'en proceso' => 'warning',
        'enviado'    => 'info',
        'pendiente'  => 'secondary',
        default      => 'light',
    };
}
