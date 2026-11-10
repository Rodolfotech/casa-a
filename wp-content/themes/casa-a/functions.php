<?php
/**
 * Funciones del theme CASA A.
 *
 * @package CASA_A
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function casa_a_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'gallery', 'caption' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );

	register_nav_menus(
		array(
			'principal' => __( 'Menú principal', 'casa-a' ),
		)
	);
}
add_action( 'after_setup_theme', 'casa_a_setup' );

function casa_a_enqueue_assets() {
	// Tipografías del nav: logo serif y textos sans, como en el diseño de referencia.
	wp_enqueue_style(
		'casa-a-fuentes',
		'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600&family=Montserrat:wght@400;500&display=swap',
		array(),
		null
	);

	wp_enqueue_style( 'casa-a-style', get_stylesheet_uri(), array( 'casa-a-fuentes' ), wp_get_theme()->get( 'Version' ) );
}
add_action( 'wp_enqueue_scripts', 'casa_a_enqueue_assets' );

/**
 * Script de "Mi selección": guarda la selección en el navegador y actualiza el contador del nav.
 */
function casa_a_enqueue_seleccion() {
	wp_enqueue_script( 'casa-a-seleccion', get_template_directory_uri() . '/assets/js/seleccion.js', array(), wp_get_theme()->get( 'Version' ), true );
}
add_action( 'wp_enqueue_scripts', 'casa_a_enqueue_seleccion' );

/**
 * Modal de producto: se abre desde las fotos del catálogo.
 */
function casa_a_enqueue_producto_modal() {
	wp_enqueue_script( 'casa-a-producto-modal', get_template_directory_uri() . '/assets/js/producto-modal.js', array( 'casa-a-seleccion' ), wp_get_theme()->get( 'Version' ), true );
}
add_action( 'wp_enqueue_scripts', 'casa_a_enqueue_producto_modal' );

/**
 * Carrusel de la portada (solo en el home).
 */
function casa_a_enqueue_slider() {
	if ( ! is_front_page() ) {
		return;
	}

	wp_enqueue_script( 'casa-a-slider', get_template_directory_uri() . '/assets/js/slider.js', array(), wp_get_theme()->get( 'Version' ), true );
}
add_action( 'wp_enqueue_scripts', 'casa_a_enqueue_slider' );

/**
 * Filtros del catálogo que se leen de la URL: categoría, estilo, color, material y disponibilidad.
 */
function casa_a_catalogo_filtros() {
	return array(
		'categoria' => 'categoria_producto',
		'estilo'    => 'estilo_producto',
		'color'     => 'color_producto',
		'material'  => 'material_producto',
	);
}

/**
 * Aplica los filtros de la URL a la consulta del catálogo y a la búsqueda de productos.
 *
 * @param WP_Query $query Consulta principal.
 */
function casa_a_catalogo_aplicar_filtros( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	$es_catalogo = $query->is_post_type_archive( 'producto' );
	$es_busqueda = $query->is_search() && 'producto' === $query->get( 'post_type' );

	if ( ! $es_catalogo && ! $es_busqueda ) {
		return;
	}

	$tax_query = array( 'relation' => 'AND' );

	foreach ( casa_a_catalogo_filtros() as $parametro => $taxonomia ) {
		$valor = isset( $_GET[ $parametro ] ) ? sanitize_title( wp_unslash( $_GET[ $parametro ] ) ) : '';

		if ( '' !== $valor ) {
			$tax_query[] = array(
				'taxonomy' => $taxonomia,
				'field'    => 'slug',
				'terms'    => $valor,
			);
		}
	}

	if ( count( $tax_query ) > 1 ) {
		$query->set( 'tax_query', $tax_query );
	}

	// Sin stock solo se oculta si el producto lo marca explícitamente con '0'.
	if ( ! empty( $_GET['disponibles'] ) ) {
		$query->set(
			'meta_query',
			array(
				'relation' => 'OR',
				array(
					'key'     => 'casa_a_disponible',
					'value'   => '0',
					'compare' => '!=',
				),
				array(
					'key'     => 'casa_a_disponible',
					'compare' => 'NOT EXISTS',
				),
			)
		);
	}

	$query->set( 'posts_per_page', 24 );
	$query->set( 'orderby', 'title' );
	$query->set( 'order', 'ASC' );
}
add_action( 'pre_get_posts', 'casa_a_catalogo_aplicar_filtros' );

