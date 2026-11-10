<?php
/**
 * Plantilla de respaldo del theme CASA A.
 *
 * @package CASA_A
 */

get_header();
?>
<main class="casa-a-main">
	<div class="casa-a-wrap">
		<?php
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				?>
				<article <?php post_class(); ?>>
					<h1 class="casa-a-entry-title"><?php the_title(); ?></h1>
					<div class="casa-a-content"><?php the_content(); ?></div>
				</article>
				<?php
			}
		} else {
			?>
			<p><?php esc_html_e( 'No hay contenido todavía.', 'casa-a' ); ?></p>
			<?php
		}
		?>
	</div>
</main>
<?php
get_footer();
