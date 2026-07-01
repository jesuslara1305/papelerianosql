<?php
/* =====================================================================
 *  control/ProductoControlador.php
 * =====================================================================
 *  Orquesta la lógica de negocio de productos: validaciones, alta/baja,
 *  edición y consultas que usan las vistas. Sigue el mismo patrón del
 *  proyecto de referencia (el controlador "envuelve" al modelo).
 * ===================================================================== */

class ProductoControlador
{
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
     *  Alta de producto desde el panel administrativo.
     *  $datos es típicamente $_POST. Devuelve [tipo, mensaje].
     * ----------------------------------------------------------------- */
    public function agregarProducto(array $datos): array
    {
        if (empty($datos['nombre']) || empty($datos['precio']) || empty($datos['categoria'])) {
            return ['error', 'Nombre, precio y categoría son obligatorios.'];
        }

        $precio = floatval($datos['precio']);
        $stock  = intval($datos['stock'] ?? 0);
        $minimo = intval($datos['stock_minimo'] ?? 0);

        if ($precio <= 0) {
            return ['error', 'El precio debe ser mayor a cero.'];
        }
        if ($stock < $minimo) {
            return ['error', 'El stock inicial no puede ser menor al stock mínimo.'];
        }

        $this->producto->setNombre(trim($datos['nombre']));
        $this->producto->setDescripcion(trim($datos['descripcion'] ?? ''));
        $this->producto->setCategoria($datos['categoria']);
        $this->producto->setPrecio($precio);
        $this->producto->setStock($stock);
        $this->producto->setStock_minimo($minimo);
        $this->producto->setImagen(trim($datos['imagen'] ?? ''));
        $this->producto->setDestacado(isset($datos['destacado']));
        $this->producto->setVentas(0);
        $this->producto->setRating(floatval($datos['rating'] ?? 5));

        $id = $this->producto->insertar();
        return $id
            ? ['exito', 'Producto «' . $datos['nombre'] . '» registrado correctamente.']
            : ['error', 'No se pudo registrar el producto. Intenta de nuevo.'];
    }

    /* Edición de producto existente */
    public function editarProducto(array $datos): array
    {
        if (empty($datos['no_producto'])) {
            return ['error', 'Producto no identificado.'];
        }
        $this->producto->setNo_producto(intval($datos['no_producto']));
        $this->producto->setNombre(trim($datos['nombre']));
        $this->producto->setDescripcion(trim($datos['descripcion'] ?? ''));
        $this->producto->setCategoria($datos['categoria']);
        $this->producto->setPrecio(floatval($datos['precio']));
        $this->producto->setStock(intval($datos['stock'] ?? 0));
        $this->producto->setStock_minimo(intval($datos['stock_minimo'] ?? 0));
        $this->producto->setImagen(trim($datos['imagen'] ?? ''));
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
}
