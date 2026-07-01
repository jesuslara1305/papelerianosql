<?php
/* =====================================================================
 *  modelo/Conexion.php  -  Capa de acceso a datos MongoDB
 * =====================================================================
 *  Esta clase encapsula TODA la comunicación con MongoDB usando el
 *  driver nativo de PHP (clase MongoDB\Driver\Manager). De esta forma
 *  no se necesita Composer ni la librería externa: basta con tener la
 *  extensión "mongodb" habilitada en php.ini (extension=mongodb).
 *
 *  Expone métodos sencillos (buscar / insertar / actualizar / eliminar
 *  / contar / agregar) para que los Modelos no tengan que escribir
 *  consultas BSON a mano. Es el equivalente NoSQL del Conexion.php
 *  basado en PDO que se usa en el proyecto de referencia ProyectoWeb.
 * ===================================================================== */

use MongoDB\Driver\Manager;
use MongoDB\Driver\Query;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Command;
use MongoDB\Driver\Exception\Exception as MongoException;

class Conexion
{
    private $manager;   // MongoDB\Driver\Manager
    private $db;        // nombre de la base de datos
    private $uri;       // cadena de conexión

    public function __construct()
    {
        $config = require __DIR__ . '/../config/config.php';
        $this->uri = $config['mongo_uri'];
        $this->db  = $config['db'];

        try {
            // Crea el administrador de conexión (no abre socket hasta usarse)
            $this->manager = new Manager($this->uri);
        } catch (MongoException $e) {
            die("Error al conectar con MongoDB: " . $e->getMessage());
        }
    }

    /* namespace completo: base.coleccion  (ej. productos.producto) */
    private function ns(string $coleccion): string
    {
        return $this->db . '.' . $coleccion;
    }

    /* -----------------------------------------------------------------
     *  BUSCAR  -  equivale a un SELECT
     *  $filtro   array asociativo con las condiciones, ej: ['categoria'=>'Peluches']
     *  $opciones array con: sort, limit, skip, projection
     *  Devuelve un array de documentos (cada uno como array asociativo).
     * ----------------------------------------------------------------- */
    public function buscar(string $coleccion, array $filtro = [], array $opciones = []): array
    {
        try {
            $query   = new Query($filtro, $opciones);
            $cursor  = $this->manager->executeQuery($this->ns($coleccion), $query);
            $cursor->setTypeMap([
                'root'     => 'array',
                'document' => 'array',
                'array'    => 'array',
            ]);
            return $cursor->toArray();
        } catch (MongoException $e) {
            error_log("Mongo buscar(): " . $e->getMessage());
            return [];
        }
    }

    /* Devuelve UN solo documento o null */
    public function buscarUno(string $coleccion, array $filtro = [], array $opciones = [])
    {
        $opciones['limit'] = 1;
        $res = $this->buscar($coleccion, $filtro, $opciones);
        return $res[0] ?? null;
    }

    /* -----------------------------------------------------------------
     *  INSERTAR  -  equivale a un INSERT
     *  Devuelve el _id insertado o false.
     * ----------------------------------------------------------------- */
    public function insertar(string $coleccion, array $documento)
    {
        try {
            $bulk = new BulkWrite();
            $id   = $bulk->insert($documento);
            $this->manager->executeBulkWrite($this->ns($coleccion), $bulk);
            // Si el documento ya traía _id propio, $id es null: devolvemos ese
            return $documento['_id'] ?? $id;
        } catch (MongoException $e) {
            error_log("Mongo insertar(): " . $e->getMessage());
            return false;
        }
    }

    /* Inserta varios documentos de golpe (útil para cargas masivas). */
    public function insertarVarios(string $coleccion, array $documentos): int
    {
        try {
            $bulk = new BulkWrite();
            foreach ($documentos as $doc) {
                $bulk->insert($doc);
            }
            $res = $this->manager->executeBulkWrite($this->ns($coleccion), $bulk);
            return $res->getInsertedCount();
        } catch (MongoException $e) {
            error_log("Mongo insertarVarios(): " . $e->getMessage());
            return 0;
        }
    }

    /* -----------------------------------------------------------------
     *  ACTUALIZAR  -  equivale a un UPDATE
     *  $cambios usa operadores Mongo, ej: ['$set' => ['stock' => 10]]
     * ----------------------------------------------------------------- */
    public function actualizar(string $coleccion, array $filtro, array $cambios, bool $varios = false): int
    {
        try {
            $bulk = new BulkWrite();
            $bulk->update($filtro, $cambios, ['multi' => $varios, 'upsert' => false]);
            $res = $this->manager->executeBulkWrite($this->ns($coleccion), $bulk);
            return $res->getModifiedCount();
        } catch (MongoException $e) {
            error_log("Mongo actualizar(): " . $e->getMessage());
            return 0;
        }
    }

    /* -----------------------------------------------------------------
     *  ELIMINAR  -  equivale a un DELETE
     * ----------------------------------------------------------------- */
    public function eliminar(string $coleccion, array $filtro, bool $varios = false): int
    {
        try {
            $bulk = new BulkWrite();
            $bulk->delete($filtro, ['limit' => $varios ? 0 : 1]);
            $res = $this->manager->executeBulkWrite($this->ns($coleccion), $bulk);
            return $res->getDeletedCount();
        } catch (MongoException $e) {
            error_log("Mongo eliminar(): " . $e->getMessage());
            return 0;
        }
    }

    /* -----------------------------------------------------------------
     *  CONTAR documentos que cumplen un filtro.
     * ----------------------------------------------------------------- */
    public function contar(string $coleccion, array $filtro = []): int
    {
        try {
            $cmd = new Command([
                'count' => $coleccion,
                'query' => (object) $filtro,
            ]);
            $res = $this->manager->executeCommand($this->db, $cmd);
            $arr = current($res->toArray());
            return isset($arr->n) ? (int) $arr->n : 0;
        } catch (MongoException $e) {
            error_log("Mongo contar(): " . $e->getMessage());
            return 0;
        }
    }

    /* -----------------------------------------------------------------
     *  AGREGAR  -  pipeline de agregación (para reportes y estadísticas)
     *  $pipeline es el arreglo de etapas ($group, $sort, etc.)
     * ----------------------------------------------------------------- */
    public function agregar(string $coleccion, array $pipeline): array
    {
        try {
            $cmd = new Command([
                'aggregate' => $coleccion,
                'pipeline'  => $pipeline,
                'cursor'    => new \stdClass(),
            ]);
            $cursor = $this->manager->executeCommand($this->db, $cmd);
            $cursor->setTypeMap([
                'root'     => 'array',
                'document' => 'array',
                'array'    => 'array',
            ]);
            return $cursor->toArray();
        } catch (MongoException $e) {
            error_log("Mongo agregar(): " . $e->getMessage());
            return [];
        }
    }
}
