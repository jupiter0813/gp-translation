<?php

namespace Wporg\TranslationEvents\Theme;

use WP_Block_Patterns_Registry;

add_action(
	'wp_head',
	function (): void {
		register_blocks();
		register_patterns();

		wp_enqueue_style(
			'wporg-translate-events-2024-style',
			get_stylesheet_uri(),
			array(),
			filemtime( __DIR__ . '/style.css' )
		);
	}
);

function register_blocks(): void {
	include_once __DIR__ . '/blocks/breadcrumbs.php';
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
						__( 'Could not register file "%s" as a block pattern ("Slug" field missing)', 'gp-translation-events' ),
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
						__( 'Could not register file "%1$s" as a block pattern (invalid slug "%2$s")', 'gp-translation-events' ),
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
						__( 'Could not register file "%s" as a block pattern ("Title" field missing)', 'gp-translation-events' ),
						$file
					)
				),
				''
			);
			continue;
		}

		// phpcs:disable WordPress.WP.I18n.LowLevelTranslationFunction
		// phpcs:disable WordPress.WP.I18n.NonSingularStringLiteralText
		$pattern_data['title'] = translate_with_gettext_context( $pattern_data['title'], 'Pattern title', 'gp-translation-events' );
		if ( ! empty( $pattern_data['description'] ) ) {
			$pattern_data['description'] = translate_with_gettext_context( $pattern_data['description'], 'Pattern description', 'gp-translation-events' );
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
