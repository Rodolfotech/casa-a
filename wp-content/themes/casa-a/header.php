<?php
/**
 * Encabezado del theme CASA A.
 *
 * @package CASA_A
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="casa-a-header">
	<?php get_template_part( 'template-parts/nav' ); ?>
</header>
<?php get_template_part( 'template-parts/seleccion' ); ?>
<?php get_template_part( 'template-parts/modal-producto' ); ?>
