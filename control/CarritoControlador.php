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
        // Nunca se acepta una cantidad negativa o en cero.
        $cantidad = max(1, $cantidad);

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

    /* Cambia la cantidad de un item (+/-).
     * Igual que en ProyectoWeb: nunca se aceptan cantidades negativas
     * ni en cero (mínimo 1 unidad), y nunca se permite superar el
     * stock real disponible en MongoDB (se "clampa" al máximo). */
    public function actualizar(int $no, int $cantidad): void
    {
        $stockMax = $this->stockDisponible($no);

        foreach ($_SESSION['carrito'] as $k => &$i) {
            if ($i['no_producto'] === $no) {
                $cantidadSegura = max(1, $cantidad);
                if ($stockMax > 0) {
                    $cantidadSegura = min($cantidadSegura, $stockMax);
                }
                $i['cantidad'] = $cantidadSegura;
            }
        }
        unset($i);
    }

    /* Stock real disponible de un producto en MongoDB (0 si no existe). */
    public function stockDisponible(int $no): int
    {
        $p = $this->producto->obtener($no);
        return $p ? (int) $p['stock'] : 0;
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
