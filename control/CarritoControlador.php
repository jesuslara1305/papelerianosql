<?php
/* =====================================================================
 *  control/CarritoControlador.php
 * =====================================================================
 *  Gestiona el carrito de compras guardado en $_SESSION['carrito'].
 *  Cada item: [no_producto, nombre, categoria, precio, imagen, cantidad].
 *  Incluye validación de stock contra MongoDB en tiempo real.
 * ===================================================================== */

class CarritoControlador
{
    private Producto $producto;

    public function __construct()
    {
        $this->producto = new Producto();
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }
    }

    public function items(): array { return $_SESSION['carrito']; }

    public function totalItems(): int
    {
        return array_sum(array_column($_SESSION['carrito'], 'cantidad'));
    }

    public function subtotal(): float
    {
        $s = 0;
        foreach ($_SESSION['carrito'] as $i) {
            $s += $i['precio'] * $i['cantidad'];
        }
        return $s;
    }

    /* Agrega un producto. Valida que haya stock suficiente. */
    public function agregar(int $no, int $cantidad = 1): array
    {
        $p = $this->producto->obtener($no);
        if (!$p) {
            return ['error', 'El producto no existe.'];
        }

        $enCarrito = 0;
        foreach ($_SESSION['carrito'] as $i) {
            if ($i['no_producto'] === $no) { $enCarrito = $i['cantidad']; break; }
        }
        if (($enCarrito + $cantidad) > $p['stock']) {
            return ['error', 'No hay suficiente stock disponible.'];
        }

        $encontrado = false;
        foreach ($_SESSION['carrito'] as &$i) {
            if ($i['no_producto'] === $no) {
                $i['cantidad'] += $cantidad;
                $encontrado = true;
                break;
            }
        }
        unset($i);

        if (!$encontrado) {
            $_SESSION['carrito'][] = [
                'no_producto' => $no,
                'nombre'      => $p['nombre'],
                'categoria'   => $p['categoria'],
                'precio'      => (float) $p['precio'],
                'imagen'      => $p['imagen'],
                'cantidad'    => $cantidad,
            ];
        }
        return ['exito', 'Producto agregado al carrito.'];
    }

    /* Cambia la cantidad de un item (+/-) */
    public function actualizar(int $no, int $cantidad): void
    {
        foreach ($_SESSION['carrito'] as $k => &$i) {
            if ($i['no_producto'] === $no) {
                $i['cantidad'] = max(1, $cantidad);
            }
        }
        unset($i);
    }

    /* Quita un item del carrito */
    public function quitar(int $no): void
    {
        $_SESSION['carrito'] = array_values(array_filter(
            $_SESSION['carrito'],
            fn($i) => $i['no_producto'] !== $no
        ));
    }

    /* Vacía el carrito (al terminar la compra) */
    public function vaciar(): void
    {
        $_SESSION['carrito'] = [];
    }
}
