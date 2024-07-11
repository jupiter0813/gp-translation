<?php namespace Wporg\TranslationEvents\Theme_2024;

/** @var string $site_header */
/** @var array $attributes */

$current_page_title = apply_filters( 'wporg_translate_page_title', '' );
$html_title         = implode( ' | ', array( $current_page_title, __( 'Translation Events', 'wporg-translate-events-2024' ) ) );

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<title><?php echo esc_html( $html_title ); ?></title>
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
		<?php wp_body_open(); ?>
		<div class="wp-site-blocks">
			<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo $site_header; ?>
			<div class="wp-block-group alignfull has-white-background-color has-background" style="padding-right:var(--wp--preset--spacing--edge-space);padding-bottom:18px;padding-left:var(--wp--preset--spacing--edge-space)">
				<h2 class="wp-block-heading"><?php echo esc_html( $current_page_title ); ?></h2>
