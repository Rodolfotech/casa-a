<?php
/**
 * Plantilla de página genérica del theme CASA A.
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
	</div>
</main>
<?php
get_footer();
