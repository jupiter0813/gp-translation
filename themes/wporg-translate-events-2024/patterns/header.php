<?php
/**
 * Title: Header
 * Slug: wporg-translation-events-2024/header
 */
namespace Wporg\TranslationEvents\Theme_2024;

use Wporg\TranslationEvents\Templates;
use Wporg\TranslationEvents\Urls;

/** @var string $url */
/** @var string $html_title */
/** @var string $html_description */
/** @var string $image_url */

$html_title       = $html_title ?? esc_html__( 'Translation Events', 'wporg-translate-events-2024' );
$url              = $url ?? Urls::events_home();
$html_description = $html_description ?? esc_html__( 'WordPress Translation Events', 'wporg-translate-events-2024' );
$image_url        = $image_url ?? Urls::event_default_image();

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<title><?php echo esc_html( $html_title ); ?></title>
		<?php
		wp_head();
		add_social_tags( $html_title, $url, $html_description, $image_url );
		?>
	</head>

	<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<div class="wp-site-blocks">
		<?php Templates::part( 'header.html' ); ?>
<?php

/**
 * Add social tags to the head of the page.
 *
 * @param string $html_title       The title of the page.
 * @param string $url              The URL of the page.
 * @param string $html_description The description of the page.
 * @param string $image_url        The URL of the image to use.
 *
 * @return void
 */
function add_social_tags( string $html_title, string $url, string $html_description, string $image_url ) {
	$meta_tags = array(
		'name'     => array(
			'twitter:card'        => 'summary',
			'twitter:site'        => '@WordPress',
			'twitter:title'       => esc_attr( $html_title ),
			'twitter:description' => esc_attr( $html_description ),
			'twitter:creator'     => '@WordPress',
			'twitter:image'       => esc_url( $image_url ),
			'twitter:image:alt'   => esc_attr( $html_title ),
		),
		'property' => array(
			'og:url'              => esc_url( $url ),
			'og:title'            => esc_attr( $html_title ),
			'og:description'      => esc_attr( $html_description ),
			'og:site_name'        => esc_attr( get_bloginfo() ),
			'og:image:url'        => esc_url( $image_url ),
			'og:image:secure_url' => esc_url( $image_url ),
			'og:image:type'       => 'image/png',
			'og:image:width'      => '1200',
			'og:image:height'     => '675',
			'og:image:alt'        => esc_attr( $html_title ),
		),
	);

	foreach ( $meta_tags as $name => $content ) {
		foreach ( $content as $key => $value ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '<meta ' . esc_attr( $name ) . '="' . esc_attr( $key ) . '" content="' . esc_attr( $value ) . '" />' . "\n";
		}
	}
}
