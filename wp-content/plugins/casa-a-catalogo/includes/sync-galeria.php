<?php
/**
 * Sincroniza las imágenes del panel de galería (jwt-gallery) con el catálogo.
 *
 * Cada `jg_gallery_image` publicada crea o actualiza un `producto` vinculado.
 * Título, descripción, medidas, uso y categoría se copian al producto. Todas las imágenes
 * de la galería pasan a la biblioteca de medios: la primera queda como destacada y el
 * resto como galería del producto (`casa_a_galeria`). Precio y unidades se
 * completan en la ficha del producto.
 *
 * @package CASA_A_Catalogo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const CASA_A_GALERIA_CPT      = 'jg_gallery_image';
const CASA_A_GALERIA_TAXONOMY = 'jg_gallery_category';

/**
 * Busca o crea la categoría de catálogo con el mismo nombre (sin distinguir mayúsculas).
 *
 * @param string $nombre Nombre de la categoría de la galería.
 * @return int|null ID del término de catálogo.
 */
function casa_a_galeria_categoria_catalogo( $nombre ) {
	$nombre = trim( $nombre );

	if ( '' === $nombre ) {
		return null;
	}

	foreach ( get_terms( array( 'taxonomy' => 'categoria_producto', 'hide_empty' => false ) ) as $termino ) {
		if ( 0 === strcasecmp( $termino->name, $nombre ) ) {
			return (int) $termino->term_id;
		}
	}

	$nuevo = wp_insert_term( ucfirst( $nombre ), 'categoria_producto' );

	return is_wp_error( $nuevo ) ? null : (int) $nuevo['term_id'];
}

/**
 * Convierte un archivo de uploads/jg-gallery en imagen de la biblioteca de medios.
 *
 * @param string $url     URL pública de la imagen de la galería.
 * @param int    $post_id Producto al que se asocia.
 * @return int|false ID del adjunto.
 */
