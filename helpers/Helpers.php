<?php
/* =====================================================================
 *  helpers/Helpers.php
 * =====================================================================
 *  Funciones de apoyo reutilizables en las vistas.
 * ===================================================================== */

/* Devuelve la URL base de la app, detectada automáticamente.
 * Así el proyecto funciona sin importar cómo se llame la carpeta
 * (SanrioShop, papelerianosql, sanrioshop-2, etc.), porque ya no
 * depende de que "base_url" esté escrito a mano en config.php. */
function base(string $ruta = ''): string
{
    static $base = null;
    if ($base === null) {
        // SCRIPT_NAME es algo como: /papelerianosql/index.php
        // Quitamos "/index.php" (o el archivo que sea) y nos quedamos
        // con la carpeta real donde vive el proyecto en ESTE servidor.
        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
        if ($base === '.') {
            $base = '';
        }
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

/* Resuelve la URL para mostrar la imagen de un producto:
 * - Si es una URL externa (http/https), se usa tal cual (compatibilidad
 *   con productos sembrados con imágenes de ejemplo).
 * - Si es un nombre de archivo (subido desde el panel admin), se arma
 *   la ruta hacia public/uploads/productos/.
 * - Si viene vacío, se muestra un placeholder. */
function imagenSrc(?string $imagen): string
{
    $imagen = trim((string) $imagen);
    if ($imagen === '') {
        return 'https://placehold.co/300x300?text=Sin+Imagen';
    }
    if (preg_match('#^(https?:)?//#i', $imagen)) {
        return $imagen;
    }
    return base('public/uploads/productos/' . $imagen);
}
