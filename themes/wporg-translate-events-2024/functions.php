<?php

namespace Wporg\TranslationEvents\Theme_2024;

use Wporg\TranslationEvents\Urls;

function register_blocks(): void {
	include_once __DIR__ . '/blocks/header/index.php';
	include_once __DIR__ . '/blocks/footer/index.php';
	include_once __DIR__ . '/blocks/page/index.php';
	include_once __DIR__ . '/blocks/pages/events/my-events/index.php';
}

add_action(
	'init',
	function (): void {
		do_action( 'wporg_translate_events_theme_init' );
	}
);

add_action(
	'wporg_translate_events_theme_init',
	function (): void {
		register_blocks();

		add_action(
			'wp_head',
			function (): void {
				add_social_tags(
					esc_html__( 'Translation Events', 'wporg-translate-events-2024' ),
					Urls::events_home(),
					esc_html__( 'WordPress Translation Events', 'wporg-translate-events-2024' ),
					Urls::event_default_image()
				);

				wp_enqueue_style(
					'wporg-translate-events-2024-style',
					get_stylesheet_uri(),
					array(),
					filemtime( __DIR__ . '/style.css' )
				);
			}
		);
	}
);

add_filter(
	'wporg_block_navigation_menus',
	function (): array {
		return array(
			'site-header-menu' => array(
				array(
					'label' => esc_html__( 'Events', 'wporg-plugins' ),
					'url'   => 'https://translate.wordpress.org/events/',
				),
				array(
					'label' => esc_html__( 'Team', 'wporg-plugins' ),
					'url'   => 'https://make.wordpress.org/polyglots/teams/',
				),
				array(
					'label' => esc_html__( 'Requests', 'wporg-plugins' ),
					'url'   => 'https://make.wordpress.org/polyglots/?resolved=unresolved',
				),
				array(
					'label' => esc_html__( 'Weekly Chats', 'wporg-plugins' ),
					'url'   => 'https://make.wordpress.org/polyglots/category/weekly-chats/',
				),
				array(
					'label' => esc_html__( 'Translate', 'wporg-plugins' ),
					'url'   => 'https://translate.wordpress.org/',
				),
				array(
					'label' => esc_html__( 'Handbook', 'wporg-plugins' ),
					'url'   => 'https://make.wordpress.org/polyglots/handbook/',
				),
			),
		);
	}
);

// Declare the base breadcrumbs, which apply to all pages.
// Pages can add additional levels of breadcrumbs.
add_filter(
	'wporg_block_site_breadcrumbs',
	function (): array {
		return array(
			array(
				'url'   => home_url(),
				'title' => __( 'Home', 'wporg-translate-events-2024' ),
			),
			array(
				'url'   => Urls::events_home(),
				'title' => __( 'Events', 'wporg-translate-events-2024' ),
			),
		);
	}
);

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
