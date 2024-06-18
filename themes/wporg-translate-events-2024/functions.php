<?php

add_action(
	'wp_head',
	function (): void {
		include_once __DIR__ . '/blocks/breadcrumbs.php';

		wp_enqueue_style(
			'wporg-translate-events-2024-style',
			get_stylesheet_uri(),
			array(),
			filemtime( __DIR__ . '/style.css' )
		);
	}
);
