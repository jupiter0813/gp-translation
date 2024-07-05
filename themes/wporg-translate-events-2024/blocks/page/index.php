<?php namespace Wporg\TranslationEvents\Theme_2024;

register_block_type(
	'wporg-translate-events-2024/page',
	array(
		'render_callback' => function ( array $attributes ) {
			ob_start();
			include_once __DIR__ . '/render.php';
			return ob_get_clean();
		},
	)
);
