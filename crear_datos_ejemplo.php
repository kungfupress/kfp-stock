<?php
/** Archivo: crear_datos_ejemplo.php */

/**
 * Inserta datos de ejemplo en las tablas de la base de datos
 *
 * @return void
 */
function Kfp_Stock_Crear_Datos_ejemplo()
{
    global $wpdb;
    $tabla_producto = $wpdb->prefix . 'producto';
    
    $wpdb->insert( 
        $tabla_producto, 
        array( 
            'id' => 1,
            'nombre' => 'Cuaderno', 
            'cantidad' => 20, 
            ) 
    );

    $wpdb->insert( 
        $tabla_producto, 
        array( 
            'id' => 2,
            'nombre' => 'Lápiz', 
            'cantidad' => 50, 
            ) 
    );

    $wpdb->insert( 
        $tabla_producto, 
        array( 
            'id' => 3,
            'nombre' => 'Sacapuntas', 
            'cantidad' => 10, 
            ) 
    );
}