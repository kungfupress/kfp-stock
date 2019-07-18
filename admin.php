<?php
/** Archivo: admin.php */

// Aquí comienza la parte administrativa del plugin
add_action("admin_menu", "Kfp_Stock_menu");

/**
 * Agrega el menú del plugin al panel de administración
 *
 * @return void
 */
function Kfp_Stock_menu() 
{
    add_menu_page(
        'Stock', 'Stock', 'manage_options', 'kfp_stock_menu', 'Kfp_Stock_admin', 
        'dashicons-feedback'
    );
}

/**
 * Muestra el panel de administración
 *
 * @return void
 */
function Kfp_Stock_admin()
{
    echo '<div class="wrap"><h1>Stock de productos</h1>';
    Kfp_Stock_Tabla_productos();
    echo Kfp_Stock_Admin_Form_comprar();
    echo Kfp_Stock_Admin_Form_vender();
    echo '</div>';
    // Agrega el script
    wp_enqueue_script('kfp-stock-script', plugins_url('script.js', __FILE__));
}

/**
 * Muestra la tabla de productos
 *
 * @return void
 */
function Kfp_Stock_Tabla_productos()
{
    global $wpdb;
    $tabla_producto = $wpdb->prefix . 'producto';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th>Nombre</th><th>Cantidad</th></tr></thead>';
    echo '<tbody id="the-list">';
    $productos = $wpdb->get_results("SELECT * FROM $tabla_producto");
    foreach ( $productos as $producto ) {
        $nombre = esc_textarea($producto->nombre);
        $cantidad = esc_textarea($producto->cantidad);
        echo "<tr><td>$nombre</a></td><td>$cantidad</td></tr>";
    }
    echo '</tbody></table>';
}

/**
 * Muestra el formulario de compra
 *
 * @return void
 */
function Kfp_Stock_Admin_Form_comprar()
{
    global $wpdb;
    $tabla_producto = $wpdb->prefix . 'producto';
    //Si viene un envío del formulario graba los cambios en la tabla
    if (!empty($_POST) 
        && $_POST['accion'] == 'comprar' 
        && $_POST['cantidad'] > 0 
        && $_POST['id_producto'] != ''
    ) {
        $cantidad_comprada = (int)$_POST['cantidad'];
        $id_producto = (int)$_POST['id_producto'];
        $wpdb->query(
            $wpdb->prepare(
                "UPDATE `$tabla_producto` SET cantidad = cantidad + %d 
                    WHERE id = %d",
                $cantidad_comprada, $id_producto
            )
        ); 
    }
    // Recupera los productos para el select del formulario
    $productos = $wpdb->get_results(
        "SELECT * from $tabla_producto ORDER BY nombre"
    ); 
    ob_start();
    ?>
    <h2>Comprar productos</h2>
    <form action="" method="post" id="kfp-stock-form-comprar">
        <input type="hidden" name="accion" value="comprar">
        <p>
            <label for="producto">Producto</label>
            <select name="id_producto" id="kfp-stock-compra-producto" required>
                <option value="">Selecciona el producto</option>
                <?php
                foreach ($productos as $producto) {
                    echo("<option value='$producto->id'>$producto->nombre</option>)");
                }
                ?>
            </select>
        </p>
        <p>
            <label for='cantidad'>Cantidad</label>
            <input type="number" name="cantidad" id="kfp-stock-compra-cantidad" 
                required>
            </p>
        <p class="submit">
            <input type="submit" class="button button-primary" value="Comprar">
        </div>
    </form>
    <?php
    return ob_get_clean();
}

/**
 * Muestra el formulario de venta
 * Sigue una estrategia distinta al de compra a la hora de grabar los datos
 *
 * @return void
 */
function Kfp_Stock_Admin_Form_vender()
{
    global $wpdb;
    $tabla_producto = $wpdb->prefix . 'producto';
    
    //Si viene un envío del formulario graba los cambios en la tabla
    if (!empty($_POST) 
        && $_POST['accion'] == 'vender' 
        && $_POST['cantidad'] > 0 
        && $_POST['id_producto'] != ''
    ) {
        $cantidad_vendida = (int)$_POST['cantidad'];
        $id_producto = (int)$_POST['id_producto'];
        $consulta_cantidad_actual = "SELECT cantidad FROM $tabla_producto 
            WHERE id = $id_producto"; 
        $cantidad_actual = $wpdb->get_var($consulta_cantidad_actual);
        $cantidad_final = $cantidad_actual - $cantidad_vendida;
        $wpdb->update(
            $tabla_producto, 
            array( 'cantidad' => $cantidad_final ), 
            array( 'id' => $id_producto ) 
        );
    }
    // Recupera los productos para el select del formulario si tienen stock
    $productos = $wpdb->get_results(
        "SELECT * from $tabla_producto WHERE cantidad > 0 ORDER BY nombre"
    );    
    ob_start();
    ?>
    <h2>Vender productos</h2>
    <form action="" method="post" id="kfp-stock-form-vender">
        <input type="hidden" name="accion" value="vender">
        <p>
            <label for="producto">Producto</label>
            <select name="id_producto" id="kfp-stock-venta-producto" required>
                <option value="">Selecciona el producto</option>
                <?php
                foreach ($productos as $producto) {
                    echo("<option data-cantidad='$producto->cantidad' 
                        value='$producto->id'>$producto->nombre</option>)");
                }
                ?>
            </select>
        </p>
        <p>
            <label for='cantidad'>Cantidad</label>
            <input type="number" name="cantidad" id="kfp-stock-venta-cantidad" 
                required>
            </p>
        <p class="submit">
            <input type="submit" class="button button-primary" value="Vender">
        </div>
    </form>
    <?php
    return ob_get_clean();
}