<?php
/**
 * Plantilla de la página Contacto: datos de contacto, mapa y formulario de mensaje.
 *
 * @package CASA_A
 */

get_header();

// Datos de contacto. Mismos valores que el footer: cambiarlos aquí y en template-parts/footer.php.
$casa_a_direccion = 'Av. Italia 1234, Providencia, Santiago';
$casa_a_telefono  = '+56 9 0000 0000';
$casa_a_correo    = 'contacto@casaa.cl';
$casa_a_horario   = 'Lunes a viernes 9:00–18:00 · Sábado 10:00–14:00';
$casa_a_respuesta = 'Menos de 24 horas hábiles';

$estado = isset( $_GET['casa_a_estado'] ) ? sanitize_key( wp_unslash( $_GET['casa_a_estado'] ) ) : '';
$avisos = array(
	'ok'      => array( 'casa-a-aviso-ok', __( 'Gracias. Recibimos tu mensaje y te responderemos pronto.', 'casa-a' ) ),
	'error'   => array( 'casa-a-aviso-error', __( 'Revisa los campos obligatorios e intenta de nuevo.', 'casa-a' ) ),
	'archivo' => array( 'casa-a-aviso-error', __( 'El archivo no se pudo subir. Usa JPG, PNG o PDF.', 'casa-a' ) ),
	'fallo'   => array( 'casa-a-aviso-error', __( 'No pudimos enviar el mensaje. Intenta más tarde o escríbenos directamente.', 'casa-a' ) ),
);
?>
<main class="casa-a-main casa-a-contacto">
	<div class="casa-a-wrap">
		<?php
		while ( have_posts() ) {
			the_post();
			if ( '' !== trim( get_the_content() ) ) {
				?>
				<div class="casa-a-content casa-a-contacto-intro"><?php the_content(); ?></div>
				<?php
			}
		}
		?>

		<header class="casa-a-contacto-cabecera">
			<p class="casa-a-etiqueta"><?php esc_html_e( 'Hablemos', 'casa-a' ); ?></p>
			<h1 class="casa-a-contacto-titulo"><?php esc_html_e( 'Contacto', 'casa-a' ); ?></h1>
			<p class="casa-a-contacto-bajada"><?php esc_html_e( '¿Tienes una producción en camino? Cuéntanos qué necesitas y te ayudamos a encontrar las piezas ideales.', 'casa-a' ); ?></p>
		</header>

		<div class="casa-a-contacto-layout">

			<div class="casa-a-contacto-info">
				<ul class="casa-a-contacto-datos">
					<li>
						<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/></svg>
						<div>
							<span class="casa-a-dato-etiqueta"><?php esc_html_e( 'Dirección', 'casa-a' ); ?></span>
							<span class="casa-a-dato-valor"><?php echo esc_html( $casa_a_direccion ); ?></span>
						</div>
					</li>
					<li>
						<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a1 1 0 0 1-1 1A16 16 0 0 1 4 5a1 1 0 0 1 1-1z"/></svg>
						<div>
							<span class="casa-a-dato-etiqueta"><?php esc_html_e( 'Teléfono', 'casa-a' ); ?></span>
							<a class="casa-a-dato-valor" href="<?php echo esc_attr( 'tel:' . preg_replace( '/[^0-9+]/', '', $casa_a_telefono ) ); ?>"><?php echo esc_html( $casa_a_telefono ); ?></a>
						</div>
					</li>
					<li>
						<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>
						<div>
							<span class="casa-a-dato-etiqueta"><?php esc_html_e( 'Email', 'casa-a' ); ?></span>
							<a class="casa-a-dato-valor" href="<?php echo esc_attr( 'mailto:' . $casa_a_correo ); ?>"><?php echo esc_html( $casa_a_correo ); ?></a>
						</div>
					</li>
					<li>
						<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
						<div>
							<span class="casa-a-dato-etiqueta"><?php esc_html_e( 'Horario', 'casa-a' ); ?></span>
							<span class="casa-a-dato-valor"><?php echo esc_html( $casa_a_horario ); ?></span>
						</div>
					</li>
					<li>
						<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="12" cy="13" r="7"/><path d="M12 10v3l2 1M9 3h6"/></svg>
						<div>
							<span class="casa-a-dato-etiqueta"><?php esc_html_e( 'Respuesta', 'casa-a' ); ?></span>
							<span class="casa-a-dato-valor"><?php echo esc_html( $casa_a_respuesta ); ?></span>
						</div>
					</li>
				</ul>

				<div class="casa-a-mapa">
					<iframe
						title="<?php esc_attr_e( 'Mapa de ubicación de Casa-a', 'casa-a' ); ?>"
						src="<?php echo esc_url( 'https://www.google.com/maps?q=' . rawurlencode( $casa_a_direccion ) . '&output=embed' ); ?>"
						loading="lazy"
						referrerpolicy="no-referrer-when-downgrade"></iframe>
					<a class="casa-a-mapa-enlace" href="<?php echo esc_url( 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $casa_a_direccion ) ); ?>" target="_blank" rel="noopener">
						<?php esc_html_e( 'Abrir en Maps', 'casa-a' ); ?>
						<svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M14 4h6v6M20 4l-9 9M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"/></svg>
					</a>
				</div>
			</div>

			<section class="casa-a-formulario casa-a-contacto-form" aria-labelledby="casa-a-form-titulo">
				<h2 id="casa-a-form-titulo" class="screen-reader-text"><?php esc_html_e( 'Enviar un mensaje', 'casa-a' ); ?></h2>

				<?php if ( isset( $avisos[ $estado ] ) ) : ?>
					<p class="casa-a-aviso <?php echo esc_attr( $avisos[ $estado ][0] ); ?>" role="status"><?php echo esc_html( $avisos[ $estado ][1] ); ?></p>
				<?php endif; ?>

				<form class="casa-a-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="casa_a_contacto">
					<?php wp_nonce_field( 'casa_a_contacto', 'casa_a_contacto_nonce' ); ?>

					<div class="casa-a-campo">
						<label for="casa-a-nombre"><?php esc_html_e( 'Nombre', 'casa-a' ); ?></label>
						<input type="text" id="casa-a-nombre" name="nombre" required autocomplete="name">
					</div>

					<div class="casa-a-campo">
						<label for="casa-a-email"><?php esc_html_e( 'Email', 'casa-a' ); ?></label>
						<input type="email" id="casa-a-email" name="email" required autocomplete="email">
					</div>

					<div class="casa-a-campo">
						<label for="casa-a-telefono"><?php esc_html_e( 'Teléfono (opcional)', 'casa-a' ); ?></label>
						<input type="tel" id="casa-a-telefono" name="telefono" autocomplete="tel">
					</div>

					<div class="casa-a-campo">
						<label for="casa-a-descripcion"><?php esc_html_e( 'Mensaje', 'casa-a' ); ?></label>
						<textarea id="casa-a-descripcion" name="descripcion" rows="6" maxlength="2000" required></textarea>
					</div>

					<p class="casa-a-privacidad-texto">
						<?php esc_html_e( 'Tus datos se usan solo para responder tu consulta. Revisa nuestra', 'casa-a' ); ?>
						<a href="<?php echo esc_url( home_url( '/politica-de-privacidad/' ) ); ?>"><?php esc_html_e( 'Política de privacidad', 'casa-a' ); ?></a>.
					</p>

					<?php /* Campo trampa anti-spam: oculto para personas. */ ?>
					<div class="casa-a-trampa" aria-hidden="true">
						<label>Sitio web <input type="text" name="sitio_web" tabindex="-1" autocomplete="off"></label>
					</div>

					<button type="submit" class="casa-a-boton-enviar"><?php esc_html_e( 'Enviar mensaje', 'casa-a' ); ?></button>
				</form>
			</section>

		</div>
	</div>
</main>
<?php
get_footer();
