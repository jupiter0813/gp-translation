<?php
namespace Wporg\TranslationEvents\Theme_2024;
use Wporg\TranslationEvents\Translation_Events;

register_block_type(
	'wporg-translate-events-2024/event-start',
	array(
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function ( array $attributes ) {
			$event_id = $attributes['id'] ?? 0;
			$event = Translation_Events::get_event_repository()->get_event( $event_id );
			$start = $event->start()->format( 'F j, Y' );
			return '<time class="wporg-marker-list-item__date-time">' . esc_html( $start ) . '</time>';
		},
	)
);

register_block_type(
	'wporg-translate-events-2024/event-end',
	array(
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function ( array $attributes ) {
			$event_id = $attributes['id'] ?? 0;
			$event = Translation_Events::get_event_repository()->get_event( $event_id );
			$end = $event->end()->format( 'F j, Y' );
			return '<p>' . esc_html( $end ) . '</p>';
		},
	)
);
