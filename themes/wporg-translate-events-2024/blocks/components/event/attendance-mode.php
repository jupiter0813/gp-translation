<?php namespace Wporg\TranslationEvents\Theme_2024;
use Wporg\TranslationEvents\Translation_Events;

register_block_type(
	'wporg-translate-events-2024/components-event-attendance-mode',
	array(
		// The $attributes argument cannot be removed despite not being used in this function,
		// because otherwise it won't be available in render.php.
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function ( array $attributes ) {
			$event = Translation_Events::get_event_repository()->get_event( $attributes['id'] );
			return '<div class="wporg-marker-list-item__location">
' . esc_html( $event->attendance_mode() ) . '</div>';
		},
	)
);
