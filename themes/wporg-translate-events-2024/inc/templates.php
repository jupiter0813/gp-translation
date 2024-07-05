<?php

namespace Wporg\TranslationEvents\Theme_2024;

class Templates {
	public static function page( string $name, array $attributes = array() ): string {
		$page_content = self::block( "wporg-translate-events-2024/events-pages-$name", $attributes );

		// The header and footer blocks must be rendered last, because other blocks may register styles or scripts,
		// or modify the page title, or add breadcrumbs.
		$header_content = self::block( 'wporg-translate-events-2024/header' );
		$footer_content = self::block( 'wporg-translate-events-2024/footer' );

		return $header_content . $page_content . $footer_content;
	}

	private static function block( string $name, array $data = array() ): string {
		$json = empty( $data ) ? '{}' : wp_json_encode( $data );
		return do_blocks( "<!-- wp:$name $json /-->" );
	}
}
