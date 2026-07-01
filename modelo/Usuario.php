<?php
/* =====================================================================
 *  modelo/Usuario.php
 * =====================================================================
 *  Maneja tanto a los CLIENTES como al ADMINISTRADOR. Se guarda en la
 *  colección "usuario". El campo "rol" distingue: 'cliente' | 'admin'.
 *
 *  Documento típico:
 *  { no_usuario:1, nombre:"María González", email:"cliente@sanrio.com",
 *    password:"<hash>", rol:"cliente", estatus:true }
 *
 *  NOTA de seguridad: las contraseñas se guardan con password_hash()
 *  y se verifican con password_verify(), nunca en texto plano.
 * ===================================================================== */

class Usuario
{
    private $no_usuario;
    private $nombre;
    private $email;
    private $password;
    private $rol;
    private $conexion;

    const COLECCION = 'usuario';

    public function __construct()
    {
        $this->conexion = new Conexion();
    }

    /** Busca un usuario por email y rol. Devuelve el documento o null. */
    public function buscarPorEmail(string $email, string $rol = null)
    {
        $filtro = ['email' => mb_strtolower(trim($email)), 'estatus' => true];
        if ($rol !== null) {
            $filtro['rol'] = $rol;
        }
        return $this->conexion->buscarUno(self::COLECCION, $filtro);
    }

    /** Registra un nuevo usuario. Devuelve no_usuario o false. */
    public function insertar()
    {
        // Evita duplicados de email
        if ($this->buscarPorEmail($this->email)) {
            return false;
        }

        $ultimos = $this->conexion->buscar(self::COLECCION, [], [
            'sort' => ['no_usuario' => -1], 'limit' => 1,
        ]);
        $this->no_usuario = (count($ultimos) > 0) ? ((int)$ultimos[0]['no_usuario'] + 1) : 1;

        $doc = [
            'no_usuario' => (int) $this->no_usuario,
            'nombre'     => $this->nombre,
            'email'      => mb_strtolower(trim($this->email)),
            'password'   => password_hash($this->password, PASSWORD_DEFAULT),
            'rol'        => $this->rol ?? 'cliente',
            'estatus'    => true,
        ];

        return $this->conexion->insertar(self::COLECCION, $doc) ? $this->no_usuario : false;
    }

    /** Verifica credenciales. Devuelve el documento del usuario o false. */
    public function verificar(string $email, string $password, string $rol)
    {
        $usuario = $this->buscarPorEmail($email, $rol);
        if ($usuario && password_verify($password, $usuario['password'])) {
            return $usuario;
        }
        return false;
    }

    /* getters / setters */
    public function setNombre($v)   { $this->nombre = $v; }
    public function setEmail($v)    { $this->email = $v; }
    public function setPassword($v) { $this->password = $v; }
    public function setRol($v)      { $this->rol = $v; }
    public function getNo_usuario() { return $this->no_usuario; }
}
