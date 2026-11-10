<?php
/**
 * Catálogo de productos: filtros a la izquierda y grilla de productos con "Agregar".
 * También sirve para los resultados de búsqueda de productos.
 *
 * @package CASA_A
 */

get_header();

$casa_a_es_busqueda = is_search();
$casa_a_total       = (int) $GLOBALS['wp_query']->found_posts;

$casa_a_grupos = array(
	'categoria' => array( __( 'Categoría', 'casa-a' ), 'categoria_producto' ),
	'estilo'    => array( __( 'Estilo', 'casa-a' ), 'estilo_producto' ),
	'color'     => array( __( 'Color', 'casa-a' ), 'color_producto' ),
	'material'  => array( __( 'Material', 'casa-a' ), 'material_producto' ),
);
?>
<main class="casa-a-main casa-a-catalogo-pagina">
	<div class="casa-a-wrap">

		<header class="casa-a-catalogo-cabecera">
			<div>
				<?php if ( $casa_a_es_busqueda ) : ?>
					<p class="casa-a-etiqueta"><?php esc_html_e( 'Búsqueda', 'casa-a' ); ?></p>
					<h1 class="casa-a-catalogo-titulo"><?php echo esc_html( sprintf( __( 'Resultados para «%s»', 'casa-a' ), get_search_query() ) ); ?></h1>
				<?php else : ?>
					<p class="casa-a-etiqueta"><?php esc_html_e( 'Arriendo', 'casa-a' ); ?></p>
					<h1 class="casa-a-catalogo-titulo"><?php esc_html_e( 'Catálogo', 'casa-a' ); ?></h1>
				<?php endif; ?>
			</div>
			<p class="casa-a-muted casa-a-catalogo-conteo">
				<?php echo esc_html( sprintf( _n( '%s producto', '%s productos', $casa_a_total, 'casa-a' ), number_format_i18n( $casa_a_total ) ) ); ?>
			</p>
		</header>

		<div class="casa-a-catalogo-layout">

			<aside class="casa-a-filtros" aria-label="<?php esc_attr_e( 'Filtros del catálogo', 'casa-a' ); ?>">
				<a class="casa-a-filtro-check <?php echo casa_a_catalogo_filtro_activo( 'disponibles', '1' ) ? 'is-active' : ''; ?>" href="<?php echo esc_url( casa_a_catalogo_url_filtro( 'disponibles', '1' ) ); ?>">
					<span class="casa-a-caja" aria-hidden="true"><?php echo casa_a_catalogo_filtro_activo( 'disponibles', '1' ) ? '✓' : ''; ?></span>
					<?php esc_html_e( 'Solo disponibles', 'casa-a' ); ?>
				</a>

				<?php
				foreach ( $casa_a_grupos as $parametro => $grupo ) {
					$terminos = get_terms(
						array(
							'taxonomy'   => $grupo[1],
							'hide_empty' => false,
						)
					);

					if ( empty( $terminos ) || is_wp_error( $terminos ) ) {
						continue;
					}
					?>
					<section class="casa-a-filtro-grupo">
						<h2 class="casa-a-filtro-titulo"><?php echo esc_html( $grupo[0] ); ?></h2>
						<div class="casa-a-chips">
							<?php foreach ( $terminos as $termino ) : ?>
								<a class="casa-a-chip <?php echo casa_a_catalogo_filtro_activo( $parametro, $termino->slug ) ? 'is-active' : ''; ?>" href="<?php echo esc_url( casa_a_catalogo_url_filtro( $parametro, $termino->slug ) ); ?>"><?php echo esc_html( $termino->name ); ?></a>
							<?php endforeach; ?>
						</div>
					</section>
					<?php
				}
				?>
				<?php if ( ! empty( $_GET ) ) : ?>
					<a class="casa-a-limpiar-filtros" href="<?php echo esc_url( get_post_type_archive_link( "producto" ) ); ?>"><?php esc_html_e( "Limpiar filtros", "casa-a" ); ?></a>
				<?php endif; ?>
			</aside>

			<section class="casa-a-catalogo-resultados">
				<?php if ( have_posts() ) : ?>
					<div class="casa-a-productos">
						<?php
						while ( have_posts() ) {
							the_post();

							$casa_a_id         = get_the_ID();
							$casa_a_codigo     = get_post_meta( $casa_a_id, 'casa_a_codigo', true );
							$casa_a_codigo     = $casa_a_codigo ? $casa_a_codigo : 'CA-' . str_pad( (string) $casa_a_id, 4, '0', STR_PAD_LEFT );
							$casa_a_precio     = get_post_meta( $casa_a_id, 'casa_a_precio_dia', true );
							$casa_a_disponible = '0' !== get_post_meta( $casa_a_id, 'casa_a_disponible', true );
							?>
							<article class="casa-a-producto">
								<div class="casa-a-producto-imagen">
									<button type="button" class="casa-a-producto-abrir" data-modal-producto="<?php echo esc_attr( function_exists( 'casa_a_producto_datos_modal' ) ? wp_json_encode( casa_a_producto_datos_modal( $casa_a_id ) ) : '' ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Ver %s', 'casa-a' ), get_the_title() ) ); ?>">
										<?php
										if ( has_post_thumbnail() ) {
											the_post_thumbnail( 'medium_large', array( 'loading' => 'lazy' ) );
										}
										?>
									</button>
									<?php if ( ! $casa_a_disponible ) : ?>
										<span class="casa-a-sin-stock"><?php esc_html_e( 'Sin stock', 'casa-a' ); ?></span>
									<?php endif; ?>
								</div>

								<div class="casa-a-producto-info">
									<p class="casa-a-producto-codigo"><?php echo esc_html( $casa_a_codigo ); ?></p>
									<div class="casa-a-producto-fila">
										<h3 class="casa-a-producto-nombre"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
										<button
											type="button"
											class="casa-a-agregar"
											data-id="<?php echo esc_attr( $casa_a_id ); ?>"
											data-codigo="<?php echo esc_attr( $casa_a_codigo ); ?>"
											data-nombre="<?php echo esc_attr( get_the_title() ); ?>"
											data-precio="<?php echo esc_attr( $casa_a_precio ); ?>"
											data-imagen="<?php echo esc_url( get_the_post_thumbnail_url( $casa_a_id, 'thumbnail' ) ); ?>"
											data-disponible="<?php echo $casa_a_disponible ? '1' : '0'; ?>"
											data-stock="<?php echo esc_attr( get_post_meta( $casa_a_id, 'casa_a_stock', true ) ); ?>"
											<?php disabled( ! $casa_a_disponible ); ?>
										><?php esc_html_e( 'Agregar', 'casa-a' ); ?></button>
									</div>
									<?php if ( '' !== (string) $casa_a_precio ) : ?>
										<p class="casa-a-producto-precio">
											<?php echo esc_html( '$' . number_format( (int) $casa_a_precio, 0, ',', '.' ) ); ?>
											<span><?php esc_html_e( '/ día', 'casa-a' ); ?></span>
										</p>
									<?php endif; ?>
								</div>
							</article>
							<?php
						}
						?>
					</div>

					<?php the_posts_pagination(); ?>
				<?php else : ?>
					<p class="casa-a-muted"><?php esc_html_e( 'No encontramos productos con esos filtros.', 'casa-a' ); ?></p>
				<?php endif; ?>
			</section>

		</div>
	</div>
</main>
<?php
get_footer();
