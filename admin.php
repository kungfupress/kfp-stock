<?php
/** Archivo: admin.php */

/**
 * Crea el formulario
 *
 * @return void
 */
function Kfp_Stock_Admin_form()
{
    global $wpdb;
    wp_enqueue_style('css_form_mania', plugins_url('style.css', __FILE__));

    if (!empty($_POST) && $_POST['cantidad'] > 0 && $_POST['id_producto'] != '') {
        $tabla_producto = $wpdb->prefix . 'producto';
        $cantidad = (int)$_POST['cantidad'];
        $id_producto = (int)$_POST['id_producto'];
        $consulta_cantidad_actual = "SELECT cantidad FROM $tabla_producto 
            WHERE id = $id_producto"; 
        $cantidad_actual = $wpdb->get_var($consulta_cantidad_actual);
        $cantidad_total = $cantidad_actual + $cantidad;
        $wpdb->update(
            $tabla_producto, 
            array( 'column' => 'cantidad', 'field' => $cantidad_total ), 
            array( 'id' => $id_producto ) 
        );
    }
    // Trae las marcas de dispositivos de la base de datos
    $tabla_producto = $wpdb->prefix . 'producto';
    $productos = $wpdb->get_results(
        "SELECT * from $tabla_producto ORDER BY nombre"
    );    
    ob_start();
    ?>
    <h3>Adquirir productos</h3>
    <form action="<?php get_the_permalink(); ?>" method="post"
        class="cuestionario">
        <div class="form-input">
            <label for="producto">Producto</label>
            <select name="id_producto" id="id_producto" required>
            <option value="">Selecciona el producto</option>
                <?php
                foreach ($productos as $producto) {
                    echo("<option value='$producto->id'>$producto->nombre</option>)");
                }
                ?>
            </select>
        </div>
        <div class="form-input">
            <label for='cantidad'>Cantidad</label>
            <input type="number" name="cantidad" id="cantidad" required>
        </div>
        <div class="form-input">
            <input type="submit" value="Comprar">
        </div>
    </form>
    <?php
    return ob_get_clean();
}