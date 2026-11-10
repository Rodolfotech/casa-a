<?php
/**
 * Datos extra del producto: unidades disponibles y galería de imágenes.
 * Los usa el modal de producto del theme.
 *
 * @package CASA_A_Catalogo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Metadatos de unidades y galería.
 */
function casa_a_producto_register_meta() {
	$auth = function () {
		return current_user_can( 'edit_posts' );
	};

	register_post_meta(
		CASA_A_CATALOGO_POST_TYPE,
		'casa_a_stock',
		array(
			'type'              => 'integer',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'absint',
			'auth_callback'     => $auth,
		)
	);
}
add_action( 'init', 'casa_a_producto_register_meta' );

/**
 * Cuadro "Inventario" en el editor de producto.
 */
function casa_a_producto_add_box() {
	add_meta_box(
		'casa_a_producto_inventario',
		__( 'Inventario', 'casa-a-catalogo' ),
		'casa_a_producto_render_box',
		CASA_A_CATALOGO_POST_TYPE,
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes', 'casa_a_producto_add_box' );

/**
 * Muestra el campo de unidades disponibles.
 *
 * @param WP_Post $post Producto que se edita.
 */
function casa_a_producto_render_box( $post ) {
	wp_nonce_field( 'casa_a_producto_inventario', 'casa_a_inventario_nonce' );

	$stock = get_post_meta( $post->ID, 'casa_a_stock', true );
	?>
	<p>
		<label for="casa_a_stock"><strong><?php esc_html_e( 'Unidades disponibles', 'casa-a-catalogo' ); ?></strong></label><br>
		<input type="number" id="casa_a_stock" name="casa_a_stock" class="widefat" min="0" step="1" value="<?php echo esc_attr( $stock ); ?>">
	</p>
	<?php
}

/**
 * Guarda las unidades disponibles. Sin unidades, el producto queda como no disponible.
 *
 * @param int $post_id ID del producto.
 */
function casa_a_producto_guardar_box( $post_id ) {
	if ( ! isset( $_POST['casa_a_inventario_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['casa_a_inventario_nonce'] ) ), 'casa_a_producto_inventario' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['casa_a_stock'] ) && '' !== $_POST['casa_a_stock'] ) {
		$stock = absint( $_POST['casa_a_stock'] );
		update_post_meta( $post_id, 'casa_a_stock', $stock );
		update_post_meta( $post_id, 'casa_a_disponible', $stock > 0 ? '1' : '0' );
	}
}
add_action( 'save_post_' . CASA_A_CATALOGO_POST_TYPE, 'casa_a_producto_guardar_box' );

/**
 * Datos del producto que necesita el modal, en un arreglo listo para JSON.
 *
 * @param int $post_id ID del producto.
 * @return array
 */
function casa_a_producto_datos_modal( $post_id ) {
	$nombres = function ( $taxonomia ) use ( $post_id ) {
		$terminos = wp_get_post_terms( $post_id, $taxonomia, array( 'fields' => 'names' ) );

		return is_wp_error( $terminos ) ? array() : array_values( $terminos );
	};

	$precio   = get_post_meta( $post_id, 'casa_a_precio_dia', true );
	$stock = get_post_meta( $post_id, 'casa_a_stock', true );
	$codigo   = get_post_meta( $post_id, 'casa_a_codigo', true );

	$imagenes = array();
	foreach ( (array) get_post_meta( $post_id, 'casa_a_galeria', true ) as $adjunto ) {
		$grande = wp_get_attachment_image_url( $adjunto, 'large' );
		if ( $grande ) {
			$imagenes[] = array(
				'grande' => $grande,
				'miniatura' => wp_get_attachment_image_url( $adjunto, 'thumbnail' ),
			);
		}
	}

	return array(
		'id'          => $post_id,
		'nombre'      => get_the_title( $post_id ),
		'codigo'      => $codigo ? $codigo : 'CA-' . str_pad( (string) $post_id, 4, '0', STR_PAD_LEFT ),
		'categorias'  => $nombres( 'categoria_producto' ),
		'atributos'   => array_merge( $nombres( 'estilo_producto' ), $nombres( 'material_producto' ), $nombres( 'color_producto' ) ),
		'medidas'     => get_post_meta( $post_id, 'casa_a_dimensiones', true ),
		'precio'      => '' === (string) $precio ? null : (int) $precio,
		'stock'       => '' === (string) $stock ? null : (int) $stock,
		'imagenes'    => $imagenes,
		'url'         => get_permalink( $post_id ),
	);
}
