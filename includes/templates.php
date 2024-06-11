<?php

namespace Wporg\TranslationEvents;

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
			self::register_styles();
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
		echo do_blocks( "<!-- $name $json /-->" );
	}

	private static function register_patterns(): void {
		include_once self::NEW_DESIGN_PATH . '/patterns/local-nav.php';
	}

	private static function register_blocks(): void {
		include_once self::NEW_DESIGN_PATH . '/blocks/breadcrumbs.php';
	}

	private static function register_styles(): void {
		wp_register_style(
			'translation-events-new-design-css',
			plugins_url( 'assets/css/new-design.css', __DIR__ ),
			array( 'dashicons' ),
			filemtime( __DIR__ . '/../assets/css/new-design.css' )
		);
		gp_enqueue_styles( 'translation-events-new-design-css' );
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
