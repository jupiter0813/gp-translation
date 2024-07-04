<?php

namespace Wporg\TranslationEvents\Theme_2024;

use Wporg\TranslationEvents\Urls;

class Renderer {
	private static string $theme_dir = __DIR__ . '/../';

	public static function page( string $name, array $attributes = array() ) {



		// The block for the page must be rendered before the header, because the block sets wp_title and the breadcrumbs.
		ob_start();
		self::block( "wporg-translate-events-2024/events-pages-$name", $attributes );
		$page_content = ob_get_clean();

		ob_start();
		self::block( 'wporg-translate-events-2024/page-title' );
		$page_title_content = ob_get_clean();

		ob_start();
		$breadcrumbs = array(
			array(
				'url'   => home_url(),
				'title' => __( 'Home', 'wporg-translate-events-2024' ),
			),
			array(
				'url'   => Urls::events_home(),
				'title' => __( 'Events', 'wporg-translate-events-2024' ),
			),
			array(
				'title' => __( 'My Events', 'wporg-translate-events-2024' ),
				'url'   => null,
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
		self::pattern( 'wporg-translation-events-2024/header' );
		$header_content = ob_get_clean();

		ob_start();
		self::pattern( 'wporg-translation-events-2024/footer' );
		$footer_content = ob_get_clean();

		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		echo do_blocks( $header_content );
		echo do_blocks( $page_title_content );
		echo do_blocks( $page_content );
		echo do_blocks( $footer_content );
		// phpcs:enable
	}

	public static function part( string $name ) {
		ob_start();
		include self::$theme_dir . "/parts/$name.html";
		$content = ob_get_clean();
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo do_blocks( $content );
	}

	public static function pattern( string $name ): void {
		$json = wp_json_encode( array( 'slug' => $name ) );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo do_blocks( "<!-- wp:pattern $json /-->" );
	}

	public static function block( string $name, array $data = array() ): void {
		$json = empty( $data ) ? '{}' : wp_json_encode( $data );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo do_blocks( "<!-- wp:$name $json /-->" );
	}
}
