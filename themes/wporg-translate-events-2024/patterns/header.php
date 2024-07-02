<?php
/**
 * Title: Header
 * Slug: wporg-translation-events-2024/header
 */
namespace Wporg\TranslationEvents\Theme_2024;

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
		<?php wp_body_open(); ?>
		<div class="wp-site-blocks">
			<?php Renderer::part( 'header' ); ?>
