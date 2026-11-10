<?php
/**
 * Template Name: Quiénes somos
 *
 * Presentación de la marca y valores. Usa esta plantilla en la página con slug "casa-a".
 *
 * @package CASA_A
 */

get_header();

$casa_a_valores = array(
	array( __( 'Calidez', 'casa-a' ), __( 'Te atendemos como parte de tu equipo. Entendemos los tiempos de un set y respondemos rápido.', 'casa-a' ) ),
	array( __( 'Calidad', 'casa-a' ), __( 'Cada pieza se revisa, limpia y fotografía antes de cada arriendo.', 'casa-a' ) ),
	array( __( 'Confianza', 'casa-a' ), __( 'Precios claros, garantías transparentes y productos que llegan tal como los viste.', 'casa-a' ) ),
);
?>
<main class="casa-a-main casa-a-nosotros">
	<section class="casa-a-wrap casa-a-nosotros-intro">
		<h1 class="casa-a-nosotros-titulo"><?php esc_html_e( 'Un hogar de objetos para contar historias.', 'casa-a' ); ?></h1>
		<div class="casa-a-nosotros-texto">
			<p><?php esc_html_e( 'Casa-a nace en Santiago de la experiencia de ambientadores y directoras de arte que necesitaban encontrar el mueble justo, rápido y en buen estado.', 'casa-a' ); ?></p>
			<p><?php esc_html_e( 'Hoy reunimos cientos de piezas de mobiliario y decoración seleccionadas para filmaciones, sesiones de fotos y eventos, con un servicio pensado para los tiempos reales de una producción.', 'casa-a' ); ?></p>
		</div>
	</section>

	<section class="casa-a-nosotros-valores" aria-label="<?php esc_attr_e( 'Nuestros valores', 'casa-a' ); ?>">
		<div class="casa-a-wrap casa-a-nosotros-grid">
			<?php foreach ( $casa_a_valores as $valor ) : ?>
				<article class="casa-a-nosotros-card">
					<h2><?php echo esc_html( $valor[0] ); ?></h2>
					<p><?php echo esc_html( $valor[1] ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>
	</section>
</main>
<?php
get_footer();
