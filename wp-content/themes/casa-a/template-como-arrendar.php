<?php
/**
 * Template Name: Cómo arrendar
 *
 * Pasos del arriendo y políticas, en acordeón. Usa esta plantilla en la página con slug "como-arrendar".
 *
 * @package CASA_A
 */

get_header();

$casa_a_pasos = array(
	array( 'I', __( 'Elige tus productos', 'casa-a' ), __( 'Navega el catálogo y agrega lo que necesitas a Mi selección.', 'casa-a' ) ),
	array( '2', __( 'Envía tu solicitud', 'casa-a' ), __( 'Indica fechas de retiro y devolución y el nombre de la productora.', 'casa-a' ) ),
	array( '3', __( 'Recibe tu presupuesto', 'casa-a' ), __( 'Te enviamos la cotización detallada por email.', 'casa-a' ) ),
	array( '4', __( 'Confirma y retira', 'casa-a' ), __( 'Confirma, paga la garantía y retira en nuestra bodega.', 'casa-a' ) ),
);

$casa_a_politicas = array(
	array( __( 'Garantía', 'casa-a' ), __( 'Texto por completar: monto y condiciones de la garantía.', 'casa-a' ) ),
	array( __( 'Plazos', 'casa-a' ), __( 'Texto por completar: plazos de arriendo y extensiones.', 'casa-a' ) ),
	array( __( 'Daños', 'casa-a' ), __( 'Texto por completar: qué ocurre si un producto se daña o pierde.', 'casa-a' ) ),
	array( __( 'Retiro y devolución', 'casa-a' ), __( 'Retiro y devolución en nuestra bodega dentro del horario de atención. Consulta por despacho a set.', 'casa-a' ) ),
);
?>
<main class="casa-a-main casa-a-arrendar">
	<div class="casa-a-wrap">
		<header class="casa-a-galeria-cabecera">
			<p class="casa-a-etiqueta"><?php esc_html_e( 'Paso a paso', 'casa-a' ); ?></p>
			<h1 class="casa-a-catalogo-titulo"><?php esc_html_e( 'Cómo arrendar', 'casa-a' ); ?></h1>
		</header>

		<ol class="casa-a-pasos">
			<?php foreach ( $casa_a_pasos as $paso ) : ?>
				<li class="casa-a-paso">
					<span class="casa-a-paso-numero" aria-hidden="true"><?php echo esc_html( $paso[0] ); ?></span>
					<h2 class="casa-a-paso-titulo"><?php echo esc_html( $paso[1] ); ?></h2>
					<p class="casa-a-paso-texto"><?php echo esc_html( $paso[2] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ol>

		<a class="casa-a-button casa-a-arrendar-boton" href="<?php echo esc_url( home_url( '/producto/' ) ); ?>"><?php esc_html_e( 'Ir al catálogo', 'casa-a' ); ?></a>

		<section class="casa-a-politicas" aria-labelledby="casa-a-politicas-titulo">
			<h2 id="casa-a-politicas-titulo" class="casa-a-politicas-titulo"><?php esc_html_e( 'Políticas de arriendo', 'casa-a' ); ?></h2>
			<?php foreach ( $casa_a_politicas as $indice => $politica ) : ?>
				<details class="casa-a-politica"<?php echo 3 === $indice ? ' open' : ''; ?>>
					<summary><?php echo esc_html( $politica[0] ); ?></summary>
					<p><?php echo esc_html( $politica[1] ); ?></p>
				</details>
			<?php endforeach; ?>
		</section>
	</div>
</main>
<?php
get_footer();
