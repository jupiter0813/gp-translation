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
			Translation_Events::get_assets()->use_new_design();
		}
	}

	public static function header( array $data = array() ) {
		self::part( 'header', $data );
	}

	public static function footer( array $data = array() ) {
		self::part( 'footer', $data );
	}

	public static function part( string $template, array $data ) {
		self::render( "parts/$template", $data );
	}

	public static function render( string $template, array $data = array() ) {
		$template_path = self::$use_new_design ? self::NEW_DESIGN_PATH : __DIR__ . '/../templates';
		if ( self::$use_new_design ) {
			// phpcs:ignore WordPress.PHP.DontExtract.extract_extract
			extract( $data, EXTR_SKIP );
			include "$template_path/$template.php";
		} else {
			gp_tmpl_load( $template, $data, "$template_path/" );
		}
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
}
