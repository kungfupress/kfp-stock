<?php
/**
 * KFP Stock
 * 
 * @wordpress-plugin
 * Plugin Name: KFP Stock
 * Author: KungFuPress
 * Version: 1.0
 * Description: Prueba de concepto para implementar un control de stock
 * Author URI: https://kungfupress.com
 */

//  Salir si se intenta acceder directamente
if (! defined('ABSPATH')) {
    exit();
}

$ruta_plugin = plugin_dir_path(__FILE__);

require_once $ruta_plugin . 'crear_tablas.php';
require_once $ruta_plugin . 'crear_datos_ejemplo.php';

register_activation_hook(__FILE__, 'Kfp_Stock_Crear_tablas');
register_activation_hook(__FILE__, 'Kfp_Stock_Crear_Datos_ejemplo');