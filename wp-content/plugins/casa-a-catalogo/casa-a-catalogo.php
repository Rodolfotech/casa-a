<?php
/**
 * Plugin Name: CASA A Catálogo
 * Description: Catálogo de productos y categorías de CASA A (renta de mobiliario y props).
 * Version: 0.1.0
 * Requires PHP: 7.3
 * Text Domain: casa-a-catalogo
 *
 * @package CASA_A_Catalogo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const CASA_A_CATALOGO_POST_TYPE = 'producto';
const CASA_A_CATALOGO_TAXONOMY  = 'categoria_producto';

/**
 * Registra el tipo de contenido "producto" y su taxonomía de categorías.
 *
 * Con esto WordPress ya ofrece agregar, editar y eliminar productos y categorías en el admin.
 */
function casa_a_catalogo_register() {
	register_taxonomy(
		CASA_A_CATALOGO_TAXONOMY,
		CASA_A_CATALOGO_POST_TYPE,
		array(
			'labels'       => array(
				'name'          => __( 'Categorías', 'casa-a-catalogo' ),
				'singular_name' => __( 'Categoría', 'casa-a-catalogo' ),
				'add_new_item'  => __( 'Agregar categoría', 'casa-a-catalogo' ),
				'edit_item'     => __( 'Editar categoría', 'casa-a-catalogo' ),
				'search_items'  => __( 'Buscar categorías', 'casa-a-catalogo' ),
			),
			'hierarchical' => true,
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => 'categoria-producto' ),
		)
	);

	register_post_type(
		CASA_A_CATALOGO_POST_TYPE,
		array(
			'labels'       => array(
				'name'          => __( 'Productos', 'casa-a-catalogo' ),
				'singular_name' => __( 'Producto', 'casa-a-catalogo' ),
				'add_new_item'  => __( 'Agregar producto', 'casa-a-catalogo' ),
				'edit_item'     => __( 'Editar producto', 'casa-a-catalogo' ),
				'search_items'  => __( 'Buscar productos', 'casa-a-catalogo' ),
			),
			'public'       => true,
			'show_in_rest' => true,
			'has_archive'  => true,
			'rewrite'      => array( 'slug' => 'producto' ),
			'supports'     => array( 'title', 'editor', 'thumbnail' ),
			'taxonomies'   => array( CASA_A_CATALOGO_TAXONOMY ),
			'menu_icon'    => 'dashicons-products',
		)
	);
}
add_action( 'init', 'casa_a_catalogo_register' );

/**
 * Taxonomías de la ficha de producto que usan los filtros del catálogo: estilo, color y material.
 */
function casa_a_catalogo_register_taxonomias() {
	$taxonomias = array(
		'estilo_producto'   => array( __( 'Estilo', 'casa-a-catalogo' ), __( 'Estilos', 'casa-a-catalogo' ) ),
		'color_producto'    => array( __( 'Color', 'casa-a-catalogo' ), __( 'Colores', 'casa-a-catalogo' ) ),
		'material_producto' => array( __( 'Material', 'casa-a-catalogo' ), __( 'Materiales', 'casa-a-catalogo' ) ),
	);

	foreach ( $taxonomias as $slug => $nombres ) {
		register_taxonomy(
			$slug,
			CASA_A_CATALOGO_POST_TYPE,
			array(
				'labels'            => array(
					'name'          => $nombres[1],
					'singular_name' => $nombres[0],
				),
				'hierarchical'      => false,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'rewrite'           => array( 'slug' => str_replace( '_', '-', $slug ) ),
			)
		);
	}
}
add_action( 'init', 'casa_a_catalogo_register_taxonomias' );

/**
 * Sanitiza el estado de stock: '1' disponible, '0' sin stock.
 *
 * @param mixed $value Valor enviado.
 * @return string
 */
function casa_a_catalogo_sanitize_stock( $value ) {
	return '0' === (string) $value ? '0' : '1';
}

/**
 * Metadatos de la ficha de arriendo: código, precio por día y stock.
 */
