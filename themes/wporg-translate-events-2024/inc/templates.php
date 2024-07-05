<?php

namespace Wporg\TranslationEvents\Theme_2024;

class Templates {
	private static string $theme_dir = __DIR__ . '/../';

	public static function page( string $name, array $attributes = array() ): string {
		$page_content       = self::block( "wporg-translate-events-2024/events-pages-$name", $attributes );
		$page_title_content = self::block( 'wporg-translate-events-2024/page-title' );

		// The header and footer blocks must be rendered last, because other blocks may register styles or scripts,
		// or modify the page title, or add breadcrumbs.
		$header_content = self::block( 'wporg-translate-events-2024/header' );
		$footer_content = self::block( 'wporg-translate-events-2024/footer' );

		return $header_content . $page_title_content . $page_content . $footer_content;
	}

	public static function part( string $name ): string {
		ob_start();
		include self::$theme_dir . "/parts/$name.html";
		$content = ob_get_clean();
		return do_blocks( $content );
	}

	public static function pattern( string $name ): string {
		$json = wp_json_encode( array( 'slug' => $name ) );
		return do_blocks( "<!-- wp:pattern $json /-->" );
	}

	public static function block( string $name, array $data = array() ): string {
		$json = empty( $data ) ? '{}' : wp_json_encode( $data );
		return do_blocks( "<!-- wp:$name $json /-->" );
	}
}
