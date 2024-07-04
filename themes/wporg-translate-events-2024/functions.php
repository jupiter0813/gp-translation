<?php

namespace Wporg\TranslationEvents\Theme_2024;

use WP_Block_Patterns_Registry;
use Wporg\TranslationEvents\Urls;

require_once __DIR__ . '/autoload.php';

add_action(
	'init',
	function (): void {
		do_action( 'wporg_translate_events_theme_init' );
	}
);

add_action(
	'wporg_translate_events_theme_init',
	function (): void {
		register_patterns();
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

function register_blocks(): void {
	include_once __DIR__ . '/blocks/page-title/index.php';
	include_once __DIR__ . '/blocks/events/pages/my-events/index.php';
}

function register_patterns(): void {
	// This code has been copied from:
	// https://github.com/WordPress/wordcamp.org/blob/production/public_html/wp-content/mu-plugins/blocks/patterns.php.

	$files = glob( __DIR__ . '/patterns/*.php' );
	if ( ! is_array( $files ) ) {
		return;
	}

	$headers = array(
		'title' => 'Title',
		'slug'  => 'Slug',
	);

	foreach ( $files as $file ) {
		$pattern_data = get_file_data( $file, $headers );

		if ( empty( $pattern_data['slug'] ) ) {
			_doing_it_wrong(
				'register_patterns',
				esc_html(
					sprintf(
					/* translators: %s: file name. */
						__( 'Could not register file "%s" as a block pattern ("Slug" field missing)', 'wporg-translate-events-2024' ),
						$file
					)
				),
				''
			);
			continue;
		}

		if ( ! preg_match( '/^[A-z0-9\/_-]+$/', $pattern_data['slug'] ) ) {
			_doing_it_wrong(
				'register_patterns',
				esc_html(
					sprintf(
					/* translators: %1s: file name; %2s: slug value found. */
						__( 'Could not register file "%1$s" as a block pattern (invalid slug "%2$s")', 'wporg-translate-events-2024' ),
						$file,
						$pattern_data['slug']
					)
				),
				''
			);
		}

		if ( WP_Block_Patterns_Registry::get_instance()->is_registered( $pattern_data['slug'] ) ) {
			continue;
		}

		// Title is a required property.
		if ( ! $pattern_data['title'] ) {
			_doing_it_wrong(
				'register_patterns',
				esc_html(
					sprintf(
					/* translators: %1s: file name; %2s: slug value found. */
						__( 'Could not register file "%s" as a block pattern ("Title" field missing)', 'wporg-translate-events-2024' ),
						$file
					)
				),
				''
			);
			continue;
		}

		// phpcs:disable WordPress.WP.I18n.LowLevelTranslationFunction
		// phpcs:disable WordPress.WP.I18n.NonSingularStringLiteralText
		$pattern_data['title'] = translate_with_gettext_context( $pattern_data['title'], 'Pattern title', 'wporg-translate-events-2024' );
		if ( ! empty( $pattern_data['description'] ) ) {
			$pattern_data['description'] = translate_with_gettext_context( $pattern_data['description'], 'Pattern description', 'wporg-translate-events-2024' );
		}
		// phpcs:enable

		// The actual pattern content is the output of the file.
		ob_start();
		include_once $file;
		$pattern_data['content'] = ob_get_clean();
		if ( ! $pattern_data['content'] ) {
			continue;
		}

		register_block_pattern( $pattern_data['slug'], $pattern_data );
	}
}

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
