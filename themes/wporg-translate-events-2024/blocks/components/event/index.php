<?php namespace Wporg\TranslationEvents\Theme_2024;

register_block_type(
	'wporg-translate-events-2024/components-event-start',
	array(
		// The $attributes argument cannot be removed despite not being used in this function,
		// because otherwise it won't be available in render.php.
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function ( array $attributes ) {
			global $translation_events_lookup;
			return '<div>' . esc_html( $translation_events_lookup[ $attributes['id'] ]->start()->format( 'F j, Y' ) ) . '</div>';
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
			return '<h1>' . esc_html( $translation_events_lookup[ $attributes['id'] ]->title() ) . '</h1>';
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
			return '<div>' . esc_html( $translation_events_lookup[ $attributes['id'] ]->attendance_mode() ) . '</div>';
		},
	)
);
