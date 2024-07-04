<?php

namespace Wporg\TranslationEvents\Theme_2024;

use Wporg\TranslationEvents\Urls;

class Renderer {
	private static string $theme_dir = __DIR__ . '/../';

	public static function page( string $name, array $attributes = array() ) {
		// Declare the base breadcrumbs, which apply to all pages.
		// Pages can add additional levels of breadcrumbs, if needed.
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

		// The block for the page must be rendered before the header,
		// because the page block sets the page title and the breadcrumbs.
		$page_content       = self::block( "wporg-translate-events-2024/events-pages-$name", $attributes );
		$page_title_content = self::block( 'wporg-translate-events-2024/page-title' );
		$header_content     = self::pattern( 'wporg-translation-events-2024/header' );
		$footer_content     = self::pattern( 'wporg-translation-events-2024/footer' );

		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $header_content;
		echo $page_title_content;
		echo $page_content;
		echo $footer_content;
		// phpcs:enable
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
