<?php
/**
 * Portada del theme CASA A.
 *
 * @package CASA_A
 */

get_header();

// Diapositivas de la cabecera: imagen, titular y botón. Cada una enlaza a su sección.
$casa_a_diapositivas = array(
	array(
		'imagen'  => 'cabecera1.jpeg',
		'titulo'  => __( 'Ambientamos tu próxima sesión de fotos', 'casa-a' ),
		'boton'   => __( 'Cómo arrendar', 'casa-a' ),
		'enlace'  => home_url( '/como-arrendar/' ),
	),
	array(
		'imagen'  => 'cabecera2.jpg',
		'titulo'  => __( 'Encuentra el prop perfecto para tu set', 'casa-a' ),
		'boton'   => __( 'Ver catálogo', 'casa-a' ),
		'enlace'  => home_url( '/producto/' ),
	),
	array(
		'imagen'  => 'cabecera3.jpeg',
		'titulo'  => __( 'Inspírate con nuestra galería', 'casa-a' ),
		'boton'   => __( 'Ver galería', 'casa-a' ),
		'enlace'  => home_url( '/galeria/' ),
	),
);
?>
<main>
	<section class="casa-a-slider" aria-roledescription="<?php esc_attr_e( 'carrusel', 'casa-a' ); ?>" aria-label="<?php esc_attr_e( 'Destacados', 'casa-a' ); ?>">
		<?php foreach ( $casa_a_diapositivas as $indice => $diapositiva ) : ?>
			<div class="casa-a-slide<?php echo 0 === $indice ? ' is-active' : ''; ?>" role="group" aria-roledescription="<?php esc_attr_e( 'diapositiva', 'casa-a' ); ?>" aria-label="<?php echo esc_attr( sprintf( __( '%1$d de %2$d', 'casa-a' ), $indice + 1, count( $casa_a_diapositivas ) ) ); ?>" <?php echo 0 === $indice ? '' : 'aria-hidden="true"'; ?>>
				<img class="casa-a-slide-img" src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/' . $diapositiva['imagen'] ); ?>" alt="" <?php echo 0 === $indice ? 'fetchpriority="high"' : 'loading="lazy"'; ?>>
				<div class="casa-a-slide-texto">
					<?php if ( 0 === $indice ) : ?>
						<h1><?php echo esc_html( $diapositiva['titulo'] ); ?></h1>
					<?php else : ?>
						<h2><?php echo esc_html( $diapositiva['titulo'] ); ?></h2>
					<?php endif; ?>
					<a class="casa-a-slide-boton" href="<?php echo esc_url( $diapositiva['enlace'] ); ?>" <?php echo 0 === $indice ? '' : 'tabindex="-1"'; ?>>
						<?php echo esc_html( $diapositiva['boton'] ); ?>
						<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
					</a>
				</div>
			</div>
		<?php endforeach; ?>

		<div class="casa-a-slider-puntos" role="tablist" aria-label="<?php esc_attr_e( 'Elegir diapositiva', 'casa-a' ); ?>">
			<?php foreach ( $casa_a_diapositivas as $indice => $diapositiva ) : ?>
				<button type="button" role="tab" class="casa-a-slider-punto<?php echo 0 === $indice ? ' is-active' : ''; ?>" aria-selected="<?php echo 0 === $indice ? 'true' : 'false'; ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Diapositiva %d', 'casa-a' ), $indice + 1 ) ); ?>" data-slide="<?php echo esc_attr( $indice ); ?>"></button>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="casa-a-main">
		<div class="casa-a-wrap casa-a-grid">
			<div class="casa-a-card">
				<h3><?php esc_html_e( 'Casa A', 'casa-a' ); ?></h3>
				<p class="casa-a-muted"><?php esc_html_e( 'Conoce la marca y lo que ofrecemos.', 'casa-a' ); ?></p>
				<a href="<?php echo esc_url( home_url( '/casa-a/' ) ); ?>"><?php esc_html_e( 'Ver más', 'casa-a' ); ?></a>
			</div>
			<div class="casa-a-card">
				<h3><?php esc_html_e( 'Estudio', 'casa-a' ); ?></h3>
				<p class="casa-a-muted"><?php esc_html_e( 'Planos, fotos y datos técnicos del espacio.', 'casa-a' ); ?></p>
				<a href="<?php echo esc_url( home_url( '/estudio/' ) ); ?>"><?php esc_html_e( 'Ver más', 'casa-a' ); ?></a>
			</div>
			<div class="casa-a-card">
				<h3><?php esc_html_e( 'Contacto', 'casa-a' ); ?></h3>
				<p class="casa-a-muted"><?php esc_html_e( 'Redes, calendario de reservas y teléfono.', 'casa-a' ); ?></p>
				<a href="<?php echo esc_url( home_url( '/contacto/' ) ); ?>"><?php esc_html_e( 'Ver más', 'casa-a' ); ?></a>
			</div>
		</div>
	</section>
</main>
<?php
get_footer();
