<?php
/** Archivo: admin.php */

define('KFP_STOCK_MENU_SLUG', 'kfp_stock');

// Aquí comienza la parte administrativa del plugin
add_action("admin_menu", "Kfp_Stock_menu");
add_action("admin_post_kfp-stock-comprar", "Kfp_Stock_Procesa_Form_compra");
add_action("admin_post_kfp-stock-vender", "Kfp_Stock_Procesa_Form_venta");
add_action("admin_notices", "kfp_stock_admin_notices");

/**
 * Agrega el menú del plugin al panel de administración
 *
 * @return void
 */
function Kfp_Stock_menu() 
{
    add_menu_page(
        'Stock', 'Stock', 'manage_options', KFP_STOCK_MENU_SLUG, 'Kfp_Stock_admin', 
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
    echo Kfp_Stock_Form_comprar();
    echo Kfp_Stock_Form_vender();
    echo '</div>';
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
function Kfp_Stock_Form_comprar()
{
    global $wpdb;
    $tabla_producto = $wpdb->prefix . 'producto';
    // Recupera los productos para el select del formulario
    $productos = $wpdb->get_results(
        "SELECT * from $tabla_producto ORDER BY nombre"
    ); 
    ob_start();
    ?>
    <h2>Comprar productos</h2>
    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" 
        method="post" id="kfp-stock-form-comprar">
        <input type="hidden" name="action" value="kfp-stock-comprar">
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
function Kfp_Stock_Form_vender()
{
    global $wpdb;
    $tabla_producto = $wpdb->prefix . 'producto';   
    // Recupera los productos para el select del formulario si tienen stock
    $productos = $wpdb->get_results(
        "SELECT * from $tabla_producto WHERE cantidad > 0 ORDER BY nombre"
    );    
    ob_start();
    ?>
    <h2>Vender productos</h2>
    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" 
        method="post" id="kfp-stock-form-vender">
        <input type="hidden" name="action" value="kfp-stock-vender">
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

/**
 * Procesa el formulario de compra
 *
 * @return void
 */
function Kfp_Stock_Procesa_Form_compra()
{
    global $wpdb;
    $tabla_producto = $wpdb->prefix . 'producto';
    //Si viene un envío del formulario graba los cambios en la tabla
    if (!empty($_POST) 
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
        $aviso = "success";
        $texto_aviso = "Se ha realizado una compra correctamente";
        wp_redirect(
            esc_url_raw(
                add_query_arg(
                    array(
                        'kfp_stock_aviso' => $aviso,
                        'kfp_stock_texto_aviso' => $texto_aviso,
                    ),
                    admin_url('admin.php?page='. KFP_STOCK_MENU_SLUG) 
                ) 
            ) 
        );
        exit();
    } else {
        wp_die();
    }
}

/**
 * Procesa el formulario de venta
 * Sigue una estrategia distinta al de compra a la hora de grabar los datos
 * para dejar documentadas dos opciones distintas
 *
 * @return void
 */
function Kfp_Stock_Procesa_Form_venta()
{
    global $wpdb;
    $tabla_producto = $wpdb->prefix . 'producto';   
    //Si viene un envío del formulario graba los cambios en la tabla
    if (!empty($_POST) 
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
        $aviso = "success";
        $texto_aviso = "Se ha realizado una venta correctamente";
        wp_redirect(
            esc_url_raw(
                add_query_arg(
                    array(
                        'kfp_stock_aviso' => $aviso,
                        'kfp_stock_texto_aviso' => $texto_aviso,
                    ),
                    admin_url('admin.php?page='. KFP_STOCK_MENU_SLUG) 
                ) 
            ) 
        );
        exit();
    } else {
        wp_die();
    }
}

/**
 * Muestra los avisos de éxito o error
 * 
 * @return void
 */
function Kfp_Stock_Admin_notices() 
{              
    if (isset($_REQUEST['kfp_stock_aviso'])) {
        if ($_REQUEST['kfp_stock_aviso'] === "success") {
            $html = '<div class="notice notice-success is-dismissible">'; 
            $html .= '<p>'; 
            $html .= htmlspecialchars($_REQUEST['kfp_stock_texto_aviso']); 
            $html .= '</p></div>';
            echo $html;
        }
        if ($_REQUEST['kfp_stock_aviso'] === "error") {
            $html = '<div class="notice notice-error is-dismissible">'; 
            $html .= '<p>'; 
            $html .= htmlspecialchars($_REQUEST['kfp_stock_texto_aviso']); 
            $html .= '</p></div>';
            echo $html;
        }
    } else {
        return;
    }
}