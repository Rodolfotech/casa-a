<?php
/**
 * Ficha de un producto del catálogo.
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
			$dimensiones    = get_post_meta( get_the_ID(), 'casa_a_dimensiones', true );
			$disponibilidad = get_post_meta( get_the_ID(), 'casa_a_disponibilidad', true );
			$categorias     = get_the_terms( get_the_ID(), 'categoria_producto' );
			?>
			<article <?php post_class(); ?>>
				<h1 class="casa-a-entry-title"><?php the_title(); ?></h1>

				<?php if ( has_post_thumbnail() ) : ?>
					<div style="margin-bottom:24px"><?php the_post_thumbnail( 'large' ); ?></div>
				<?php endif; ?>

				<ul class="casa-a-muted" style="list-style:none;padding:0;margin:0 0 24px">
					<?php if ( $dimensiones ) : ?>
						<li><?php esc_html_e( 'Dimensiones:', 'casa-a' ); ?> <?php echo esc_html( $dimensiones ); ?></li>
					<?php endif; ?>
					<?php if ( $disponibilidad ) : ?>
						<li><?php esc_html_e( 'Disponibilidad:', 'casa-a' ); ?> <?php echo esc_html( $disponibilidad ); ?></li>
					<?php endif; ?>
					<?php if ( $categorias && ! is_wp_error( $categorias ) ) : ?>
						<li><?php esc_html_e( 'Categoría:', 'casa-a' ); ?> <?php echo esc_html( implode( ', ', wp_list_pluck( $categorias, 'name' ) ) ); ?></li>
					<?php endif; ?>
				</ul>

				<div class="casa-a-content"><?php the_content(); ?></div>

				<p style="margin-top:32px">
					<a class="casa-a-button" href="<?php echo esc_url( home_url( '/contacto/' ) ); ?>"><?php esc_html_e( 'Consultar disponibilidad', 'casa-a' ); ?></a>
				</p>
			</article>
			<?php
		}
		?>
	</div>
</main>
<?php
get_footer();
