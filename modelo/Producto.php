<?php
/* =====================================================================
 *  modelo/Producto.php
 * =====================================================================
 *  Representa un producto de la papelería. Mantiene el mismo estilo del
 *  proyecto de referencia (atributos privados + getters/setters) pero el
 *  almacenamiento es en la colección "producto" de MongoDB.
 *
 *  Documento típico:
 *  {
 *     no_producto: 1,
 *     nombre: "Hello Kitty Peluche Grande",
 *     descripcion: "...",
 *     categoria: "Peluches",
 *     precio: 29.99,
 *     stock: 45,
 *     stock_minimo: 5,
 *     ventas: 123,
 *     rating: 5,
 *     imagen: "https://...",
 *     destacado: true,
 *     estatus: true
 *  }
 * ===================================================================== */

class Producto
{
    private $no_producto;
    private $nombre;
    private $descripcion;
    private $categoria;
    private $precio;
    private $stock;
    private $stock_minimo;
    private $ventas;
    private $rating;
    private $imagen;
    private $destacado;
    private $conexion;

    const COLECCION = 'producto';

    public function __construct()
    {
        $this->conexion = new Conexion();
    }

    public function getConexion() { return $this->conexion; }

    /* ------------------- LECTURA ------------------- */

    /** Lista productos activos. $filtro y $opciones son opcionales. */
    public function listar(array $filtro = [], array $opciones = []): array
    {
        $filtro = array_merge(['estatus' => true], $filtro);
        if (empty($opciones['sort'])) {
            $opciones['sort'] = ['no_producto' => 1];
        }
        return $this->conexion->buscar(self::COLECCION, $filtro, $opciones);
    }

    /** Productos marcados como destacados (sección "Productos Destacados"). */
    public function destacados(int $limite = 6): array
    {
        return $this->listar(['destacado' => true], ['limit' => $limite]);
    }

    /** Productos de una categoría concreta. */
    public function porCategoria(string $categoria): array
    {
        return $this->listar(['categoria' => $categoria]);
    }

    /** Un producto por su número. */
    public function obtener(int $no)
    {
        return $this->conexion->buscarUno(self::COLECCION, ['no_producto' => $no]);
    }

    /** Productos con stock por debajo del mínimo (alertas de inventario). */
    public function stockBajo(): array
    {
        // $expr permite comparar dos campos del mismo documento
        return $this->conexion->buscar(self::COLECCION, [
            'estatus' => true,
            '$expr'   => ['$lte' => ['$stock', '$stock_minimo']],
        ]);
    }

    /* ------------------- ESCRITURA ------------------- */

    /** Inserta el producto actual. Devuelve el no_producto o false. */
    public function insertar()
    {
        // Genera un no_producto correlativo simple (max + 1)
        if ($this->no_producto === null) {
            $ultimos = $this->conexion->buscar(self::COLECCION, [], [
                'sort'  => ['no_producto' => -1],
                'limit' => 1,
            ]);
            $this->no_producto = (count($ultimos) > 0) ? ((int)$ultimos[0]['no_producto'] + 1) : 1;
        }

        $doc = [
            'no_producto'  => (int) $this->no_producto,
            'nombre'       => $this->nombre,
            'descripcion'  => $this->descripcion,
            'categoria'    => $this->categoria,
            'precio'       => (float) $this->precio,
            'stock'        => (int) $this->stock,
            'stock_minimo' => (int) $this->stock_minimo,
            'ventas'       => (int) ($this->ventas ?? 0),
            'rating'       => (float) ($this->rating ?? 0),
            'imagen'       => $this->imagen,
            'destacado'    => (bool) ($this->destacado ?? false),
            'estatus'      => true,
        ];

        $ok = $this->conexion->insertar(self::COLECCION, $doc);
        return $ok ? $this->no_producto : false;
    }

    /** Edita el producto identificado por no_producto. */
    public function editar(): bool
    {
        $cambios = ['$set' => [
            'nombre'       => $this->nombre,
            'descripcion'  => $this->descripcion,
            'categoria'    => $this->categoria,
            'precio'       => (float) $this->precio,
            'stock'        => (int) $this->stock,
            'stock_minimo' => (int) $this->stock_minimo,
            'imagen'       => $this->imagen,
            'destacado'    => (bool) $this->destacado,
        ]];
        $n = $this->conexion->actualizar(self::COLECCION,
            ['no_producto' => (int) $this->no_producto], $cambios);
        return $n >= 0;
    }

    /** Baja lógica (no borra el documento, solo cambia estatus). */
    public function eliminar(): bool
    {
        $n = $this->conexion->actualizar(self::COLECCION,
            ['no_producto' => (int) $this->no_producto],
            ['$set' => ['estatus' => false]]);
        return $n >= 0;
    }

    /** Descuenta unidades del stock y suma a ventas (al confirmar una venta). */
    public function descontarStock(int $no, int $cantidad): bool
    {
        $n = $this->conexion->actualizar(self::COLECCION,
            ['no_producto' => $no],
            ['$inc' => ['stock' => -$cantidad, 'ventas' => $cantidad]]);
        return $n >= 0;
    }

    /* ------------------- GETTERS / SETTERS ------------------- */
    public function setNo_producto($v) { $this->no_producto = $v; }
    public function getNo_producto()   { return $this->no_producto; }
    public function setNombre($v)      { $this->nombre = $v; }
    public function getNombre()        { return $this->nombre; }
    public function setDescripcion($v) { $this->descripcion = $v; }
    public function getDescripcion()   { return $this->descripcion; }
    public function setCategoria($v)   { $this->categoria = $v; }
    public function getCategoria()     { return $this->categoria; }
    public function setPrecio($v)      { $this->precio = $v; }
    public function getPrecio()        { return $this->precio; }
    public function setStock($v)       { $this->stock = $v; }
    public function getStock()         { return $this->stock; }
    public function setStock_minimo($v){ $this->stock_minimo = $v; }
    public function getStock_minimo()  { return $this->stock_minimo; }
    public function setVentas($v)      { $this->ventas = $v; }
    public function getVentas()        { return $this->ventas; }
    public function setRating($v)      { $this->rating = $v; }
    public function getRating()        { return $this->rating; }
    public function setImagen($v)      { $this->imagen = $v; }
    public function getImagen()        { return $this->imagen; }
    public function setDestacado($v)   { $this->destacado = $v; }
    public function getDestacado()     { return $this->destacado; }
}