function casa_a_galeria_importar_imagen( $url, $post_id ) {
	$uploads = wp_upload_dir();
	$base    = trailingslashit( $uploads['baseurl'] ) . 'jg-gallery/';

	if ( 0 !== strpos( $url, $base ) ) {
		return false;
	}

	$origen = trailingslashit( $uploads['basedir'] ) . 'jg-gallery/' . substr( $url, strlen( $base ) );

	if ( ! is_readable( $origen ) ) {
		return false;
	}

	$archivo = wp_upload_bits( basename( $origen ), null, file_get_contents( $origen ) );

	if ( ! empty( $archivo['error'] ) ) {
		return false;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';

	$adjunto = wp_insert_attachment(
		array(
			'post_mime_type' => wp_check_filetype( $archivo['file'] )['type'],
			'post_title'     => sanitize_text_field( pathinfo( $archivo['file'], PATHINFO_FILENAME ) ),
			'post_status'    => 'inherit',
		),
		$archivo['file'],
		$post_id
	);

	if ( is_wp_error( $adjunto ) ) {
		return false;
	}

	wp_update_attachment_metadata( $adjunto, wp_generate_attachment_metadata( $adjunto, $archivo['file'] ) );

	return (int) $adjunto;
}

/**
 * Crea o actualiza el producto vinculado a una imagen de la galería.
 *
 * @param int     $galeria_id ID de la imagen de galería.
 * @param WP_Post $galeria    Imagen de galería.
 */
function casa_a_galeria_sincronizar( $galeria_id, $galeria ) {
	if ( wp_is_post_autosave( $galeria_id ) || wp_is_post_revision( $galeria_id ) ) {
		return;
	}

	if ( 'publish' !== $galeria->post_status ) {
		return;
	}

	$producto_id = (int) get_post_meta( $galeria_id, '_casa_a_producto_id', true );
	$producto    = $producto_id ? get_post( $producto_id ) : null;

	$datos = array(
		'post_type'    => 'producto',
		'post_title'   => $galeria->post_title,
		'post_content' => $galeria->post_content,
		'post_status'  => 'publish',
	);

	if ( ! $producto || 'producto' !== $producto->post_type ) {
		$producto_id = wp_insert_post( $datos, true );

		if ( is_wp_error( $producto_id ) || 0 === (int) $producto_id ) {
			return;
		}

		$producto_id = (int) $producto_id;
		update_post_meta( $producto_id, 'casa_a_disponible', '1' );
		update_post_meta( $producto_id, 'casa_a_stock', 1 );
		update_post_meta( $producto_id, 'casa_a_codigo', 'CA-' . str_pad( (string) $producto_id, 4, '0', STR_PAD_LEFT ) );
		update_post_meta( $galeria_id, '_casa_a_producto_id', $producto_id );
		update_post_meta( $producto_id, '_casa_a_galeria_id', $galeria_id );
	} else {
		$datos['ID'] = $producto_id;
		wp_update_post( $datos );
	}

	update_post_meta( $producto_id, 'casa_a_dimensiones', get_post_meta( $galeria_id, '_jg_measurements', true ) );
	// Precio por día: solo si la galería lo trae, para no borrar un precio cargado en el catálogo.
	$precio_dia = (int) get_post_meta( $galeria_id, '_jg_precio_dia', true );

	if ( $precio_dia > 0 ) {
		update_post_meta( $producto_id, 'casa_a_precio_dia', $precio_dia );
	}

	// Cantidad: unidades disponibles. Si la galería no la trae, se conserva la del catálogo.
	$cantidad = get_post_meta( $galeria_id, '_jg_cantidad', true );

	if ( '' !== (string) $cantidad ) {
		update_post_meta( $producto_id, 'casa_a_stock', absint( $cantidad ) );
		update_post_meta( $producto_id, 'casa_a_disponible', absint( $cantidad ) > 0 ? '1' : '0' );
	}

	// Categoría: se reemplaza por la de la galería.
	$categoria = wp_get_post_terms( $galeria_id, CASA_A_GALERIA_TAXONOMY, array( 'fields' => 'names' ) );
	$ids       = array();

	if ( ! empty( $categoria ) && ! is_wp_error( $categoria ) ) {
		$id = casa_a_galeria_categoria_catalogo( $categoria[0] );
		if ( $id ) {
			$ids[] = $id;
		}
	}

	wp_set_object_terms( $producto_id, $ids, 'categoria_producto', false );

	// Imágenes: cada URL se importa una sola vez y se reutiliza en las siguientes sincronizaciones.
	$mapa  = (array) get_post_meta( $producto_id, '_casa_a_origen_adjuntos', true );
	$urls  = get_post_meta( $galeria_id, '_jg_image_url', false );
	$galeria_adjuntos = array();

	foreach ( $urls as $url ) {
		$url = (string) $url;

		if ( '' === $url ) {
			continue;
		}

		if ( empty( $mapa[ $url ] ) || ! get_post( $mapa[ $url ] ) ) {
			$adjunto = casa_a_galeria_importar_imagen( $url, $producto_id );

			if ( ! $adjunto ) {
				continue;
			}

			$mapa[ $url ] = $adjunto;
		}

		$galeria_adjuntos[] = (int) $mapa[ $url ];
	}

	if ( $galeria_adjuntos ) {
		update_post_meta( $producto_id, '_casa_a_origen_adjuntos', $mapa );
		update_post_meta( $producto_id, 'casa_a_galeria', $galeria_adjuntos );
		set_post_thumbnail( $producto_id, $galeria_adjuntos[0] );
	}
}

/**
 * Al enviar la imagen de galería a la papelera, también se envía el producto vinculado.
 *
 * @param int $galeria_id ID de la imagen de galería.
 */
function casa_a_galeria_quitar_producto( $galeria_id ) {
	if ( CASA_A_GALERIA_CPT !== get_post_type( $galeria_id ) ) {
		return;
	}

	$producto_id = (int) get_post_meta( $galeria_id, '_casa_a_producto_id', true );

	if ( $producto_id && 'producto' === get_post_type( $producto_id ) ) {
		wp_trash_post( $producto_id );
	}
}
add_action( 'wp_trash_post', 'casa_a_galeria_quitar_producto' );
add_action( 'before_delete_post', 'casa_a_galeria_quitar_producto' );

/**
 * El plugin de galería guarda las URLs de las imágenes después de crear el post. Por eso la
 * sincronización se encola y se ejecuta al final de la petición, cuando ya están todos los datos.
 *
 * @param int|null $galeria_id ID a encolar, o null para leer la cola.
 * @return int[]
 */
function casa_a_galeria_cola( $galeria_id = null ) {
	static $ids = array();

	if ( null !== $galeria_id ) {
		$ids[] = (int) $galeria_id;
	}

	return $ids;
}

add_action(
	'save_post_' . CASA_A_GALERIA_CPT,
	function ( $galeria_id ) {
		casa_a_galeria_cola( $galeria_id );
	},
	20
);

add_action(
	'shutdown',
	function () {
		foreach ( array_unique( casa_a_galeria_cola() ) as $galeria_id ) {
			$galeria = get_post( $galeria_id );

			if ( $galeria ) {
				casa_a_galeria_sincronizar( $galeria_id, $galeria );
			}
		}
	}
);
