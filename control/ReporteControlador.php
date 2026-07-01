<?php
/* =====================================================================
 *  control/ReporteControlador.php
 * =====================================================================
 *  Genera las estadísticas que se muestran en el panel administrativo
 *  (Ilustraciones 11 y 12 del documento): tarjetas resumen, ventas
 *  diarias, ventas por categoría, productos más vendidos y pedidos por
 *  estado. Usa pipelines de agregación de MongoDB.
 * ===================================================================== */

class ReporteControlador
{
    private Conexion $con;

    public function __construct()
    {
        $this->con = new Conexion();
    }

    /* Tarjetas superiores: ventas totales, pedidos, productos, stock total */
    public function resumen(): array
    {
        // Ventas totales (suma de total de todos los pedidos)
        $ventas = $this->con->agregar('pedido', [
            ['$group' => ['_id' => null, 'total' => ['$sum' => '$total']]],
        ]);
        $totalVentas = $ventas[0]['total'] ?? 0;

        // Stock total (suma de stock de productos activos)
        $stock = $this->con->agregar('producto', [
            ['$match' => ['estatus' => true]],
            ['$group' => ['_id' => null, 'total' => ['$sum' => '$stock']]],
        ]);
        $stockTotal = $stock[0]['total'] ?? 0;

        return [
            'ventas_totales' => $totalVentas,
            'pedidos'        => $this->con->contar('pedido'),
            'productos'      => $this->con->contar('producto', ['estatus' => true]),
            'stock_total'    => $stockTotal,
        ];
    }

    /* Ventas agrupadas por día (gráfica de líneas) */
    public function ventasDiarias(): array
    {
        return $this->con->agregar('pedido', [
            ['$group' => ['_id' => '$fecha', 'total' => ['$sum' => '$total']]],
            ['$sort'  => ['_id' => 1]],
        ]);
    }

    /* Ventas agrupadas por categoría (gráfica de pastel) */
    public function ventasPorCategoria(): array
    {
        return $this->con->agregar('pedido', [
            ['$unwind' => '$items'],
            ['$lookup' => [
                'from'         => 'producto',
                'localField'   => 'items.no_producto',
                'foreignField' => 'no_producto',
                'as'           => 'prod',
            ]],
            ['$unwind' => '$prod'],
            ['$group'  => [
                '_id'   => '$prod.categoria',
                'total' => ['$sum' => ['$multiply' => ['$items.cantidad', '$items.precio']]],
            ]],
            ['$sort'   => ['total' => -1]],
        ]);
    }

    /* Productos más vendidos (gráfica de barras) */
    public function masVendidos(int $limite = 5): array
    {
        return $this->con->agregar('producto', [
            ['$match' => ['estatus' => true]],
            ['$sort'  => ['ventas' => -1]],
            ['$limit' => $limite],
            ['$project' => ['_id' => 0, 'nombre' => 1, 'ventas' => 1]],
        ]);
    }

    /* Cantidad de pedidos por estado (barras horizontales) */
    public function pedidosPorEstado(): array
    {
        return $this->con->agregar('pedido', [
            ['$group' => ['_id' => '$estado', 'cantidad' => ['$sum' => 1]]],
        ]);
    }
}
