<?php
/**
 * Componente de navegación reutilizable: logo, menú, buscador de productos
 * y "Mi selección".
 *
 * Uso en cualquier plantilla: get_template_part( 'template-parts/nav' );
 *
 * Los enlaces salen del menú asignado a la ubicación "principal" (Apariencia →
 * Menús). Si no hay menú, se usa casa_a_menu_fallback().
 *
 * @package CASA_A
 */

// Pendiente: conectar con la selección real del usuario. Por ahora siempre es 0.
$casa_a_seleccion = 0;
?>
<div class="casa-a-wrap casa-a-nav">
	<a class="casa-a-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">Casa-a</a>

	<nav class="casa-a-nav-links" aria-label="<?php esc_attr_e( 'Navegación principal', 'casa-a' ); ?>">
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

	<form class="casa-a-buscador" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<input type="hidden" name="post_type" value="producto">
		<label class="screen-reader-text" for="casa-a-buscar"><?php esc_html_e( 'Buscar producto', 'casa-a' ); ?></label>
		<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
		<input type="search" id="casa-a-buscar" name="s" placeholder="<?php esc_attr_e( 'Buscar producto o código...', 'casa-a' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>">
	</form>

	<a class="casa-a-seleccion" href="#" aria-label="<?php echo esc_attr( sprintf( __( 'Mi selección: %d', 'casa-a' ), $casa_a_seleccion ) ); ?>">
		<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M5 8h14l-1 12H6L5 8z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/></svg>
		<span class="casa-a-seleccion-texto"><?php esc_html_e( 'Mi selección', 'casa-a' ); ?></span>
		<span class="casa-a-seleccion-contador"><?php echo esc_html( $casa_a_seleccion ); ?></span>
	</a>
</div>
