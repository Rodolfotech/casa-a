<?php
/**
 * Plantilla de la página Estudio: planos, fotos y datos técnicos.
 *
 * @package CASA_A
 */

get_header();
?>
<main class="casa-a-main">
	<div class="casa-a-wrap">
		<?php
		while ( have_posts() ) {
			the_post();
			?>
			<article <?php post_class(); ?>>
				<h1 class="casa-a-entry-title"><?php the_title(); ?></h1>
				<div class="casa-a-content"><?php the_content(); ?></div>
			</article>
			<?php
		}
		?>

		<section class="casa-a-section-alt" style="margin-top:48px">
			<h2><?php esc_html_e( 'Datos técnicos', 'casa-a' ); ?></h2>
			<div class="casa-a-grid">
				<div class="casa-a-card">
					<h3><?php esc_html_e( 'Planos', 'casa-a' ); ?></h3>
					<p class="casa-a-muted"><?php esc_html_e( 'Planta del espacio con medidas. Agrega la imagen desde la página.', 'casa-a' ); ?></p>
				</div>
				<div class="casa-a-card">
					<h3><?php esc_html_e( 'Fotos del espacio', 'casa-a' ); ?></h3>
					<p class="casa-a-muted"><?php esc_html_e( 'Galería del estudio físico. Agrega las fotos desde la página.', 'casa-a' ); ?></p>
				</div>
				<div class="casa-a-card">
					<h3><?php esc_html_e( 'Especificaciones', 'casa-a' ); ?></h3>
					<p class="casa-a-muted"><?php esc_html_e( 'Superficie, aforo, equipamiento y accesos.', 'casa-a' ); ?></p>
				</div>
			</div>
		</section>
	</div>
</main>
<?php
get_footer();
