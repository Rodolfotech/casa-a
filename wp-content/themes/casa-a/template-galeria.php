<?php
/**
 * Template Name: Galería
 *
 * Galería pública "En acción": muestra las imágenes publicadas en el panel de galería (jwt-gallery).
 * Crea una página nueva y elige esta plantilla. No uses el slug "galeria": esa ruta es el login del panel.
 *
 * @package CASA_A
 */

get_header();

$casa_a_galeria = new WP_Query(
	array(
		'post_type'      => 'jg_gallery_image',
		'post_status'    => 'publish',
		'posts_per_page' => 60,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'no_found_rows'  => true,
	)
);
?>
<main class="casa-a-main casa-a-galeria-pagina">
	<div class="casa-a-wrap">
		<header class="casa-a-galeria-cabecera">
			<p class="casa-a-etiqueta"><?php esc_html_e( 'En acción', 'casa-a' ); ?></p>
			<h1 class="casa-a-catalogo-titulo"><?php the_title(); ?></h1>
		</header>

		<?php if ( $casa_a_galeria->have_posts() ) : ?>
			<div class="casa-a-galeria-grid">
				<?php
				while ( $casa_a_galeria->have_posts() ) {
					$casa_a_galeria->the_post();

					$categorias = wp_get_post_terms( get_the_ID(), 'jg_gallery_category', array( 'fields' => 'names' ) );
					$etiqueta   = ( ! empty( $categorias ) && ! is_wp_error( $categorias ) ) ? $categorias[0] : '';

					foreach ( (array) get_post_meta( get_the_ID(), '_jg_image_url', false ) as $url ) {
						if ( ! $url ) {
							continue;
						}
						?>
						<figure class="casa-a-galeria-item">
							<img src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy">
							<?php if ( $etiqueta ) : ?>
								<figcaption><?php echo esc_html( $etiqueta ); ?></figcaption>
							<?php endif; ?>
						</figure>
						<?php
					}
				}
				wp_reset_postdata();
				?>
			</div>
		<?php else : ?>
			<p class="casa-a-muted"><?php esc_html_e( 'Todavía no hay imágenes publicadas.', 'casa-a' ); ?></p>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
