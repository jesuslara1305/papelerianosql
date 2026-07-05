<?php
/* =====================================================================
 *  CONFIGURACIÓN GLOBAL  -  SanrioShop ERP
 *  Sistema de Información para Papelería (MongoDB / NoSQL)
 * =====================================================================
 *  Aquí se concentran los parámetros de conexión a la base de datos
 *  MongoDB y la ruta base de la aplicación. Si mueves el proyecto a
 *  un hosting o cambias el nombre de la carpeta, solo modificas aquí.
 * ===================================================================== */

return [

    /* --- Conexión a MongoDB ----------------------------------------
     * Por defecto apunta al servidor local que ya tienes corriendo:
     *   mongodb://127.0.0.1:27017
     * En Hostinger / Atlas reemplaza la URI por la cadena que te den,
     * por ejemplo: mongodb+srv://usuario:pass@cluster.mongodb.net
     */
    "mongo_uri"  => "mongodb://127.0.0.1:27017",

    /* Nombre de la base de datos. En tu consola hiciste `use productos`,
     * así que dejamos "productos" como base principal. */
    "db"         => "productos",

    /* --- Ruta base de la app ---------------------------------------
     * YA NO ES NECESARIO TOCAR ESTO NI QUE COINCIDA CON EL NOMBRE DE
     * LA CARPETA. La app detecta sola la ruta donde se publicó (ver
     * función base() en helpers/Helpers.php), así que da igual si tu
     * carpeta se llama SanrioShop, sanrioshop, papelerianosql, etc.
     * Se deja aquí solo como referencia / respaldo, ya sin uso real.
     */
    "base_url"   => "/sanrioshop",

    /* Zona horaria para fechas de pedidos y reportes */
    "timezone"   => "America/Mexico_City",
];
