<?php
/**
 * Panel "Mi selección": lista de productos, pestaña de proyectos y formulario de cotización.
 *
 * Se abre desde el enlace "Mi selección" del nav. El contenido de la lista lo dibuja
 * assets/js/seleccion.js a partir de lo guardado en el navegador.
 *
 * @package CASA_A
 */

$casa_a_hoy = gmdate( 'Y-m-d' );
?>
<div class="casa-a-drawer-fondo" data-seleccion-cerrar hidden></div>

<aside class="casa-a-drawer" role="dialog" aria-modal="true" aria-labelledby="casa-a-drawer-titulo" aria-hidden="true" hidden>
	<header class="casa-a-drawer-cabecera">
		<div>
			<h2 id="casa-a-drawer-titulo" class="casa-a-drawer-titulo"><?php esc_html_e( 'Mi selección', 'casa-a' ); ?></h2>
			<p class="casa-a-drawer-resumen" data-seleccion-resumen></p>
		</div>
		<button type="button" class="casa-a-drawer-cerrar" data-seleccion-cerrar aria-label="<?php esc_attr_e( 'Cerrar', 'casa-a' ); ?>">
			<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
		</button>
	</header>

	<div class="casa-a-drawer-tabs" role="tablist">
		<button type="button" role="tab" id="tab-productos" aria-controls="panel-productos" aria-selected="true" data-tab="productos"><?php esc_html_e( 'Productos', 'casa-a' ); ?></button>
		<button type="button" role="tab" id="tab-proyectos" aria-controls="panel-proyectos" aria-selected="false" data-tab="proyectos" tabindex="-1"><?php esc_html_e( 'Proyectos', 'casa-a' ); ?></button>
		<button type="button" role="tab" id="tab-cotizar" aria-controls="panel-cotizar" aria-selected="false" data-tab="cotizar" tabindex="-1"><?php esc_html_e( 'Cotizar', 'casa-a' ); ?></button>
	</div>

	<section class="casa-a-drawer-panel" role="tabpanel" id="panel-productos" aria-labelledby="tab-productos" data-panel="productos">
		<ul class="casa-a-drawer-lista" data-seleccion-lista></ul>
		<p class="casa-a-muted casa-a-drawer-vacio" data-seleccion-vacio><?php esc_html_e( 'Todavía no agregas productos. Usa "Agregar" en el catálogo.', 'casa-a' ); ?></p>
		<div class="casa-a-drawer-pie">
			<button type="button" class="casa-a-boton-enviar" data-seleccion-continuar disabled><?php esc_html_e( 'Continuar a cotización', 'casa-a' ); ?></button>
		</div>
	</section>

	<section class="casa-a-drawer-panel" role="tabpanel" id="panel-proyectos" aria-labelledby="tab-proyectos" data-panel="proyectos" hidden>
		<p class="casa-a-muted"><?php esc_html_e( 'Aún no tienes proyectos guardados.', 'casa-a' ); ?></p>
	</section>

	<section class="casa-a-drawer-panel" role="tabpanel" id="panel-cotizar" aria-labelledby="tab-cotizar" data-panel="cotizar" hidden>
		<?php
		$casa_a_estado = isset( $_GET['casa_a_cotizacion'] ) ? sanitize_key( wp_unslash( $_GET['casa_a_cotizacion'] ) ) : '';
		if ( 'error' === $casa_a_estado || 'fallo' === $casa_a_estado ) {
			?>
			<p class="casa-a-aviso casa-a-aviso-error" role="status" data-cotizacion-aviso><?php esc_html_e( 'No pudimos enviar la cotización. Revisa los datos (RUT válido, fechas y al menos un producto disponible) e intenta de nuevo.', 'casa-a' ); ?></p>
			<?php
		}
		?>

		<form class="casa-a-form casa-a-cotizacion" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-cotizacion-form>
			<input type="hidden" name="action" value="casa_a_cotizar">
			<input type="hidden" name="items" value="[]" data-cotizacion-items>
			<?php wp_nonce_field( 'casa_a_cotizar', 'casa_a_cotizar_nonce' ); ?>

			<div class="casa-a-campo">
				<label for="cot-nombre"><?php esc_html_e( 'Nombre', 'casa-a' ); ?> <span aria-hidden="true">*</span></label>
				<input type="text" id="cot-nombre" name="nombre" required autocomplete="name">
			</div>

			<div class="casa-a-campo">
				<label for="cot-rut"><?php esc_html_e( 'RUT', 'casa-a' ); ?> <span aria-hidden="true">*</span></label>
				<input type="text" id="cot-rut" name="rut" required maxlength="12" placeholder="12.345.678-9" autocomplete="off" pattern="[0-9.]{1,11}-[0-9kK]">
			</div>

			<div class="casa-a-campo">
				<label for="cot-productora"><?php esc_html_e( 'Productora', 'casa-a' ); ?></label>
				<input type="text" id="cot-productora" name="productora" autocomplete="organization">
			</div>

			<div class="casa-a-campo">
				<label for="cot-email"><?php esc_html_e( 'Email', 'casa-a' ); ?> <span aria-hidden="true">*</span></label>
				<input type="email" id="cot-email" name="email" required autocomplete="email">
			</div>

			<div class="casa-a-campo">
				<label for="cot-telefono"><?php esc_html_e( 'Teléfono', 'casa-a' ); ?></label>
				<input type="tel" id="cot-telefono" name="telefono" autocomplete="tel">
			</div>

			<div class="casa-a-form-fila">
				<div class="casa-a-campo">
					<label for="cot-retiro"><?php esc_html_e( 'Fecha de retiro', 'casa-a' ); ?> <span aria-hidden="true">*</span></label>
					<input type="date" id="cot-retiro" name="fecha_retiro" required min="<?php echo esc_attr( $casa_a_hoy ); ?>">
				</div>
				<div class="casa-a-campo">
					<label for="cot-devolucion"><?php esc_html_e( 'Fecha de devolución', 'casa-a' ); ?> <span aria-hidden="true">*</span></label>
					<input type="date" id="cot-devolucion" name="fecha_devolucion" required min="<?php echo esc_attr( $casa_a_hoy ); ?>">
				</div>
			</div>

			<?php /* Campo trampa anti-spam: oculto para personas. */ ?>
			<div class="casa-a-trampa" aria-hidden="true">
				<label>Sitio web <input type="text" name="sitio_web" tabindex="-1" autocomplete="off"></label>
			</div>

			<button type="submit" class="casa-a-boton-enviar" data-cotizacion-enviar><?php esc_html_e( 'Solicitar cotización', 'casa-a' ); ?></button>
		</form>
	</section>
</aside>
