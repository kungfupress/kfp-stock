<?php
/** 
 * Archivo: crear-tablas.php 
 */

/**
 * Crea las tablas necesarias durante la activación del plugin
 *
 * @return void
 */
function Kfp_Stock_Crear_tablas()
{
    global $wpdb;
    $sql = array(); 
    $tabla_producto = $wpdb->prefix . 'producto';
    $charset_collate = $wpdb->get_charset_collate();
    
    // Consulta para crear las tablas
    // Mas adelante utiliza dbDelta, si la tabla ya existe no la crea sino que la
    // modifica con los posibles cambios y sin pérdida de datos
    $sql[] = "CREATE TABLE $tabla_producto (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        nombre varchar(100) NOT NULL,
        cantidad mediumint(9),
        PRIMARY KEY (id)
        ) $charset_collate";
    
    include_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}