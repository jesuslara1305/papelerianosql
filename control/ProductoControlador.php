<?php
/* =====================================================================
 *  control/ProductoControlador.php
 * =====================================================================
 *  Orquesta la lógica de negocio de productos: validaciones, alta/baja,
 *  edición y consultas que usan las vistas. Sigue el mismo patrón del
 *  proyecto de referencia (el controlador "envuelve" al modelo).
 *
 *  Validaciones reforzadas (igual que ProyectoWeb):
 *    - Precio: nunca negativo ni en cero.
 *    - Stock: nunca negativo, con un tope razonable de inventario.
 *    - Stock mínimo: nunca menor a 1.
 *    - Imagen: se sube como ARCHIVO (no URL de texto libre), aceptando
 *      png, jpg, jpeg, webp, gif y svg — igual que el
 *      accept="image/*" de ProyectoWeb.
 * ===================================================================== */

class ProductoControlador
{
    /* Tope máximo razonable de inventario para evitar valores absurdos
     * como 100000000000 en el campo de stock. */
    const STOCK_MAXIMO = 100000;

    /* Formatos de imagen aceptados (extensión => mime esperado). */
    const FORMATOS_IMAGEN = [
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'webp' => 'image/webp',
        'gif'  => 'image/gif',
        'svg'  => 'image/svg+xml',
    ];

    private Producto $producto;

    public function __construct()
    {
        $this->producto = new Producto();
    }

    public function getProducto(): Producto { return $this->producto; }

    /* Consultas usadas por las vistas */
    public function destacados(int $n = 6): array { return $this->producto->destacados($n); }
    public function listar(array $f = [], array $o = []): array { return $this->producto->listar($f, $o); }
    public function porCategoria(string $c): array { return $this->producto->porCategoria($c); }
    public function stockBajo(): array { return $this->producto->stockBajo(); }

    /* -----------------------------------------------------------------
     *  Sube el archivo de imagen a public/uploads/productos/ y devuelve
     *  el nombre físico generado, o [null, mensaje_error] si algo falla.
     * ----------------------------------------------------------------- */
    private function subirImagen(?array $file): array
    {
        if (!isset($file) || !isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return [null, null]; // No se subió nada (válido al editar: se conserva la actual)
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return [null, 'Ocurrió un error al subir la imagen. Intenta de nuevo.'];
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!array_key_exists($ext, self::FORMATOS_IMAGEN)) {
            return [null, 'Formato de imagen no permitido. Usa PNG, JPG, JPEG, WEBP, GIF o SVG.'];
        }

        // Verificación adicional del tipo real del archivo (no solo la extensión).
        if (function_exists('finfo_open')) {
            $finfo    = finfo_open(FILEINFO_MIME_TYPE);
            $mimeReal = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            $mimesValidos = array_values(self::FORMATOS_IMAGEN);
            // Algunos SVG se detectan como text/xml o text/plain según el servidor.
            $mimesValidos[] = 'text/xml';
            $mimesValidos[] = 'text/plain';
            if ($mimeReal && !in_array($mimeReal, $mimesValidos, true)) {
                return [null, 'El archivo no parece ser una imagen válida.'];
            }
        }

        $nombreFisico = uniqid('prod_') . '.' . $ext;
        $directorio   = __DIR__ . '/../public/uploads/productos/';
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }

        if (!move_uploaded_file($file['tmp_name'], $directorio . $nombreFisico)) {
            return [null, 'No se pudo guardar la imagen en el servidor.'];
        }