/**
 * La búsqueda de productos usa la misma plantilla que el catálogo.
 *
 * @param string $template Plantilla elegida por WordPress.
 * @return string
 */
function casa_a_catalogo_plantilla_busqueda( $template ) {
	if ( is_search() && 'producto' === get_query_var( 'post_type' ) ) {
		$catalogo = locate_template( 'archive-producto.php' );

		if ( $catalogo ) {
			return $catalogo;
		}
	}

	return $template;
}
add_filter( 'template_include', 'casa_a_catalogo_plantilla_busqueda' );

/**
 * URL del catálogo con un filtro cambiado. Si el valor ya está activo, lo quita.
 *
 * @param string $clave Parámetro: categoria, estilo, color, material o disponibles.
 * @param string $valor Valor del filtro.
 * @return string
 */
function casa_a_catalogo_url_filtro( $clave, $valor ) {
	$activos = array();

	foreach ( array_merge( array_keys( casa_a_catalogo_filtros() ), array( 'disponibles', 's' ) ) as $parametro ) {
		if ( ! empty( $_GET[ $parametro ] ) ) {
			$activos[ $parametro ] = sanitize_text_field( wp_unslash( $_GET[ $parametro ] ) );
		}
	}

	if ( isset( $activos[ $clave ] ) && $activos[ $clave ] === $valor ) {
		unset( $activos[ $clave ] );
	} else {
		$activos[ $clave ] = $valor;
	}

	return add_query_arg( $activos, get_post_type_archive_link( 'producto' ) );
}

/**
 * Indica si un filtro está activo en la URL.
 *
 * @param string $clave Parámetro.
 * @param string $valor Valor.
 * @return bool
 */
function casa_a_catalogo_filtro_activo( $clave, $valor ) {
	return isset( $_GET[ $clave ] ) && sanitize_text_field( wp_unslash( $_GET[ $clave ] ) ) === $valor;
}

/**
 * Menú de respaldo cuando aún no hay menú asignado.
 */
function casa_a_menu_fallback() {
	$items = array(
		__( 'Inicio', 'casa-a' )            => '/',
		__( 'Catálogo', 'casa-a' )          => '/producto/',
		__( 'Galería', 'casa-a' )           => '/galeria/',
		__( 'Quiénes somos', 'casa-a' )     => '/casa-a/',
		__( 'Cómo arrendar', 'casa-a' )     => '/como-arrendar/',
		__( 'Contacto', 'casa-a' )          => '/contacto/',
	);

	echo '<ul class="casa-a-nav-list">';
	foreach ( $items as $etiqueta => $ruta ) {
		echo '<li><a href="' . esc_url( home_url( $ruta ) ) . '">' . esc_html( $etiqueta ) . '</a></li>';
	}
	echo '</ul>';
}

/**
 * Procesa el formulario de contacto de la página Contacto.
 */
