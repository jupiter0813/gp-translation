<?php namespace Wporg\TranslationEvents\Theme_2024;
use Wporg\TranslationEvents\Translation_Events;

register_block_type(
	'wporg-translate-events-2024/components-event-start',
	array(
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function ( array $attributes ) {
			$event = Translation_Events::get_event_repository()->get_event( $attributes['id'] );
			return '<time class="wporg-marker-list-item__date-time">' . esc_html( $event->start()->format( 'F j, Y' ) ) . '</time>';
		},
	)
);

register_block_type(
	'wporg-translate-events-2024/components-event-end',
	array(
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function ( array $attributes ) {
			$event = Translation_Events::get_event_repository()->get_event( $attributes['id'] );
			return '<p>' . esc_html( $event->end() ) . '</p>';
		},
	)
);
