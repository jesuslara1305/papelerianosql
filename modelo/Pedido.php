<?php
/* =====================================================================
 *  modelo/Pedido.php
 * =====================================================================
 *  Representa un pedido/venta. Se guarda en la colección "pedido".
 *  Cada pedido incluye los productos comprados (array embebido), datos
 *  del cliente, totales y un estado del flujo:
 *      pendiente | en proceso | enviado | entregado
 *
 *  Documento típico:
 *  { no_pedido:1001, cliente:"María González", email:"maria@example.com",
 *    direccion:"...", ciudad:"...", cp:"...",
 *    items:[{no_producto:3, nombre:"Kuromi Mini Bolso", cantidad:1, precio:35.99}],
 *    subtotal:85.97, envio:0, total:85.97,
 *    estado:"entregado", fecha:"2026-04-08" }
 * ===================================================================== */

class Pedido
{
    private $no_pedido;
    private $cliente;
    private $email;
    private $direccion;
    private $ciudad;
    private $cp;
    private $items;
    private $subtotal;
    private $envio;
    private $total;
    private $estado;
    private $conexion;

    const COLECCION = 'pedido';

    public function __construct()
    {
        $this->conexion = new Conexion();
    }

    public function getConexion() { return $this->conexion; }

    /** Lista pedidos, opcionalmente filtrados por estado. */
    public function listar(string $estado = null): array
    {
        $filtro = [];
        if ($estado !== null && $estado !== '' && $estado !== 'todos') {
            $filtro['estado'] = $estado;
        }
        return $this->conexion->buscar(self::COLECCION, $filtro, [
            'sort' => ['no_pedido' => -1],
        ]);
    }

    /** Inserta un pedido nuevo. Devuelve no_pedido o false. */
    public function insertar()
    {
        $ultimos = $this->conexion->buscar(self::COLECCION, [], [
            'sort' => ['no_pedido' => -1], 'limit' => 1,
        ]);
        $this->no_pedido = (count($ultimos) > 0) ? ((int)$ultimos[0]['no_pedido'] + 1) : 1001;

        $doc = [
            'no_pedido' => (int) $this->no_pedido,
            'cliente'   => $this->cliente,
            'email'     => $this->email,
            'direccion' => $this->direccion,
            'ciudad'    => $this->ciudad,
            'cp'        => $this->cp,
            'items'     => $this->items,
            'subtotal'  => (float) $this->subtotal,
            'envio'     => (float) ($this->envio ?? 0),
            'total'     => (float) $this->total,
            'estado'    => $this->estado ?? 'pendiente',
            'fecha'     => date('Y-m-d'),
        ];

        return $this->conexion->insertar(self::COLECCION, $doc) ? $this->no_pedido : false;
    }

    /** Cambia el estado de un pedido (desde el panel de administración). */
    public function cambiarEstado(int $no, string $estado): bool
    {
        $n = $this->conexion->actualizar(self::COLECCION,
            ['no_pedido' => $no], ['$set' => ['estado' => $estado]]);
        return $n >= 0;
    }

    /* getters / setters */
    public function setCliente($v)   { $this->cliente = $v; }
    public function setEmail($v)     { $this->email = $v; }
    public function setDireccion($v) { $this->direccion = $v; }
    public function setCiudad($v)    { $this->ciudad = $v; }
    public function setCp($v)        { $this->cp = $v; }
    public function setItems($v)     { $this->items = $v; }
    public function setSubtotal($v)  { $this->subtotal = $v; }
    public function setEnvio($v)     { $this->envio = $v; }
    public function setTotal($v)     { $this->total = $v; }
    public function setEstado($v)    { $this->estado = $v; }
    public function getNo_pedido()   { return $this->no_pedido; }
}
