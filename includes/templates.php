<?php

namespace Wporg\TranslationEvents;

class Templates {
	private const LEGACY_TEMPLATE_DIRECTORY   = __DIR__ . '/../templates';
	private static string $template_directory = self::LEGACY_TEMPLATE_DIRECTORY;

	public static function set_template_directory( string $path ): void {
		self::$template_directory = $path;
	}

	public static function header( array $data = array() ) {
		$breadcrumbs = array(
			array(
				'url'   => home_url(),
				'title' => __( 'Home', 'wporg-translate-events-2024' ),
			),
			array(
				'url'   => Urls::events_home(),
				'title' => __( 'Events', 'wporg-translate-events-2024' ),
			),
		);

		if ( ! empty( $data['breadcrumbs'] ) ) {
			$breadcrumbs = array_merge( $breadcrumbs, $data['breadcrumbs'] );
		}

		add_filter(
			'wporg_block_site_breadcrumbs',
			function () use ( $breadcrumbs ): array {
				return $breadcrumbs;
			}
		);
		self::pattern( 'wporg-translation-events-2024/header', $data );
	}

	public static function footer( array $data = array() ) {
		self::pattern( 'wporg-translation-events-2024/footer', $data );
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
