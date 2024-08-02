<?php namespace Wporg\TranslationEvents\Theme_2024;
use Wporg\TranslationEvents\Translation_Events;

register_block_type(
	'wporg-translate-events-2024/components-event-excerpt',
	array(
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function ( array $attributes ) {
			$event = Translation_Events::get_event_repository()->get_event( $attributes['id'] );
			return '<p>' . esc_html( get_the_excerpt( $event->id() ) ) . '</p>';
		},
	)
);
