<?php
    if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();
    global $wpdb;

    $tabla_producto = $wpdb->prefix . 'producto';
    $wpdb->query("DROP TABLE IF EXISTS $tabla_producto" );