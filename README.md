# SanrioShop — ERP para Papelería (PHP + MongoDB, patrón MVC)

Sistema de Información (ERP) para la papelería **Sanrio Shop**. Replica las 12 interfaces
del documento del proyecto, conserva la estética rosa/morado kawaii de la página de
referencia y sigue la arquitectura **Modelo-Vista-Controlador** del proyecto `ProyectoWeb`,
pero usando una base de datos **NoSQL (MongoDB)** en lugar de SQL.

---

## 1. Requisitos

- **PHP 8.0 o superior** (con un servidor web: Apache/XAMPP, o el servidor embebido de PHP).
- **MongoDB** corriendo localmente en `mongodb://127.0.0.1:27017` (ya lo tienes instalado: MongoDB 8.3.4).
- **Extensión `mongodb` de PHP** habilitada.

### Habilitar la extensión de MongoDB en PHP

1. Descarga el driver desde https://pecl.php.net/package/mongodb (DLL para Windows que
   coincida con tu versión de PHP, arquitectura x64 y Thread Safe/NTS según tu `php.ini`).
2. Copia el archivo `php_mongodb.dll` a la carpeta `ext/` de tu instalación de PHP.
3. En tu `php.ini` agrega esta línea:
   ```
   extension=mongodb
   ```
4. Reinicia Apache / el servidor.
5. Verifica con `php -m` (debe aparecer **mongodb** en la lista).

> En XAMPP el `php.ini` está en `xampp/php/php.ini` y la carpeta de extensiones en `xampp/php/ext/`.

---

## 2. Instalación del proyecto

1. Copia la carpeta `SanrioShop` dentro de la raíz web de tu servidor:
   - XAMPP/Apache → `htdocs/sanrioshop`
   - (la carpeta debe llamarse `sanrioshop` para que coincida con la configuración)

2. Revisa `config/config.php`. Por defecto ya está listo para tu entorno:
   ```php
   "mongo_uri" => "mongodb://127.0.0.1:27017",  // tu servidor MongoDB local
   "db"        => "productos",                   // la base que usaste con: use productos
   "base_url"  => "/sanrioshop",                 // carpeta donde publicaste el proyecto
   ```
   - Si publicas en la **raíz del dominio**, deja `"base_url" => ""`.
   - En Hostinger/Atlas cambia `mongo_uri` por la cadena `mongodb+srv://...` que te den.

---

## 3. Cargar los datos demo (seed)

El script `seed/seed.php` inserta en MongoDB los productos, usuarios y pedidos de ejemplo.

**Opción A — por terminal** (recomendada), dentro de la carpeta del proyecto:
```
php seed/seed.php
```

**Opción B — por navegador:**
```
http://localhost/sanrioshop/seed/seed.php
```

Es seguro ejecutarlo varias veces: limpia y recarga las colecciones desde cero.

Después de cargarlo puedes verificar en `mongosh`:
```
use productos
db.producto.find()
db.usuario.find()
db.pedido.find()
```

---

## 4. Usar el sistema

Abre en el navegador:
```
http://localhost/sanrioshop/
```

### Credenciales de prueba

| Rol           | Email                | Contraseña  |
|---------------|----------------------|-------------|
| Cliente       | cliente@sanrio.com   | cliente123  |
| Administrador | admin@sanrio.com     | admin123    |

- Como **cliente** puedes navegar el catálogo, agregar al carrito, capturar el envío y el
  pago, y generar un pedido (esto descuenta el stock en tiempo real).
- Como **administrador** entras al **Panel de Administración** con: gestión de productos
  y stock (alta/edición/baja con alertas de stock bajo), gestión de pedidos (cambio de
  estado) y reportes con gráficas (ventas diarias, ventas por categoría, productos más
  vendidos y pedidos por estado).

---

## 5. Estructura del proyecto (MVC)

```
SanrioShop/
│  index.php            ← Front controller: recibe acciones POST y delega al router
│  .htaccess            ← Reescritura de URLs hacia index.php
│
├─ config/
│   config.php          ← Conexión a MongoDB y ruta base
│
├─ modelo/              ← MODELO (datos)
│   Conexion.php        ← Capa de acceso a MongoDB (driver nativo)
│   Producto.php
│   Usuario.php
│   Pedido.php
│
├─ control/             ← CONTROLADOR (lógica)
│   router.php          ← Enruta las vistas según ?url=
│   AuthControlador.php
│   ProductoControlador.php
│   PedidoControlador.php
│   CarritoControlador.php
│   ReporteControlador.php
│
├─ vista/               ← VISTA (interfaces HTML/CSS)
│   header_gral.php / footer_gral.php
│   inicio.php          ← Ilustraciones 2, 4, 5
│   login.php           ← Ilustración 3
│   productos.php
│   carrito.php         ← Ilustración 6
│   checkout/           ← Ilustraciones 7 y 8 (envío y pago)
│   admin/              ← Ilustraciones 9, 10, 11, 12 (panel admin y reportes)
│
├─ helpers/Helpers.php  ← Funciones de apoyo (rutas, escape, precios, flash)
├─ estilos/styles.css   ← Tema Sanrio (rosa/morado)
├─ public/img/          ← Logo e imágenes locales
└─ seed/seed.php        ← Carga de datos demo en MongoDB
```

### Equivalencia con el proyecto de referencia (ProyectoWeb)

| ProyectoWeb (SQL/PDO)      | SanrioShop (MongoDB)                  |
|----------------------------|---------------------------------------|
| `index.php` front controller | `index.php` (igual)                 |
| `control/navbar.php` (router) | `control/router.php`               |
| `modelo/Conexion.php` (PDO) | `modelo/Conexion.php` (driver Mongo)  |
| Modelos con getters/setters | Modelos con getters/setters (igual)   |
| Tablas SQL                  | Colecciones: `producto`, `usuario`, `pedido` |

---

## 6. Tecnologías usadas

- **Backend:** PHP (patrón MVC), sesiones para login y carrito.
- **Base de datos:** MongoDB (NoSQL), driver nativo de PHP (sin Composer).
- **Frontend:** HTML5, CSS3, Bootstrap 5.3.2, Font Awesome 6.5.1, Chart.js 4.4.1.
- **Seguridad:** contraseñas cifradas con `password_hash()` / `password_verify()`.
