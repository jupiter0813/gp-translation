<?php
namespace Wporg\TranslationEvents\Theme_2024;

register_block_type(
	'wporg-translate-events-2024/events-pages-my-events',
	array(
		'render_callback' => function ( array $attributes ) {
			add_filter(
				'wp_title',
				function (): string {
					$title = __( 'My Events', 'wporg-translate-events-2024' );
					return implode( ' | ', array( $title, __( 'Translation Events', 'wporg-translate-events-2024' ) ) );
				}
			);

			add_filter(
				'wporg_block_site_breadcrumbs',
				function ( $breadcrumbs ): array {
					return array_merge(
						$breadcrumbs,
						array(
							array(
								'title' => __( 'My Events', 'wporg-translate-events-2024' ),
								'url'   => null,
							),
						)
					);
				}
			);

			return include_once __DIR__ . '/render.php';
		},
	)
);
