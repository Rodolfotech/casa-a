<?php
/**
 * Componente de pie de página reutilizable: marca, menú, contacto, ayuda y legal.
 *
 * Uso en cualquier plantilla:
 *   get_template_part( 'template-parts/footer' );
 * Para cambiar datos sin editar el archivo:
 *   get_template_part( 'template-parts/footer', null, array( 'telefono' => '+56 2 1234 5678' ) );
 *
 * Los enlaces del menú salen del menú "principal" (Apariencia → Menús).
 * Si no hay menú, se usa casa_a_menu_fallback().
 *
 * @package CASA_A
 */

$casa_a_args = isset( $args ) && is_array( $args ) ? $args : array();

$casa_a_datos = wp_parse_args(
	$casa_a_args,
	array(
		'descripcion'   => __( 'Arriendo de mobiliario y decoración para filmaciones, fotografía y eventos.', 'casa-a' ),
		'direccion'     => 'Av. Italia 1234, Providencia, Santiago',
		'telefono'      => '+56 9 0000 0000',
		'correo'        => 'contacto@casaa.cl',
		'instagram'     => '#',
		'facebook'      => '#',
		'ayuda_titulo'  => __( '¿Primera vez?', 'casa-a' ),
		'ayuda_texto'   => __( 'Revisa cómo funciona el arriendo, garantías y plazos.', 'casa-a' ),
		// Número con código de país, solo dígitos (ej. 56912345678). Vacío oculta WhatsApp.
		'whatsapp'      => '',
		'ubicacion'     => __( 'Santiago, Chile', 'casa-a' ),
	)
);

$casa_a_tel_href = 'tel:' . preg_replace( '/[^0-9+]/', '', $casa_a_datos['telefono'] );
$casa_a_wa_href  = $casa_a_datos['whatsapp'] ? 'https://wa.me/' . preg_replace( '/[^0-9]/', '', $casa_a_datos['whatsapp'] ) : '';
?>
<footer class="casa-a-footer">
	<div class="casa-a-wrap casa-a-footer-grid">

		<div class="casa-a-footer-marca">
			<a class="casa-a-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">Casa-a</a>
			<p><?php echo esc_html( $casa_a_datos['descripcion'] ); ?></p>
			<div class="casa-a-footer-redes">
				<a href="<?php echo esc_url( $casa_a_datos['instagram'] ); ?>" aria-label="Instagram">
					<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="0.8" fill="currentColor"/></svg>
				</a>
				<a href="<?php echo esc_url( $casa_a_datos['facebook'] ); ?>" aria-label="Facebook">
					<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M15 3h-2.5A3.5 3.5 0 0 0 9 6.5V9H6.5v3H9v9h3.5v-9h2.5l.5-3h-3V7a1 1 0 0 1 1-1H15z"/></svg>
				</a>
			</div>
		</div>

		<div>
			<h3 class="casa-a-footer-titulo"><?php esc_html_e( 'Menú', 'casa-a' ); ?></h3>
			<nav class="casa-a-footer-menu" aria-label="<?php esc_attr_e( 'Menú del pie de página', 'casa-a' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'principal',
						'container'      => false,
						'menu_class'     => 'casa-a-nav-list',
						'fallback_cb'    => 'casa_a_menu_fallback',
					)
				);
				?>
			</nav>
		</div>

		<div>
			<h3 class="casa-a-footer-titulo"><?php esc_html_e( 'Contacto', 'casa-a' ); ?></h3>
			<ul class="casa-a-footer-lista">
				<li>
					<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/></svg>
					<span><?php echo esc_html( $casa_a_datos['direccion'] ); ?></span>
				</li>
				<li>
					<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a1 1 0 0 1-1 1A16 16 0 0 1 4 5a1 1 0 0 1 1-1z"/></svg>
					<a href="<?php echo esc_attr( $casa_a_tel_href ); ?>"><?php echo esc_html( $casa_a_datos['telefono'] ); ?></a>
				</li>
				<li>
					<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>
					<a href="mailto:<?php echo esc_attr( $casa_a_datos['correo'] ); ?>"><?php echo esc_html( $casa_a_datos['correo'] ); ?></a>
				</li>
			</ul>
		</div>

		<div>
			<h3 class="casa-a-footer-titulo"><?php echo esc_html( $casa_a_datos['ayuda_titulo'] ); ?></h3>
			<p class="casa-a-footer-ayuda"><?php echo esc_html( $casa_a_datos['ayuda_texto'] ); ?></p>
			<a class="casa-a-button" href="<?php echo esc_url( home_url( '/como-arrendar/' ) ); ?>"><?php esc_html_e( 'Cómo arrendar', 'casa-a' ); ?></a>
			<?php if ( $casa_a_wa_href ) : ?>
				<a class="casa-a-footer-whatsapp" href="<?php echo esc_url( $casa_a_wa_href ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Escríbenos por WhatsApp', 'casa-a' ); ?></a>
			<?php endif; ?>
		</div>

	</div>

	<div class="casa-a-footer-base">
		<div class="casa-a-wrap casa-a-footer-base-interior">
			<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> Casa-a · <?php echo esc_html( $casa_a_datos['ubicacion'] ); ?></p>
			<nav aria-label="<?php esc_attr_e( 'Legal', 'casa-a' ); ?>">
				<a href="<?php echo esc_url( home_url( '/politica-de-privacidad/' ) ); ?>"><?php esc_html_e( 'Política de privacidad', 'casa-a' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/terminos-y-condiciones/' ) ); ?>"><?php esc_html_e( 'Términos y condiciones', 'casa-a' ); ?></a>
			</nav>
		</div>
	</div>
</footer>

<?php if ( $casa_a_wa_href ) : ?>
	<a class="casa-a-whatsapp-flotante" href="<?php echo esc_url( $casa_a_wa_href ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Escribir por WhatsApp', 'casa-a' ); ?>">
		<svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M20 11.5A8.5 8.5 0 0 1 8.2 19.3L4 20.5l1.2-4A8.5 8.5 0 1 1 20 11.5z"/><path d="M9 9.5c0 3 2.5 5.5 5.5 5.5l1-1.2-2-1-.8.8a4.5 4.5 0 0 1-2.5-2.5l.8-.8-1-2z"/></svg>
	</a>
<?php endif; ?>
