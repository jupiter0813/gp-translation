<?php

namespace Wporg\TranslationEvents;

class Templates {
	private const LEGACY_TEMPLATE_DIRECTORY   = __DIR__ . '/../templates';
	private static string $template_directory = self::LEGACY_TEMPLATE_DIRECTORY;

	public static function set_template_directory( string $path ): void {
		self::$template_directory = $path;
	}

	public static function header( array $data = array() ) {
		self::part( 'header', $data );
	}

	public static function footer( array $data = array() ) {
		self::part( 'footer', $data );
	}

	public static function part( string $template, array $data = array() ) {
		self::render( "parts/$template", $data );
	}

	public static function render( string $template, array $data = array() ) {
		if ( self::LEGACY_TEMPLATE_DIRECTORY === self::$template_directory ) {
			// Using legacy templates.
			gp_tmpl_load( $template, $data, self::$template_directory . '/' );
		} else {
			// Using a theme.
			// phpcs:ignore WordPress.PHP.DontExtract.extract_extract
			extract( $data, EXTR_SKIP );
			if ( str_ends_with( $template, '.html' ) ) {
				ob_start();
				include self::$template_directory . "/$template";
				$content = ob_get_clean();
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo do_blocks( $content );
			} else {
				include self::$template_directory . "/$template.php";
			}
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
