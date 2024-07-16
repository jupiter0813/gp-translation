<?php namespace Wporg\TranslationEvents\Theme_2024;

register_block_type(
	'wporg-translate-events-2024/components/events/start-date',
	array(
		// The $attributes argument cannot be removed despite not being used in this function,
		// because otherwise it won't be available in render.php.
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function ( array $attributes ) {
			global $post;
			ob_start();
			$event_start = get_post_meta( $post->ID, '_event_start', true );
			include_once __DIR__ . '/render.php';
			return do_blocks( ob_get_clean() );
		},
	)
);
