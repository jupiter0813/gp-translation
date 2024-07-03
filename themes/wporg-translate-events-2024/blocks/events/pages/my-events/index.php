<?php
namespace Wporg\TranslationEvents\Theme_2024;

register_block_type(
	'wporg-translate-events/events-pages-my-events',
	array(
		'render_callback' => function ( array $attributes ) {
			return include_once __DIR__ . '/render.php';
		},
	)
);
