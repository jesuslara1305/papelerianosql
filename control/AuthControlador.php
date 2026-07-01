<?php
/* =====================================================================
 *  control/AuthControlador.php
 * =====================================================================
 *  Maneja el inicio de sesión (cliente y administrador), el registro de
 *  nuevos clientes y el cierre de sesión. Guarda los datos del usuario
 *  autenticado en $_SESSION para controlar el acceso a las vistas.
 * ===================================================================== */

class AuthControlador
{
    private Usuario $usuario;

    public function __construct()
    {
        $this->usuario = new Usuario();
    }

    /* -----------------------------------------------------------------
     *  LOGIN. $datos = $_POST con email, password y rol.
     *  Devuelve [tipo, mensaje, destino].
     * ----------------------------------------------------------------- */
    public function login(array $datos): array
    {
        $email = trim($datos['email'] ?? '');
        $pass  = $datos['password'] ?? '';
        $rol   = $datos['rol'] ?? 'cliente';

        if ($email === '' || $pass === '') {
            return ['error', 'Ingresa tu correo y contraseña.', ''];
        }

        $u = $this->usuario->verificar($email, $pass, $rol);
        if (!$u) {
            return ['error', 'Correo o contraseña incorrectos.', ''];
        }

        // Guarda la sesión
        $_SESSION['no_usuario'] = $u['no_usuario'];
        $_SESSION['nombre']     = $u['nombre'];
        $_SESSION['email']      = $u['email'];
        $_SESSION['rol']        = $u['rol'];

        $destino = ($u['rol'] === 'admin') ? 'admin' : 'inicio';
        return ['exito', 'Bienvenido(a) ' . $u['nombre'], $destino];
    }

    /* -----------------------------------------------------------------
     *  REGISTRO de un cliente nuevo.
     * ----------------------------------------------------------------- */
    public function registrar(array $datos): array
    {
        $nombre = trim($datos['nombre'] ?? '');
        $email  = trim($datos['email'] ?? '');
        $pass   = $datos['password'] ?? '';

        if ($nombre === '' || $email === '' || $pass === '') {
            return ['error', 'Todos los campos son obligatorios.'];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['error', 'El correo no tiene un formato válido.'];
        }
        if (strlen($pass) < 6) {
            return ['error', 'La contraseña debe tener al menos 6 caracteres.'];
        }

        $this->usuario->setNombre($nombre);
        $this->usuario->setEmail($email);
        $this->usuario->setPassword($pass);
        $this->usuario->setRol('cliente');

        $id = $this->usuario->insertar();
        return $id
            ? ['exito', 'Cuenta creada. Ya puedes iniciar sesión.']
            : ['error', 'Ese correo ya está registrado.'];
    }

    /* Cierra la sesión actual */
    public function logout(): void
    {
        session_unset();
        session_destroy();
    }

    /* Helpers de control de acceso */
    public static function autenticado(): bool { return isset($_SESSION['no_usuario']); }
    public static function esAdmin(): bool     { return (($_SESSION['rol'] ?? '') === 'admin'); }
}
