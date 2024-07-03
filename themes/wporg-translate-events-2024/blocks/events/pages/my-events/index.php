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

			return include_once __DIR__ . '/render.php';
		},
	)
);