function casa_a_contacto_procesar() {
	check_admin_referer( 'casa_a_contacto', 'casa_a_contacto_nonce' );

	$destino = wp_get_referer() ? wp_get_referer() : home_url( '/contacto/' );

	// Campo trampa: los humanos no lo llenan.
	if ( ! empty( $_POST['sitio_web'] ) ) {
		wp_safe_redirect( add_query_arg( 'casa_a_estado', 'ok', $destino ) );
		exit;
	}

	$nombre      = isset( $_POST['nombre'] ) ? sanitize_text_field( wp_unslash( $_POST['nombre'] ) ) : '';
	$telefono    = isset( $_POST['telefono'] ) ? sanitize_text_field( wp_unslash( $_POST['telefono'] ) ) : '';
	$pais        = isset( $_POST['prefijo'] ) ? sanitize_text_field( wp_unslash( $_POST['prefijo'] ) ) : '';
	$email       = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$descripcion = isset( $_POST['descripcion'] ) ? sanitize_textarea_field( wp_unslash( $_POST['descripcion'] ) ) : '';
	$promociones = ! empty( $_POST['promociones'] ) ? 'Sí' : 'No';

	if ( '' === $nombre || ! is_email( $email ) || '' === $descripcion ) {
		wp_safe_redirect( add_query_arg( 'casa_a_estado', 'error', $destino ) );
		exit;
	}

	$adjunto_ruta = '';
	if ( ! empty( $_FILES['adjunto']['name'] ) && UPLOAD_ERR_OK === $_FILES['adjunto']['error'] ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';

		$archivo = wp_handle_upload(
			$_FILES['adjunto'],
			array(
				'test_form' => false,
				'mimes'     => array(
					'jpg|jpeg' => 'image/jpeg',
					'png'      => 'image/png',
					'pdf'      => 'application/pdf',
				),
			)
		);

		if ( isset( $archivo['error'] ) ) {
			wp_safe_redirect( add_query_arg( 'casa_a_estado', 'archivo', $destino ) );
			exit;
		}

		$adjunto_ruta = $archivo['file'];
	}

	$asunto  = sprintf( __( 'Nueva cotización desde CASA A: %s', 'casa-a' ), $nombre );
	$cuerpo  = sprintf( __( "Nombre: %s\nTeléfono: %s %s\nCorreo: %s\nPromociones: %s\n\nDescripción del proyecto:\n%s", 'casa-a' ), $nombre, $pais, $telefono, $email, $promociones, $descripcion );
	$cabeceras = array( 'Reply-To: ' . $nombre . ' <' . $email . '>' );
	$adjuntos  = $adjunto_ruta ? array( $adjunto_ruta ) : array();

	$enviado = wp_mail( get_option( 'admin_email' ), $asunto, $cuerpo, $cabeceras, $adjuntos );

	// Se borra la copia subida para no dejarla accesible en la carpeta pública de uploads.
	if ( $adjunto_ruta && file_exists( $adjunto_ruta ) ) {
		wp_delete_file( $adjunto_ruta );
	}

	wp_safe_redirect( add_query_arg( 'casa_a_estado', $enviado ? 'ok' : 'fallo', $destino ) );
	exit;
}
add_action( 'admin_post_nopriv_casa_a_contacto', 'casa_a_contacto_procesar' );
add_action( 'admin_post_casa_a_contacto', 'casa_a_contacto_procesar' );

/**
 * Valida un RUT chileno (cuerpo y dígito verificador).
 *
 * @param string $rut RUT con o sin puntos y guion.
 * @return bool
 */
function casa_a_rut_valido( $rut ) {
	$rut = strtoupper( preg_replace( '/[^0-9kK]/', '', (string) $rut ) );

	if ( strlen( $rut ) < 2 ) {
		return false;
	}

	$cuerpo = substr( $rut, 0, -1 );
	$dv     = substr( $rut, -1 );

	if ( ! ctype_digit( $cuerpo ) ) {
		return false;
	}

	$suma      = 0;
	$multiplo  = 2;

	for ( $i = strlen( $cuerpo ) - 1; $i >= 0; $i-- ) {
		$suma    += (int) $cuerpo[ $i ] * $multiplo;
		$multiplo = 7 === $multiplo ? 2 : $multiplo + 1;
	}

	$resto    = 11 - ( $suma % 11 );
	$esperado = 11 === $resto ? '0' : ( 10 === $resto ? 'K' : (string) $resto );

	return $dv === $esperado;
}

/**
 * Procesa la solicitud de cotización de "Mi selección".
 *
 * Los precios y los códigos se leen de la base de datos, nunca del navegador.
 */
