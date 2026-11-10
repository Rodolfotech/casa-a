<?php
/**
 * Modal reutilizable de producto: galería con miniaturas, datos y "Agregar a mi selección".
 *
 * Se abre desde cualquier elemento con data-modal-producto (JSON del producto).
 * Lo controla assets/js/producto-modal.js.
 *
 * @package CASA_A
 */
?>
<div class="casa-a-modal-fondo" data-modal-cerrar hidden></div>

<div class="casa-a-modal" role="dialog" aria-modal="true" aria-labelledby="casa-a-modal-titulo" aria-hidden="true" hidden>
	<button type="button" class="casa-a-modal-cerrar" data-modal-cerrar aria-label="<?php esc_attr_e( 'Cerrar', 'casa-a' ); ?>">
		<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
	</button>

	<div class="casa-a-modal-galeria">
		<div class="casa-a-modal-principal">
			<img src="" alt="" data-modal-principal>
		</div>
		<div class="casa-a-modal-miniaturas" data-modal-miniaturas></div>
	</div>

	<div class="casa-a-modal-info">
		<p class="casa-a-modal-etiqueta" data-modal-etiqueta></p>
		<h2 id="casa-a-modal-titulo" class="casa-a-modal-titulo" data-modal-titulo></h2>
		<p class="casa-a-modal-atributos" data-modal-atributos></p>

		<dl class="casa-a-modal-datos">
			<div>
				<dt><?php esc_html_e( 'Medidas', 'casa-a' ); ?></dt>
				<dd data-modal-medidas></dd>
			</div>
			<div>
				<dt><?php esc_html_e( 'Valor arriendo', 'casa-a' ); ?></dt>
				<dd data-modal-precio></dd>
			</div>
			<div>
				<dt><?php esc_html_e( 'Disponibles', 'casa-a' ); ?></dt>
				<dd data-modal-stock></dd>
			</div>
		</dl>

		<div class="casa-a-modal-accion">
			<div class="casa-a-cantidad">
				<button type="button" data-modal-menos aria-label="<?php esc_attr_e( 'Quitar una unidad', 'casa-a' ); ?>">−</button>
				<input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="3" value="1" class="casa-a-cantidad-input" data-modal-cantidad aria-label="<?php esc_attr_e( 'Cantidad', 'casa-a' ); ?>">
				<button type="button" data-modal-mas aria-label="<?php esc_attr_e( 'Agregar una unidad', 'casa-a' ); ?>">+</button>
			</div>
			<button type="button" class="casa-a-boton-enviar" data-modal-agregar><?php esc_html_e( 'Agregar a mi selección', 'casa-a' ); ?></button>
		</div>

		<p class="casa-a-aviso casa-a-aviso-ok" data-modal-aviso role="status" hidden></p>
	</div>
</div>
