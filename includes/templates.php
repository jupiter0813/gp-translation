<?php

namespace Wporg\TranslationEvents;

use WP_Block_Patterns_Registry;

class Templates {
	private const NEW_DESIGN_PATH = __DIR__ . '/../templates/new-design';

	private static bool $use_new_design = false;

	public static function use_new_design( bool $also_in_production = false ): void {
		if ( $also_in_production ) {
			// If it's enabled for production, it's also enabled for development, so it's always enabled.
			self::$use_new_design = true;
		} else {
			// Only enable if new design has been explicitly enabled.
			self::$use_new_design = defined( 'TRANSLATION_EVENTS_NEW_DESIGN' ) && TRANSLATION_EVENTS_NEW_DESIGN;
		}

		if ( self::$use_new_design ) {
			self::register_blocks();
			self::register_patterns();
			( new Styles( self::$use_new_design ) )->init();
		}
	}

	public static function render( string $template, array $data = array() ) {
		$template_path = self::$use_new_design ? self::NEW_DESIGN_PATH : __DIR__ . '/../templates';
		gp_tmpl_load( $template, $data, "$template_path/" );
	}

	public static function part( string $template, array $data ) {
		self::render( "parts/$template", $data );
	}

	public static function pattern( string $name, array $data = array() ): void {
		$data['slug'] = $name;
		$json         = wp_json_encode( $data );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo do_blocks( "<!-- wp:pattern $json /-->" );
	}

	public static function block( string $name, array $data = array() ): void {
		$json = empty( $data ) ? '{}' : wp_json_encode( $data );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo do_blocks( "<!-- wp:$name $json /-->" );
	}

	private static function register_patterns(): void {
		// This code has been copied from:
		// https://github.com/WordPress/wordcamp.org/blob/production/public_html/wp-content/mu-plugins/blocks/patterns.php.

		$files = glob( self::NEW_DESIGN_PATH . '/patterns/*.php' );
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

	private static function register_blocks(): void {
		include_once self::NEW_DESIGN_PATH . '/blocks/breadcrumbs.php';
	}

	/**
	 * @deprecated Not for use in new design. Instead, the header template should be used.
	 */
	public static function header( array $data = array() ) {
		self::part( 'header', $data );
	}

	/**
	 * @deprecated Not for use in new design. Instead, the footer template should be used.
	 */
	public static function footer( array $data = array() ) {
		self::part( 'footer', $data );
	}
}
