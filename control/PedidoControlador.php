<?php
/* =====================================================================
 *  control/PedidoControlador.php
 * =====================================================================
 *  Procesa el checkout: toma el carrito (guardado en sesión), valida
 *  stock, crea el pedido en MongoDB y descuenta el inventario en tiempo
 *  real (sincronización ventas <-> inventario, como pide el documento).
 * ===================================================================== */

class PedidoControlador
{
    private Pedido $pedido;
    private Producto $producto;

    public function __construct()
    {
        $this->pedido   = new Pedido();
        $this->producto = new Producto();
    }

    public function getPedido(): Pedido { return $this->pedido; }

    public function listar(string $estado = null): array
    {
        return $this->pedido->listar($estado);
    }

    /** Pedidos de un cliente específico, para su vista de Perfil. */
    public function listarPorCliente(string $email): array
    {
        return $this->pedido->listarPorEmail($email);
    }

    /* -----------------------------------------------------------------
     *  Crea un pedido a partir de los datos de envío/pago + carrito.
     *  $datos: datos de $_POST (nombre, email, direccion, ciudad, cp).
     *  $carrito: arreglo de items [{no_producto, nombre, precio, cantidad}].
     * ----------------------------------------------------------------- */
    public function crearPedido(array $datos, array $carrito): array
    {
        if (empty($carrito)) {
            return ['error', 'Tu carrito está vacío.', null];
        }
        if (empty($datos['nombre']) || empty($datos['direccion'])) {
            return ['error', 'Faltan datos de envío.', null];
        }

        // Valida stock disponible para cada item
        $subtotal = 0;
        foreach ($carrito as $item) {
            $p = $this->producto->obtener((int) $item['no_producto']);
            if (!$p) {
                return ['error', 'Un producto ya no existe en el catálogo.', null];
            }
            if ($p['stock'] < $item['cantidad']) {
                return ['error', 'Stock insuficiente para «' . $p['nombre'] . '».', null];
            }
            $subtotal += $item['precio'] * $item['cantidad'];
        }

        $this->pedido->setCliente($datos['nombre']);
        $this->pedido->setEmail($datos['email'] ?? '');
        $this->pedido->setDireccion($datos['direccion']);
        $this->pedido->setCiudad($datos['ciudad'] ?? '');
        $this->pedido->setCp($datos['cp'] ?? '');
        $this->pedido->setItems($carrito);
        $this->pedido->setSubtotal($subtotal);
        $this->pedido->setEnvio(0); // Envío gratis, como en las vistas del PDF

        // Total con IVA (16%), igual que en ProyectoWeb: $total += $total*0.16;
        $total = $subtotal;
        $total += $total * 0.16;
        $this->pedido->setTotal($total);
        $this->pedido->setEstado('pendiente');

        $no = $this->pedido->insertar();
        if (!$no) {
            return ['error', 'No se pudo registrar el pedido.', null];
        }

        // Descuenta inventario en tiempo real
        foreach ($carrito as $item) {
            $this->producto->descontarStock((int) $item['no_producto'], (int) $item['cantidad']);
        }

        return ['exito', 'Pedido #' . $no . ' registrado con éxito.', $no];
    }

    /* Cambia el estado de un pedido desde el panel admin */
    public function cambiarEstado(int $no, string $estado): array
    {
        $validos = ['pendiente', 'en proceso', 'enviado', 'entregado'];
        if (!in_array($estado, $validos, true)) {
            return ['error', 'Estado no válido.'];
        }
        return $this->pedido->cambiarEstado($no, $estado)
            ? ['exito', 'Estado actualizado.']
            : ['error', 'No se pudo actualizar el estado.'];
    }
}