function casa_a_catalogo_register_meta_ficha() {
	$auth = function () {
		return current_user_can( 'edit_posts' );
	};

	register_post_meta(
		CASA_A_CATALOGO_POST_TYPE,
		'casa_a_codigo',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => $auth,
		)
	);

	register_post_meta(
		CASA_A_CATALOGO_POST_TYPE,
		'casa_a_precio_dia',
		array(
			'type'              => 'integer',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'absint',
			'auth_callback'     => $auth,
		)
	);

	register_post_meta(
		CASA_A_CATALOGO_POST_TYPE,
		'casa_a_disponible',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'casa_a_catalogo_sanitize_stock',
			'auth_callback'     => $auth,
		)
	);
}
add_action( 'init', 'casa_a_catalogo_register_meta_ficha' );

/**
 * Metadatos del producto: dimensiones y tipo de disponibilidad.
 */
function casa_a_catalogo_register_meta() {
	register_post_meta(
		CASA_A_CATALOGO_POST_TYPE,
		'casa_a_dimensiones',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	register_post_meta(
		CASA_A_CATALOGO_POST_TYPE,
		'casa_a_disponibilidad',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'casa_a_catalogo_sanitize_disponibilidad',
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);
}
add_action( 'init', 'casa_a_catalogo_register_meta' );

/**
 * Valores permitidos para la disponibilidad del producto.
 *
 * @return array
 */
function casa_a_catalogo_disponibilidad_options() {
	return array(
		'renta' => __( 'Renta', 'casa-a-catalogo' ),
		'venta' => __( 'Venta', 'casa-a-catalogo' ),
		'ambos' => __( 'Renta y venta', 'casa-a-catalogo' ),
	);
}

/**
 * Restringe la disponibilidad a los valores permitidos.
 *
 * @param string $value Valor enviado.
 * @return string
 */
function casa_a_catalogo_sanitize_disponibilidad( $value ) {
	$value = sanitize_key( $value );

	return array_key_exists( $value, casa_a_catalogo_disponibilidad_options() ) ? $value : 'renta';
}

/**
 * Agrega el cuadro de datos del producto en el editor clásico.
 */
function casa_a_catalogo_add_meta_box() {
	add_meta_box(
		'casa_a_catalogo_datos',
		__( 'Datos del producto', 'casa-a-catalogo' ),
		'casa_a_catalogo_render_meta_box',
		CASA_A_CATALOGO_POST_TYPE,
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'casa_a_catalogo_add_meta_box' );

/**
 * Muestra los campos de dimensiones y disponibilidad.
 *
 * @param WP_Post $post Producto que se edita.
 */
function casa_a_catalogo_render_meta_box( $post ) {
	wp_nonce_field( 'casa_a_catalogo_guardar', 'casa_a_catalogo_nonce' );

	$dimensiones   = get_post_meta( $post->ID, 'casa_a_dimensiones', true );
	$disponibilidad = get_post_meta( $post->ID, 'casa_a_disponibilidad', true );
	?>
	<p>
		<label for="casa_a_dimensiones"><strong><?php esc_html_e( 'Dimensiones', 'casa-a-catalogo' ); ?></strong></label><br>
		<input type="text" id="casa_a_dimensiones" name="casa_a_dimensiones" class="widefat" value="<?php echo esc_attr( $dimensiones ); ?>" placeholder="120 x 60 x 75 cm">
	</p>
	<p>
		<label for="casa_a_disponibilidad"><strong><?php esc_html_e( 'Disponibilidad', 'casa-a-catalogo' ); ?></strong></label><br>
		<select id="casa_a_disponibilidad" name="casa_a_disponibilidad" class="widefat">
			<?php foreach ( casa_a_catalogo_disponibilidad_options() as $valor => $etiqueta ) : ?>
				<option value="<?php echo esc_attr( $valor ); ?>" <?php selected( $disponibilidad, $valor ); ?>><?php echo esc_html( $etiqueta ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<?php
}

/**
 * Guarda los campos del cuadro de datos.
 *
 * @param int $post_id ID del producto.
 */
function casa_a_catalogo_save_meta( $post_id ) {
	if ( ! isset( $_POST['casa_a_catalogo_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['casa_a_catalogo_nonce'] ) ), 'casa_a_catalogo_guardar' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$dimensiones = isset( $_POST['casa_a_dimensiones'] ) ? sanitize_text_field( wp_unslash( $_POST['casa_a_dimensiones'] ) ) : '';
	update_post_meta( $post_id, 'casa_a_dimensiones', $dimensiones );

	$disponibilidad = isset( $_POST['casa_a_disponibilidad'] ) ? casa_a_catalogo_sanitize_disponibilidad( wp_unslash( $_POST['casa_a_disponibilidad'] ) ) : 'renta';
	update_post_meta( $post_id, 'casa_a_disponibilidad', $disponibilidad );
}
add_action( 'save_post_' . CASA_A_CATALOGO_POST_TYPE, 'casa_a_catalogo_save_meta' );

/**
 * Al activar, registra el tipo de contenido y actualiza los permalinks.
 */
function casa_a_catalogo_activate() {
	casa_a_catalogo_register();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'casa_a_catalogo_activate' );


/**
 * Al desactivar, limpia los permalinks.
 */
function casa_a_catalogo_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'casa_a_catalogo_deactivate' );

/**
 * Cuadro "Ficha de arriendo" en el editor de producto: código, precio por día y stock.
 */
function casa_a_catalogo_add_ficha_box() {
	add_meta_box(
		'casa_a_catalogo_ficha',
		__( 'Ficha de arriendo', 'casa-a-catalogo' ),
		'casa_a_catalogo_render_ficha',
		CASA_A_CATALOGO_POST_TYPE,
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes', 'casa_a_catalogo_add_ficha_box' );

/**
 * Muestra los campos de la ficha de arriendo.
 *
 * @param WP_Post $post Producto que se edita.
 */
function casa_a_catalogo_render_ficha( $post ) {
	wp_nonce_field( 'casa_a_catalogo_ficha', 'casa_a_ficha_nonce' );

	$codigo    = get_post_meta( $post->ID, 'casa_a_codigo', true );
	$precio    = get_post_meta( $post->ID, 'casa_a_precio_dia', true );
	$disponible = '0' !== get_post_meta( $post->ID, 'casa_a_disponible', true );
	?>
	<p>
		<label for="casa_a_codigo"><strong><?php esc_html_e( 'Código', 'casa-a-catalogo' ); ?></strong></label><br>
		<input type="text" id="casa_a_codigo" name="casa_a_codigo" class="widefat" value="<?php echo esc_attr( $codigo ); ?>" placeholder="CA-0100">
	</p>
	<p>
		<label for="casa_a_precio_dia"><strong><?php esc_html_e( 'Precio por día (CLP)', 'casa-a-catalogo' ); ?></strong></label><br>
		<input type="number" id="casa_a_precio_dia" name="casa_a_precio_dia" class="widefat" min="0" step="1" value="<?php echo esc_attr( $precio ); ?>">
	</p>
	<p>
		<label>
			<input type="checkbox" name="casa_a_disponible" value="1" <?php checked( $disponible ); ?>>
			<?php esc_html_e( 'Disponible para arriendo', 'casa-a-catalogo' ); ?>
		</label>
	</p>
	<?php
}

/**
 * Guarda la ficha de arriendo.
 *
 * @param int $post_id ID del producto.
 */
function casa_a_catalogo_guardar_ficha( $post_id ) {
	if ( ! isset( $_POST['casa_a_ficha_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['casa_a_ficha_nonce'] ) ), 'casa_a_catalogo_ficha' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$codigo = isset( $_POST['casa_a_codigo'] ) ? sanitize_text_field( wp_unslash( $_POST['casa_a_codigo'] ) ) : '';
	update_post_meta( $post_id, 'casa_a_codigo', $codigo );

	$precio = isset( $_POST['casa_a_precio_dia'] ) ? absint( $_POST['casa_a_precio_dia'] ) : 0;
	update_post_meta( $post_id, 'casa_a_precio_dia', $precio );

	$disponible = empty( $_POST['casa_a_disponible'] ) ? '0' : '1';
	update_post_meta( $post_id, 'casa_a_disponible', $disponible );
}
add_action( 'save_post_' . CASA_A_CATALOGO_POST_TYPE, 'casa_a_catalogo_guardar_ficha' );

require_once __DIR__ . '/includes/sync-galeria.php';
require_once __DIR__ . '/includes/producto-extra.php';