        return [$nombreFisico, null];
    }

    /* Valida los campos numéricos comunes a alta y edición.
     * Devuelve null si todo está bien, o un mensaje de error. */
    private function validarNumeros(float $precio, int $stock, int $stock_minimo): ?string
    {
        if ($precio <= 0) {
            return 'El precio debe ser mayor a cero.';
        }
        if ($stock < 0) {
            return 'El stock no puede ser un valor negativo.';
        }
        if ($stock > self::STOCK_MAXIMO) {
            return 'El stock no puede superar ' . number_format(self::STOCK_MAXIMO) . ' unidades.';
        }
        if ($stock_minimo < 1) {
            return 'El stock mínimo no puede ser menor a 1.';
        }
        if ($stock < $stock_minimo) {
            return 'El stock inicial no puede ser menor al stock mínimo.';
        }
        return null;
    }

    /* -----------------------------------------------------------------
     *  Alta de producto desde el panel administrativo.
     *  $datos es típicamente $_POST. $files es típicamente $_FILES.
     *  Devuelve [tipo, mensaje].
     * ----------------------------------------------------------------- */
    public function agregarProducto(array $datos, array $files = []): array
    {
        if (empty($datos['nombre']) || empty($datos['precio']) || empty($datos['categoria'])) {
            return ['error', 'Nombre, precio y categoría son obligatorios.'];
        }

        $precio = floatval($datos['precio']);
        $stock  = intval($datos['stock'] ?? 0);
        $minimo = intval($datos['stock_minimo'] ?? 1);

        $errorNumeros = $this->validarNumeros($precio, $stock, $minimo);
        if ($errorNumeros) {
            return ['error', $errorNumeros];
        }

        [$imagen, $errorImagen] = $this->subirImagen($files['imagen'] ?? null);
        if ($errorImagen) {
            return ['error', $errorImagen];
        }
        if (!$imagen) {
            return ['error', 'La imagen del producto es obligatoria.'];
        }

        $this->producto->setNombre(trim($datos['nombre']));
        $this->producto->setDescripcion(trim($datos['descripcion'] ?? ''));
        $this->producto->setCategoria($datos['categoria']);
        $this->producto->setPrecio($precio);
        $this->producto->setStock($stock);
        $this->producto->setStock_minimo($minimo);
        $this->producto->setImagen($imagen);
        $this->producto->setDestacado(isset($datos['destacado']));
        $this->producto->setVentas(0);
        $this->producto->setRating(floatval($datos['rating'] ?? 5));

        $id = $this->producto->insertar();
        return $id
            ? ['exito', 'Producto «' . $datos['nombre'] . '» registrado correctamente.']
            : ['error', 'No se pudo registrar el producto. Intenta de nuevo.'];
    }

    /* Edición de producto existente */
    public function editarProducto(array $datos, array $files = []): array
    {
        if (empty($datos['no_producto'])) {
            return ['error', 'Producto no identificado.'];
        }

        $precio = floatval($datos['precio']);
        $stock  = intval($datos['stock'] ?? 0);
        $minimo = intval($datos['stock_minimo'] ?? 1);

        $errorNumeros = $this->validarNumeros($precio, $stock, $minimo);
        if ($errorNumeros) {
            return ['error', $errorNumeros];
        }

        [$imagenNueva, $errorImagen] = $this->subirImagen($files['imagen'] ?? null);
        if ($errorImagen) {
            return ['error', $errorImagen];
        }

        // Si no se subió una imagen nueva, se conserva la que ya tenía el producto.
        if (!$imagenNueva) {
            $actual     = $this->producto->obtener((int) $datos['no_producto']);
            $imagenNueva = $actual['imagen'] ?? '';
        }

        $this->producto->setNo_producto(intval($datos['no_producto']));
        $this->producto->setNombre(trim($datos['nombre']));
        $this->producto->setDescripcion(trim($datos['descripcion'] ?? ''));
        $this->producto->setCategoria($datos['categoria']);
        $this->producto->setPrecio($precio);
        $this->producto->setStock($stock);
        $this->producto->setStock_minimo($minimo);
        $this->producto->setImagen($imagenNueva);
        $this->producto->setDestacado(isset($datos['destacado']));

        return $this->producto->editar()
            ? ['exito', 'Producto actualizado correctamente.']
            : ['error', 'No se pudo actualizar el producto.'];
    }

    /* Baja lógica */
    public function eliminarProducto(int $no): array
    {
        $this->producto->setNo_producto($no);
        return $this->producto->eliminar()
            ? ['exito', 'Producto dado de baja.']
            : ['error', 'No se pudo eliminar el producto.'];
    }

    /* -----------------------------------------------------------------
     *  Reabastecimiento de inventario: suma unidades al stock actual
     *  de un producto. Igual que en el resto del sistema: nunca se
     *  acepta una cantidad negativa o en cero, y el stock resultante
     *  nunca puede superar el tope máximo de inventario.
     * ----------------------------------------------------------------- */
    public function reabastecer(int $no, int $cantidad): array
    {
        if ($cantidad < 1) {
            return ['error', 'La cantidad a reabastecer debe ser mayor a cero.'];
        }

        $p = $this->producto->obtener($no);
        if (!$p) {
            return ['error', 'El producto no existe.'];
        }

        $stockActual = (int) $p['stock'];
        if ($stockActual >= self::STOCK_MAXIMO) {
            return ['error', 'Este producto ya alcanzó el máximo de inventario permitido (' . number_format(self::STOCK_MAXIMO) . ' unidades).'];
        }

        // No se permite rebasar el tope máximo: se ajusta la cantidad si es necesario.
        $cantidadReal = min($cantidad, self::STOCK_MAXIMO - $stockActual);

        $ok = $this->producto->incrementarStock($no, $cantidadReal);
        if (!$ok) {
            return ['error', 'No se pudo actualizar el inventario. Intenta de nuevo.'];
        }

        $mensaje = 'Se agregaron ' . $cantidadReal . ' unidades a «' . $p['nombre'] . '». Nuevo stock: ' . ($stockActual + $cantidadReal) . '.';
        if ($cantidadReal < $cantidad) {
            $mensaje .= ' (Se ajustó la cantidad para no superar el máximo de inventario.)';
        }
        return ['exito', $mensaje];
    }
}
