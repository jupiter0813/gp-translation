<?php namespace Wporg\TranslationEvents\Theme_2024;

register_block_type(
	'wporg-translate-events-2024/components-event-start',
	array(
		// The $attributes argument cannot be removed despite not being used in this function,
		// because otherwise it won't be available in render.php.
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function ( array $attributes ) {
			global $translation_events_lookup;
			return '<time class="wporg-marker-list-item__date-time">' . esc_html( $translation_events_lookup[ $attributes['id'] ]->start()->format( 'F j, Y' ) ) . '</time>';
		},
	)
);
register_block_type(
	'wporg-translate-events-2024/components-event-title',
	array(
		// The $attributes argument cannot be removed despite not being used in this function,
		// because otherwise it won't be available in render.php.
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function ( array $attributes ) {
			global $translation_events_lookup;
			return '<h3 class="wporg-marker-list-item__title">
					<a href="' . esc_url( \Wporg\TranslationEvents\Urls::event_details( $attributes['id'] ) ) . '">' .
						esc_html( $translation_events_lookup[ $attributes['id'] ]->title() ) .
					'</a>
				</h3>';
		},
	)
);
register_block_type(
	'wporg-translate-events-2024/components-event-attendance-mode',
	array(
		// The $attributes argument cannot be removed despite not being used in this function,
		// because otherwise it won't be available in render.php.
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function ( array $attributes ) {
			global $translation_events_lookup;
			return '<div class="wporg-marker-list-item__location">
' . esc_html( $translation_events_lookup[ $attributes['id'] ]->attendance_mode() ) . '</div>';
		},
	)
);

register_block_type(
	'wporg-translate-events-2024/components-event-my-event-flag',
	array(
		// The $attributes argument cannot be removed despite not being used in this function,
		// because otherwise it won't be available in render.php.
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function ( array $attributes ) {
			global $translation_events_lookup;
			return '<span class="my-event-flag">' . $attributes['my_event_flag'] . '</span>';
		},
	)
);