function casa_a_cotizacion_procesar() {
	check_admin_referer( 'casa_a_cotizar', 'casa_a_cotizar_nonce' );

	$destino = wp_get_referer() ? wp_get_referer() : home_url( '/' );

	// Campo trampa anti-spam: los humanos no lo llenan.
	if ( ! empty( $_POST['sitio_web'] ) ) {
		wp_safe_redirect( add_query_arg( 'casa_a_cotizacion', 'ok', $destino ) );
		exit;
	}

	$error = function () use ( $destino ) {
		wp_safe_redirect( add_query_arg( 'casa_a_cotizacion', 'error', $destino ) );
		exit;
	};

	$nombre     = isset( $_POST['nombre'] ) ? sanitize_text_field( wp_unslash( $_POST['nombre'] ) ) : '';
	$rut        = isset( $_POST['rut'] ) ? sanitize_text_field( wp_unslash( $_POST['rut'] ) ) : '';
	$productora = isset( $_POST['productora'] ) ? sanitize_text_field( wp_unslash( $_POST['productora'] ) ) : '';
	$email      = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$telefono   = isset( $_POST['telefono'] ) ? sanitize_text_field( wp_unslash( $_POST['telefono'] ) ) : '';
	$retiro     = isset( $_POST['fecha_retiro'] ) ? sanitize_text_field( wp_unslash( $_POST['fecha_retiro'] ) ) : '';
	$devolucion = isset( $_POST['fecha_devolucion'] ) ? sanitize_text_field( wp_unslash( $_POST['fecha_devolucion'] ) ) : '';

	if ( '' === $nombre || ! casa_a_rut_valido( $rut ) || ! is_email( $email ) ) {
		$error();
	}

	foreach ( array( $retiro, $devolucion ) as $fecha ) {
		if ( ! preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', $fecha, $partes ) || ! checkdate( (int) $partes[2], (int) $partes[3], (int) $partes[1] ) ) {
			$error();
		}
	}

	if ( $devolucion < $retiro ) {
		$error();
	}

	$items = isset( $_POST['items'] ) ? json_decode( wp_unslash( $_POST['items'] ), true ) : null;

	if ( ! is_array( $items ) ) {
		$error();
	}

	$lineas = array();
	$total  = 0;

	foreach ( $items as $item ) {
		$id       = isset( $item['id'] ) ? absint( $item['id'] ) : 0;
		$cantidad = isset( $item['cantidad'] ) ? min( 99, max( 1, absint( $item['cantidad'] ) ) ) : 1;
		$producto = $id ? get_post( $id ) : null;

		if ( ! $producto || 'producto' !== $producto->post_type || 'publish' !== $producto->post_status ) {
			continue;
		}

		if ( '0' === get_post_meta( $id, 'casa_a_disponible', true ) ) {
			continue;
		}

		// Nunca más unidades que el stock real del producto.
		$stock = get_post_meta( $id, 'casa_a_stock', true );

		if ( '' !== (string) $stock ) {
			$cantidad = min( $cantidad, (int) $stock );

			if ( $cantidad < 1 ) {
				continue;
			}
		}

		$codigo = get_post_meta( $id, 'casa_a_codigo', true );
		$codigo = $codigo ? $codigo : 'CA-' . str_pad( (string) $id, 4, '0', STR_PAD_LEFT );
		$precio = (int) get_post_meta( $id, 'casa_a_precio_dia', true );

		$lineas[] = sprintf( '%s · %s × %d · $%s / día', $codigo, $producto->post_title, $cantidad, number_format_i18n( $precio * $cantidad ) );
		$total   += $precio * $cantidad;
	}

	if ( empty( $lineas ) ) {
		$error();
	}

	$dias = (int) ( ( strtotime( $devolucion ) - strtotime( $retiro ) ) / DAY_IN_SECONDS ) + 1;

	$asunto = sprintf( __( 'Solicitud de cotización de %s', 'casa-a' ), $nombre );
	$cuerpo = implode(
		"\n",
		array_merge(
			array(
				sprintf( __( 'Nombre: %s', 'casa-a' ), $nombre ),
				sprintf( __( 'RUT: %s', 'casa-a' ), $rut ),
				sprintf( __( 'Productora: %s', 'casa-a' ), $productora ),
				sprintf( __( 'Email: %s', 'casa-a' ), $email ),
				sprintf( __( 'Teléfono: %s', 'casa-a' ), $telefono ),
				sprintf( __( 'Fecha de retiro: %s', 'casa-a' ), $retiro ),
				sprintf( __( 'Fecha de devolución: %s (%d días)', 'casa-a' ), $devolucion, $dias ),
				'',
				__( 'Productos:', 'casa-a' ),
			),
			$lineas,
			array(
				'',
				sprintf( __( 'Total por día: $%s', 'casa-a' ), number_format_i18n( $total ) ),
			)
		)
	);

	$enviado = wp_mail(
		get_option( 'admin_email' ),
		$asunto,
		$cuerpo,
		array( 'Reply-To: ' . $nombre . ' <' . $email . '>' )
	);

	wp_safe_redirect( add_query_arg( 'casa_a_cotizacion', $enviado ? 'ok' : 'fallo', $destino ) );
	exit;
}
add_action( 'admin_post_nopriv_casa_a_cotizar', 'casa_a_cotizacion_procesar' );
add_action( 'admin_post_casa_a_cotizar', 'casa_a_cotizacion_procesar' );
