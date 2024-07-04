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
		self::header(
			array(
				'title'       => __( 'My Events', 'wporg-translate-events-2024' ),
				'breadcrumbs' => array(
					array(
						'title' => __( 'My Events', 'wporg-translate-events-2024' ),
						'url'   => null,
					),
				),
			)
		);
		$header_content = ob_get_clean();

		ob_start();
		self::footer();
		$footer_content = ob_get_clean();

		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		echo do_blocks( $header_content );
		echo do_blocks( $page_title_content );
		echo do_blocks( $page_content );
		echo do_blocks( $footer_content );
		// phpcs:enable
	}

	private static function header( array $data = array() ) {
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

	private static function footer( array $data = array() ) {
		self::pattern( 'wporg-translation-events-2024/footer', $data );
	}

	public static function part( string $name ) {
		ob_start();
		include self::$theme_dir . "/parts/$name.html";
		$content = ob_get_clean();
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo do_blocks( $content );
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
